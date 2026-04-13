<?php
namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Service\ImageUploader;
use App\Entity\Articles;
use App\Repository\ImageRepository;
use App\Repository\CategoryRepository;
use App\Service\YoutubeEmbedService;

class ArticleController
{
    private ArticleRepository $articleRepository;
    private ImageRepository $imageRepository;
    private CategoryRepository $categoryRepository;
    private YoutubeEmbedService $youtubeEmbedService;

    public function __construct(ArticleRepository $articleRepository, ImageRepository $imageRepository, CategoryRepository $categoryRepository, YoutubeEmbedService $youtubeEmbedService )
    {
        $this->articleRepository = $articleRepository;
        $this->imageRepository = $imageRepository;
        $this->categoryRepository = $categoryRepository;
        $this->youtubeEmbedService = $youtubeEmbedService;
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Vérification CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Erreur CSRF : token invalide');
        }

            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $images = $_FILES['images'] ?? null;
            $categoryId = $_POST['category_id'] ?? null;

             if (!is_numeric($categoryId) || !$this->categoryRepository->findById((int)$categoryId)) {
                        throw new \Exception("Catégorie invalide.");
            }

            $errors = [];
            $uploadDir = __DIR__ . '/../../../public/uploads/';
            $uploader = new ImageUploader($uploadDir);

            //  gère le cas où aucune image n'est envoyée
            $uploadedImages = [];
            if (
                $images &&
                isset($images['error']) &&
                is_array($images['error']) &&
                // Vérifie s'il y a au moins une image envoyée sans erreur
                array_filter($images['error'], fn($err) => $err === UPLOAD_ERR_OK)
            ) {
                $uploadedImages = $uploader->uploadMultiple($images, $errors);
            }

