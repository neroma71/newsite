<?php 
namespace App\Controller;

use App\Entity\Actu;
use App\Repository\ActuRepository;
use App\Service\ImageUploader;

class ActuController
{
    private ActuRepository $actuRepository;

    public function __construct(ActuRepository $actuRepository)
    {
        $this->actuRepository = $actuRepository;
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Vérification CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Erreur CSRF : token invalide');
        }
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $image = $_FILES['image'] ?? null;

            $errors = [];
            $uploadDir = __DIR__ . '/../../../public/uploads/';
            $uploader = new ImageUploader($uploadDir);

            // Modification : utilisation de uploadSingle
            $uploadResult = $uploader->uploadSingle($image, $errors);
            $img = $uploadResult ? $uploadResult['name'] : '';

            $actu = new Actu([
                'title' => $title,
                'content' => $content,
                'image' => $img,
                'created_at' => new \DateTime()
            ]);
            $this->actuRepository->createActu($actu);
            // Redirection après traitement
             header('Location: /newsite/views/manage/actumanager.php');
            exit;
        }
    }
    public function update(int $id, array &$errors = []): Actu
    {
        $actu = $this->actuRepository->findById($id);
        if (!$actu) {
            throw new \Exception("Actu with ID $id not found.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Vérification CSRF
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('Erreur CSRF : token invalide');
            }

            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $image = $_FILES['image'] ?? null;

            $uploadDir = __DIR__ . '/../../../public/uploads/';
            $uploader = new ImageUploader($uploadDir);

            // Modification : utilisation de uploadSingle avec gestion de l'image existante
            $uploadResult = $uploader->uploadSingle($image, $errors, $actu->getImage());
            $img = $uploadResult ? $uploadResult['name'] : $actu->getImage();

            $actu->setTitle($title)
                 ->setContent($content)
                 ->setImage($img);

            $this->actuRepository->updateActu($actu);

            // Rediriger vers le manager plutôt que actu.php
            header('Location: /newsite/views/manage/actumanager.php');
            exit;
        }

        return $actu;
    }
    
    public function delete(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                throw new \Exception('Erreur CSRF : token invalide');
            }

            $actu = $this->actuRepository->findById($id);
            if (!$actu) {
                throw new \Exception("Actu not found.");
            }

            $uploadDir = __DIR__ . '/../../../public/uploads/';
            $image = $actu->getImage();

            if ($image && file_exists($uploadDir . $image)) {
                unlink($uploadDir . $image);
            }

            $this->actuRepository->deleteActu($id);

            header('Location: /newsite/views/manage/actumanager.php');
            exit;

        } catch (\Exception $e) {
            echo "Erreur : " . htmlspecialchars($e->getMessage());
            exit;
        }
    }
}