<?php
namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;

class UsersController extends BaseController
{
    private UsersRepository $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function dashboard(): void
    {
        $this->render('manage/dashboard.php');
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('users/register.php');
            return;
        }

        $this->ensureMethod('POST');
        $this->ensureCsrf();

        $errors = [];

        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Un mail valide est requis.';
        }

        if (empty($password) || strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        if (!empty($errors)) {
            $this->render('users/register.php', [
                'errors' => $errors
            ]);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_ARGON2I);

        $user = new Users([
            'email' => $email,
            'password' => $hashedPassword
        ]);

        $this->usersRepository->createUsers($user);

        header('Location: ' . BASE_URL . '/users/login');
        exit;
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('users/login.php');
            return;
        }

        $this->ensureMethod('POST');
        $this->ensureCsrf();

        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        if (empty($email) || empty($password)) {
            $this->render('users/login.php', [
                'error' => 'Tous les champs sont obligatoires.'
            ]);
            return;
        }

        $user = $this->usersRepository->findByEmail($email);

        if ($user && password_verify($password, $user->getPassword())) {

            // Protection contre la fixation de session
            session_regenerate_id(true);

            // On stocke uniquement l'identifiant de l'utilisateur
            $_SESSION['user_id'] = $user->getId();

            header('Location: ' . BASE_URL . '/manage/dashboard');
            exit;
        }

        $this->render('users/login.php', [
            'error' => 'Identifiants invalides.'
        ]);
    }
}