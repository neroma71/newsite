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

    private function processUpload(array $file, array &$errors): ?string
    {
    if (!isset($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Erreur lors de l'upload.";
        return null;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $mimeType = mime_content_type($file['tmp_name']);
    $size = $file['size'];

    if (!in_array($extension, $this->allowedExtensions)) {
        $errors[] = "Extension non autorisée pour {$file['name']}.";
        return null;
    }

    if (!in_array($mimeType, $this->allowedMimeTypes)) {
        $errors[] = "Type MIME non autorisé pour {$file['name']}.";
        return null;
    }

    if ($size > $this->maxSize) {
        $errors[] = "{$file['name']} est trop volumineux.";
        return null;
    }

    $uniqueName = bin2hex(random_bytes(16)) . '_' . basename($file['name']);
    $target = $this->uploadDir . $uniqueName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        $errors[] = "Erreur lors du déplacement du fichier.";
        return null;
    }

    return $uniqueName;
    }

    // Methode pour upload les images de la page home et les image de categorie
    public function upload(?array $file, ?string $currentImage = null, array &$errors = []): string
    {
        if (
            !$file ||
            !isset($file['error']) ||
            $file['error'] === UPLOAD_ERR_NO_FILE
        ) {
            return $currentImage ?? '';
        }

        $result = $this->uploadSingle($file, $errors, $currentImage);

        return $result ? $result['name'] : ($currentImage ?? '');
    }

        // Methode pour update les images d'un article de la page actu
    public function uploadSingle(array $file, array &$errors, ?string $currentImage = null): ?array
    {
        $filename = $this->processUpload($file, $errors);

            if (!$filename) {
                return null;
            }

            // Supprimer ancienne image
            if ($currentImage) {
                $oldPath = $this->uploadDir . basename($currentImage);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
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
                $file = [
                    'name' => $files['name'][$key],
                    'tmp_name' => $tmpName,
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key],
                ];

                $filename = $this->processUpload($file, $errors);

                if ($filename) {
                    $image = new Image();
                    $image->setPath('/uploads/' . $filename);
                    $image->setImageTitle($file['name']);
                    $uploadedImages[] = $image;
                }
            }

            return $uploadedImages;
        }
}
