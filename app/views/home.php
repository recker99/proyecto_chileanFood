    <?php include __DIR__ . '/partials/header.php'; ?>
    <?php include __DIR__ . '/partials/user-bar.php'; ?>
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <!-- Slider principal -->
    <header class="hero">
        <div class="hero-slider">
            <div class="slide active" style="background-image: url('/chilean_food_app/assets/images/heroes/empanadas.png');">
                <div class="slide-overlay"></div>
                <div class="slide-content">
                    <h2>Empanadas de Pino</h2>
                    <p>El clásico chileno por excelencia</p>
                </div>
            </div>
            <div class="slide" style="background-image: url('/chilean_food_app/assets/images/heroes/pastel_de_choclo.png');">
                <div class="slide-overlay"></div>
                <div class="slide-content">
                    <h2>Pastel de Choclo</h2>
                    <p>Dulzura y tradición en cada bocado</p>
                </div>
            </div>
            <div class="slide" style="background-image: url('/chilean_food_app/assets/images/heroes/asado_chileno.png');">
                <div class="slide-overlay"></div>
                <div class="slide-content">
                    <h2>Asado Chileno</h2>
                    <p>La parrilla que une familias</p>
                </div>
            </div>
            <div class="slide" style="background-image: url('/chilean_food_app/assets/images/heroes/cazuela.png');">
                <div class="slide-overlay"></div>
                <div class="slide-content">
                    <h2>Cazuela Chilena</h2>
                    <p>El calor de lo tradicional</p>
                </div>
            </div>
            <div class="slide" style="background-image: url('/chilean_food_app/assets/images/heroes/completos.png');">
                <div class="slide-overlay"></div>
                <div class="slide-content">
                    <h2>Completo</h2>
                    <p>Excelente comida al paso</p>
                </div>
            </div>
        </div>

        <!-- Controles del slider -->
        <div class="slider-controls">
            <button class="slider-prev">‹</button>
            <div class="slider-dots">
                <span class="dot active" data-slide="0"></span>
                <span class="dot" data-slide="1"></span>
                <span class="dot" data-slide="2"></span>
                <span class="dot" data-slide="3"></span>
                <span class="dot" data-slide="4"></span>
            </div>
            <button class="slider-next">›</button>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="container">
        <section class="welcome-section">
            <h1>Bienvenido al portal gastronómico chileno</h1>
        </section>

        <!-- Sección regiones -->
        <section class="regions">
            <h2>Regiones de Chile</h2>
            <div class="region-grid">
                <a href="/chilean_food_app/restaurants/region/norte" class="region-card">
                    <h3>Norte</h3>
                    <p>Sabores del desierto</p>
                </a>
                <a href="/chilean_food_app/restaurants/region/centro" class="region-card">
                    <h3>Zona Central</h3>
                    <p>Tradición y modernidad</p>
                </a>
                <a href="/chilean_food_app/restaurants/region/sur" class="region-card">
                    <h3>Sur</h3>
                    <p>Cocina mapuche y alemana</p>
                </a>
            </div>
        </section>

        <!-- Sección platos destacados -->
        <section class="featured-restaurants">
            <h2>Platos Destacados por Nuestra Comunidad</h2>
            <div class="restaurant-grid">
                <?php 
                    $featuredDishes = $this->restaurantModel->getFeaturedDishes();
                    if(!empty($featuredDishes)): 
                        foreach($featuredDishes as $dish): 
                ?>
                <div class="restaurant-card">
                    <?php 
                        // Construir ruta correcta de la imagen del plato
                        $imagePath = $dish['dish_image'];

                        // Si la imagen no tiene la ruta completa (por ejemplo solo el nombre del archivo)
                        if (strpos($imagePath, '/chilean_food_app/') === false) {
                            $imagePath = "/chilean_food_app/assets/images/dishes/{$dish['restaurant_id']}/" . basename($imagePath);
                        }

                        // Si no existe o está vacía, usar una imagen por defecto
                        if (empty($dish['dish_image']) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                            $imagePath = "/chilean_food_app/assets/images/default_dish.jpg";
                        }
                    ?>
                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($dish['dish_name']) ?>">

                    <div class="restaurant-info">
                        <h3><?= htmlspecialchars($dish['restaurant_name']) ?></h3>
                        <span class="region-tag"><?= ucfirst($dish['region']) ?></span>
                        
                        <div class="dish-info">
                            <div class="dish-name"><?= htmlspecialchars($dish['dish_name']) ?></div>
                            <p class="dish-description"><?= htmlspecialchars($dish['dish_description']) ?></p>
                            <div class="dish-price">$<?= number_format($dish['dish_price'], 0, ',', '.') ?></div>
                        </div>
                        
                        <div class="rating">
                            <div class="stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?= $i <= $dish['avg_rating'] ? 'filled' : '' ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-value"><?= number_format($dish['avg_rating'], 1) ?></span>
                            <span class="rating-count">(<?= $dish['review_count'] ?> reseñas)</span>
                        </div>
                        
                        <a href="/chilean_food_app/restaurant/detail/<?= $dish['restaurant_id'] ?>" class="btn">Ver Restaurante</a>
                        
                        <div class="restaurant-meta">
                            <span class="meta-item location"><?= htmlspecialchars($dish['location']) ?></span>
                            <span class="meta-item reviews"><?= $dish['review_count'] ?> reseñas</span>
                        </div>
                    </div>
                </div>
                <?php 
                    endforeach; 
                else: 
                ?>
                <div class="no-results">
                    <h3>Próximamente más platos destacados</h3>
                    <p>Estamos trabajando para agregar más opciones gastronómicas.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Scripts -->
    <script src="./assets/js/navbar.js"></script>
    <script src="./assets/js/slider.js"></script>

     <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>