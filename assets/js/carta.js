// =============================================
// VARIABLES GLOBALES Y CONFIGURACIÓN
// =============================================

const AppState = {
    activeCategoryFilter: 'Todos',
    activeRegionFilter: 'Todo Chile',
    currentRestaurantId: null,
    menuItemsData: [],
    isInitialized: false
};

// Cache de elementos DOM para mejor performance
const DOMCache = {
    menuList: null,
    searchInput: null,
    noResultsMessage: null,
    restaurantModal: null,
    reviewForm: null,
    starRatingInput: null,
    currentYear: null
};

// =============================================
// INICIALIZACIÓN DE LA APLICACIÓN
// =============================================

/**
 * Inicializa la aplicación cuando el DOM está listo
 */
function initializeApp() {
    try {
        // Configurar elementos del DOM en cache
        initializeDOMCache();
        
        // Configurar año actual
        setCurrentYear();
        
        // Configurar event listeners
        setupEventListeners();
        
        // Cargar datos del menú
        loadMenuItems();
        
        AppState.isInitialized = true;
        console.log('Aplicación inicializada correctamente');
    } catch (error) {
        console.error('Error al inicializar la aplicación:', error);
        handleInitializationError();
    }
}

/**
 * Inicializa la cache de elementos DOM
 */
function initializeDOMCache() {
    DOMCache.menuList = document.getElementById('menu-list');
    DOMCache.searchInput = document.getElementById('search-input');
    DOMCache.noResultsMessage = document.getElementById('no-results');
    DOMCache.restaurantModal = document.getElementById('restaurant-modal');
    DOMCache.reviewForm = document.getElementById('review-form');
    DOMCache.starRatingInput = document.getElementById('star-rating-input');
    DOMCache.currentYear = document.getElementById('current-year');
}

/**
 * Configura el año actual en el footer
 */
function setCurrentYear() {
    if (DOMCache.currentYear) {
        DOMCache.currentYear.textContent = new Date().getFullYear();
    }
}

/**
 * Configura todos los event listeners
 */
function setupEventListeners() {
    // Búsqueda en tiempo real
    if (DOMCache.searchInput) {
        DOMCache.searchInput.addEventListener('input', debounce(applyFiltersAndSearch, 300));
    }
    
    // Filtros de categoría y región
    setupFilterListeners('.category-filter-button', 'category');
    setupFilterListeners('.region-filter-button', 'region');
    
    // Formulario de reseñas
    setupReviewFormListeners();
    
    // Cerrar modal al hacer clic fuera
    setupModalCloseListeners();
}

/**
 * Maneja errores durante la inicialización
 */
function handleInitializationError() {
    // Mostrar mensaje de error al usuario
    if (DOMCache.menuList) {
        DOMCache.menuList.innerHTML = `
            <div class="col-span-full text-center py-16 bg-white rounded-xl shadow-lg border border-gray-100">
                <i class="fas fa-exclamation-triangle text-7xl text-red-500 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-600 mb-2">Error de carga</h3>
                <p class="text-lg text-gray-500 max-w-lg mx-auto">No se pudieron cargar los datos. Por favor, recarga la página.</p>
                <button onclick="location.reload()" class="mt-4 bg-primary text-white px-6 py-2 rounded-lg hover:bg-red-700 transition duration-300">
                    Recargar Página
                </button>
            </div>
        `;
    }
}

// =============================================
// GESTIÓN DE DATOS DEL MENÚ
// =============================================

/**
 * Carga los elementos del menú desde la API
 */
async function loadMenuItems() {
    try {
        showLoadingState();
        
        const response = await fetch('api/get_menu_items.php');
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        AppState.menuItemsData = data;
        
        applyFiltersAndSearch();
        
    } catch (error) {
        console.error('Error al cargar los elementos del menú:', error);
        await handleMenuLoadError();
    }
}

/**
 * Maneja errores de carga del menú
 */
async function handleMenuLoadError() {
    // Usar datos por defecto como fallback
    AppState.menuItemsData = getDefaultMenuItems();
    
    // Mostrar notificación de error
    showNotification('Usando datos de demostración', 'warning');
    
    applyFiltersAndSearch();
}

/**
 * Muestra estado de carga
 */
function showLoadingState() {
    if (DOMCache.menuList) {
        DOMCache.menuList.innerHTML = `
            <div class="col-span-full text-center py-16">
                <div class="inline-block animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-primary"></div>
                <p class="mt-4 text-gray-600">Cargando menú...</p>
            </div>
        `;
    }
}

