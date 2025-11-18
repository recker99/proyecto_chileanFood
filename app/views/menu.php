<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/user-bar.php'; ?>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<!-- MODAL DE DETALLES DEL RESTAURANTE Y RESEÑAS -->
<div id="restaurant-modal" class="modal fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50 hidden" onclick="if(event.target === this) closeModal()">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform transition-all duration-300">
        <!-- Encabezado del Modal -->
        <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-center z-10">
            <h2 id="modal-restaurant-name" class="text-3xl font-bold text-gray-800">Nombre del Restaurante</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-primary transition duration-200 text-3xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Contenido del Modal -->
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Información del Restaurante -->
                <div>
                    <h3 class="text-2xl font-semibold text-secondary mb-4 border-b pb-2">Información y Ubicación</h3>
                    <p id="modal-restaurant-address" class="text-gray-600 mb-2"><i class="fas fa-map-marker-alt text-primary mr-2"></i> Dirección: </p>
                    <p id="modal-restaurant-hours" class="text-gray-600 mb-4"><i class="fas fa-clock text-primary mr-2"></i> Horarios: </p>
                    <p class="text-sm text-gray-500 mb-4"><i class="fas fa-user-tag text-primary mr-2"></i> Tu ID de Usuario: <span id="current-user-id" class="font-mono text-xs bg-gray-100 px-2 py-1 rounded"><?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'No identificado'; ?></span></p>

                    <a id="modal-map-link" href="#" target="_blank" class="block w-full text-center bg-primary text-white font-bold py-3 rounded-lg hover:bg-red-700 transition duration-300 mb-6">
                        <i class="fas fa-route mr-2"></i> Ver en Google Maps
                    </a>
                </div>

                <!-- Formulario de Reseña -->
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <h3 class="text-2xl font-semibold text-secondary mb-3">Deja tu Reseña</h3>
                    <form id="review-form">
                        <input type="hidden" id="review-restaurant-id" name="restaurant_id">
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Puntuación:</label>
                            <div id="star-rating-input" class="star-rating text-3xl text-gray-300 cursor-pointer">
                                <i class="far fa-star" data-value="1"></i>
                                <i class="far fa-star" data-value="2"></i>
                                <i class="far fa-star" data-value="3"></i>
                                <i class="far fa-star" data-value="4"></i>
                                <i class="far fa-star" data-value="5"></i>
                            </div>
                            <input type="hidden" id="review-rating" name="rating" value="0" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="review-comment" class="block text-gray-700 text-sm font-bold mb-2">Comentario:</label>
                            <textarea id="review-comment" name="comment" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-secondary" placeholder="¿Qué te pareció el lugar y su comida?" required></textarea>
                        </div>
                        
                        <!-- Botón de Envío -->
                        <button type="submit" class="bg-tertiary-color text-white font-bold py-2 px-4 rounded-lg hover:bg-teal-700 transition duration-300 w-full disabled:opacity-50" id="submit-review-btn">
                            <i class="fas fa-paper-plane mr-2"></i> Enviar Reseña
                        </button>
                        
                        <!-- Mensaje de estado para usuarios no logueados -->
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <p class="text-sm mt-2 text-center text-red-500">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Debes <a href="login.php" class="underline">iniciar sesión</a> para enviar reseñas
                            </p>
                        <?php endif; ?>
                        
                        <p id="review-status-message" class="text-sm mt-2 text-center"></p>
                    </form>
                </div>
            </div>
            
            <!-- Listado de Reseñas Existentes -->
            <div class="mt-8">
                <h3 class="text-2xl font-semibold text-secondary mb-4 border-b pb-2">Reseñas de Usuarios</h3>
                <div id="reviews-list" class="space-y-4">
                    <!-- Las reseñas se cargarán aquí -->
                    <p id="loading-reviews" class="text-gray-500 text-center">Cargando reseñas...</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIN DEL MODAL -->


