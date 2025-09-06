<?php
namespace App\Repository;
use \PDO;
use App\Entity\Category;

class CategoryRepository {
    private PDO $db; 

    public function __construct(PDO $db)
    {
        $this->setDb($db);
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
    public function setDb($db)
    {
        $this->db = $db;

        return $this;
    }

    public function findAll(): array
    {
        $query = $this->getDb()->query('SELECT * FROM category ORDER BY id DESC');
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        $categories = [];
        foreach ($data as $row) {
            $categories[] = new Category($row);
        }
        return $categories;
    }

    public function findById(int $id): ?Category
    {
        $query = $this->getDb()->prepare('SELECT * FROM category WHERE id = :id LIMIT 1');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            return new Category($data);
        }
        
        return null;
    }
    public function createCategory(Category $category): Category
    {
        $query = $this->getDb()->prepare('INSERT INTO category (title, description, image) VALUES (:title, :description, :image)');
        $query->bindValue(':title', $category->getTitle(), PDO::PARAM_STR);
        $query->bindValue(':description', $category->getDescription(), PDO::PARAM_STR);
        $query->bindValue(':image', $category->getImage(), PDO::PARAM_STR);
        $query->execute();
        
        $category->setId($this->getDb()->lastInsertId());
        return $category;
    }
    public function updateCategory(Category $category): Category
    {
        $query = $this->getDb()->prepare('UPDATE category SET title = :title, description = :description, image = :image WHERE id = :id');
        $query->bindValue(':id', $category->getId(), PDO::PARAM_INT);
        $query->bindValue(':title', $category->getTitle(), PDO::PARAM_STR);
        $query->bindValue(':description', $category->getDescription(), PDO::PARAM_STR);
        $query->bindValue(':image', $category->getImage(), PDO::PARAM_STR);
        $query->execute();
        
        return $category;
    }
    public function deleteCategory(int $id): bool
    {
        $query = $this->getDb()->prepare('DELETE FROM category WHERE id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        return $query->execute();
    }
}