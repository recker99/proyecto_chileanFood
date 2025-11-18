<?php
    require_once __DIR__ . '/../models/MenuModel.php';

    class MenuController {
        private $menuModel;

        public function __construct() {
            $this->menuModel = new MenuModel();
        }

        public function index() {
            $featuredDishes = $this->menuModel->getFeaturedDishes();
            require_once __DIR__ . '/../views/menu.php';
        }

    }
?>