/**
 * Datos de demostración para casos de error
 */
function getDefaultMenuItems() {
    return [
        {
            id: 1,
            name: 'Pisco Sour (Nacional)',
            description: 'El cóctel nacional chileno: pisco, jugo de limón de pica, jarabe de goma y espuma de clara de huevo. Clásico y refrescante.',
            price: 4500,
            filters: ['Bebidas y Licores'],
            restaurant_id: 'RST-345',
            restaurant_region: 'Centro',
            image: 'https://placehold.co/600x400/DC3545/FFFFFF?text=Pisco+Sour',
            restaurant_name: 'Bar La Moneda',
            restaurant_address: 'Agustinas 1001, Santiago',
            map_url: 'https://maps.google.com/?q=Santiago+Chile',
            hours: '18:00 - 02:00',
            icon: 'fas fa-cocktail'
        },
        {
            id: 2,
            name: 'Marraqueta Completa',
            description: 'Dos panes crujientes (marraqueta) servidos con palta fresca, jamón, queso y una taza de café o té. Ideal para comenzar.',
            price: 3500,
            filters: ['Desayuno', 'Entradas'],
            restaurant_id: 'RST-678',
            restaurant_region: 'Centro',
            image: 'https://placehold.co/600x400/0033A0/FFFFFF?text=Marraqueta+Completa',
            restaurant_name: 'Café El Buen Día',
            restaurant_address: 'Providencia 2500, Santiago',
            map_url: 'https://maps.google.com/?q=Santiago+Chile',
            hours: '08:00 - 18:00',
            icon: 'fas fa-mug-hot'
        },
        {
            id: 3,
            name: 'Empanadas de Pino',
            description: 'Tradicionales empanadas chilenas, rellenas de carne, cebolla, aceitunas y huevo duro. Perfectas para empezar.',
            price: 2500,
            filters: ['Almuerzo', 'Cena', 'Entradas'],
            restaurant_id: 'RST-123',
            restaurant_region: 'Norte',
            image: 'https://placehold.co/600x400/DC3545/FFFFFF?text=Empanadas+de+Pino',
            restaurant_name: 'La Picantería del Norte',
            restaurant_address: 'Av. Principal 123, Arica',
            map_url: 'https://maps.google.com/?q=Arica+Chile',
            hours: '12:00 - 22:00',
            icon: 'fas fa-pepper-hot'
        },
        {
            id: 4,
            name: 'Pastel de Choclo',
            description: 'Cremosa masa de choclo dulce sobre un pino de carne, pollo y especias. Un clásico reconfortante, gratinado al horno.',
            price: 7500,
            filters: ['Almuerzo', 'Cena', 'Platos Principales'],
            restaurant_id: 'RST-456',
            restaurant_region: 'Centro',
            image: 'https://placehold.co/600x400/0033A0/FFFFFF?text=Pastel+de+Choclo',
            restaurant_name: 'El Fogón Central',
            restaurant_address: 'Calle Central 456, Santiago',
            map_url: 'https://maps.google.com/?q=Santiago+Chile',
            hours: '13:00 - 23:00',
            icon: 'fas fa-concierge-bell'
        },
        {
            id: 5,
            name: 'Mote con Huesillo',
            description: 'Bebida refrescante a base de mote de trigo, duraznos deshidratados y jugo acaramelado. El postre de verano por excelencia.',
            price: 2200,
            filters: ['Bebidas y Licores', 'Postres', 'Almuerzo'],
            restaurant_id: 'RST-789',
            restaurant_region: 'Centro',
            image: 'https://placehold.co/600x400/DC3545/FFFFFF?text=Mote+con+Huesillo',
            restaurant_name: 'El Rincón Mapuche',
            restaurant_address: 'Ruta Interlagos 789, Pucón',
            map_url: 'https://maps.google.com/?q=Pucón+Chile',
            hours: '11:00 - 20:00',
            icon: 'fas fa-ice-cream'
        },
        {
            id: 6,
            name: 'Cazuela de Vacuno',
            description: 'Caldo sustancioso con carne de vacuno, zapallo, choclo, papa y arroz. El alma de la cocina tradicional chilena.',
            price: 6800,
            filters: ['Almuerzo', 'Cena', 'Platos Principales'],
            restaurant_id: 'RST-012',
            restaurant_region: 'Sur',
            image: 'https://placehold.co/600x400/0033A0/FFFFFF?text=Cazuela+de+Vacuno',
            restaurant_name: 'La Nonna del Sur',
            restaurant_address: 'Plaza de Armas 500, Puerto Montt',
            map_url: 'https://maps.google.com/?q=Puerto+Montt+Chile',
            hours: '12:00 - 21:00',
            icon: 'fas fa-soup'
        },
        {
            id: 7,
            name: 'Churrasco Italiano',
            description: 'Clásico sándwich chileno con carne de vacuno, palta, tomate y mayonesa. Servido en pan frica o amasado.',
            price: 5900,
            filters: ['Almuerzo', 'Cena', 'Platos Principales'],
            restaurant_id: 'RST-901',
            restaurant_region: 'Centro',
            image: 'https://placehold.co/600x400/DC3545/FFFFFF?text=Churrasco+Italiano',
            restaurant_name: 'La Fuente Chilena',
            restaurant_address: 'Av. Libertador 300, Valparaíso',
            map_url: 'https://maps.google.com/?q=Valparaíso+Chile',
            hours: '10:00 - 23:00',
            icon: 'fas fa-burger'
        },
        {
            id: 8,
            name: 'Curanto al Hoyo',
            description: 'Elaboración tradicional del sur, cocinado en un hoyo con piedras calientes. Contiene mariscos, carnes y papas.',
            price: 12000,
            filters: ['Almuerzo', 'Cena', 'Platos Principales'],
            restaurant_id: 'RST-234',
            restaurant_region: 'Sur',
            image: 'https://placehold.co/600x400/0033A0/FFFFFF?text=Curanto+al+Hoyo',
            restaurant_name: 'Sabores de Chiloé',
            restaurant_address: 'Costanera 100, Puerto Varas',
            map_url: 'https://maps.google.com/?q=Puerto+Varas+Chile',
            hours: '13:00 - 20:00',
            icon: 'fas fa-fish'
        }
    ];
}

