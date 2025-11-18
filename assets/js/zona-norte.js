// =============================================
// VARIABLES GLOBALES - ZONA NORTE
// =============================================

const NorteApp = {
    restaurants: [],
    filteredRestaurants: [],
    selectedRegion: 'all',
    searchTerm: '',
    isInitialized: false,
    map: null,
    markers: []
};

// Cache de elementos DOM
const NorteDOM = {
    restaurantsList: null,
    loadingRestaurants: null,
    noRestaurants: null,
    searchInput: null,
    filterRegion: null,
    restaurantsCount: null,
    regionInfo: null,
    selectedRegionName: null,
    selectedRegionDesc: null,
    selectedRegionRestaurants: null,
    norteMap: null
};

// Coordenadas de las regiones del norte de Chile
const regionCoordinates = {
    'Arica y Parinacota': { lat: -18.478, lng: -70.312, zoom: 8 },
    'Tarapacá': { lat: -20.215, lng: -69.785, zoom: 8 },
    'Antofagasta': { lat: -23.650, lng: -70.400, zoom: 7 },
    'Atacama': { lat: -27.366, lng: -70.332, zoom: 8 }
};

// =============================================
// INICIALIZACIÓN DE LA PÁGINA
// =============================================

/**
 * Inicializa la página de Zona Norte
 */
function initializeNortePage() {
    try {
        initializeNorteDOM();
        setupNorteEventListeners();
        initializeMap();
        loadRestaurants();
        
        NorteApp.isInitialized = true;
        console.log('Página Zona Norte inicializada correctamente');
    } catch (error) {
        console.error('Error al inicializar la página Zona Norte:', error);
        handleNorteInitializationError();
    }
}

/**
 * Inicializa la cache de elementos DOM
 */
function initializeNorteDOM() {
    NorteDOM.restaurantsList = document.getElementById('restaurants-list');
    NorteDOM.loadingRestaurants = document.getElementById('loading-restaurants');
    NorteDOM.noRestaurants = document.getElementById('no-restaurants');
    NorteDOM.searchInput = document.getElementById('search-restaurants');
    NorteDOM.filterRegion = document.getElementById('filter-region');
    NorteDOM.restaurantsCount = document.getElementById('restaurants-count');
    NorteDOM.regionInfo = document.getElementById('region-info');
    NorteDOM.selectedRegionName = document.getElementById('selected-region-name');
    NorteDOM.selectedRegionDesc = document.getElementById('selected-region-desc');
    NorteDOM.selectedRegionRestaurants = document.getElementById('selected-region-restaurants');
    NorteDOM.norteMap = document.getElementById('norte-map');
}

/**
 * Inicializa el mapa Leaflet
 */
function initializeMap() {
    if (!NorteDOM.norteMap) return;

    // Crear mapa centrado en el norte de Chile
    NorteApp.map = L.map('norte-map').setView([-22.5, -69.5], 6);

    // Añadir capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(NorteApp.map);

    // Añadir marcadores de regiones
    addRegionMarkers();
}

/**
 * Añade marcadores de regiones al mapa
 */
function addRegionMarkers() {
    const regionInfo = {
        'Arica y Parinacota': {
            name: 'Arica y Parinacota',
            description: 'Región fronteriza con Perú y Bolivia, conocida por su cultura andina y playas.',
            restaurants: '5 restaurantes especializados'
        },
        'Tarapacá': {
            name: 'Tarapacá',
            description: 'Tierra de la pampa salitrera y cultura pampina.',
            restaurants: '8 restaurantes tradicionales'
        },
        'Antofagasta': {
            name: 'Antofagasta',
            description: 'Principal puerto del norte, corazón de la minería chilena.',
            restaurants: '12 restaurantes costeros'
        },
        'Atacama': {
            name: 'Atacama',
            description: 'Desierto más árido del mundo, con oasis y geisers.',
            restaurants: '7 restaurantes del desierto'
        }
    };

    Object.keys(regionCoordinates).forEach(region => {
        const coords = regionCoordinates[region];
        const info = regionInfo[region];
        
        // Crear marcador personalizado
        const marker = L.marker([coords.lat, coords.lng], {
            icon: L.divIcon({
                className: 'region-marker',
                html: `<div style="background: #f59e0b; border: 3px solid #d97706; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;"></div>`,
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            })
        }).addTo(NorteApp.map);

        // Añadir popup informativo
        marker.bindPopup(`
            <div style="min-width: 200px;">
                <h3 style="color: #d97706; margin-bottom: 8px; font-weight: bold;">${info.name}</h3>
                <p style="margin-bottom: 8px; color: #666;">${info.description}</p>
                <div style="display: flex; align-items: center; color: #f59e0b;">
                    <i class="fas fa-utensils" style="margin-right: 8px;"></i>
                    <span style="font-weight: bold;">${info.restaurants}</span>
                </div>
                <button onclick="selectRegionOnMap('${region}')" 
                        style="margin-top: 12px; background: #f59e0b; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; width: 100%;">
                    Ver Restaurantes
                </button>
            </div>
        `);

        // Evento click en el marcador
        marker.on('click', function() {
            selectRegionOnMap(region);
        });

        NorteApp.markers.push(marker);
    });
}

