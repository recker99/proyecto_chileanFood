<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/RestaurantModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';
require_once __DIR__ . '/../models/ContactModel.php';

class DashboardController {
    private $userModel;
    private $restaurantModel;
    private $reviewModel;
    private $contactModel;

    public function __construct() {
        // Verificar sesión y rol de administrador
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /chilean_food_app/auth/login');
            exit;
        }

        $this->userModel = new UserModel();
        $this->restaurantModel = new RestaurantModel();
        $this->reviewModel = new ReviewModel();
        $this->contactModel = new ContactModel();
    }

    public function index() {
        $data = [
            'total_users' => $this->userModel->getTotalUsers(),
            'total_restaurants' => $this->restaurantModel->getTotalRestaurants(),
            'total_reviews' => $this->reviewModel->getTotalReviews(),
            'pending_contacts' => $this->contactModel->getPendingContactsCount(),
            'recent_users' => $this->userModel->getRecentUsers(5),
            'recent_restaurants' => $this->restaurantModel->getRecentRestaurants(5)
        ];

        require_once __DIR__ . '/../views/dashboard/index.php';
    }

    // Gestión de Usuarios
    public function users() {
        $users = $this->userModel->getAllUsers();
        require_once __DIR__ . '/../views/dashboard/users.php';
    }

    public function editUser($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'role' => trim($_POST['role'])
            ];

            if ($this->userModel->updateUser($id, $data)) {
                header('Location: /chilean_food_app/dashboard/users?success=Usuario actualizado');
                exit;
            } else {
                $error = "Error al actualizar el usuario";
            }
        }

        $user = $this->userModel->getUserById($id);
        require_once __DIR__ . '/../views/dashboard/edit_user.php';
    }

    public function deleteUser($id) {
        if ($this->userModel->deleteUser($id)) {
            header('Location: /chilean_food_app/dashboard/users?success=Usuario eliminado');
        } else {
            header('Location: /chilean_food_app/dashboard/users?error=Error al eliminar usuario');
        }
        exit;
    }

    // Gestión de Restaurantes
    public function restaurants() {
        $restaurants = $this->restaurantModel->getAllRestaurants();
        require_once __DIR__ . '/../views/dashboard/restaurants.php';
    }

    public function editRestaurant($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'location' => trim($_POST['location']),
                'region' => trim($_POST['region']),
                'phone' => trim($_POST['phone']),
                'email' => trim($_POST['email'])
            ];

            if ($this->restaurantModel->updateRestaurant($id, $data)) {
                header('Location: /chilean_food_app/dashboard/restaurants?success=Restaurante actualizado');
                exit;
            } else {
                $error = "Error al actualizar el restaurante";
            }
        }

        $restaurant = $this->restaurantModel->getRestaurantById($id);
        require_once __DIR__ . '/../views/dashboard/edit_restaurant.php';
    }

    public function deleteRestaurant($id) {
        if ($this->restaurantModel->deleteRestaurant($id)) {
            header('Location: /chilean_food_app/dashboard/restaurants?success=Restaurante eliminado');
        } else {
            header('Location: /chilean_food_app/dashboard/restaurants?error=Error al eliminar restaurante');
        }
        exit;
    }

    // Gestión de Reseñas
    public function reviews() {
        $reviews = $this->reviewModel->getAllReviews();
        require_once __DIR__ . '/../views/dashboard/reviews.php';
    }

    public function deleteReview($id) {
        if ($this->reviewModel->deleteReview($id)) {
            header('Location: /chilean_food_app/dashboard/reviews?success=Reseña eliminada');
        } else {
            header('Location: /chilean_food_app/dashboard/reviews?error=Error al eliminar reseña');
        }
        exit;
    }

    // Gestión de Mensajes de Contacto
    public function contactMessages() {
        $messages = $this->contactModel->getAllMessages();
        require_once __DIR__ . '/../views/dashboard/contact_messages.php';
    }

    public function markContactAsRead($id) {
        if ($this->contactModel->markAsRead($id)) {
            header('Location: /chilean_food_app/dashboard/contact-messages?success=Mensaje marcado como leído');
        } else {
            header('Location: /chilean_food_app/dashboard/contact-messages?error=Error al marcar mensaje');
        }
        exit;
    }

    public function deleteContactMessage($id) {
        if ($this->contactModel->deleteMessage($id)) {
            header('Location: /chilean_food_app/dashboard/contact-messages?success=Mensaje eliminado');
        } else {
            header('Location: /chilean_food_app/dashboard/contact-messages?error=Error al eliminar mensaje');
        }
        exit;
    }
}
?>