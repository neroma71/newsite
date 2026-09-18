<?php
namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Service\ImageUploader;
use App\Entity\Articles;
use App\Repository\ImageRepository;
use App\Repository\CategoryRepository;
use App\Service\YoutubeEmbedService;

class ArticleController extends BaseController
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

        public function create(array &$errors = []): void
        {
            // GET → afficher le formulaire
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $categories = $this->categoryRepository->findAll();

                $this->render('manage/createArticle.php', [
                    'categories' => $categories,
                    'errors' => $errors
                ]);
                return;
            }

            // POST → traitement
            $this->ensureCsrf();

            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $images = $_FILES['images'] ?? null;
            $categoryId = $_POST['category_id'] ?? null;

            if (!is_numeric($categoryId) || !$this->categoryRepository->findById((int)$categoryId)) {
                throw new \Exception("Catégorie invalide.");
            }

            $uploadDir = __DIR__ . '/../../../public/uploads/';
            $uploader = new ImageUploader($uploadDir);

            $uploadedImages = [];

            if (
                $images &&
                isset($images['error']) &&
                is_array($images['error']) &&
                array_filter($images['error'], fn($err) => $err === UPLOAD_ERR_OK)
            ) {
                $uploadedImages = $uploader->uploadMultiple($images, $errors);
            }

            if (!empty($errors)) {
                $categories = $this->categoryRepository->findAll();

                $this->render('manage/createArticle.php', [
                    'categories' => $categories,
                    'errors' => $errors
                ]);
                return;
            }

            $article = new Articles([
                'title' => $title,
                'content' => $content,
                'images' => $uploadedImages,
                'categoryId' => (int)$categoryId
            ]);

            $this->articleRepository->createArticle($article);

            header('Location: /newsite/manage/articles');
            exit;
        }

        public function update(int $id, array &$errors = []): void
        {
            $article = $this->articleRepository->findById($id);

            if (!$article) {
                throw new \Exception("Article not found");
            }

            // GET → afficher le formulaire
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $categories = $this->categoryRepository->findAll();

                $this->render('manage/editArticle.php', [
                    'article' => $article,
                    'categories' => $categories,
                    'errors' => $errors
                ]);
                return;
            }

            // POST → traitement
            $this->ensureCsrf();

            $title = trim($_POST['title'] ?? $article->getTitle());
            $content = trim($_POST['content'] ?? $article->getContent());
            $images = $_FILES['images'] ?? null;
            $imageFiles = $_FILES['image_files'] ?? [];
            $categoryId = $_POST['category_id'] ?? $article->getCategoryId();
            $imageTitles = $_POST['image_titles'] ?? [];
            $deleteImages = $_POST['delete_images'] ?? [];

            $uploadDir = __DIR__ . '/../../../public/uploads/';
            $uploader = new ImageUploader($uploadDir);

            // Suppression des images cochées
            foreach ($deleteImages as $imageIdToDelete) {

                $imageToDelete = null;

                foreach ($article->getImages() as $image) {
                    if ($image->getId() == $imageIdToDelete) {
                        $imageToDelete = $image;
                        break;
                    }
                }

                if ($imageToDelete) {
                    $filePath = $uploadDir . basename($imageToDelete->getPath());

                    if (is_file($filePath)) {
                        unlink($filePath);
                    }

                    $this->imageRepository->delete($imageIdToDelete);
                    $article->removeImage($imageToDelete);
                }
            }

            // Upload de nouvelles images
            if (
                $images &&
                isset($images['error']) &&
                is_array($images['error']) &&
                array_filter($images['error'], fn($err) => $err === UPLOAD_ERR_OK)
            ) {
                $uploadedImages = $uploader->uploadMultiple($images, $errors);

                foreach ($uploadedImages as $image) {
                    $article->addImage($image);
                }
            }

            // Modification des images existantes
            foreach ($article->getImages() as $image) {

                $imgId = $image->getId();

                if (isset($imageTitles[$imgId])) {
                    $image->setImageTitle(trim($imageTitles[$imgId]));
                }

                if (
                    isset($imageFiles['error'][$imgId]) &&
                    $imageFiles['error'][$imgId] === UPLOAD_ERR_OK
                ) {
                    $file = [
                        'name' => $imageFiles['name'][$imgId],
                        'type' => $imageFiles['type'][$imgId],
                        'tmp_name' => $imageFiles['tmp_name'][$imgId],
                        'error' => $imageFiles['error'][$imgId],
                        'size' => $imageFiles['size'][$imgId],
                    ];

                    $uploaded = $uploader->uploadSingle($file, $errors);

                    if ($uploaded) {

                        $oldPath = $uploadDir . basename($image->getPath());

                        if (is_file($oldPath)) {
                            unlink($oldPath);
                        }

                        $image->setPath('/uploads/' . $uploaded['name']);
                    }
                }

                $this->imageRepository->update($image);
            }

            // Validation catégorie
            if (
                !is_numeric($categoryId) ||
                !$this->categoryRepository->findById((int)$categoryId)
            ) {
                throw new \Exception("Catégorie invalide.");
            }

            // Mise à jour de l'article
            $article->setTitle($title);
            $article->setContent($content);
            $article->setCategoryId((int)$categoryId);

            $this->articleRepository->updateArticle($article);

            header('Location: /newsite/manage/articles');
            exit;
        }

        public function delete(): void
        {
            $this->ensureMethod('POST');
            $this->ensureCsrf();

            try {
                $id = (int)($_POST['delete_id'] ?? 0);

                if ($id <= 0) {
                    throw new \Exception("ID d'article invalide.");
                }

                $article = $this->articleRepository->findById($id);

                if (!$article) {
                    throw new \Exception("Aucun article trouvé avec l'ID $id.");
                }

                $uploadDir = __DIR__ . '/../../../public/uploads/';

                // Suppression des images physiques + BDD
                foreach ($article->getImages() as $image) {

                    $filePath = $uploadDir . basename($image->getPath());

                    if (is_file($filePath)) {
                        unlink($filePath);
                    }

                    $this->imageRepository->delete($image->getId());
                }

                // Suppression de l'article
                $this->articleRepository->deleteArticle($id);

                header('Location: /newsite/manage/articles');
                exit;

            } catch (\Exception $e) {
                error_log($e->getMessage());

                header('Location: /newsite/manage/articles?error=1');
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

       private function abort404(): void
            {
            http_response_code(404);
            require VIEW_PATH . '/front/404.php';
            exit;
            }

      public function show(): void
        {
            if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
                 $this->abort404();
            }

            $id = (int) $_GET['id'];

            $article = $this->articleRepository->findById($id);

            if (!$article) {
                 $this->abort404();
            }

            $categoryId = isset($_GET['category']) && ctype_digit($_GET['category'])
                ? (int) $_GET['category']
                : $article->getCategoryId();

            $categorie = $this->categoryRepository->findById($categoryId);

            if (!$categorie) {
                 $this->abort404();
            }

            $categories = $this->categoryRepository->findAll();

            $splitContent = $this->youtubeEmbedService->extract($article->getContent());

            $prevNext = $this->articleRepository->findPrevNext($id, $categoryId);

            $this->render('front/article.php', [
                'article' => $article,
                'categories' => $categories,
                'categorie' => $categorie,
                'categoryId' => $categoryId,
                'prevId' => $prevNext['prev'],
                'nextId' => $prevNext['next'],
                'splitContent' => $splitContent,
            ]);
        }

         public function manager(): void
        {
            $articles = $this->articleRepository->findAllWithCategory();

            $this->render('manage/articleManager.php', [
                'articles' => $articles,
            ]);
        }
}