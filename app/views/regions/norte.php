<?php 
$page_title = "Zona Norte - Gastronomía Chilena";
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/user-bar.php'; 
include __DIR__ . '/../partials/navbar.php'; 

// 🔥 CONEXIÓN A LA BASE DE DATOS PARA OBTENER RESTAURANTES REALES
require_once __DIR__ . '/../../models/RestaurantModel.php';
require_once __DIR__ . '/../../models/ReviewModel.php';

$restaurantModel = new RestaurantModel();
$reviewModel = new ReviewModel();

// Obtener restaurantes de la zona norte - USANDO EL MÉTODO CORRECTO
$restaurants = $restaurantModel->getRestaurantsByRegion('norte');

// Calcular ratings para cada restaurante
foreach ($restaurants as &$restaurant) {
    $ratingData = $reviewModel->getAverageRating($restaurant['id']);
    $restaurant['avg_rating'] = $ratingData['average'];
    $restaurant['review_count'] = $ratingData['total'];
}
unset($restaurant); // Romper la referencia
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #norte-map {
        height: 400px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
    }
    .region-marker {
        background: #f59e0b;
        border: 3px solid #d97706;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    .region-marker:hover {
        background: #d97706;
        transform: scale(1.2);
    }
    .leaflet-popup-content {
        margin: 12px;
        font-family: system-ui, -apple-system, sans-serif;
    }
    .leaflet-popup-content h3 {
        color: #d97706;
        margin-bottom: 8px;
        font-weight: bold;
    }
    
    .restaurant-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .restaurant-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
</style>

