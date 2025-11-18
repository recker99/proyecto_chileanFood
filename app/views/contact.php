
    <?php include __DIR__ . '/partials/header.php'; ?>
    <?php include __DIR__ . '/partials/user-bar.php'; ?>
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <main class="container">
        <section class="contact-section">
            <h1>📞 Contáctanos</h1>
            
            <?php if(isset($success) && $success): ?>
                <div class="success-message">
                    ¡Gracias por tu mensaje! Te contactaremos pronto.
                </div>
            <?php endif; ?>

            <div class="contact-grid">
                <div class="contact-form">
                    <h2>Envíanos un Mensaje</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Asunto:</label>
                            <input type="text" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label>Mensaje:</label>
                            <textarea name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                    </form>
                </div>

                <div class="contact-info">
                    <h2>Información de Contacto</h2>
                    <div class="contact-item">
                        <strong>📧 Email:</strong> info@saboreschile.cl
                    </div>
                    <div class="contact-item">
                        <strong>📞 Teléfono:</strong> +56 2 2345 6789
                    </div>
                    <div class="contact-item">
                        <strong>📍 Dirección:</strong> Santiago, Chile
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>