// =============================================
// FILTRADO Y BÚSQUEDA
// =============================================

/**
 * Aplica filtros y búsqueda a los elementos del menú
 */
function applyFiltersAndSearch() {
    const searchText = DOMCache.searchInput ? DOMCache.searchInput.value.toLowerCase().trim() : '';
    
    const filteredItems = AppState.menuItemsData.filter(item => {
        const categoryMatch = AppState.activeCategoryFilter === 'Todos' || 
                             item.filters.includes(AppState.activeCategoryFilter);
        
        const regionMatch = AppState.activeRegionFilter === 'Todo Chile' || 
                           item.restaurant_region === AppState.activeRegionFilter;
        
        const searchMatch = searchText === '' || 
                           item.name.toLowerCase().includes(searchText) || 
                           item.description.toLowerCase().includes(searchText) ||
                           item.restaurant_name.toLowerCase().includes(searchText);
        
        return categoryMatch && regionMatch && searchMatch;
    });
    
    renderMenuItems(filteredItems);
}

/**
 * Configura listeners para los botones de filtro
 */
function setupFilterListeners(selector, type) {
    const buttons = document.querySelectorAll(selector);
    
    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const filterValue = button.getAttribute('data-filter');
            
            // Actualizar estado
            if (type === 'category') {
                AppState.activeCategoryFilter = filterValue;
            } else {
                AppState.activeRegionFilter = filterValue;
            }
            
            // Actualizar UI
            updateActiveFilterState(buttons, button, type);
            
            // Aplicar filtros
            applyFiltersAndSearch();
        });
    });
}

/**
 * Actualiza el estado visual de los filtros activos
 */
function updateActiveFilterState(allButtons, activeButton, type) {
    const activeClass = type === 'category' ? 'active-category-filter' : 'active-region-filter';
    
    allButtons.forEach(btn => btn.classList.remove(activeClass));
    activeButton.classList.add(activeClass);
}

// =============================================
// RENDERIZADO DE ELEMENTOS - BOTÓN SIEMPRE VISIBLE CON TAILWIND
// =============================================

/**
 * Renderiza los elementos del menú en el DOM
 */
function renderMenuItems(items) {
    if (!DOMCache.menuList) return;
    
    DOMCache.menuList.innerHTML = '';
    
    if (items.length === 0) {
        showNoResultsMessage();
        return;
    }
    
    hideNoResultsMessage();
    
    const itemsHtml = items.map(item => createMenuItemHTML(item)).join('');
    DOMCache.menuList.innerHTML = itemsHtml;
    
    // Configurar event listeners para efectos hover después de renderizar
    setTimeout(() => {
        setupCardHoverEffects();
    }, 100);
}

