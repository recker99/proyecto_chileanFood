<?php
/*
 * Menú de Navegación Responsive con Dropdown para Zonas Gastronómicas.
 * Usa lógica PHP para mostrar el enlace de Dashboard solo si el usuario es administrador.
 * Se asume que Font Awesome y estilos CSS/Tailwind están cargados.
 */
?>

<nav class="navbar bg-gray-900 shadow-lg text-white font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="/chilean_food_app/" class="text-2xl font-extrabold tracking-tight">
                    <h2 class="text-white hover:text-red-400 transition duration-300">Sabores<span class="text-red-500">Chile</span></h2>
                </a>
            </div>

            <!-- Menú de escritorio (Oculto en móvil) -->
            <div class="hidden md:flex space-x-6 items-center" id="navMenu">
                <a href="/chilean_food_app/" class="nav-link text-gray-300 hover:text-red-400 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Inicio</a>
                
                <a href="/chilean_food_app/menu" class="nav-link text-gray-300 hover:text-red-400 px-3 py-2 rounded-md text-sm font-medium transition duration-300">La Carta (Menú)</a>
                
                <!-- Dropdown para Zonas Gastronómicas -->
                <div class="dropdown relative group">
                    <button class="nav-link text-gray-300 hover:text-red-400 px-3 py-2 rounded-md text-sm font-medium flex items-center transition duration-300">
                        Zonas Gastronómicas
                        <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                    </button>
                    <div class="dropdown-content absolute min-w-[180px] z-10 p-2 bg-gray-800 rounded-lg shadow-xl hidden group-hover:block top-full mt-2 -left-4">
                        <a href="/chilean_food_app/region/norte" class="block px-4 py-2 text-sm text-gray-200 hover:bg-red-600 hover:text-white rounded-md">Zona Norte</a>
                        <a href="/chilean_food_app/region/centro" class="block px-4 py-2 text-sm text-gray-200 hover:bg-red-600 hover:text-white rounded-md">Zona Central</a>
                        <a href="/chilean_food_app/region/sur" class="block px-4 py-2 text-sm text-gray-200 hover:bg-red-600 hover:text-white rounded-md">Zona Sur</a>
                    </div>
                </div>

                <a href="/chilean_food_app/contact" class="nav-link text-gray-300 hover:text-red-400 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Contacto</a>

                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="/chilean_food_app/dashboard" class="nav-link admin-link bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-full text-sm font-semibold transition duration-300 flex items-center">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                <?php endif; ?>
            </div>

            <!-- Botón toggle móvil -->
            <div class="md:hidden flex items-center">
                <button id="navToggle" class="nav-toggle inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" aria-controls="navMobile" aria-expanded="false">
                    <span class="sr-only">Abrir menú principal</span>
                    <i class="fas fa-bars h-6 w-6" id="toggleIconOpen"></i>
                    <i class="fas fa-times h-6 w-6 hidden" id="toggleIconClose"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Menú móvil (Oculto por defecto, se muestra con JS) -->
    <div class="md:hidden" id="navMobile">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-800">
            <a href="/chilean_food_app/" class="nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Inicio</a>
            <a href="/chilean_food_app/menu" class="nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">La Carta (Menú)</a>
            
            <!-- Zonas Gastronómicas individuales para móvil -->
            <span class="block px-3 pt-4 pb-2 text-xs font-semibold uppercase text-gray-400 border-t border-gray-700 mt-2">Zonas Gastronómicas</span>
            <a href="/chilean_food_app/region/norte" class="nav-link sub-link block px-5 py-2 rounded-md text-sm font-medium text-gray-400 hover:bg-gray-700 hover:text-white ml-2">Zona Norte</a>
            <a href="/chilean_food_app/region/centro" class="nav-link sub-link block px-5 py-2 rounded-md text-sm font-medium text-gray-400 hover:bg-gray-700 hover:text-white ml-2">Zona Central</a>
            <a href="/chilean_food_app/region/sur" class="nav-link sub-link block px-5 py-2 rounded-md text-sm font-medium text-gray-400 hover:bg-gray-700 hover:text-white ml-2">Zona Sur</a>
            
            <a href="/chilean_food_app/contact" class="nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white mt-2 border-t border-gray-700 pt-3">Contacto</a>

            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="/chilean_food_app/dashboard" class="nav-link admin-link block px-3 py-2 rounded-md text-base font-medium text-white bg-red-600 hover:bg-red-700 text-center mt-2">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    // Lógica JavaScript para manejar el menú móvil
    document.getElementById('navToggle').addEventListener('click', function() {
        const mobileMenu = document.getElementById('navMobile');
        const openIcon = document.getElementById('toggleIconOpen');
        const closeIcon = document.getElementById('toggleIconClose');

        if (mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.remove('hidden');
            openIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
        } else {
            mobileMenu.classList.add('hidden');
            openIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }
    });
</script>