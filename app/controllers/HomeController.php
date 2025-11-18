<?php
    require_once __DIR__ . '/../models/RestaurantModel.php';

    class HomeController {
        private $restaurantModel;

        public function __construct() {
            $this->restaurantModel = new RestaurantModel();
        }

        public function index() {
            $featuredDishes = $this->restaurantModel->getFeaturedDishes();
            require_once __DIR__ . '/../views/home.php';
        }

    }
?>