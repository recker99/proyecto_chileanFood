<?php
require_once __DIR__ . '/../models/RestaurantModel.php';
require_once __DIR__ . '/../models/DishModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';

class RestaurantController
{
    private $restaurantModel;
    private $dishModel;
    private $reviewModel;

    public function __construct()
    {
        $this->restaurantModel = new RestaurantModel();
        $this->dishModel = new DishModel();
        $this->reviewModel = new ReviewModel();
    }

    public function detail($id)
    {
        try {
            // Obtener datos del restaurante
            $restaurant = $this->restaurantModel->getById($id);
            
            if (!$restaurant) {
                $this->view('error/404');
                return;
            }

            // Obtener platos del restaurante
            $dishes = $this->dishModel->getByRestaurant($id);
            
            // Obtener especialidades
            $specialties = !empty($restaurant['specialties']) 
                ? explode(',', $restaurant['specialties']) 
                : [];

            // 🔥 CALCULAR RATING PROMEDIO Y CONTAR RESEÑAS
            $ratingData = $this->reviewModel->getAverageRating($id);
            
            // Agregar los datos de rating al array del restaurante
            $restaurant['avg_rating'] = $ratingData['average'];
            $restaurant['review_count'] = $ratingData['total'];

            // Obtener reseñas para mostrar en la pestaña
            $reviews = $this->reviewModel->getReviewsByRestaurant($id);

            // DEBUG: Verificar datos
            error_log("=== RESTAURANT CONTROLLER DEBUG ===");
            error_log("Restaurant ID: " . $id);
            error_log("Restaurant Data: " . print_r($restaurant, true));
            error_log("Rating Data: " . print_r($ratingData, true));
            error_log("Reviews Count: " . count($reviews));

            $this->view('restaurants/detail', [
                'restaurant' => $restaurant,
                'dishes' => $dishes,
                'specialties' => $specialties,
                'reviews' => $reviews
            ]);

        } catch (Exception $e) {
            error_log("Error en RestaurantController detail: " . $e->getMessage());
            $this->view('error/500', ['message' => $e->getMessage()]);
        }
    }

    // ... otros métodos que puedas tener (index, search, etc.)

    public function index()
    {
        try {
            $restaurants = $this->restaurantModel->getAll();
            $this->view('restaurants/index', ['restaurants' => $restaurants]);
        } catch (Exception $e) {
            $this->view('error/500', ['message' => $e->getMessage()]);
        }
    }

    public function search()
    {
        try {
            $query = $_GET['q'] ?? '';
            $region = $_GET['region'] ?? '';
            
            $restaurants = $this->restaurantModel->search($query, $region);
            $this->view('restaurants/search', [
                'restaurants' => $restaurants,
                'searchQuery' => $query,
                'searchRegion' => $region
            ]);
        } catch (Exception $e) {
            $this->view('error/500', ['message' => $e->getMessage()]);
        }
    }

    private function view($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . '/../views/' . $view . '.php';
    }
}