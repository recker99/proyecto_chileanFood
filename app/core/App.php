<?php
class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        echo "<!-- ========== DEBUG START ========== -->\n";
        echo "<!-- URL completa: " . ($_GET['url'] ?? 'home') . " -->\n";
        echo "<!-- URL parseada: ";
        print_r($url);
        echo " -->\n";

        // Mapeo de controladores
        $controllers = [
            '' => 'HomeController',
            'home' => 'HomeController',
            'auth' => 'AuthController',
            'dashboard' => 'DashboardController',
            'restaurants' => 'RestaurantController',
            'restaurant' => 'RestaurantController',
            // ========================================================
            // AÑADIDO: Ruta para la página principal del menú
            'menu' => 'MenuController',
            // ========================================================
            'contact' => 'ContactController',
            'search' => 'SearchController',
            'review' => 'ReviewController',
            'region' => 'RegionController'
        ];

        // Determinar controlador
        $controllerKey = $url[0] ?? '';
        echo "<!-- Controller Key: '{$controllerKey}' -->\n";
        
        if (array_key_exists($controllerKey, $controllers)) {
            $this->controller = $controllers[$controllerKey];
            unset($url[0]);
            echo "<!-- Controller seleccionado: {$this->controller} -->\n";
        } else {
            echo "<!-- Controller NO encontrado, usando: {$this->controller} -->\n";
        }

        // Cargar el controlador
        $controllerFile = 'app/controllers/' . $this->controller . '.php';
        echo "<!-- Controller file: {$controllerFile} -->\n";
        echo "<!-- File exists: " . (file_exists($controllerFile) ? 'YES' : 'NO') . " -->\n";
        
        // El require_once debe ir dentro de un manejo de errores en un entorno de producción, 
        // pero para debug lo dejamos así.
        require_once $controllerFile;
        $this->controller = new $this->controller;

        // Method
        if(isset($url[1])) {
            $this->method = $url[1];
            unset($url[1]);
            echo "<!-- Method: {$this->method} -->\n";
        } else {
            echo "<!-- No method specified, using: {$this->method} -->\n";
        }

        // Parameters
        $this->params = $url ? array_values($url) : [];
        echo "<!-- Params: ";
        print_r($this->params);
        echo " -->\n";

        // Verificar que el método existe
        echo "<!-- Method exists: " . (method_exists($this->controller, $this->method) ? 'YES' : 'NO') . " -->\n";

        echo "<!-- ========== DEBUG END ========== -->\n";

        // Llamar al método del controlador con sus parámetros
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if(isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}