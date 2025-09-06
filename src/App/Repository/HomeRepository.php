<?php
namespace App\Repository;
use \PDO;
use App\Entity\Home;

class HomeRepository{
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
        $query = $this->getDb()->query('SELECT * FROM home');
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        $homes = [];
        foreach ($data as $row) {
            $homes[] = new Home($row);
        }
        return $homes;
    }

    public function findById(int $id): ?Home
    {
        $query = $this->getDb()->prepare('SELECT * FROM home WHERE id = :id LIMIT 1');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            return new Home($data);
        }
        
        return null;
    }
    
    public function createHome(Home $home): Home
    {
        $query = $this->getDb()->prepare('INSERT INTO home (title, subtitle, description,  image1, image2, image3, image4) VALUES (:title, :subtitle,  :description, :image1, :image2, :image3, :image4)');
        $query->bindValue(':title', $home->getTitle());
        $query->bindValue(':subtitle', $home->getsubTitle());
        $query->bindValue(':description', $home->getDescription());
        $query->bindValue(':image1', $home->getImage1());
        $query->bindValue(':image2', $home->getImage2());
        $query->bindValue(':image3', $home->getImage3());
        $query->bindValue(':image4', $home->getImage4());
        $query->execute();
        
        // Set the ID of the home object
        $home->setId($this->getDb()->lastInsertId());
        
        return $home;
    }

    public function updateHome(Home $home): Home
    {
        $query = $this->getDb()->prepare('UPDATE home SET title = :title, subtitle = :subtitle, description = :description, image1 = :image1, image2 = :image2, image3 = :image3, image4 = :image4 WHERE id = :id');
        $query->bindValue(':id', $home->getId(), PDO::PARAM_INT);
        $query->bindValue(':title', $home->getTitle());
        $query->bindValue(':subtitle', $home->getSubtitle());
        $query->bindValue(':description', $home->getDescription());
        $query->bindValue(':image1', $home->getImage1());
        $query->bindValue(':image2', $home->getImage2());
        $query->bindValue(':image3', $home->getImage3());
        $query->bindValue(':image4', $home->getImage4());
        $query->execute();
        
        return $home;
    }

    public function deleteHome(int $id): bool
    {
        $query = $this->getDb()->prepare('DELETE FROM home WHERE id = :id LIMIT 1');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        return $query->execute();
    }

}