/**
 * Crea el HTML para un elemento del menú - CON BOTÓN SIEMPRE VISIBLE Y CLARO
 */
function createMenuItemHTML(item) {
    const regionColor = getRegionColor(item.restaurant_region);
    const safeItemJSON = JSON.stringify(item).replace(/'/g, "\\'");
    
    return `
        <div class="menu-card group bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl border border-gray-200 flex flex-col h-full hover:border-primary hover:-translate-y-2">
            <!-- Imagen del plato con efecto hover -->
            <div class="relative h-48 bg-gradient-to-br from-primary to-secondary overflow-hidden">
                <img 
                    src="${item.image}" 
                    alt="${item.name}" 
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    loading="lazy"
                    onerror="this.onerror=null; this.src='https://placehold.co/600x400/fecaca/991b1b?text=Plato+Chileno';"
                >
                <!-- Overlay en hover -->
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300"></div>
                
                <!-- Badge de región -->
                <div class="absolute top-3 right-3">
                    <span class="text-xs uppercase ${regionColor} text-white px-3 py-1 rounded-full font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">
                        ${item.restaurant_region}
                    </span>
                </div>
                <!-- Icono del tipo de plato -->
                <div class="absolute bottom-3 left-3 text-2xl text-white drop-shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="${item.icon}"></i>
                </div>
            </div>
            
            <!-- Contenido de la card -->
            <div class="p-5 flex flex-col flex-grow">
                <!-- Nombre y precio -->
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-lg font-bold text-gray-800 leading-tight group-hover:text-primary transition-colors duration-300">${item.name}</h3>
                    <span class="text-2xl font-black text-green-600 ml-3 flex-shrink-0 group-hover:scale-110 transition-transform duration-300">${formatPrice(item.price)}</span>
                </div>
                
                <!-- Descripción -->
                <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-grow group-hover:text-gray-700 transition-colors duration-300">${item.description}</p>

                <!-- Información del restaurante -->
                <div class="space-y-2 pt-3 border-t border-gray-100 group-hover:border-gray-200 transition-colors duration-300">
                    <div class="flex items-center text-sm text-gray-700">
                        <i class="fas fa-store text-primary mr-2 flex-shrink-0 text-xs group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="font-medium truncate group-hover:text-primary transition-colors duration-300">${item.restaurant_name}</span>
                    </div>
                    <div class="flex items-start text-xs text-gray-500">
                        <i class="fas fa-map-marker-alt text-primary mr-2 flex-shrink-0 mt-0.5 group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="truncate group-hover:text-gray-600 transition-colors duration-300">${item.restaurant_address}</span>
                    </div>
                </div>
                
                <!-- Botón de acción - SIEMPRE VISIBLE Y CLARO -->
                <div class="mt-4 pt-3 border-t border-gray-100 group-hover:border-gray-200 transition-colors duration-300">
                    <button onclick="window.openModal(${safeItemJSON})" 
                            class="w-full bg-gradient-to-r from-blue-700 to-teal-600 text-white font-bold py-3 px-4 rounded-xl hover:from-blue-800 hover:to-teal-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl text-sm flex items-center justify-center border-0 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <i class="fas fa-star-half-alt mr-2"></i> Ver Detalles y Reseñas
                    </button>
                </div>
            </div>
        </div>
    `;
}

/**
 * Configura efectos hover para las cards
 */
function setupCardHoverEffects() {
    const cards = document.querySelectorAll('.menu-card');
    
    cards.forEach(card => {
        // Efecto de sombra y elevación al hacer hover
        card.addEventListener('mouseenter', function() {
            this.style.zIndex = '10';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
    });
}

/**
 * Obtiene el color CSS para una región
 */
function getRegionColor(region) {
    const colorMap = {
        'Norte': 'bg-amber-500',
        'Sur': 'bg-purple-500',
        'Centro': 'bg-primary'
    };
    
    return colorMap[region] || 'bg-primary';
}

/**
 * Muestra mensaje de sin resultados
 */
function showNoResultsMessage() {
    if (DOMCache.noResultsMessage) {
        DOMCache.noResultsMessage.classList.remove('hidden');
    }
}

/**
 * Oculta mensaje de sin resultados
 */
function hideNoResultsMessage() {
    if (DOMCache.noResultsMessage) {
        DOMCache.noResultsMessage.classList.add('hidden');
    }
}

// =============================================
// GESTIÓN DEL MODAL Y RESEÑAS
// =============================================

/**
 * Abre el modal de detalles del restaurante
 */
window.openModal = function(item) {
    if (!DOMCache.restaurantModal) return;
    
    // Actualizar contenido del modal
    updateModalContent(item);
    
    // Mostrar modal
    DOMCache.restaurantModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    
    // Configurar reseñas
    setupReviewListener(item.restaurant_id);
    
    // Resetear formulario
    resetReviewForm();
}

/**
 * Actualiza el contenido del modal
 */
function updateModalContent(item) {
    document.getElementById('modal-restaurant-name').textContent = item.restaurant_name;
    document.getElementById('modal-restaurant-address').textContent = `Dirección: ${item.restaurant_address}`;
    document.getElementById('modal-restaurant-hours').textContent = `Horarios: ${item.hours}`;
    
    const mapLink = document.getElementById('modal-map-link');
    if (mapLink) {
        mapLink.href = item.map_url;
    }
}

/**
 * Cierra el modal
 */
window.closeModal = function() {
    if (DOMCache.restaurantModal) {
        DOMCache.restaurantModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        AppState.currentRestaurantId = null;
    }
}

/**
 * Configura listeners para cerrar el modal
 */
function setupModalCloseListeners() {
    // Cerrar con ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !DOMCache.restaurantModal.classList.contains('hidden')) {
            closeModal();
        }
    });
}

