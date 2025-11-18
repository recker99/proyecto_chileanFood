<?php
/**
 * partials/header.php
 * Estructura de cabecera para abrir la aplicación.
 * Incluye la configuración del viewport, hojas de estilo CSS y la configuración de Tailwind.
 */

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sabores de Chile - Gastronomía Tradicional</title>
    
    <!-- 1. CARGA DEL CDN DE TAILWIND CSS -->
    <!-- Esto habilita todas las clases como bg-gray-800 y grid en el footer -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- 2. CONFIGURACIÓN DE TAILWIND PARA CLASES PERSONALIZADAS -->
    <!-- Esto define el color 'secondary-color' que estás usando -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Defino 'secondary-color' como un rojo vibrante
                        'secondary-color': '#F44336', 
                    },
                }
            }
        }
    </script>
    
    <!-- Estilos principales -->
    <link rel="stylesheet" href="/chilean_food_app/assets/css/styles.css">
    <link rel="stylesheet" href="/chilean_food_app/assets/css/slider.css">
    <link rel="stylesheet" href="/chilean_food_app/assets/css/regions.css">
    <link rel="stylesheet" href="/chilean_food_app/assets/css/contact.css">
    <link rel="stylesheet" href="/chilean_food_app/assets/css/restaurant.css">

    <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>