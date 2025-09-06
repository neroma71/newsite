<?php

namespace App\Repository;

use App\Entity\Users;
use PDO;

class UsersRepository{
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
    
    public function createUsers(Users $users): Users
    {
        $query = $this->getDb()->prepare('INSERT INTO users (email, password) VALUES (:email, :password)');
        $query->bindValue(':email', $users->getEmail(), PDO::PARAM_STR);
        $query->bindValue(':password', $users->getPassword(), PDO::PARAM_STR);
        $query->execute();
        
        // Assuming the ID is auto-incremented and we want to set it back to the Users object
        $users->setId($this->getDb()->lastInsertId());
        
        return $users;
    }

    public function findByEmail(string $email): ?Users
    {
        $query = $this->getDb()->prepare('SELECT * FROM users WHERE email = :email');
        $query->bindValue(':email', $email, PDO::PARAM_STR);
        $query->execute();
        
        $data = $query->fetch(PDO::FETCH_ASSOC);
       return $data ? new Users($data) : null;
    }

  public function findById(int $id): ?Users
    {
    $query = $this->getDb()->prepare('SELECT * FROM users WHERE id = :id');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();

    $data = $query->fetch(PDO::FETCH_ASSOC);
    return $data ? new Users($data) : null;
    }
    
}