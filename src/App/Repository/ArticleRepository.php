<?php
    namespace App\Repository;
    use App\Repository\CategoryRepository;
    use App\Entity\Articles;
    use PDO;

    class ArticleRepository
    {
        private PDO $db;
        private CategoryRepository $categoryRepository;
        private ImageRepository $imageRepository;    

            public function __construct(PDO $db, CategoryRepository $categoryRepository, ImageRepository $imageRepository)
            {
                $this->db = $db;
                $this->categoryRepository = $categoryRepository;
                $this->imageRepository = $imageRepository;
            }

            /**
             * Get the value of db
             */ 
            public function getDb()
            {
                return $this->db;
            }

            /**
             * Set the value of db
             *
             * @return  self
             */ 
            public function setDb($db): self
            {
                $this->db = $db;

                return $this;
            }

        public function findAll(): array
        {
            $query = $this->db->prepare('SELECT * FROM articles');
            $query->execute();
            $articles = [];

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $article = new Articles();
                $article->setId($row['id']);
                $article->setTitle($row['title']);
                $article->setContent($row['content']);
                $images = $this->imageRepository->findByArticleId($row['id']);
                $article->setImages($images);
                $article->setCategoryId($row['category_id']);
                $articles[] = $article;
            }

            return $articles;
        }

        public function findById(int $id): ?Articles
        {
            $query = $this->db->prepare('SELECT * FROM articles WHERE id = :id');
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $article = new Articles();
                $article->setId($row['id']);
                $article->setTitle($row['title']);
                $article->setContent($row['content']);
                $images = $this->imageRepository->findByArticleId($row['id']);
                $article->setImages($images);
                $article->setCategoryId($row['category_id']);
                return $article;
            }

            return null;
        }
        
        public function findByCategoryId(int $categoryId, int $limit, int $offset ): array
        {
            $query = $this->db->prepare('SELECT * FROM articles WHERE category_id = :category_id ORDER BY id DESC LIMIT :limit OFFSET :offset');
            $query->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->bindValue(':offset', $offset, PDO::PARAM_INT);
            $query->execute();
            $articles = [];

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $article = new Articles();
                $article->setId($row['id']);
                $article->setTitle($row['title']);
                $article->setContent($row['content']);
                $images = $this->imageRepository->findByArticleId($row['id']);
                $article->setImages($images);
                $article->setCategoryId($row['category_id']);
                $articles[] = $article;
            }

            return $articles;
        }

        public function createArticle(Articles $article): Articles {
                $query = $this->db->prepare('INSERT INTO articles (title, content, category_id) VALUES (:title, :content, :category_id)');
                $query->bindValue(':title', $article->getTitle(), PDO::PARAM_STR);
                $query->bindValue(':content', $article->getContent(), PDO::PARAM_STR);
                $query->bindValue(':category_id', $article->getCategoryId(), PDO::PARAM_INT);
                $query->execute();

                $article->setId($this->db->lastInsertId());
                foreach ($article->getImages() as $image) {
                $image->setArticleId($article->getId());
                $this->imageRepository->save($image);
                }
                return $article;
        }

        public function updateArticle(Articles $article): Articles
            {
                $query = $this->db->prepare('UPDATE articles SET title = :title, content = :content, category_id = :category_id WHERE id = :id');
                $query->bindValue(':id', $article->getId(), PDO::PARAM_INT);
                $query->bindValue(':title', $article->getTitle(), PDO::PARAM_STR);
                $query->bindValue(':content', $article->getContent(), PDO::PARAM_STR);
                $query->bindValue(':category_id', $article->getCategoryId(), PDO::PARAM_INT);
                $query->execute();

                // Mise à jour ou insertion des images via ImageRepository
                foreach ($article->getImages() as $image) {
                    if ($image->getId()) {
                        $this->imageRepository->update($image);
                    } else {
                        $image->setArticleId($article->getId());
                        $this->imageRepository->save($image);
                    }
                }

                return $article;
            }

        public function deleteArticle(int $id): void
            {
                // Supprime les images associées à l'article
                $this->imageRepository->deleteByArticleId($id);

                // Supprime l'article
                $query = $this->db->prepare('DELETE FROM articles WHERE id = :id');
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();
            }

        public function findAllWithCategory(): array
            {
                $sql = "SELECT a.*, c.title AS category_title
                FROM articles a
                JOIN category c ON a.category_id = c.id";

                $stmt = $this->db->prepare($sql);
                $stmt->execute();

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $articles = [];
                foreach ($results as $row) {
                    $article = new Articles();
                    $article->setId($row['id']);
                    $article->setTitle($row['title']);
                    $article->setContent($row['content']);
                    $article->setCategoryId($row['category_id']);
                    $article->setCategoryTitle($row['category_title']); 

                    $articles[] = $article;
                }

                return $articles;
            }

        public function findCount(int $id): int
        {
            $query = $this->db->prepare('SELECT COUNT(*) as total FROM articles WHERE category_id = :id');
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);

            return $result['total'] ?? 0;
        }

        public function findArticlesByCategoryAndQuery($categoryId, $query)
        {
            $query = trim($query);
            if ($query === '') {
                return [];
            }
            $sql = "SELECT * FROM articles WHERE category_id = :category_id AND title LIKE :query";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
            $stmt->execute();
            $articles = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $article = new Articles();
                $article->setId($row['id']);
                $article->setTitle($row['title']);
                $images = $this->imageRepository->findByArticleId($row['id']);
                $article->setImages($images);
                $articles[] = $article;
            }
            return $articles;
        }


}

