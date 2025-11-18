<?php
require_once __DIR__ . '/../models/RestaurantModel.php';

class RegionController {
    private $restaurantModel;

    public function __construct() {
        $this->restaurantModel = new RestaurantModel();
    }

    public function norte() {
        $restaurants = $this->restaurantModel->getRestaurantsByRegion('norte');
        require_once __DIR__ . '/../views/regions/norte.php';
    }

    public function centro() {
        $restaurants = $this->restaurantModel->getRestaurantsByRegion('centro');
        require_once __DIR__ . '/../views/regions/centro.php';
    }

    public function sur() {
        $restaurants = $this->restaurantModel->getRestaurantsByRegion('sur');
        require_once __DIR__ . '/../views/regions/sur.php';
    }

    // Método para manejar regiones dinámicas si es necesario
    public function show($region) {
        $validRegions = ['norte', 'centro', 'sur'];
        
        if (in_array($region, $validRegions)) {
            $methodName = $region;
            $this->$methodName();
        } else {
            // Región no válida
            header('HTTP/1.0 404 Not Found');
            require_once __DIR__ . '/../views/errors/404.php';
        }
    }
}
?>