<?php 
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/user-bar.php';
include __DIR__ . '/../partials/navbar.php'; 

// 🔥 SOLUCIÓN: Ruta corregida para ReviewModel
require_once __DIR__ . '/../../models/ReviewModel.php';
$reviewModel = new ReviewModel();

// Obtener el rating promedio y contar reseñas
$ratingData = $reviewModel->getAverageRating($restaurant['id']);
$restaurant['avg_rating'] = $ratingData['average'];
$restaurant['review_count'] = $ratingData['total'];
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* Hero */
    .restaurant-hero {
        position: relative;
        height: 380px;
        border-radius: 14px;
        overflow: hidden;
    }
    .restaurant-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .restaurant-info-overlay {
        position: absolute;
        bottom: 25px;
        left: 0;
        width: 100%;
        color: white;
        text-shadow: 0 3px 10px rgba(0,0,0,0.7);
    }
    .restaurant-info-overlay h1 {
        font-size: 2.5rem;
        font-weight: 800;
    }

    .star {
        color: #ccc;
        font-size: 1.5rem;
    }
    .star.filled {
        color: #ffcc33;
    }

    /* Map */
    #restaurant-map {
        height: 350px;
        width: 100%;
        border-radius: 12px;
    }

    /* Dish Cards */
    .dishes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
    }
    .dish-card {
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        border: 1px solid #eee;
        transition: 0.3s;
    }
    .dish-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }
    .dish-image-container {
        position: relative;
        height: 160px;
    }
    .dish-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .dish-overlay {
        opacity: 0;
        transition: 0.3s;
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.45);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .dish-card:hover .dish-overlay {
        opacity: 1;
    }
    .btn-review {
        background: #ffb300;
        padding: 8px 16px;
        border-radius: 6px;
        color: white;
        border: none;
        font-weight: bold;
    }

    /* Estilos para las reseñas */
    .review-card {
        border-left: 4px solid #ffcc33;
        background: #f8f9fa;
    }
    .star-rating {
        display: flex;
        gap: 5px;
        margin-bottom: 10px;
    }
    .star-label {
        font-size: 24px;
        color: #ccc;
        cursor: pointer;
        transition: color 0.2s;
    }
    .star-rating input:checked ~ .star-label,
    .star-rating input:hover ~ .star-label,
    .star-label:hover {
        color: #ffcc33 !important;
    }
    .star-rating input {
        display: none;
    }
    
    /* Animación para nueva reseña */
    .new-review {
        animation: highlight 2s ease-in-out;
    }
    @keyframes highlight {
        0% { background-color: #ffffcc; }
        100% { background-color: #f8f9fa; }
    }
</style>

<main class="container py-4">

    <!-- =============================
         HERO DEL RESTAURANTE CON RATING CORREGIDO
    ============================= -->
    <section class="restaurant-detail-header mb-4">
        <div class="restaurant-hero">
            <img src="<?= htmlspecialchars($restaurant['image'] ?? '/chilean_food_app/assets/images/placeholder-restaurant.png') ?>"
                 alt="<?= htmlspecialchars($restaurant['name']) ?>">

            <div class="restaurant-info-overlay text-center">
                <h1><?= htmlspecialchars($restaurant['name']) ?></h1>

                <div class="restaurant-meta mt-2">
                    <span class="badge bg-warning text-dark"><?= ucfirst($restaurant['region']) ?></span>  
                    <span class="ms-2">📍 <?= htmlspecialchars($restaurant['location']) ?></span>

                    <div class="rating mt-2">
                        <?php 
                        // 🔥 RATING CALCULADO CORRECTAMENTE
                        $avg_rating = $restaurant['avg_rating'] ?? 0;
                        $review_count = $restaurant['review_count'] ?? 0;
                        ?>
                        
                        <!-- Estrellas simples y efectivas -->
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= round($avg_rating) ? 'filled' : '' ?>">★</span>
                        <?php endfor; ?>
                        
                        <span><?= number_format($avg_rating, 1) ?></span>
                        <span class="text-light">(<?= $review_count ?> reseñas)</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================
         NAV SECCIONES
    ============================= -->
    <nav class="nav nav-pills justify-content-center my-4 restaurant-navigation">
        <a class="nav-link active" href="#informacion" data-bs-toggle="tab">📋 Información</a>
        <a class="nav-link" href="#menu" data-bs-toggle="tab">🍽️ Menú</a>
        <a class="nav-link" href="#ubicacion" data-bs-toggle="tab">📍 Ubicación</a>
        <a class="nav-link" href="#reseñas" data-bs-toggle="tab">⭐ Reseñas</a>
    </nav>

    <div class="tab-content mt-4">

        <!-- =============================
             INFORMACIÓN GENERAL
        ============================= -->
        <div class="tab-pane fade show active" id="informacion">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="p-4 shadow-sm rounded bg-white">
                        <h3>📋 Información General</h3>
                        <p><strong>Dirección:</strong> <?= htmlspecialchars($restaurant['address']) ?></p>
                        <p><strong>Horario:</strong> 
                            <?php if (!empty($restaurant['opening_hours'])): ?>
                                <?= htmlspecialchars($restaurant['opening_hours']) ?>
                            <?php else: ?>
                                <span class="text-muted">Horario no especificado</span>
                            <?php endif; ?>
                        </p>
                        <p><strong>Email:</strong> 
                            <?php if (!empty($restaurant['email'])): ?>
                                <?= htmlspecialchars($restaurant['email']) ?>
                            <?php else: ?>
                                <span class="text-muted">No disponible</span>
                            <?php endif; ?>
                        </p>
                        <p><strong>Teléfono:</strong> 
                            <?php if (!empty($restaurant['phone'])): ?>
                                <?= htmlspecialchars($restaurant['phone']) ?>
                            <?php else: ?>
                                <span class="text-muted">No disponible</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="p-4 shadow-sm rounded bg-white">
                        <h3>🎯 Especialidades</h3>
                        <?php if (!empty($specialties)): ?>
                            <?php foreach ($specialties as $s): ?>
                                <span class="badge bg-secondary me-1 mb-1"><?= htmlspecialchars($s) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No hay especialidades registradas.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- =============================
             MENÚ
        ============================= -->
        <div class="tab-pane fade" id="menu">
            <div class="text-center mb-4">
                <h2>🍽️ Menú del restaurante</h2>
            </div>

            <?php if (!empty($dishes)): ?>
                <div class="dishes-grid">
                    <?php foreach ($dishes as $dish): ?>
                        <div class="dish-card">
                            <div class="dish-image-container">
                                <img src="<?= htmlspecialchars($dish['image'] ?: '/chilean_food_app/assets/images/placeholder-dish.png') ?>">
                                <div class="dish-overlay">
                                    <button class="btn-review"
                                            onclick="openReviewModal(<?= $dish['id'] ?>, '<?= htmlspecialchars($dish['name']) ?>')">
                                        ✍️ Calificar
                                    </button>
                                </div>
                            </div>
                            <div class="p-3 text-center">
                                <h4><?= htmlspecialchars($dish['name']) ?></h4>
                                <p class="text-muted"><?= htmlspecialchars($dish['description']) ?></p>
                                <strong>$<?= number_format($dish['price'], 0, ',', '.') ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-secondary text-center">
                    Este restaurante aún no tiene platos registrados.
                </div>
            <?php endif; ?>
        </div>

        <!-- =============================
             UBICACIÓN
        ============================= -->
        <div class="tab-pane fade" id="ubicacion">
            <h3 class="mb-3">📍 Ubicación</h3>
            <div id="restaurant-map"></div>
            <div class="p-3 mt-3 bg-white shadow-sm rounded">
                <strong>Dirección:</strong> <?= htmlspecialchars($restaurant['address']) ?><br>
                <strong>Horario:</strong> 
                    <?php if (!empty($restaurant['opening_hours'])): ?>
                        <?= htmlspecialchars($restaurant['opening_hours']) ?>
                    <?php else: ?>
                        <span class="text-muted">Horario no especificado</span>
                    <?php endif; ?>
                <br>
                <strong>Tel:</strong> 
                    <?php if (!empty($restaurant['phone'])): ?>
                        <?= htmlspecialchars($restaurant['phone']) ?>
                    <?php else: ?>
                        <span class="text-muted">No disponible</span>
                    <?php endif; ?>
            </div>
        </div>

        <!-- =============================
             RESEÑAS
        ============================= -->
        <div class="tab-pane fade" id="reseñas">
            <h3 class="mb-4">⭐ Reseñas del Restaurante</h3>

            <!-- LISTA DE RESEÑAS EXISTENTES -->
            <div class="reviews-list mb-5">
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card p-4 mb-3 rounded <?= isset($_SESSION['new_review_added']) ? 'new-review' : '' ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong class="h5"><?= htmlspecialchars($review['username']) ?></strong>
                                    <div class="text-warning mb-2">
                                        <?= str_repeat("★", $review['rating']) ?><?= str_repeat("☆", 5 - $review['rating']) ?>
                                        <span class="text-muted ms-2">(<?= $review['rating'] ?>/5)</span>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <?= date("d/m/Y H:i", strtotime($review['created_at'])) ?>
                                </small>
                            </div>
                            <p class="mb-0"><?= htmlspecialchars($review['comment']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        Aún no hay reseñas para este restaurante. ¡Sé el primero en opinar!
                    </div>
                <?php endif; ?>
            </div>

            <hr class="my-4">

            <!-- FORMULARIO PARA AGREGAR RESEÑA -->
            <h4 class="mb-3">✍️ Agregar una Reseña</h4>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Debes <a href="/chilean_food_app/auth/login" class="alert-link">iniciar sesión</a> para dejar una reseña.
                </div>
            <?php else: ?>
                <form id="reviewForm" method="POST" class="p-4 bg-white rounded shadow-sm">
                    <input type="hidden" name="restaurant_id" value="<?= $restaurant['id'] ?>">

                    <!-- Calificación con estrellas interactivas -->
                    <div class="mb-4">
                        <label class="form-label"><strong>Calificación:</strong></label>
                        <div class="star-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" id="star_restaurant_<?= $i ?>" name="rating" value="<?= $i ?>" required>
                                <label for="star_restaurant_<?= $i ?>" class="star-label">★</label>
                            <?php endfor; ?>
                        </div>
                        <small class="text-muted">Selecciona de 1 a 5 estrellas</small>
                    </div>

                    <!-- Comentario -->
                    <div class="mb-4">
                        <label class="form-label"><strong>Comentario:</strong></label>
                        <textarea name="comment" class="form-control" rows="4" 
                                  placeholder="Comparte tu experiencia en este restaurante..." 
                                  required id="commentTextarea"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-paper-plane me-2"></i>Enviar Reseña ⭐
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// JavaScript para el formulario de reseñas
document.addEventListener('DOMContentLoaded', function() {
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Deshabilitar botón
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';
            
            fetch('/chilean_food_app/review/addRestaurant', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    clearForm();
                    
                    setTimeout(() => {
                        const url = new URL(window.location.href);
                        url.searchParams.set('new_review', 'true');
                        window.location.href = url.toString();
                    }, 1500);
                } else {
                    showAlert('error', data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Reseña ⭐';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error de conexión');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Reseña ⭐';
            });
        });

        // Función para limpiar el formulario
        function clearForm() {
            const textarea = document.getElementById('commentTextarea');
            if (textarea) textarea.value = '';
            
            const starInputs = document.querySelectorAll('.star-rating input');
            starInputs.forEach(input => {
                input.checked = false;
            });
            
            const starLabels = document.querySelectorAll('.star-rating .star-label');
            starLabels.forEach(label => {
                label.style.color = '#ccc';
            });
        }

        // Interactividad para las estrellas
        const starInputs = document.querySelectorAll('.star-rating input');
        const starLabels = document.querySelectorAll('.star-rating .star-label');
        
        starInputs.forEach(input => {
            input.addEventListener('change', function() {
                const rating = this.value;
                starLabels.forEach(label => label.style.color = '#ccc');
                for (let i = 0; i < rating; i++) {
                    starLabels[i].style.color = '#ffcc33';
                }
            });
        });

        // Efecto hover para las estrellas
        starLabels.forEach((label, index) => {
            label.addEventListener('mouseenter', function() {
                for (let i = 0; i <= index; i++) {
                    starLabels[i].style.color = '#ffcc33';
                }
            });
            
            label.addEventListener('mouseleave', function() {
                const checkedInput = document.querySelector('.star-rating input:checked');
                if (checkedInput) {
                    const rating = checkedInput.value;
                    for (let i = 0; i < 5; i++) {
                        starLabels[i].style.color = i < rating ? '#ffcc33' : '#ccc';
                    }
                } else {
                    starLabels.forEach(label => {
                        label.style.color = '#ccc';
                    });
                }
            });
        });

        // Verificar si hay nueva reseña y hacer scroll
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('new_review') === 'true') {
            setTimeout(() => {
                const reseñasSection = document.getElementById('reseñas');
                if (reseñasSection) {
                    reseñasSection.scrollIntoView({ behavior: 'smooth' });
                }
                const newUrl = window.location.pathname + window.location.hash;
                window.history.replaceState({}, document.title, newUrl);
            }, 500);
        }
    }
});

// Función para mostrar alertas
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${type === 'success' ? '✅' : '❌'} ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const form = document.getElementById('reviewForm');
    if (form) {
        form.parentNode.insertBefore(alertDiv, form);
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}

function openReviewModal(id, name) {
    document.getElementById("dishId").value = id;
    document.getElementById("dishName").textContent = name;
    new bootstrap.Modal(document.getElementById("reviewModal")).show();
}

// Leaflet Map
document.addEventListener("DOMContentLoaded", () => {
    const lat = <?= $restaurant['latitude'] ?? '-33.4489' ?>;
    const lng = <?= $restaurant['longitude'] ?? '-70.6693' ?>;

    const map = L.map("restaurant-map").setView([lat, lng], 15);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap",
    }).addTo(map);

    L.marker([lat, lng])
        .addTo(map)
        .bindPopup("<?= htmlspecialchars($restaurant['name']) ?>")
        .openPopup();
});
</script>

<?php 
if (isset($_SESSION['new_review_added'])) {
    unset($_SESSION['new_review_added']);
}
include __DIR__ . '/../partials/footer.php'; 
?>