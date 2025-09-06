<?php
// src/Service/ArticleManager.php
namespace App\Service;

use App\Entity\Articles;
use App\Entity\Image;
use App\Repository\ArticleRepository;
use App\Repository\ImageRepository;

class ArticleManager
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private ImageRepository $imageRepository,
        private ImageUploader $uploader
    ) {}

    public function create(array $data, array $files): array
    {
        $errors = [];

        $uploadedImages = $this->uploader->uploadMultiple($files['images'] ?? [], $errors);

        if (!empty($errors)) {
            return $errors;
        }

        $article = new Articles([
            'title' => $data['title'] ?? '',
            'content' => $data['content'] ?? '',
            'categoryId' => $data['category_id'] ?? null,
            'images' => $uploadedImages,
        ]);

        $this->articleRepository->createArticle($article);

        return [];
    }

    public function update(int $id, array $data, array $files): array
    {
        $errors = [];
        $article = $this->articleRepository->findById($id);

        if (!$article) {
            throw new \Exception("Article not found");
        }

        // Suppression d'images
        foreach ($data['delete_images'] ?? [] as $imageId) {
            foreach ($article->getImages() as $image) {
                if ($image->getId() == $imageId) {
                    $this->imageRepository->delete($imageId);
                    $article->removeImage($image);
                    break;
                }
            }
        }

        // Upload de nouvelles images
        if (!empty($files['images']['name'][0])) {
            $uploaded = $this->uploader->uploadMultiple($files['images'], $errors);
            foreach ($uploaded as $img) {
                $article->addImage($img);
            }
        }

        // Mise à jour des images existantes
        foreach ($article->getImages() as $image) {
            $id = $image->getId();

            if (isset($data['image_titles'][$id])) {
                $image->setImageTitle(trim($data['image_titles'][$id]));
            }

            if (isset($files['image_files']['error'][$id]) &&
                $files['image_files']['error'][$id] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploader->uploadSingle([
                    'name' => $files['image_files']['name'][$id],
                    'type' => $files['image_files']['type'][$id],
                    'tmp_name' => $files['image_files']['tmp_name'][$id],
                    'error' => $files['image_files']['error'][$id],
                    'size' => $files['image_files']['size'][$id],
                ], $errors);

                if ($uploaded) {
                    $image->setPath($uploaded['path']);
                }
            }

            $this->imageRepository->update($image);
        }

        $article->setTitle($data['title']);
        $article->setContent($data['content']);
        $article->setCategoryId($data['category_id']);

        $this->articleRepository->updateArticle($article);

        return $errors;
    }

    public function delete(int $id): void
    {
        $article = $this->articleRepository->findById($id);
        if (!$article) {
            throw new \Exception("Article not found");
        }

        $this->articleRepository->deleteArticle($id);
    }
}