/**
 * Configura event listeners para la página
 */
function setupNorteEventListeners() {
    // Búsqueda en tiempo real
    if (NorteDOM.searchInput) {
        NorteDOM.searchInput.addEventListener('input', (e) => {
            NorteApp.searchTerm = e.target.value.toLowerCase().trim();
            applyNorteFilters();
        });
    }
    
    // Filtro por región
    if (NorteDOM.filterRegion) {
        NorteDOM.filterRegion.addEventListener('change', (e) => {
            NorteApp.selectedRegion = e.target.value;
            applyNorteFilters();
            if (e.target.value !== 'all') {
                flyToRegion(e.target.value);
            }
        });
    }
}

/**
 * Vuela al mapa a la región seleccionada
 */
function flyToRegion(region) {
    if (NorteApp.map && regionCoordinates[region]) {
        const coords = regionCoordinates[region];
        NorteApp.map.flyTo([coords.lat, coords.lng], coords.zoom, {
            duration: 1.5
        });
    }
}

/**
 * Configura interacciones del mapa
 */
function setupMapInteractions() {
    // Hacer clic en leyenda del mapa
    const regionLegends = document.querySelectorAll('.region-legend');
    regionLegends.forEach(legend => {
        legend.addEventListener('click', function() {
            const region = this.getAttribute('data-region');
            selectRegionOnMap(region);
        });
    });
}

/**
 * Selecciona una región en el mapa
 */
function selectRegionOnMap(region) {
    // Actualizar filtro
    NorteApp.selectedRegion = region;
    if (NorteDOM.filterRegion) {
        NorteDOM.filterRegion.value = region;
    }
    
    // Volar al mapa a la región
    flyToRegion(region);
    
    // Aplicar filtros
    applyNorteFilters();
    
    // Mostrar información de la región
    showRegionInfo(region);
    
    // Efecto visual en la leyenda
    highlightRegionOnMap(region);
}

/**
 * Destaca la región seleccionada en el mapa
 */
function highlightRegionOnMap(region) {
    const allLegends = document.querySelectorAll('.region-legend');
    allLegends.forEach(legend => {
        legend.classList.remove('bg-amber-200', 'border-2', 'border-amber-400');
        legend.classList.add('bg-amber-100');
    });
    
    // Aplicar highlight a la región seleccionada
    const selectedLegend = document.querySelector(`.region-legend[data-region="${region}"]`);
    
    if (selectedLegend) {
        selectedLegend.classList.remove('bg-amber-100');
        selectedLegend.classList.add('bg-amber-200', 'border-2', 'border-amber-400');
    }
}

/**
 * Muestra información de la región seleccionada
 */
function showRegionInfo(region) {
    if (!NorteDOM.regionInfo || !NorteDOM.selectedRegionName || !NorteDOM.selectedRegionDesc || !NorteDOM.selectedRegionRestaurants) return;
    
    const regionInfo = getRegionInfo(region);
    
    NorteDOM.selectedRegionName.textContent = regionInfo.name;
    NorteDOM.selectedRegionDesc.textContent = regionInfo.description;
    NorteDOM.selectedRegionRestaurants.textContent = regionInfo.restaurantsText;
    
    NorteDOM.regionInfo.classList.remove('hidden');
}