// =============================================
// GESTIÓN DE RESEÑAS
// =============================================

/**
 * Configura el listener de reseñas para un restaurante
 */
async function setupReviewListener(restaurantId) {
    AppState.currentRestaurantId = restaurantId;
    
    const reviewsList = document.getElementById('reviews-list');
    const loadingReviews = document.getElementById('loading-reviews');
    
    if (!reviewsList || !loadingReviews) return;
    
    loadingReviews.classList.remove('hidden');
    reviewsList.innerHTML = '';
    
    try {
        const response = await fetch(`api/get_reviews.php?restaurant_id=${restaurantId}`);
        const reviews = await response.json();
        
        loadingReviews.classList.add('hidden');
        renderReviews(reviews);
        
    } catch (error) {
        console.error("Error al obtener reseñas:", error);
        showReviewsError();
    }
}

/**
 * Renderiza las reseñas en el modal
 */
function renderReviews(reviews) {
    const reviewsList = document.getElementById('reviews-list');
    if (!reviewsList) return;
    
    if (reviews.length === 0) {
        reviewsList.innerHTML = `
            <p class="text-gray-500 text-center py-4">
                Sé el primero en dejar una reseña para este restaurante.
            </p>
        `;
        return;
    }
    
    const reviewsHtml = reviews.map(review => createReviewHTML(review)).join('');
    reviewsList.innerHTML = reviewsHtml;
}

/**
 * Crea el HTML para una reseña
 */
function createReviewHTML(review) {
    const date = new Date(review.created_at).toLocaleDateString('es-CL');
    const maskedUserId = review.user_id ? 
        review.user_id.substring(0, 4) + '...' + review.user_id.substring(review.user_id.length - 4) : 
        'Usuario';
    
    return `
        <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-semibold text-gray-700">${maskedUserId}</span>
                <span class="text-xs text-gray-400">${date}</span>
            </div>
            <div class="text-xl mb-2">
                ${renderStars(review.rating)}
            </div>
            <p class="text-gray-600 italic">"${review.comment}"</p>
        </div>
    `;
}

/**
 * Muestra error al cargar reseñas
 */
function showReviewsError() {
    const reviewsList = document.getElementById('reviews-list');
    if (reviewsList) {
        reviewsList.innerHTML = `
            <p class="text-red-500 text-center py-4">
                Error al cargar las reseñas. Intenta nuevamente.
            </p>
        `;
    }
}

/**
 * Configura listeners del formulario de reseñas
 */
function setupReviewFormListeners() {
    if (!DOMCache.starRatingInput || !DOMCache.reviewForm) return;
    
    // Rating con estrellas
    DOMCache.starRatingInput.addEventListener('click', handleStarRatingClick);
    
    // Envío del formulario
    DOMCache.reviewForm.addEventListener('submit', handleReviewSubmit);
}

/**
 * Maneja el clic en las estrellas de rating
 */
function handleStarRatingClick(e) {
    if (e.target.classList.contains('fa-star')) {
        const rating = parseInt(e.target.getAttribute('data-value'));
        updateStarRating(rating);
    }
}

/**
 * Maneja el envío del formulario de reseña
 */
