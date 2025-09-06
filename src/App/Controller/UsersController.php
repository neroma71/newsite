<?php
namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;

class UsersController
{
    private UsersRepository $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function register(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $errors = [];

            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';

            if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errors[] = 'Un mail valide est requis.';     
            }

            if(empty($password) || strlen($password) < 8){
                $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
            }

            // Si des erreurs sont présentes, les afficher dans la vue
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                // Pas besoin de require db_connect.php ni de global $bdd ici
                $usersRepository = $this->usersRepository;
                $usersController = $this;
                include __DIR__ . '/../../../views/users/login.php';
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_ARGON2I);

            $user = new Users([
                'email' => $email,
                'password' => $hashedPassword
            ]);
            
            $this->usersRepository->createUsers($user);

            // Redirection vers la page de login après l'inscription réussie
            header('Location: /newsite/views/users/login.php');
            exit;
        }
        else{
            // Si la méthode n'est pas POST, afficher le formulaire d'inscription
            include __DIR__ . '/../../../views/users/register.php';
        }

    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';

            if (empty($email) || empty($password)) {
                $_SESSION['error_message'] = 'Tous les champs sont obligatoires.';
                header('Location: /newsite/views/users/login.php');
                exit;
            }

            $user = $this->usersRepository->findByEmail($email);

            if ($user && password_verify($password, $user->getPassword())) {
                session_regenerate_id(true); //  protection contre fixation de session
                $_SESSION['user_id'] = $user->getId(); //  Ne stocke que l'ID
         
                header('Location: /jerome2/views/manage/dashboard.php');
                exit;
            } else {
                $_SESSION['error'] = 'Identifiants invalides.';
                header('Location: /newsite/views/users/login.php');
                exit;
            }
        } else {
            include __DIR__ . '/../../../views/users/login.php';
            return;
        }
    }
  
  
}