/**
 * Obtiene información de una región específica
 */
function getRegionInfo(region) {
    const regionData = {
        'Arica y Parinacota': {
            name: 'Arica y Parinacota',
            description: 'Frontera con Perú y Bolivia, conocida por su gastronomía andina y mariscos del Pacífico.',
            restaurantsText: '5 restaurantes especializados en comida altiplánica'
        },
        'Tarapacá': {
            name: 'Tarapacá',
            description: 'Tierra de oasis y cultura pampina, con influencias bolivianas y peruanas.',
            restaurantsText: '8 restaurantes con platos mineros y andinos'
        },
        'Antofagasta': {
            name: 'Antofagasta',
            description: 'Puerto principal del norte, famoso por sus mariscos y cocina costera.',
            restaurantsText: '12 restaurantes de mariscos y comida marina'
        },
        'Atacama': {
            name: 'Atacama',
            description: 'Desierto florido y valles, con cocina de productos locales únicos.',
            restaurantsText: '7 restaurantes con sabores del desierto'
        }
    };
    
    return regionData[region] || {
        name: region,
        description: 'Región del norte de Chile',
        restaurantsText: 'Restaurantes de la zona'
    };
}

// =============================================
// GESTIÓN DE RESTAURANTES
// =============================================

/**
 * Carga los restaurantes del norte
 */
async function loadRestaurants() {
    try {
        showNorteLoadingState();
        
        // En una implementación real, esto vendría de una API
        // Por ahora usamos datos de ejemplo
        NorteApp.restaurants = await getSampleRestaurants();
        
        applyNorteFilters();
        
    } catch (error) {
        console.error('Error al cargar restaurantes:', error);
        handleNorteLoadError();
    }
}

/**
 * Obtiene restaurantes de ejemplo para el norte
 */
async function getSampleRestaurants() {
    // Simular delay de API
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    return [
        {
            id: 1,
            name: 'La Picantería del Norte',
            description: 'Especialistas en comida altiplánica y platos tradicionales ariqueños.',
            region: 'Arica y Parinacota',
            address: 'Av. Principal 123, Arica',
            rating: 4.5,
            reviews: 128,
            image: 'https://placehold.co/600x400/f59e0b/ffffff?text=Picantería+Norte',
            specialties: ['Asado Altiplánico', 'Chairo Paceño', 'Sajta de Pollo'],
            hours: '12:00 - 22:00',
            priceRange: '$$',
            phone: '+56 9 1234 5678',
            coordinates: { lat: -18.478, lng: -70.312 }
        },
        {
            id: 2,
            name: 'El Sabroso Andino',
            description: 'Auténtica comida andina con ingredientes de la zona altiplánica.',
            region: 'Arica y Parinacota',
            address: 'Calle Andina 456, Putre',
            rating: 4.7,
            reviews: 89,
            image: 'https://placehold.co/600x400/d97706/ffffff?text=Sabroso+Andino',
            specialties: ['Llama a la Parrilla', 'Quinua Real', 'Trucha Andina'],
            hours: '11:00 - 20:00',
            priceRange: '$$$',
            phone: '+56 9 2345 6789',
            coordinates: { lat: -18.198, lng: -69.559 }
        },
        {
            id: 3,
            name: 'Mariscos del Pacífico',
            description: 'Los mejores mariscos frescos del océano Pacífico norte.',
            region: 'Antofagasta',
            address: 'Costanera 789, Antofagasta',
            rating: 4.3,
            reviews: 215,
            image: 'https://placehold.co/600x400/b45309/ffffff?text=Mariscos+Pacífico',
            specialties: ['Ceviche Norteño', 'Caldillo de Congrio', 'Machas a la Parmesana'],
            hours: '13:00 - 23:00',
            priceRange: '$$',
            phone: '+56 9 3456 7890',
            coordinates: { lat: -23.650, lng: -70.400 }
        },
        {
            id: 4,
            name: 'El Minero Contento',
            description: 'Comida típica de la pampa salitrera y platos reconfortantes.',
            region: 'Tarapacá',
            address: 'Av. Salitrera 321, Iquique',
            rating: 4.6,
            reviews: 167,
            image: 'https://placehold.co/600x400/92400e/ffffff?text=Minero+Contento',
            specialties: ['Charquicán Campero', 'Cazuela Minera', 'Pastel de Choclo'],
            hours: '12:30 - 21:30',
            priceRange: '$$',
            phone: '+56 9 4567 8901',
            coordinates: { lat: -20.215, lng: -70.150 }
        },
        {
            id: 5,
            name: 'Sabores del Desierto',
            description: 'Cocina innovadora con productos únicos del desierto de Atacama.',
            region: 'Atacama',
            address: 'Oasis 654, San Pedro de Atacama',
            rating: 4.8,
            reviews: 194,
            image: 'https://placehold.co/600x400/78350f/ffffff?text=Sabores+Desierto',
            specialties: ['Conejo del Valle', 'Tumbo Atacameño', 'Queso de Cabra Local'],
            hours: '10:00 - 19:00',
            priceRange: '$$$',
            phone: '+56 9 5678 9012',
            coordinates: { lat: -22.908, lng: -68.199 }
        },
        {
            id: 6,
            name: 'La Casona del Puerto',
            description: 'Tradición pesquera en el corazón del puerto de Tocopilla.',
            region: 'Antofagasta',
            address: 'Puerto Viejo 987, Tocopilla',
            rating: 4.4,
            reviews: 132,
            image: 'https://placehold.co/600x400/854d0e/ffffff?text=Casona+Puerto',
            specialties: ['Pescado Frito', 'Mariscal Caliente', 'Empanadas de Mariscos'],
            hours: '11:30 - 22:00',
            priceRange: '$$',
            phone: '+56 9 6789 0123',
            coordinates: { lat: -22.092, lng: -70.198 }
        }
    ];
}

