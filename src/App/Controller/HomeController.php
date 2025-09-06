<?php
namespace App\Controller;

use App\Entity\Home;
use App\Repository\HomeRepository;
use App\Service\ImageUploader;

class HomeController
{
    private HomeRepository $homeRepository;

    public function __construct(HomeRepository $homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $subtitle = $_POST['subtitle'] ?? '';
            $description = $_POST['description'] ?? '';
            $image1 = $_FILES['image1'] ?? null;
            $image2 = $_FILES['image2'] ?? null;
            $image3 = $_FILES['image3'] ?? null;
            $image4 = $_FILES['image4'] ?? null;

            $errors = [];
            $uploadDir = __DIR__ . '/../../../public/uploads/';
            $uploader = new ImageUploader($uploadDir);

            $img1 = $uploader->upload($image1, null, $errors);
            $img2 = $uploader->upload($image2, null, $errors);
            $img3 = $uploader->upload($image3, null, $errors);
            $img4 = $uploader->upload($image4, null, $errors);

            $home = new Home([
                'title' => $title,
                'subtitle' => $subtitle,
                'description' => $description,
                'image1' => $img1,
                'image2' => $img2,
                'image3' => $img3,
                'image4' => $img4
            ]);
            $this->homeRepository->createHome($home);
            // Redirection après traitement
            header('Location: /newsite/public/index.php');
            exit;
        }
    }

   public function update(int $id, array &$errors = []): Home
{
    $home = $this->homeRepository->findById($id);
    if (!$home) {
        throw new \Exception("Home not found");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? $home->getTitle();
        $subtitle = $_POST['subtitle'] ?? $home->getSubtitle();
        $description = $_POST['description'] ?? $home->getDescription();

        $uploadDir = __DIR__ . '/../../../public/uploads/';
        $uploader = new ImageUploader($uploadDir);

        // IMAGE 1
        if (!empty($_POST['delete_image1'])) {
            if ($home->getImage1() && file_exists($uploadDir . $home->getImage1())) {
                unlink($uploadDir . $home->getImage1());
            }
            $img1 = null;
        } else {
            $img1 = $uploader->upload($_FILES['image1'] ?? null, $home->getImage1(), $errors);
        }

        // IMAGE 2
        if (!empty($_POST['delete_image2'])) {
            if ($home->getImage2() && file_exists($uploadDir . $home->getImage2())) {
                unlink($uploadDir . $home->getImage2());
            }
            $img2 = null;
        } else {
            $img2 = $uploader->upload($_FILES['image2'] ?? null, $home->getImage2(), $errors);
        }

        // IMAGE 3
        if (!empty($_POST['delete_image3'])) {
            if ($home->getImage3() && file_exists($uploadDir . $home->getImage3())) {
                unlink($uploadDir . $home->getImage3());
            }
            $img3 = null;
        } else {
            $img3 = $uploader->upload($_FILES['image3'] ?? null, $home->getImage3(), $errors);
        }

        // IMAGE 4
        if (!empty($_POST['delete_image4'])) {
            if ($home->getImage4() && file_exists($uploadDir . $home->getImage4())) {
                unlink($uploadDir . $home->getImage4());
            }
            $img4 = null;
        } else {
            $img4 = $uploader->upload($_FILES['image4'] ?? null, $home->getImage4(), $errors);
        }

        // Mise à jour de l’entité
        $home->setTitle($title);
        $home->setSubtitle($subtitle);
        $home->setDescription($description);
        $home->setImage1($img1);
        $home->setImage2($img2);
        $home->setImage3($img3);
        $home->setImage4($img4);

        if(empty($errors)){
            // Sauvegarde en BDD
        $this->homeRepository->updateHome($home);
        // Redirection
        header('Location: /newsite/views/manage/manager.php');
        exit;
        }
    }

    return $home;
}

}
