<?php 
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/user-bar.php';
include __DIR__ . '/../partials/navbar.php'; 

// Modelo de reseñas
require_once __DIR__ . '/../../models/ReviewModel.php';
$reviewModel = new ReviewModel();

// Rating promedio y total reseñas
$ratingData = $reviewModel->getAverageRating($restaurant['id']);
$restaurant['avg_rating']   = $ratingData['average'];
$restaurant['review_count'] = $ratingData['total'];
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="/chilean_food_app/assets/css/restaurant-detail.css">

<main class="container py-4">

    <!-- HERO (SOLO IMAGEN) -->
    <section class="restaurant-detail-header mb-3">
        <div class="restaurant-hero">
            <img src="<?= htmlspecialchars($restaurant['image'] ?? '/chilean_food_app/assets/images/placeholder-restaurant.png') ?>"
                 alt="<?= htmlspecialchars($restaurant['name']) ?>">
        </div>
    </section>

   <!-- HEADER INFO BAJO LA FOTO -->
    <?php 
        $avg_rating   = $restaurant['avg_rating'] ?? 0;
        $review_count = $restaurant['review_count'] ?? 0;
    ?>
    <section class="restaurant-header-info mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <!-- Nombre + ubicación -->
            <div>
                <h1 class="mb-1"><?= htmlspecialchars($restaurant['name']) ?></h1>
                <div class="restaurant-header-meta mb-2">
                    <span class="badge bg-warning text-dark">
                        <?= ucfirst($restaurant['region']) ?>
                    </span>
                    <span class="ms-2">
                        📍 <?= htmlspecialchars($restaurant['address']) ?>
                    </span>
                </div>

                <!-- ⭐ Calificación (estrellas + promedio todo junto) -->
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= round($avg_rating) ? 'filled' : '' ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <div class="ms-1">
                        <span class="fw-bold"><?= number_format($avg_rating, 1) ?></span>
                        <span class="text-muted">/ 5 · <?= (int)$review_count ?> reseñas</span>
                    </div>
                </div>
            </div>

            <!-- (Opcional) podrías dejar aquí algún botón de acción futuro -->
            <!--
            <div>
                <a href="#reseñas" class="btn btn-outline-primary btn-sm">
                    Ver reseñas
                </a>
            </div>
            -->
        </div>
    </section>

    <!-- NAV DE SECCIONES -->
    <nav class="nav nav-pills justify-content-center my-4 restaurant-navigation">
        <a class="nav-link active" href="#informacion" data-bs-toggle="tab">📋 Información</a>
        <a class="nav-link" href="#menu"       data-bs-toggle="tab">🍽️ Menú</a>
        <a class="nav-link" href="#ubicacion"  data-bs-toggle="tab">📍 Ubicación</a>
        <a class="nav-link" href="#reseñas"    data-bs-toggle="tab">⭐ Reseñas</a>
    </nav>

    <div class="tab-content mt-4">

        <!--TAB: INFORMACIÓN GENERAL-->
        <div class="tab-pane fade show active" id="informacion">
            <div class="row g-4">
                <!-- Datos básicos -->
                <div class="col-md-6">
                    <div class="p-4 shadow-sm rounded bg-white h-100">
                        <h3 class="mb-3">📋 Información General</h3>
                        <p><strong>Dirección:</strong> <?= htmlspecialchars($restaurant['address']) ?></p>
                        <p>
                            <strong>Horario:</strong>
                            <?php if (!empty($restaurant['opening_hours'])): ?>
                                <?= htmlspecialchars($restaurant['opening_hours']) ?>
                            <?php else: ?>
                                <span class="text-muted">Horario no especificado</span>
                            <?php endif; ?>
                        </p>
                        <p>
                            <strong>Email:</strong>
                            <?php if (!empty($restaurant['email'])): ?>
                                <?= htmlspecialchars($restaurant['email']) ?>
                            <?php else: ?>
                                <span class="text-muted">No disponible</span>
                            <?php endif; ?>
                        </p>
                        <p>
                            <strong>Teléfono:</strong>
                            <?php if (!empty($restaurant['phone'])): ?>
                                <?= htmlspecialchars($restaurant['phone']) ?>
                            <?php else: ?>
                                <span class="text-muted">No disponible</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- Especialidades -->
                <div class="col-md-6">
                    <div class="p-4 shadow-sm rounded bg-white h-100">
                        <h3 class="mb-3">🎯 Especialidades</h3>
                        <?php if (!empty($specialties)): ?>
                            <?php foreach ($specialties as $s): ?>
                                <span class="badge bg-secondary me-1 mb-1">
                                    <?= htmlspecialchars($s) ?>
                                </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">No hay especialidades registradas.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: MENÚ -->
        <div class="tab-pane fade" id="menu">
            <div class="text-center mb-4">
                <h2>🍽️ Menú del restaurante</h2>
                <p class="text-muted mb-0">Descubre los platos más representativos de la gastronomía chilena.</p>
            </div>

            <?php if (!empty($dishes)): ?>
                <div class="dishes-grid">
                    <?php foreach ($dishes as $dish): ?>
                        <div class="dish-card">
                            <div class="dish-image-container">
                                <img src="<?= htmlspecialchars($dish['image'] ?: '/chilean_food_app/assets/images/placeholder-dish.png') ?>"
                                     alt="<?= htmlspecialchars($dish['name']) ?>">
                                <div class="dish-overlay">
                                    <button class="btn-review"
                                            onclick="openReviewModal(<?= (int)$dish['id'] ?>, '<?= htmlspecialchars($dish['name'], ENT_QUOTES) ?>')">
                                        ✍️ Calificar
                                    </button>
                                </div>
                            </div>
                            <div class="p-3 text-center">
                                <h4 class="mb-1"><?= htmlspecialchars($dish['name']) ?></h4>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($dish['description']) ?></p>
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

        <!-- TAB: UBICACIÓN (SOLO MAPA + RESUMEN) -->
        <div class="tab-pane fade" id="ubicacion">
            <h3 class="mb-3">📍 Ubicación</h3>
            <p class="text-muted">
                Este restaurante se encuentra en 
                <strong><?= htmlspecialchars($restaurant['address']) ?></strong>,
                región de <strong><?= htmlspecialchars($restaurant['region']) ?></strong>.
            </p>
            <div id="restaurant-map"></div>
        </div>

        <!-- TAB: RESEÑAS -->
        <div class="tab-pane fade" id="reseñas">
            <h3 class="mb-4">⭐ Reseñas del Restaurante</h3>

            <!-- Lista de reseñas -->
            <div class="reviews-list mb-5">
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card p-4 mb-3 rounded <?= isset($_SESSION['new_review_added']) ? 'new-review' : '' ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong class="h5 mb-1 d-block"><?= htmlspecialchars($review['username']) ?></strong>
                                    <div class="text-warning mb-2">
                                        <?= str_repeat("★", (int)$review['rating']) ?>
                                        <?= str_repeat("☆", 5 - (int)$review['rating']) ?>
                                        <span class="text-muted ms-2">(<?= (int)$review['rating'] ?>/5)</span>
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

            <!-- Formulario para nueva reseña -->
            <h4 class="mb-3">✍️ Agregar una Reseña</h4>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Debes <a href="/chilean_food_app/auth/login" class="alert-link">iniciar sesión</a> para dejar una reseña.
                </div>
            <?php else: ?>
                <form id="reviewForm" method="POST" class="p-4 bg-white rounded shadow-sm">
                    <input type="hidden" name="restaurant_id" value="<?= (int)$restaurant['id'] ?>">

                    <!-- Estrellas -->
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
                        <textarea name="comment"
                                  id="commentTextarea"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Comparte tu experiencia en este restaurante..."
                                  required></textarea>
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
<script src='./../js/restaurant-detail.js'></script>

<script>
    /* MAPA LEAFLET */
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