async function handleReviewSubmit(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('submit-review-btn');
    const statusMsg = document.getElementById('review-status-message');
    const rating = parseInt(document.getElementById('review-rating').value);
    const comment = document.getElementById('review-comment').value.trim();
    
    // Validación
    if (!validateReview(rating, comment, statusMsg)) return;
    
    // Enviar reseña
    await submitReview(rating, comment, submitBtn, statusMsg);
}

/**
 * Valida los datos de la reseña
 */
function validateReview(rating, comment, statusMsg) {
    if (rating === 0) {
        statusMsg.textContent = 'Por favor, selecciona una puntuación.';
        return false;
    }
    
    if (comment === '') {
        statusMsg.textContent = 'Por favor, escribe un comentario.';
        return false;
    }
    
    if (!AppState.currentRestaurantId) {
        statusMsg.textContent = 'Error: No se identificó el restaurante.';
        return false;
    }
    
    statusMsg.textContent = '';
    return true;
}

/**
 * Envía la reseña al servidor
 */
async function submitReview(rating, comment, submitBtn, statusMsg) {
    submitBtn.disabled = true;
    statusMsg.textContent = 'Enviando...';
    
    try {
        const formData = new FormData();
        formData.append('restaurant_id', AppState.currentRestaurantId);
        formData.append('rating', rating);
        formData.append('comment', comment);
        
        const response = await fetch('api/submit_review.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            handleReviewSuccess(statusMsg);
        } else {
            throw new Error(data.message || 'Error desconocido');
        }
        
    } catch (error) {
        handleReviewError(error, statusMsg);
    } finally {
        submitBtn.disabled = false;
    }
}

/**
 * Maneja el éxito del envío de reseña
 */
function handleReviewSuccess(statusMsg) {
    statusMsg.textContent = '¡Reseña enviada con éxito!';
    statusMsg.className = 'text-sm mt-2 text-center text-green-600';
    
    resetReviewForm();
    
    // Recargar reseñas
    if (AppState.currentRestaurantId) {
        setupReviewListener(AppState.currentRestaurantId);
    }
    
    // Limpiar mensaje después de 5 segundos
    setTimeout(() => {
        statusMsg.textContent = '';
    }, 5000);
}

/**
 * Maneja errores en el envío de reseña
 */
function handleReviewError(error, statusMsg) {
    console.error("Error al guardar la reseña:", error);
    statusMsg.textContent = `Error al guardar: ${error.message}`;
    statusMsg.className = 'text-sm mt-2 text-center text-red-500';
}

/**
 * Resetea el formulario de reseñas
 */
function resetReviewForm() {
    if (DOMCache.reviewForm) {
        DOMCache.reviewForm.reset();
    }
    updateStarRating(0);
    
    const statusMsg = document.getElementById('review-status-message');
    if (statusMsg) {
        statusMsg.textContent = '';
        statusMsg.className = 'text-sm mt-2 text-center text-red-500';
    }
}

// =============================================
// FUNCIONES DE UTILIDAD
// =============================================

/**
 * Formatea el precio en formato chileno
 */
function formatPrice(price) {
    return `$${price.toLocaleString('es-CL')}`;
}

/**
 * Renderiza estrellas de rating
 */
function renderStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        const starClass = i <= rating ? 
            'fas text-yellow-500' : 
            'far text-gray-300';
        stars += `<i class="${starClass} fa-star"></i>`;
    }
    return stars;
}

/**
 * Actualiza la visualización del rating con estrellas
 */
function updateStarRating(rating) {
    const ratingInput = document.getElementById('review-rating');
    const stars = document.querySelectorAll('#star-rating-input .fa-star');
    
    if (ratingInput) {
        ratingInput.value = rating;
    }
    
    stars.forEach(star => {
        const starValue = parseInt(star.getAttribute('data-value'));
        if (starValue <= rating) {
            star.classList.remove('far', 'text-gray-300');
            star.classList.add('fas', 'text-yellow-500');
        } else {
            star.classList.remove('fas', 'text-yellow-500');
            star.classList.add('far', 'text-gray-300');
        }
    });
}

/**
 * Debounce para optimizar búsquedas
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Muestra notificaciones al usuario
 */
function showNotification(message, type = 'info') {
    // Implementación básica de notificación
    console.log(`[${type.toUpperCase()}] ${message}`);
    // Aquí podrías integrar una librería de notificaciones como Toastify
}

// =============================================
// INICIALIZACIÓN
// =============================================

// Inicializar la aplicación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeApp);

// Exponer funciones globales necesarias
window.closeModal = closeModal;