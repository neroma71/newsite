<?php
namespace App\Service;

use App\Entity\Image;

class ImageUploader
{
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private array $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private int $maxSize = 2097152; // 2 Mo
    private string $uploadDir;

    public function __construct(string $uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    public function upload(?array $file, ?string $currentImage = null, array &$errors = []): string
    {
        if ($file && isset($file['name']) && $file['error'] === UPLOAD_ERR_OK) {
            $fileInfo = pathinfo($file['name']);
            $extension = strtolower($fileInfo['extension'] ?? '');
            $mimeType = mime_content_type($file['tmp_name']);
            $fileSize = $file['size'];
            if (!in_array($extension, $this->allowedExtensions)) {
                $errors[] = "Extension non autorisée pour " . $file['name'] . ".";
                return $currentImage ?? '';
            }
            if (!in_array($mimeType, $this->allowedMimeTypes)) {
                $errors[] = "Type MIME non autorisé pour " . $file['name'] . ".";
                return $currentImage ?? '';
            }
            if ($fileSize > $this->maxSize) {
                $errors[] = $file['name'] . " est trop volumineux (max 2 Mo).";
                return $currentImage ?? '';
            }
            $uniqueName = uniqid() . '_' . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $this->uploadDir . $uniqueName)) {
                if ($currentImage) {
                    @unlink($this->uploadDir . basename($currentImage));
                }
                return $uniqueName;
            } else {
                $errors[] = "Erreur lors de l'upload de " . $file['name'] . ".";
                return $currentImage ?? '';
            }
        }
        return $currentImage ?? '';
    }
        // Methode pour update les images d'un article
        public function uploadSingle(array $file, array &$errors): ?array
            {
                $filename = uniqid() . '-' . basename($file['name']);
                $target = $this->uploadDir . $filename;

                if (!move_uploaded_file($file['tmp_name'], $target)) {
                    $errors[] = 'Impossible de déplacer le fichier uploadé.';
                    return null;
                }

                return [
                    'name' => $filename,
                    'path' => '/uploads/' . $filename,
                ];
            }

        public function uploadMultiple(array $files, array &$errors): array
            {
                $uploadedImages = [];

                foreach ($files['tmp_name'] as $key => $tmpName) {
                    $originalName = $files['name'][$key];
                    $error = $files['error'][$key];
                    $size = $files['size'][$key];

                    if ($error !== UPLOAD_ERR_OK) {
                        $errors[] = "Erreur lors de l'upload du fichier $originalName.";
                        continue;
                    }

                    $fileInfo = pathinfo($originalName);
                    $extension = strtolower($fileInfo['extension'] ?? '');
                    $mimeType = mime_content_type($tmpName);

                    if (!in_array($extension, $this->allowedExtensions)) {
                        $errors[] = "Extension non autorisée pour $originalName.";
                        continue;
                    }

                    if (!in_array($mimeType, $this->allowedMimeTypes)) {
                        $errors[] = "Type MIME non autorisé pour $originalName.";
                        continue;
                    }

                    if ($size > $this->maxSize) {
                        $errors[] = "$originalName est trop volumineux (max 2 Mo).";
                        continue;
                    }

                    $uniqueName = uniqid() . '_' . basename($originalName);
                    $targetPath = $this->uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $image = new Image();
                        $image->setPath('/uploads/' . $uniqueName);
                        $image->setImageTitle($originalName); // Titre = nom de fichier par défaut
                        $uploadedImages[] = $image;
                    } else {
                        $errors[] = "Erreur lors de l'upload de $originalName.";
                    }
                }

                return $uploadedImages;
            }

}
