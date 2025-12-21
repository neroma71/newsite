<?php
namespace App\Entity;

class Actu
{
    private ?int $id = null;
    private ?string $title = null;
    private ?string $content = null;
    private ?string $image = null;
    private ?\DateTimeInterface $createdAt = null;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
        // s'assurer que createdAt est toujours initialisé
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Retourne un DateTimeInterface garanti (initialisé si nécessaire)
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
        return $this->createdAt;
    }

    /**
     * Accepte \DateTimeInterface, string (date), int (timestamp) ou null
     */
    public function setCreatedAt($createdAt): self
    {
        // si déjà un objet DateTime
        if ($createdAt instanceof \DateTimeInterface) {
            $this->createdAt = $createdAt;
            return $this;
        }

        // si int ou chaîne numérique => timestamp UNIX
        if (is_int($createdAt) || (is_string($createdAt) && ctype_digit($createdAt))) {
            $this->createdAt = (new \DateTimeImmutable())->setTimestamp((int)$createdAt);
            return $this;
        }

        // si chaîne date au format SQL / ISO
        if (is_string($createdAt) && $createdAt !== '') {
            try {
                $this->createdAt = new \DateTimeImmutable($createdAt);
            } catch (\Throwable $e) {
                $this->createdAt = new \DateTimeImmutable();
            }
            return $this;
        }

        // null ou valeur non reconnue -> valeur par défaut maintenant
        $this->createdAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Helper : créer un DateTimeImmutable depuis un timestamp (null = now)
     */
    public static function fromTimestamp(?int $timestamp = null): \DateTimeImmutable
    {
        if ($timestamp === null) {
            return new \DateTimeImmutable();
        }
        return (new \DateTimeImmutable())->setTimestamp((int)$timestamp);
    }

 
    public function hydrate(array $data): self
    {
        foreach ($data as $key => $value) {
            // convertir snake_case (ex: created_at) en camelCase (createdAt) pour les setters
            $parts = explode('_', $key);
            $camel = array_shift($parts);
            foreach ($parts as $part) {
                $camel .= ucfirst($part);
            }
            $method = 'set' . ucfirst($camel);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }
}