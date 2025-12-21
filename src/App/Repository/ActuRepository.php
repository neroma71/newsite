<?php
namespace App\Repository;
use \PDO;
use App\Entity\Actu;

class ActuRepository{
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
        $query = $this->getDb()->query('SELECT * FROM actu ORDER BY id DESC');
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        $actus = [];
        foreach ($data as $row) {
            $actus[] = new Actu($row);
        }
        return $actus;
    }

    public function findById(int $id): ?Actu
    {
        $query = $this->getDb()->prepare('SELECT * FROM actu WHERE id = :id LIMIT 1');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new Actu($data);
        }

        return null;
    }

    public function createActu(Actu $actu): Actu
    {
        $query = $this->getDb()->prepare('INSERT INTO actu (title, content, image, created_at) VALUES (:title, :content, :image, :created_at)');
        $query->bindValue(':title', $actu->getTitle(), PDO::PARAM_STR);
        $query->bindValue(':content', $actu->getContent(), PDO::PARAM_STR);
        $query->bindValue(':image', $actu->getImage(), PDO::PARAM_STR);
        $query->bindValue(':created_at', $actu->getCreatedAt()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $query->execute();
        $actuId = (int)$this->getDb()->lastInsertId();
        return $this->findById($actuId) ?? $actu;
    }

    public function updateActu(Actu $actu): Actu
    {
        $query = $this->getDb()->prepare('UPDATE actu SET title = :title, content = :content, image = :image, created_at = :created_at WHERE id = :id');
        $query->bindValue(':id', $actu->getId(), PDO::PARAM_INT);
        $query->bindValue(':title', $actu->getTitle(), PDO::PARAM_STR);
        $query->bindValue(':content', $actu->getContent(), PDO::PARAM_STR);
        $query->bindValue(':image', $actu->getImage(), PDO::PARAM_STR);
        $query->bindValue(':created_at', $actu->getCreatedAt()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $query->execute();
        return $this->findById($actu->getId()) ?? $actu;    
    } 
    
    public function deleteActu(int $id): void
    {
        $query = $this->getDb()->prepare('DELETE FROM actu WHERE id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
    }

    public function findAllPaginated(int $offset, int $limit): array
    {
        $query = $this->getDb()->prepare("SELECT * FROM actu ORDER BY created_at DESC LIMIT :offset, :limit");
        $query->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $query->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $query->execute();
        
        $actus = [];
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $actus[] = new Actu($row);
        }
        return $actus;
    }

    public function countAll(): int
    {
        $query = "SELECT COUNT(*) FROM actu";
        return (int)$this->getDb()->query($query)->fetchColumn();
    }
}