<?php
namespace App\Repository;

use App\Entity\Image;
use PDO;

class ImageRepository
{
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function save(Image $image): void {
        $stmt = $this->db->prepare('INSERT INTO images (path, article_id, imageTitle) VALUES (:path, :article_id, :imageTitle)');
        $stmt->bindValue(':path', $image->getPath(), PDO::PARAM_STR);
        $stmt->bindValue(':imageTitle', $image->getImageTitle(), PDO::PARAM_STR);
        $stmt->bindValue(':article_id', $image->getArticleId(), PDO::PARAM_INT);
        $stmt->execute();
    }

    public function findByArticleId(int $articleId): array {
        $stmt = $this->db->prepare('SELECT * FROM images WHERE article_id = :article_id');
        $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        $images = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $image = new Image();
            $image->setId($row['id']);
            $image->setPath($row['path']);
            $image->setImageTitle($row['imageTitle']);
            $image->setArticleId($row['article_id']);
            $images[] = $image;
        }

        return $images;
    }

        public function update(Image $image): void {
        $stmt = $this->db->prepare('UPDATE images SET path = :path, imageTitle = :imageTitle WHERE id = :id');
        $stmt->bindValue(':path', $image->getPath(), PDO::PARAM_STR);
        $stmt->bindValue(':imageTitle', $image->getImageTitle(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $image->getId(), PDO::PARAM_INT);
        $stmt->execute();
    }

        public function deleteByArticleId(int $articleId): void {
        $stmt = $this->db->prepare('DELETE FROM images WHERE article_id = :article_id');
        $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();
    }

        public function delete(int $imageId): void
    {
            $stmt = $this->db->prepare('DELETE FROM images WHERE id = :id');
            $stmt->bindValue(':id', $imageId, PDO::PARAM_INT);
            $stmt->execute();
    }
}
