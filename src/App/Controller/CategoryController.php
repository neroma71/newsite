<?php
namespace App\Controller;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\ImageUploader;
class CategoryController
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Vérification CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Erreur CSRF : token invalide');
        }

            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $image = $_FILES['image'] ?? null;

            $errors = [];
            $uploadDir = __DIR__ . '/../../../public/uploads/';
            $uploader = new ImageUploader($uploadDir);

            $img = $uploader->upload($image, null, $errors);

            if (empty($errors)) {
                $category = new Category([
                    'title' => $title,
                    'description' => $description,
                    'image' => $img
                ]);
                $this->categoryRepository->createCategory($category);
                // Redirection après traitement
                 header('Location: ../../views/manage/category.php');
                exit;
            } else {
                // Gérer les erreurs d'upload
                foreach ($errors as $error) {
                    echo "<p>Error: {$error}</p>";
                }
            }
        }
    }

    public function update(int $id)
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new \Exception("Category not found");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Vérification CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Erreur CSRF : token invalide');
        }
        
            $title = $_POST['title'] ?? $category->getTitle();
            $description = $_POST['description'] ?? $category->getDescription();
            $image = $_FILES['image'] ?? null;

            $errors = [];
            $uploadDir = __DIR__ . '/../../../public/uploads/';
            $uploader = new ImageUploader($uploadDir);

            if ($image && $image['error'] === UPLOAD_ERR_OK) {
                $img = $uploader->upload($image, null, $errors);
                if (empty($errors)) {
                    $category->setImage($img);
                }
            }

            $category->setTitle($title);
            $category->setDescription($description);
            $this->categoryRepository->updateCategory($category);

            // Redirection après traitement
            header('Location: ../../views/manage/category.php');
            exit;
        }
    }

    public function delete(int $id, string $csrfToken): void
    {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
            throw new \Exception('Token CSRF invalide');
        }

        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new \Exception("Catégorie introuvable");
        }

        $this->categoryRepository->deleteCategory($id);

        header('Location: ../../views/manage/category.php');
        exit;
    }
}