<!-- MAIN CONTENT: Carta del Menú -->
<main class="flex-grow py-12 px-4 sm:px-6 lg:px-8 app-container">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-800 mb-4 text-center">
            Explora Nuestra <span class="text-primary">Carta</span>
        </h1>
        <p class="text-xl text-gray-600 mb-10 text-center max-w-3xl mx-auto">
            Descubre los sabores auténticos de Chile, desde el Desayuno hasta la Cena.
        </p>
        
        <!-- Campo de Búsqueda -->
        <div class="mb-8 max-w-xl mx-auto">
            <div class="relative">
                <input 
                    type="text" 
                    id="search-input" 
                    placeholder="Busca un plato (ej: choclo, carne, pisco)..." 
                    class="w-full py-4 pl-14 pr-4 text-lg border-2 border-gray-300 rounded-2xl focus:outline-none focus:border-primary transition duration-300 shadow-xl"
                >
                <i class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl"></i>
            </div>
        </div>

        <!-- Título de Filtros de Categoría -->
        <h2 class="text-2xl font-bold text-gray-700 mb-3 text-center border-b-2 border-gray-200 pb-2 max-w-2xl mx-auto">Filtrar por Momento del Día / Tipo</h2>
        
        <!-- Contenedor de Filtros de Categoría (ROJO) - ESTÁTICO -->
        <div id="category-filter-container" class="flex flex-wrap justify-center gap-3 md:gap-4 mb-10">
            <button data-filter="Todos" class="category-filter-button bg-white text-gray-700 font-bold py-3 px-6 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 border-2 border-gray-200 text-sm md:text-base active-category-filter">
                <i class="fas fa-list mr-2"></i>Todos
            </button>
            <button data-filter="Desayuno" class="category-filter-button bg-white text-gray-700 font-bold py-3 px-6 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 border-2 border-gray-200 text-sm md:text-base">
                <i class="fas fa-coffee mr-2"></i>Desayuno
            </button>
            <button data-filter="Almuerzo" class="category-filter-button bg-white text-gray-700 font-bold py-3 px-6 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 border-2 border-gray-200 text-sm md:text-base">
                <i class="fas fa-bowl-food mr-2"></i>Almuerzo
            </button>
            <button data-filter="Cena" class="category-filter-button bg-white text-gray-700 font-bold py-3 px-6 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 border-2 border-gray-200 text-sm md:text-base">
                <i class="fas fa-wine-glass mr-2"></i>Cena
            </button>
            <button data-filter="Platos Principales" class="category-filter-button bg-white text-gray-700 font-bold py-3 px-6 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 border-2 border-gray-200 text-sm md:text-base">
                <i class="fas fa-concierge-bell mr-2"></i>Platos Principales
            </button>
            <button data-filter="Entradas" class="category-filter-button bg-white text-gray-700 font-bold py-3 px-6 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 border-2 border-gray-200 text-sm md:text-base">
                <i class="fas fa-cheese mr-2"></i>Entradas
            </button>
            <button data-filter="Postres" class="category-filter-button bg-white text-gray-700 font-bold py-3 px-6 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 border-2 border-gray-200 text-sm md:text-base">
                <i class="fas fa-ice-cream mr-2"></i>Postres
            </button>
            <button data-filter="Bebidas y Licores" class="category-filter-button bg-white text-gray-700 font-bold py-3 px-6 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 border-2 border-gray-200 text-sm md:text-base">
                <i class="fas fa-cocktail mr-2"></i>Bebidas y Licores
            </button>
        </div>

        <!-- Título de Filtros de Región -->
        <h2 class="text-2xl font-bold text-gray-700 mb-3 text-center border-b-2 border-gray-200 pb-2 max-w-2xl mx-auto">Filtrar por Zona Geográfica <i class="fas fa-mountain ml-1 text-tertiary-color"></i></h2>
        
        <!-- Contenedor de Filtros de Región (AZUL/TERCIARIO) - ESTÁTICO -->
        <div id="region-filter-container" class="flex flex-wrap justify-center gap-3 md:gap-4 mb-14">
            <button data-filter="Todo Chile" class="region-filter-button bg-white text-gray-700 font-bold py-3 px-6 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 border-2 border-gray-200 text-sm md:text-base active-region-filter">
                <i class="fas fa-globe mr-2"></i>Todo Chile
            </button>
            <button data-filter="Norte" class="region-filter-button bg-white text-gray-700 font-bold py-3 px-6 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 border-2 border-gray-200 text-sm md:text-base">
                <i class="fas fa-sun mr-2"></i>Norte
            </button>
            <button data-filter="Centro" class="region-filter-button bg-white text-gray-700 font-bold py-3 px-6 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 border-2 border-gray-200 text-sm md:text-base">
                <i class="fas fa-city mr-2"></i>Centro
            </button>
            <button data-filter="Sur" class="region-filter-button bg-white text-gray-700 font-bold py-3 px-6 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 border-2 border-gray-200 text-sm md:text-base">
                <i class="fas fa-snowflake mr-2"></i>Sur
            </button>
        </div>

        <!-- Contenedor de Listado de Platos - 4 COLUMNAS -->
        <div id="menu-list" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <!-- Los elementos del menú se generarán con JavaScript -->
            
            <!-- Mensaje de Sin Resultados (Oculto por defecto) -->
            <div id="no-results" class="col-span-full hidden text-center py-16 bg-white rounded-xl shadow-lg border border-gray-100">
                <i class="fas fa-utensils-slash text-7xl text-gray-300 mb-6 animate-pulse"></i>
                <h3 class="text-2xl font-bold text-gray-600 mb-2">¡Ups! No hay resultados.</h3>
                <p class="text-lg text-gray-500 max-w-lg mx-auto">Prueba ajustando los filtros de **Categoría** y **Zona** o simplificando tu búsqueda.</p>
            </div>
        </div>
    </div>
</main>

<!-- Scripts -->
<script src="./assets/js/navbar.js"></script>
<script src="./assets/js/slider.js"></script>
<script src="./assets/js/carta.js"></script>

<?php include __DIR__ . '/partials/footer.php'; ?>