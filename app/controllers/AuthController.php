<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
        // Iniciar sesión solo si no está activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

     public function index() {
        header('Location: /chilean_food_app/auth/login');
        exit;
    }


    public function login() {
        $error = '';
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            error_log("=== LOGIN ATTEMPT ===");
            error_log("Email: " . $email);

            $user = $this->userModel->login($email, $password);
            
            if($user) {
                error_log("✅ Login exitoso para: " . $user['name']);
                
                // Establecer sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                
                error_log("Sesión establecida - Redirigiendo...");
                
                // Diferentes redirecciones según el rol
                if($user['role'] == 'admin') {
                    $redirect_url = '/chilean_food_app/dashboard';
                    error_log("Redirigiendo ADMIN a: " . $redirect_url);
                } else {
                    $redirect_url = '/chilean_food_app/';
                    error_log("Redirigiendo USER a: " . $redirect_url);
                }
                
                // Forzar la redirección
                header('Location: ' . $redirect_url);
                exit(); // ¡IMPORTANTE! terminar la ejecución
            } else {
                $error = "Credenciales incorrectas. Por favor, verifica tu email y contraseña.";
                error_log("Login fallido");
            }
        }
        
        // Solo cargar la vista si no hubo redirección
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function register() {
        $error = '';
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validaciones
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Validar que las contraseñas coincidan
            if ($password !== $confirm_password) {
                $error = "Las contraseñas no coinciden.";
            } 
            // Validar longitud de contraseña
            elseif (strlen($password) < 6) {
                $error = "La contraseña debe tener al menos 6 caracteres.";
            }
            // Validar email
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "El formato del email no es válido.";
            }
            // Validar nombre
            elseif (strlen($name) < 2) {
                $error = "El nombre debe tener al menos 2 caracteres.";
            }
            else {
                $data = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password
                ];

                if($this->userModel->register($data)) {
                    header('Location: /chilean_food_app/auth/login?registered=1');
                    exit;
                } else {
                    $error = "Error al crear la cuenta. El email ya podría estar en uso.";
                }
            }
        }
        
        // Solo cargar la vista si no hubo redirección
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function logout() {
        // Asegurar que la sesión esté iniciada antes de manipularla
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpiar variables de sesión
        $_SESSION = [];

        // Destruir la sesión completamente
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        // Redirigir al HomeController@index
        header('Location: /chilean_food_app/home/index');
        exit;
    }

}