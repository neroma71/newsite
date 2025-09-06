<?php
namespace App\Entity;

    class Articles {
        private ?int $id = null;
        private string $title;
        private string $content;
        private array $images = array();
        private ?int $categoryId = null;
        private ?string $categoryTitle = null;

        public function __construct($data = []) {
            if (!empty($data)) {
                $this->hydrate($data);
            }
        }


        // Getters and Setters...

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
         * Get the value of content
         */ 
        public function getContent()
        {
                return $this->content;
        }

        /**
         * Set the value of content
         *
         * @return  self
         */ 
        public function setContent($content)
        {
                $this->content = $content;

                return $this;
        }

          /**
         * Get the value of image
         */ 
        public function getImages(): array
        {
            return $this->images;
        }
        
        public function addImage(Image $image): void
        {
            $this->images[] = $image;
        }

         /**
         * Set the value of images
         *
         * @return  self
         */ 
        public function setImages($images)
        {
                $this->images = $images;

                return $this;
        }

        /**
         * Get the value of categoryId
         */ 
        public function getCategoryId()
        {
                return $this->categoryId;
        }

        /**
         * Set the value of categoryId
         *
         * @return  self
         */ 
        public function setCategoryId($categoryId)
        {
                $this->categoryId = $categoryId;

                return $this;
        }

        /**
         * Get the value of categoryTitle
         */
        public function getCategoryTitle(): ?string
        {
            return $this->categoryTitle ?? '';
        }

        /**
         * Set the value of categoryTitle
         *
         * @return  self
         */
        public function setCategoryTitle(?string $categoryTitle): self
        {
            $this->categoryTitle = $categoryTitle;
            return $this;
        }

        public function removeImage(Image $image): void
        {
            foreach ($this->images as $key => $img) {
                if ($img->getId() === $image->getId()) {
                    unset($this->images[$key]);
                    // Reindexe le tableau pour éviter les trous
                    $this->images = array_values($this->images);
                    break;
                }
            }
        }

        /**
         * Hydrate the object with data
         */
        public function hydrate(array $data): void
    {
        if (isset($data['id'])) {
            $this->setId($data['id']);
        }
        if (isset($data['title'])) {
            $this->setTitle($data['title']);
        }
        if (isset($data['content'])) {
            $this->setContent($data['content']);
        }
        if (isset($data['categoryId'])) {
            $this->setCategoryId($data['categoryId']);
        }
        if (isset($data['categoryTitle'])) { 
            $this->setCategoryTitle($data['categoryTitle']);
        }

        $this->setImages([]); // reset images

        if (isset($data['images'])) {
            if (is_array($data['images'])) {
                foreach ($data['images'] as $imageData) {
                if ($imageData instanceof Image) {
                    // L'objet est déjà une instance d'Image, on l'ajoute directement
                    $this->addImage($imageData);
                } elseif (is_array($imageData)) {
                    $image = new Image();
                    $image->setId($imageData['id'] ?? 0);
                    $image->setPath($imageData['path'] ?? '');
                    $image->setImageTitle($imageData['imageTitle'] ?? '');
                    $image->setArticleId($this->getId());
                    $this->addImage($image);
                } elseif (is_string($imageData)) {
                    $image = new Image();
                    $image->setPath($imageData);
                    $image->setArticleId($this->getId());
                    $this->addImage($image);
                }
            }
        }
    }
}
    }