<?php include __DIR__ . '/../../partials/header.php'; ?>
<?php include __DIR__ . '/../../partials/user-bar.php'; ?>
<?php include __DIR__ . '/../../partials/navbar.php'; ?>

<main class="container">
    <section class="region-header">
        <h1>🍴 Zona Sur de Chile</h1>
        <p class="region-description">Explora la rica herencia mapuche y la influencia alemana en la cocina sureña</p>
    </section>

    <!-- Buscador (mismo código que norte.php) -->
    <section class="search-section">
        <div class="search-container">
            <form action="/chilean_food_app/search" method="GET" class="search-form">
                <div class="search-input-group">
                    <input type="text" name="q" placeholder="Buscar platos, restaurantes..." 
                           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="search-input">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Restaurantes de la zona sur -->
    <section class="restaurants-section">
        <h2>Restaurantes de la Zona Sur</h2>
        
        <?php if (!empty($restaurants)): ?>
            <div class="restaurants-grid">
                <?php foreach ($restaurants as $restaurant): ?>
                    <div class="restaurant-card">
                        <img src="/chilean_food_app/assets/images/restaurants/<?= $restaurant['id'] ?? '1' ?>.jpg" 
                             alt="<?= htmlspecialchars($restaurant['restaurant_name'] ?? 'Restaurante') ?>"
                             onerror="this.src='/chilean_food_app/assets/images/placeholder-restaurant.jpg'">
                        
                        <div class="restaurant-info">
                            <h3><?= htmlspecialchars($restaurant['restaurant_name'] ?? 'Restaurante') ?></h3>
                            <span class="location">📍 <?= htmlspecialchars($restaurant['location'] ?? 'Ubicación no disponible') ?></span>
                            
                            <div class="rating">
                                <div class="stars">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?= $i <= ($restaurant['avg_rating'] ?? 0) ? 'filled' : '' ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                                <span class="rating-value"><?= number_format($restaurant['avg_rating'] ?? 0, 1) ?></span>
                                <span class="rating-count">(<?= $restaurant['review_count'] ?? 0 ?> reseñas)</span>
                            </div>
                            
                            <p class="restaurant-description"><?= htmlspecialchars($restaurant['restaurant_description'] ?? 'Restaurante tradicional de la zona sur') ?></p>
                            
                            <div class="restaurant-actions">
                                <a href="/chilean_food_app/restaurant/detail/<?= $restaurant['id'] ?? '1' ?>" class="btn btn-primary">Ver Detalles</a>
                                <a href="/chilean_food_app/restaurant/menu/<?= $restaurant['id'] ?? '1' ?>" class="btn btn-secondary">Ver Menú</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <h3>Próximamente más restaurantes en esta zona</h3>
                <p>Estamos trabajando para agregar más opciones gastronómicas en la zona sur.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include __DIR__ . '/../../partials/footer.php'; ?>