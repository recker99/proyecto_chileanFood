<?php
class RestaurantController {
    private $restaurantModel;

    public function __construct() {
        require_once 'app/models/RestaurantModel.php';
        $this->restaurantModel = new RestaurantModel();
    }

    public function detail($id) {
        require_once 'app/models/ReviewModel.php';
        $reviewModel = new ReviewModel();

        // Obtener el restaurante
        $restaurant = $this->restaurantModel->getRestaurantById($id);

        if (!$restaurant) {
            header('HTTP/1.0 404 Not Found');
            require_once 'app/views/errors/404.php';
            return;
        }

        // Obtener los platos
        $dishes = $this->restaurantModel->getDishesByRestaurant($id);

        // Obtener reseñas del restaurante
        $reviews = $reviewModel->getReviewsByRestaurant($id);

        // Variables accesibles en la vista
        $title = $restaurant['name'] . ' - Sabores de Chile';

        // Hacemos disponibles las variables para la vista
        require 'app/views/restaurant/detail.php';
    }



    public function menu($id) {
        $restaurant = $this->restaurantModel->getRestaurantById($id);
        
        if (!$restaurant) {
            header('HTTP/1.0 404 Not Found');
            require_once 'app/views/errors/404.php';
            return;
        }

        $dishes = $this->restaurantModel->getDishesByRestaurant($id);
        
        $data = [
            'title' => 'Menú - ' . $restaurant['name'],
            'restaurant' => $restaurant,
            'dishes' => $dishes
        ];
        
        require_once 'app/views/restaurant/menu.php';
    }
}
?>