<?php
namespace App\Entity;

class Home
{
    private int $id;
    private string $title;
    private string $subtitle;
    private string $description;
    private ?string $image1 = null;
    private ?string $image2 = null;
    private ?string $image3 = null;
    private ?string $image4 = null;
    
    public function __construct($data = [])
    {
        $this->hydrate($data);
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of subtitle
     */ 
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set the value of subtitle
     *
     * @return  self
     */ 
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of image
     */ 
    public function getImage1(): ?string
    {
        return $this->image1;
    }

    /**
     * Set the value of image
     *
     * @return  self
     */ 
    public function setImage1(?string $image1): self
    {
        $this->image1 = $image1;

        return $this;   
    } 
    
        /**
     * Get the value of image2
     */ 
    public function getImage2(): ?string
    {
        return $this->image2;
    }

    /**
     * Set the value of image2
     *
     * @return  self
     */ 
    public function setImage2(?string $image2): self
    {
        $this->image2 = $image2;

        return $this;
    }

    /**
     * Get the value of image3
     */ 
    public function getImage3(): ?string
    {
        return $this->image3;
    }

    /**
     * Set the value of image3
     *
     * @return  self
     */ 
    public function setImage3(?string $image3): self
    {
        $this->image3 = $image3;

        return $this;
    }

    /**
     * Get the value of image4
     */ 
    public function getImage4(): ?string
    {
        return $this->image4;
    }

    /**
     * Set the value of image4
     *
     * @return  self
     */ 
    public function setImage4(?string $image4): self
    {
        $this->image4 = $image4;

        return $this;
    }

    public function hydrate(array $data): void
    {
        if (isset($data['id'])) {
            $this->setId($data['id']);
        }
        if (isset($data['title'])) {
            $this->setTitle($data['title']);
        }
        if (isset($data['subtitle'])) {
            $this->setSubtitle($data['subtitle']);
        }
        if (isset($data['description'])) {
            $this->setDescription($data['description']);
        }
        if (isset($data['image1'])) {
            $this->setImage1($data['image1']);
        }
        if (isset($data['image2'])) {
            $this->setImage2($data['image2']);
        }
        if (isset($data['image3'])) {
            $this->setImage3($data['image3']);
        }
        if (isset($data['image4'])) {
            $this->setImage4($data['image4']);
        }
    }
}