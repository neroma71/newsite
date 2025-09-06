<?php
namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Service\ImageUploader;
use App\Entity\Articles;
use App\Repository\ImageRepository;

class ArticleController
{
    private ArticleRepository $articleRepository;
    private ImageRepository $imageRepository;

    public function __construct(ArticleRepository $articleRepository, ImageRepository $imageRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->imageRepository = $imageRepository;
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $images = $_FILES['images'] ?? null;
            $categoryId = $_POST['category_id'] ?? null;

            $errors = [];
            $uploadDir = __DIR__ . '/../../../public/uploads/';
            $uploader = new ImageUploader($uploadDir);

            $uploadedImages = $uploader->uploadMultiple($images, $errors);

            if (empty($errors)) {
                $article = new Articles([
                    'title' => $title,
                    'content' => $content,
                    'images' => $uploadedImages,
                    'categoryId' => $categoryId
                ]);
                $this->articleRepository->createArticle($article);
                header('Location: /jerome2/views/manage/articlemanager.php');
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
                $title = $_POST['title'] ?? $article->getTitle();
                $content = $_POST['content'] ?? $article->getContent();
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

                // mise à jour des autres infos
                $article->setTitle($title);
                $article->setContent($content);
                $article->setCategoryId($categoryId);

                // sauvegarde de l'update de l'article
                $this->articleRepository->updateArticle($article);

                header('Location: /newsite/views/manage/articlemanager.php');
                exit;
            }
        }

      public function delete(int $id, string $csrfToken)
            {
                if (!isset($_SESSION['csrf_token']) || $csrfToken !== $_SESSION['csrf_token']) {
                    throw new \Exception('Token CSRF invalide');
                }

                $article = $this->articleRepository->findById($id);
                if (!$article) {
                    throw new \Exception("Article not found");
                }

                $this->articleRepository->deleteArticle($id);
                header('Location: /newsite/views/manage/articlemanager.php');
                exit;
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

    public function getPrevNextArticleIds(int $articleId, int $categoryId): array
    {
        $articles = $this->articleRepository->findByCategoryId($categoryId, 1000, 0);
        $prevId = $nextId = null;
        $currentIndex = null;
        foreach ($articles as $idx => $art) {
            if ($art->getId() == $articleId) {
                $currentIndex = $idx;
                break;
            }
        }
        if ($currentIndex !== null) {
            if ($currentIndex > 0) {
                $prevId = $articles[$currentIndex - 1]->getId();
            }
            if ($currentIndex < count($articles) - 1) {
                $nextId = $articles[$currentIndex + 1]->getId();
            }
        }
        return ['prev' => $prevId, 'next' => $nextId];
    }

    public static function embedYoutube($content) {
        // Remplace les liens YouTube par des iframes
        $pattern = '/https?:\/\/(www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/';
        $replacement = '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/$2" frameborder="0" allowfullscreen loading="lazy"></iframe>';
        return preg_replace($pattern, $replacement, $content);
    }

    public static function embedYoutubeOembed($content) {
         // Remplace le balisage <oembed> CKEditor par un iframe YouTube
        return preg_replace_callback(
            '/<figure class="media"><oembed url="https:\/\/youtu\.be\/([a-zA-Z0-9_-]+)[^"]*"><\/oembed><\/figure>/',
            function ($matches) {
                $videoId = $matches[1];
                return '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/' . $videoId . '" frameborder="0" allowfullscreen loading="lazy"></iframe>';
            },
            $content
        );
    }

    public static function extractYoutubeOembed($content) {
        // Récupère tous les <figure class="media"><oembed ...></oembed></figure>
        $videos = [];
        preg_match_all('/<figure class="media"><oembed url="https:\/\/youtu\.be\/([a-zA-Z0-9_-]+)[^"]*"><\/oembed><\/figure>/', $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $videos[] = '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/' . $m[1] . '" frameborder="0" allowfullscreen loading="lazy"></iframe>';
        }
        // Supprime les vidéos du contenu texte
        $text = preg_replace('/<figure class="media"><oembed url="https:\/\/youtu\.be\/([a-zA-Z0-9_-]+)[^"]*"><\/oembed><\/figure>/', '', $content);
        return ['text' => $text, 'videos' => $videos];
    }
}
