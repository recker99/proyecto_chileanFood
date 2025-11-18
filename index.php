<?php
    // Define la constante para el path base
    define('BASE_PATH', dirname(__FILE__));

    // Incluir archivos core
    require_once 'app/core/Model.php';
    require_once 'app/core/App.php';
    require_once 'config/database.php';

    // Iniciar la aplicación
    $app = new App();
?>