<!-- MAIN CONTENT: Zona Norte -->
<main class="flex-grow py-8 px-4 sm:px-6 lg:px-8 app-container">
    <div class="max-w-7xl mx-auto">
        <!-- Encabezado -->
        <div class="text-center mb-12">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-800 mb-4">
                Descubre la Gastronomía del <span class="text-amber-600">Norte de Chile</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                La Zona Norte de Chile es famosa por su vasta geografía desértica y costera. 
                Los platos típicos se centran en los productos del mar frescos, como el ceviche y los guisos marinos, 
                así como en la cocina del interior, donde destacan las preparaciones con carne de llama y 
                el uso de especias andinas. Aquí encontrarás una mezcla única de tradiciones culinarias.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Columna del Mapa -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-24">
                    <h2 class="text-2xl font-bold text-amber-700 mb-6 flex items-center">
                        <i class="fas fa-map mr-3"></i>
                        Mapa del Norte de Chile
                    </h2>
                    
                    <!-- Mapa Leaflet Interactivo -->
                    <div class="relative bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-4 border-2 border-amber-200">
                        <div id="norte-map"></div>

                        <!-- Leyenda Interactiva -->
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center justify-between p-3 bg-amber-100 rounded-lg cursor-pointer hover:bg-amber-200 transition-colors region-legend" data-region="Arica y Parinacota">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-amber-500 rounded mr-3"></div>
                                    <span class="font-semibold text-amber-800">Arica y Parinacota</span>
                                </div>
                                <span class="text-amber-600 font-bold">
                                    <?= count(array_filter($restaurants, function($r) { return strpos(strtolower($r['location'] ?? ''), 'arica') !== false; })) ?> restaurantes
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-amber-100 rounded-lg cursor-pointer hover:bg-amber-200 transition-colors region-legend" data-region="Tarapacá">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-amber-500 rounded mr-3"></div>
                                    <span class="font-semibold text-amber-800">Tarapacá</span>
                                </div>
                                <span class="text-amber-600 font-bold">
                                    <?= count(array_filter($restaurants, function($r) { return strpos(strtolower($r['location'] ?? ''), 'tarapacá') !== false || strpos(strtolower($r['location'] ?? ''), 'iquique') !== false; })) ?> restaurantes
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-amber-100 rounded-lg cursor-pointer hover:bg-amber-200 transition-colors region-legend" data-region="Antofagasta">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-amber-500 rounded mr-3"></div>
                                    <span class="font-semibold text-amber-800">Antofagasta</span>
                                </div>
                                <span class="text-amber-600 font-bold">
                                    <?= count(array_filter($restaurants, function($r) { return strpos(strtolower($r['location'] ?? ''), 'antofagasta') !== false; })) ?> restaurantes
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-amber-100 rounded-lg cursor-pointer hover:bg-amber-200 transition-colors region-legend" data-region="Atacama">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-amber-500 rounded mr-3"></div>
                                    <span class="font-semibold text-amber-800">Atacama</span>
                                </div>
                                <span class="text-amber-600 font-bold">
                                    <?= count(array_filter($restaurants, function($r) { return strpos(strtolower($r['location'] ?? ''), 'atacama') !== false || strpos(strtolower($r['location'] ?? ''), 'copiapo') !== false; })) ?> restaurantes
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la región seleccionada -->
                    <div id="region-info" class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200 hidden">
                        <h3 id="selected-region-name" class="text-lg font-bold text-amber-800 mb-2"></h3>
                        <p id="selected-region-desc" class="text-sm text-amber-700"></p>
                        <div class="mt-3 flex items-center text-amber-600">
                            <i class="fas fa-utensils mr-2"></i>
                            <span id="selected-region-restaurants" class="font-semibold"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna de Restaurantes -->
            <div class="lg:col-span-2">
                <!-- Filtros y Búsqueda -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                    <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                                Restaurantes del Norte
                            </h2>
                            <p class="text-gray-600" id="restaurants-count">
                                Mostrando <?= count($restaurants) ?> restaurantes
                            </p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="search-restaurants" 
                                    placeholder="Buscar restaurante..." 
                                    class="w-full sm:w-64 py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition duration-300"
                                >
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            
                            <select id="filter-region" class="py-2 px-4 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition duration-300">
                                <option value="all">Todas las regiones</option>
                                <option value="Arica y Parinacota">Arica y Parinacota</option>
                                <option value="Tarapacá">Tarapacá</option>
                                <option value="Antofagasta">Antofagasta</option>
                                <option value="Atacama">Atacama</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Lista de Restaurantes -->
                <div id="restaurants-list" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php if (!empty($restaurants)): ?>
                        <?php foreach ($restaurants as $restaurant): ?>
                            <div class="restaurant-card bg-white rounded-2xl shadow-lg overflow-hidden" 
                                 data-name="<?= htmlspecialchars(strtolower($restaurant['name'])) ?>"
                                 data-region="<?= htmlspecialchars($restaurant['location'] ?? '') ?>">
                                <div class="h-48 bg-gradient-to-br from-amber-500 to-orange-500 relative overflow-hidden">
                                    <img src="<?= htmlspecialchars($restaurant['image'] ?? 'https://placehold.co/600x400/f59e0b/ffffff?text=' . urlencode($restaurant['name'])) ?>" 
                                         alt="<?= htmlspecialchars($restaurant['name']) ?>"
                                         class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                                    <!-- SOLO LA REGIÓN EN LA IMAGEN -->
                                    <div class="absolute top-4 right-4">
                                        <span class="bg-amber-600 text-white text-sm font-semibold px-3 py-1 rounded-full">
                                            <?= htmlspecialchars($restaurant['location'] ?? 'Norte') ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">
                                        <?= htmlspecialchars($restaurant['name']) ?>
                                    </h3>
                                    
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                        <?= htmlspecialchars($restaurant['description'] ?? 'Descubre los sabores auténticos del norte chileno.') ?>
                                    </p>
                                    
                                    <!-- SE ELIMINÓ LA SECCIÓN DE RATING CON ESTRELLAS -->
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-gray-500 text-sm">
                                            <i class="fas fa-map-marker-alt mr-2"></i>
                                            <span><?= htmlspecialchars($restaurant['address'] ?? 'Ubicación no especificada') ?></span>
                                        </div>
                                        
                                        <a href="/chilean_food_app/restaurant/detail/<?= $restaurant['id'] ?>" 
                                           class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 transform hover:scale-105">
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-2 text-center py-16 bg-white rounded-xl shadow-lg border border-gray-100">
                            <i class="fas fa-store-slash text-7xl text-gray-300 mb-6"></i>
                            <h3 class="text-2xl font-bold text-gray-600 mb-2">No hay restaurantes</h3>
                            <p class="text-lg text-gray-500 max-w-lg mx-auto">
                                No se encontraron restaurantes en la zona norte.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Mensaje sin resultados (para búsqueda/filtros) -->
                <div id="no-restaurants" class="hidden text-center py-16 bg-white rounded-xl shadow-lg border border-gray-100">
                    <i class="fas fa-search text-7xl text-gray-300 mb-6"></i>
                    <h3 class="text-2xl font-bold text-gray-600 mb-2">No se encontraron resultados</h3>
                    <p class="text-lg text-gray-500 max-w-lg mx-auto">
                        No hay restaurantes que coincidan con tu búsqueda.
                    </p>
                </div>
            </div>
        </div>

        <!-- Sección de Platos Típicos del Norte -->
        <div class="mt-16">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">
                    Platos <span class="text-amber-600">Típicos del Norte</span>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Descubre los sabores auténticos que han alimentado a generaciones en el norte chileno
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Plato 1 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="h-40 bg-gradient-to-br from-amber-500 to-orange-500 relative overflow-hidden">
                        <img src="https://placehold.co/600x400/f59e0b/ffffff?text=Asado+altiplánico" 
                             alt="Asado altiplánico"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 text-lg mb-2">Asado Altiplánico</h3>
                        <p class="text-gray-600 text-sm mb-3">Carnes de llama y alpaca preparadas con hierbas andinas.</p>
                        <span class="inline-block bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-1 rounded">
                            Arica y Parinacota
                        </span>
                    </div>
                </div>

                <!-- Plato 2 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="h-40 bg-gradient-to-br from-amber-500 to-orange-500 relative overflow-hidden">
                        <img src="https://placehold.co/600x400/d97706/ffffff?text=Chairo" 
                             alt="Chairo"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 text-lg mb-2">Chairo Paceño</h3>
                        <p class="text-gray-600 text-sm mb-3">Sopa tradicional con chuño, carne y verduras andinas.</p>
                        <span class="inline-block bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-1 rounded">
                            Tarapacá
                        </span>
                    </div>
                </div>

                <!-- Plato 3 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="h-40 bg-gradient-to-br from-amber-500 to-orange-500 relative overflow-hidden">
                        <img src="https://placehold.co/600x400/b45309/ffffff?text=Caldillo+congrio" 
                             alt="Caldillo de congrio"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 text-lg mb-2">Caldillo de Congrio</h3>
                        <p class="text-gray-600 text-sm mb-3">Sopa de pescado norteña con ají y especias locales.</p>
                        <span class="inline-block bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-1 rounded">
                            Antofagasta
                        </span>
                    </div>
                </div>

                <!-- Plato 4 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="h-40 bg-gradient-to-br from-amber-500 to-orange-500 relative overflow-hidden">
                        <img src="https://placehold.co/600x400/92400e/ffffff?text=Empanadas+de+mariscos" 
                             alt="Empanadas de mariscos"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 text-lg mb-2">Empanadas de Mariscos</h3>
                        <p class="text-gray-600 text-sm mb-3">Rellenas con mariscos frescos del Pacífico norte.</p>
                        <span class="inline-block bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-1 rounded">
                            Atacama
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// JavaScript para filtros y búsqueda
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-restaurants');
    const filterRegion = document.getElementById('filter-region');
    const restaurantCards = document.querySelectorAll('.restaurant-card');
    const noResults = document.getElementById('no-restaurants');
    const restaurantsCount = document.getElementById('restaurants-count');

    function filterRestaurants() {
        const searchTerm = searchInput.value.toLowerCase();
        const regionFilter = filterRegion.value;
        
        let visibleCount = 0;

        restaurantCards.forEach(card => {
            const name = card.getAttribute('data-name');
            const region = card.getAttribute('data-region');
            
            const matchesSearch = name.includes(searchTerm);
            const matchesRegion = regionFilter === 'all' || region === regionFilter;
            
            if (matchesSearch && matchesRegion) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Mostrar/ocultar mensaje de no resultados
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
            restaurantsCount.textContent = 'No se encontraron restaurantes';
        } else {
            noResults.classList.add('hidden');
            restaurantsCount.textContent = `Mostrando ${visibleCount} restaurantes`;
        }
    }

    searchInput.addEventListener('input', filterRestaurants);
    filterRegion.addEventListener('change', filterRestaurants);

    // Inicializar mapa Leaflet
    const map = L.map('norte-map').setView([-20.0, -69.0], 5);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);


    // Agregar marcadores para los restaurantes
    <?php foreach ($restaurants as $restaurant): ?>
        <?php if (!empty($restaurant['latitude']) && !empty($restaurant['longitude'])): ?>
            L.marker([<?= $restaurant['latitude'] ?>, <?= $restaurant['longitude'] ?>])
                .addTo(map)
                .bindPopup(`
                    <h3><?= htmlspecialchars($restaurant['name']) ?></h3>
                    <p><?= htmlspecialchars($restaurant['address'] ?? '') ?></p>
                    <a href="/chilean_food_app/restaurant/detail/<?= $restaurant['id'] ?>" 
                       class="inline-block mt-2 bg-amber-500 text-white px-3 py-1 rounded text-sm">
                        Ver detalles
                    </a>
                `);
        <?php endif; ?>
    <?php endforeach; ?>

    // Interactividad para la leyenda de regiones
    const regionLegends = document.querySelectorAll('.region-legend');
    regionLegends.forEach(legend => {
        legend.addEventListener('click', function() {
            const region = this.getAttribute('data-region');
            filterRegion.value = region;
            filterRestaurants();
        });
    });
});
</script>

<?php 
include __DIR__ . '/../partials/footer.php'; 
?>