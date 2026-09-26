<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\ImageUploader;
use App\Repository\ArticleRepository;

class CategoryController extends BaseController
{
    private CategoryRepository $categoryRepository;
    private ArticleRepository $articleRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        ArticleRepository $articleRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
    }

    public function manager(): void
    {
        $categories = $this->categoryRepository->findAll();

        $this->render('manage/category.php', [
            'categories' => $categories
        ]);
    }

    public function create(array &$errors = []): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('manage/createCategory.php', [
                'errors' => $errors
            ]);
            return;
        }

        $this->ensureMethod('POST');
        $this->ensureCsrf();

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = $_FILES['image'] ?? null;

        $errors = [];

        $uploadDir = __DIR__ . '/../../../public/uploads/';
        $uploader = new ImageUploader($uploadDir);

        $img = $uploader->upload($image, null, $errors);

        if (!empty($errors)) {
            $this->render('manage/createCategory.php', [
                'errors' => $errors
            ]);
            return;
        }

        $category = new Category([
            'title' => $title,
            'description' => $description,
            'image' => $img
        ]);

        $this->categoryRepository->createCategory($category);

        header('Location: ' . BASE_URL . '/manage/categories');
        exit;
    }

    public function update(int $id, array &$errors = []): void
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            http_response_code(404);
            exit('Catégorie introuvable');
        }

        // GET → afficher le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('manage/editCategory.php', [
                'category' => $category,
                'errors' => $errors
            ]);
            return;
        }

        // POST → traitement
        $this->ensureMethod('POST');
        $this->ensureCsrf();

        $title = trim($_POST['title'] ?? $category->getTitle());
        $description = trim($_POST['description'] ?? $category->getDescription());
        $image = $_FILES['image'] ?? null;

        $errors = [];

        $uploadDir = __DIR__ . '/../../../public/uploads/';
        $uploader = new ImageUploader($uploadDir);

        // Nouvelle image uniquement si une image a réellement été envoyée
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $img = $uploader->upload($image, null, $errors);

            if (empty($errors)) {
                // Supprimer l'ancienne image
                $oldImage = $category->getImage();

                if ($oldImage && file_exists($uploadDir . $oldImage)) {
                    unlink($uploadDir . $oldImage);
                }

                $category->setImage($img);
            }
        }

        // Erreur d'upload
        if (!empty($errors)) {
            $this->render('manage/editCategory.php', [
                'category' => $category,
                'errors' => $errors
            ]);
            return;
        }

        $category->setTitle($title);
        $category->setDescription($description);

        $this->categoryRepository->updateCategory($category);

        header('Location: ' . BASE_URL . '/manage/categories');
        exit;
    }

    public function delete(): void
    {
        $this->ensureMethod('POST');
        $this->ensureCsrf();

        $id = (int) ($_POST['delete_id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            exit('ID de catégorie invalide');
        }

        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            http_response_code(404);
            exit('Catégorie introuvable');
        }

        $uploadDir = __DIR__ . '/../../../public/uploads/';
        $image = $category->getImage();

        if ($image && file_exists($uploadDir . $image)) {
            unlink($uploadDir . $image);
        }

        $this->categoryRepository->deleteCategory($id);

        header('Location: ' . BASE_URL . '/manage/categories');
        exit;
    }

    public function show(): void
    {
        $categoryId = (int) ($_GET['id'] ?? 0);
        $page = (int) ($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';

        $categories = $this->categoryRepository->findAll();
        $limit = 5;

        $categorie = $this->categoryRepository->findById($categoryId);

        if (!$categorie) {
            require VIEW_PATH . '/front/404.php';
            exit;
        }

        if ($search) {
            $articles = $this->articleRepository
                ->findArticlesByCategoryAndQuery($categoryId, $search);

            $totalPages = 1;
            $currentPage = 1;
            $pagination = null;
        } else {
            $pagination = $this->articleRepository
                ->getPaginatedData($categoryId, $page, $limit);

            $articles = $pagination['articles'];
            $totalPages = $pagination['totalPages'];
            $currentPage = $pagination['currentPage'];
        }

        $this->render('front/categories.php', [
            'categories'  => $categories,
            'categorie'   => $categorie,
            'articles'    => $articles,
            'totalPages'  => $totalPages,
            'currentPage' => $currentPage,
            'search'      => $search,
            'pagination'  => $pagination,
            'categoryId'  => $categoryId,
        ]);
    }
}