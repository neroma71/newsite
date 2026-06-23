<?php 
namespace App\Controller;

use App\Entity\Actu;
use App\Repository\ActuRepository;
use App\Repository\CategoryRepository;
use App\Repository\HomeRepository;
use App\Service\ImageUploader;

class ActuController extends BaseController
{
    private ActuRepository $actuRepository;
    private HomeRepository $homeRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(ActuRepository $actuRepository, HomeRepository $homeRepository, CategoryRepository $categoryRepository)
    {
        $this->actuRepository = $actuRepository;
        $this->homeRepository = $homeRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function create(array &$errors = []): void
    {
        // CAS GET → afficher formulaire
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('manage/createActu.php', [
                'errors' => $errors
            ]);
            return;
        }

        // CAS POST → traitement
        $this->ensureMethod('POST');
        $this->ensureCsrf();

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $image = $_FILES['image'] ?? null;

        $uploadDir = __DIR__ . '/../../../public/uploads/';
        $uploader = new ImageUploader($uploadDir);

        $uploadResult = $uploader->uploadSingle($image, $errors);
        $img = $uploadResult ? $uploadResult['name'] : null;

        $actu = new Actu();
        $actu->setTitle($title)
            ->setContent($content)
            ->setImage($img);

        $this->actuRepository->createActu($actu);

        header('Location: /newsite/manage/actus');
        exit;
    }

    public function update(int $id, array &$errors = []): void
    {
        $actu = $this->actuRepository->findById($id);

        if (!$actu) {
            throw new \Exception("Actu with ID $id not found.");
        }

        // GET → formulaire
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('manage/editActu.php', [
                'actu' => $actu,
                'errors' => $errors
            ]);
            return;
        }

        // POST → traitement
        $this->ensureCsrf();

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $image = $_FILES['image'] ?? null;

        $errors = [];
        $uploadDir = __DIR__ . '/../../../public/uploads/';
        $uploader = new ImageUploader($uploadDir);

        $uploadResult = $uploader->uploadSingle($image, $errors, $actu->getImage());
        $img = $uploadResult ? $uploadResult['name'] : $actu->getImage();

        $actu->setTitle($title)
            ->setContent($content)
            ->setImage($img);

        $this->actuRepository->updateActu($actu);

        header('Location: /newsite/manage/actus');
        exit;
    }
    
    public function delete(int $id): void
    {
         $this->ensureMethod('POST');

        try {
            $this->ensureCsrf();

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

            header('Location: /newsite/manage/actus');
            exit;

        } catch (\Exception $e) {
            error_log($e->getMessage());
            header('Location: /newsite/manage/actus?error=1');
            exit;
        }
    }

    public function manager(): void
    {

    $actus = $this->actuRepository->findAll();

    $this->render('manage/actumanager.php', [
        'actus' => $actus
    ]);
    }

    public function show(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $actus = $this->actuRepository->findAllPaginated($offset, $limit);

        $totalActus = $this->actuRepository->countAll();
        $totalPages = (int) ceil($totalActus / $limit);

        $homes = $this->homeRepository->findAll();
        $categories = $this->categoryRepository->findAll();

        $this->render('front/actu.php', [
            'actus' => $actus,
            'homes' => $homes,
            'categories' => $categories,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }

}