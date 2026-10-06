<?php

namespace App\Controller;

use App\Entity\Home;
use App\Repository\HomeRepository;
use App\Service\ImageUploader;
use App\Repository\CategoryRepository;

class HomeController extends BaseController
{
    private HomeRepository $homeRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(
        HomeRepository $homeRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->homeRepository = $homeRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function manager(): void
    {
        $homes = $this->homeRepository->findAll();

        $this->render('manage/homeManager.php', [
            'homes' => $homes
        ]);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('manage/createHome.php');
            return;
        }

        $this->ensureMethod('POST');
        $this->ensureCsrf();

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

        if (!empty($errors)) {
            $this->render('manage/createHome.php', [
                'errors' => $errors
            ]);
            return;
        }

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

        header('Location: ' . BASE_URL . '/manage/home');
        exit;
    }

    public function update(int $id): void
    {
        $home = $this->homeRepository->findById($id);

        if (!$home) {
            http_response_code(404);
            exit('Home not found');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('manage/editHome.php', [
                'home' => $home,
                'errors' => []
            ]);
            return;
        }

        $this->ensureMethod('POST');
        $this->ensureCsrf();

        $title = $_POST['title'] ?? $home->getTitle();
        $subtitle = $_POST['subtitle'] ?? $home->getSubtitle();
        $description = $_POST['description'] ?? $home->getDescription();

        $errors = [];

        $uploadDir = __DIR__ . '/../../../public/uploads/';
        $uploader = new ImageUploader($uploadDir);

        // IMAGE 1
        if (!empty($_POST['delete_image1'])) {
            if ($home->getImage1() && file_exists($uploadDir . $home->getImage1())) {
                unlink($uploadDir . $home->getImage1());
            }

            $img1 = null;
        } else {
            $img1 = $uploader->upload(
                $_FILES['image1'] ?? null,
                $home->getImage1(),
                $errors
            );
        }

        // IMAGE 2
        if (!empty($_POST['delete_image2'])) {
            if ($home->getImage2() && file_exists($uploadDir . $home->getImage2())) {
                unlink($uploadDir . $home->getImage2());
            }

            $img2 = null;
        } else {
            $img2 = $uploader->upload(
                $_FILES['image2'] ?? null,
                $home->getImage2(),
                $errors
            );
        }

        // IMAGE 3
        if (!empty($_POST['delete_image3'])) {
            if ($home->getImage3() && file_exists($uploadDir . $home->getImage3())) {
                unlink($uploadDir . $home->getImage3());
            }

            $img3 = null;
        } else {
            $img3 = $uploader->upload(
                $_FILES['image3'] ?? null,
                $home->getImage3(),
                $errors
            );
        }

        // IMAGE 4
        if (!empty($_POST['delete_image4'])) {
            if ($home->getImage4() && file_exists($uploadDir . $home->getImage4())) {
                unlink($uploadDir . $home->getImage4());
            }

            $img4 = null;
        } else {
            $img4 = $uploader->upload(
                $_FILES['image4'] ?? null,
                $home->getImage4(),
                $errors
            );
        }

        if (!empty($errors)) {
            $this->render('manage/editHome.php', [
                'home' => $home,
                'errors' => $errors
            ]);
            return;
        }

        $home->setTitle($title);
        $home->setSubtitle($subtitle);
        $home->setDescription($description);
        $home->setImage1($img1);
        $home->setImage2($img2);
        $home->setImage3($img3);
        $home->setImage4($img4);

        $this->homeRepository->updateHome($home);

        header('Location: ' . BASE_URL . '/manage/home');
        exit;
    }

    public function delete(int $id): void
    {
        $this->ensureMethod('POST');
        $this->ensureCsrf();

        $home = $this->homeRepository->findById($id);

        if (!$home) {
            throw new \Exception('Accueil non trouvé');
        }

        $this->homeRepository->deleteHome($id);

        header('Location: ' . BASE_URL . '/manage/home');
        exit;
    }

    public function show(): void
    {
        $this->render('front/index.php', [
            'homes' => $this->homeRepository->findAll(),
            'categories' => $this->categoryRepository->findAll(),
            'baseUrl' => BASE_URL
        ]);
    }
}