/**
 * Aplica filtros a los restaurantes
 */
function applyNorteFilters() {
    NorteApp.filteredRestaurants = NorteApp.restaurants.filter(restaurant => {
        const regionMatch = NorteApp.selectedRegion === 'all' || 
                           restaurant.region === NorteApp.selectedRegion;
        
        const searchMatch = NorteApp.searchTerm === '' || 
                           restaurant.name.toLowerCase().includes(NorteApp.searchTerm) ||
                           restaurant.description.toLowerCase().includes(NorteApp.searchTerm) ||
                           restaurant.specialties.some(spec => spec.toLowerCase().includes(NorteApp.searchTerm));
        
        return regionMatch && searchMatch;
    });
    
    renderRestaurants();
    updateRestaurantsCount();
}

/**
 * Renderiza la lista de restaurantes
 */
function renderRestaurants() {
    if (!NorteDOM.restaurantsList) return;
    
    NorteDOM.restaurantsList.innerHTML = '';
    
    if (NorteApp.filteredRestaurants.length === 0) {
        showNoRestaurantsMessage();
        return;
    }
    
    hideNoRestaurantsMessage();
    
    const restaurantsHtml = NorteApp.filteredRestaurants.map(restaurant => 
        createRestaurantCard(restaurant)
    ).join('');
    
    NorteDOM.restaurantsList.innerHTML = restaurantsHtml;
}

/**
 * Crea el HTML para una tarjeta de restaurante
 */