            if (empty($errors)) {
                $article = new Articles([
                    'title' => $title,
                    'content' => $content,
                    'images' => $uploadedImages,
                    'categoryId' => $categoryId
                ]);
                $this->articleRepository->createArticle($article);
                header('Location: /newsite/views/manage/articlemanager.php');
                exit;
            } else {
                foreach ($errors as $error) {
                    echo "<p>Error: {$error}</p>";
                }
            }
        }
    }
       public function update(int $id)
        {
            $article = $this->articleRepository->findById($id);
            if (!$article) {
                throw new \Exception("Article not found");
            }
    
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                 $postedToken = $_POST['csrf_token'] ?? '';
                if (!$postedToken || !hash_equals($_SESSION['csrf_token'], $postedToken)) {
                throw new \Exception("Invalid CSRF token");
                }
                $title = trim($_POST['title'] ?? $article->getTitle());
                $content = trim($_POST['content'] ?? $article->getContent());
                $images = $_FILES['images'] ?? null;
                $imageFiles = $_FILES['image_files'] ?? [];
                $categoryId = $_POST['category_id'] ?? $article->getCategoryId();
                $imageTitles = $_POST['image_titles'] ?? [];
                $deleteImages = $_POST['delete_images'] ?? [];

                $errors = [];
                $uploadDir = __DIR__ . '/../../../public/uploads/';
                $uploader = new ImageUploader($uploadDir);

                // Suppression des images cochées
                foreach ($deleteImages as $imageIdToDelete) {
                    // Récupère l'objet Image associé (dans l'article)
                    $imageToDelete = null;
                    foreach ($article->getImages() as $image) {
                        if ($image->getId() == $imageIdToDelete) {
                            $imageToDelete = $image;
                            break;
                        }
                    }
                    if ($imageToDelete) {
                        // Suppression en base
                        $this->imageRepository->delete($imageIdToDelete);
                        // Suppression dans l'objet Article
                        $article->removeImage($imageToDelete);
                    }
                }

                // upload de nouvelles images (si envoyées)
                if ($images && isset($images['error'][0]) && $images['error'][0] === UPLOAD_ERR_OK) {
                    $uploadedImages = $uploader->uploadMultiple($images, $errors);
                    foreach ($uploadedImages as $image) {
                        $article->addImage($image);
                    }
                }

                // mise à jour des titres et remplacement d'images existantes
                foreach ($article->getImages() as $image) {
                    $imgId = $image->getId();

                    if (isset($imageTitles[$imgId])) {
                        $image->setImageTitle(trim($imageTitles[$imgId]));
                    }

                    if (isset($imageFiles['error'][$imgId]) && $imageFiles['error'][$imgId] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $imageFiles['name'][$imgId],
                            'type' => $imageFiles['type'][$imgId],
                            'tmp_name' => $imageFiles['tmp_name'][$imgId],
                            'error' => $imageFiles['error'][$imgId],
                            'size' => $imageFiles['size'][$imgId],
                        ];

                        $uploaded = $uploader->uploadSingle($file, $errors);

                        if ($uploaded) {
                            $image->setPath('/uploads/' . $uploaded['name']);
                        }
                    }

                    // mise à jour en BDD
                    $this->imageRepository->update($image);
                }

                   // Validation catégorie
                    if (!is_numeric($categoryId) || !$this->categoryRepository->findById((int)$categoryId)) {
                        throw new \Exception("Catégorie invalide.");
                    }

                // mise à jour des autres infos
                $article->setTitle($title);
                $article->setContent($content);
                $article->setCategoryId($categoryId);

                // sauvegarde de l'update de l'article
                $this->articleRepository->updateArticle($article);

                header('Location: ./articlemanager.php');
                exit;
            }
        }

      public function delete(): void
            {
                if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['delete_id'], $_POST['csrf_token'])) {
                    return;
                }

                try {
                    $id = (int) $_POST['delete_id'];
                    $csrfToken = $_POST['csrf_token'] ?? '';

                    // Vérification CSRF
                    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
                        throw new \Exception('Erreur CSRF : token invalide');
                    }

                    $article = $this->articleRepository->findById($id);
                    if (!$article) {
                        throw new \Exception("Aucun article trouvé avec l’ID $id.");
                    }

                    $this->articleRepository->deleteArticle($id);

                    header('Location: /newsite/views/manage/articlemanager.php');
                    exit;

                } catch (\Exception $e) {
                    echo "Erreur : " . htmlspecialchars($e->getMessage());
                    echo "<br><a href='javascript:history.back()'>Retour</a>";
                    exit;
                }
            }

      public function getPaginatedData(int $categoryId, int $currentPage = 1, int $limit = 10): array
            {
                $totalArticles = $this->articleRepository->findCount($categoryId);
                $totalPages = max(1, ceil($totalArticles / $limit));

                $currentPage = max(1, min($currentPage, $totalPages));
                $offset = ($currentPage - 1) * $limit;

                $articles = $this->articleRepository->findByCategoryId($categoryId, $limit, $offset);

                return [
                    'articles' => $articles,
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'totalArticles' => $totalArticles,
                ];
            }


            public function show(): void
            {
                $id = (int) ($_GET['id'] ?? 0);
                $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;

                if ($id <= 0) {
                    header('Location: /');
                    exit;
                }

                $article = $this->articleRepository->findById($id);

                if (!$article) {
                    header('Location: /');
                    exit;
                }

                $categories = $this->categoryRepository->findAll();

                if (!$categoryId || !$this->categoryRepository->findById($categoryId)) {
                    $categoryId = $article->getCategoryId();
                }

                $categorie = $this->categoryRepository->findById($categoryId);

                if (!$categorie) {
                    header('Location: /');
                    exit;
                }

                $splitContent = $this->youtubeEmbedService->extract($article->getContent());

                $prevNext = $this->articleRepository->findPrevNext($id, $categoryId);

                $this->render('article.php', [
                    'article' => $article,
                    'categories' => $categories,
                    'categorie' => $categorie,
                    'categoryId' => $categoryId,
                    'prevId' => $prevNext['prev'],
                    'nextId' => $prevNext['next'],
                    'splitContent' => $splitContent,
                ]);
            }

     private function render(string $view, array $data = []): void
    {
            extract($data);
            require __DIR__ . '/../../../public/' . $view;
    }
    
}