function createRestaurantCard(restaurant) {
    return `
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="relative h-48 bg-gradient-to-br from-amber-500 to-orange-500 overflow-hidden">
                <img 
                    src="${restaurant.image}" 
                    alt="${restaurant.name}" 
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                >
                <div class="absolute top-3 right-3">
                    <span class="bg-amber-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                        ${restaurant.region}
                    </span>
                </div>
                <div class="absolute bottom-3 left-3">
                    <span class="bg-black bg-opacity-50 text-white text-sm font-semibold px-2 py-1 rounded">
                        ${restaurant.priceRange}
                    </span>
                </div>
            </div>
            
            <div class="p-5">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-xl font-bold text-gray-800 group-hover:text-amber-600 transition-colors duration-300">
                        ${restaurant.name}
                    </h3>
                    <div class="flex items-center bg-amber-100 text-amber-800 px-2 py-1 rounded-lg">
                        <i class="fas fa-star text-amber-500 mr-1"></i>
                        <span class="font-bold">${restaurant.rating}</span>
                    </div>
                </div>
                
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                    ${restaurant.description}
                </p>
                
                <div class="flex items-center text-sm text-gray-500 mb-3">
                    <i class="fas fa-map-marker-alt text-amber-500 mr-2"></i>
                    <span class="truncate">${restaurant.address}</span>
                </div>
                
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <i class="fas fa-clock text-amber-500 mr-2"></i>
                    <span>${restaurant.hours}</span>
                </div>
                
                <div class="mb-4">
                    <div class="flex flex-wrap gap-1">
                        ${restaurant.specialties.map(specialty => 
                            `<span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">${specialty}</span>`
                        ).join('')}
                    </div>
                </div>
                
                <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-comment-alt text-amber-500 mr-1"></i>
                        <span>${restaurant.reviews} reseñas</span>
                    </div>
                    <button onclick="viewRestaurantDetails(${restaurant.id})" 
                            class="bg-amber-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-amber-600 transition-colors duration-300 text-sm">
                        Ver Detalles
                    </button>
                </div>
            </div>
        </div>
    `;
}

/**
 * Actualiza el contador de restaurantes
 */
function updateRestaurantsCount() {
    if (!NorteDOM.restaurantsCount) return;
    
    const total = NorteApp.filteredRestaurants.length;
    const regionText = NorteApp.selectedRegion === 'all' ? 
        'todas las regiones' : 
        NorteApp.selectedRegion;
    
    NorteDOM.restaurantsCount.textContent = 
        `${total} restaurante${total !== 1 ? 's' : ''} en ${regionText}`;
}

// =============================================
// ESTADOS DE LA INTERFAZ
// =============================================

/**
 * Muestra estado de carga
 */
function showNorteLoadingState() {
    if (NorteDOM.loadingRestaurants) {
        NorteDOM.loadingRestaurants.classList.remove('hidden');
    }
    if (NorteDOM.restaurantsList) {
        NorteDOM.restaurantsList.innerHTML = '';
    }
    hideNoRestaurantsMessage();
}

/**
 * Oculta estado de carga
 */
function hideNorteLoadingState() {
    if (NorteDOM.loadingRestaurants) {
        NorteDOM.loadingRestaurants.classList.add('hidden');
    }
}

/**
 * Muestra mensaje sin restaurantes
 */
function showNoRestaurantsMessage() {
    hideNorteLoadingState();
    if (NorteDOM.noRestaurants) {
        NorteDOM.noRestaurants.classList.remove('hidden');
    }
}

/**
 * Oculta mensaje sin restaurantes
 */
function hideNoRestaurantsMessage() {
    if (NorteDOM.noRestaurants) {
        NorteDOM.noRestaurants.classList.add('hidden');
    }
}

/**
 * Maneja errores de inicialización
 */
function handleNorteInitializationError() {
    if (NorteDOM.restaurantsList) {
        NorteDOM.restaurantsList.innerHTML = `
            <div class="col-span-full text-center py-16 bg-white rounded-xl shadow-lg border border-gray-100">
                <i class="fas fa-exclamation-triangle text-7xl text-red-500 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-600 mb-2">Error de carga</h3>
                <p class="text-lg text-gray-500 max-w-lg mx-auto">No se pudieron cargar los datos.</p>
                <button onclick="location.reload()" class="mt-4 bg-amber-500 text-white px-6 py-2 rounded-lg hover:bg-amber-600 transition duration-300">
                    Recargar Página
                </button>
            </div>
        `;
    }
}

/**
 * Maneja errores de carga
 */
function handleNorteLoadError() {
    showNoRestaurantsMessage();
}

// =============================================
// FUNCIONES GLOBALES
// =============================================

/**
 * Ver detalles del restaurante (para implementar)
 */
function viewRestaurantDetails(restaurantId) {
    window.location.href = `/chilean_food_app/restaurant/detail/${restaurantId}`;
}



// =============================================
// INICIALIZACIÓN
// =============================================

// Inicializar la página cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeNortePage);