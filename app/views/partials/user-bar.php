<div class="user-top-bar">
    <div class="user-top-container">
        <div class="user-info">
            <?php if (isset($_SESSION['user_id'])): ?>
                <i class="fas fa-user-circle"></i>
                <span class="user-greeting">Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <span class="user-badge <?= ($_SESSION['user_role'] === 'admin') ? 'admin-badge' : 'user-badge' ?>">
                    <i class="fas fa-shield-alt"></i>
                    <?= ucfirst($_SESSION['user_role']) ?>
                </span>
            <?php else: ?>
                <i class="fas fa-user"></i>
                <span class="user-greeting">Bienvenido visitante</span>
            <?php endif; ?>
        </div>
        
        <div class="user-actions">
            
            <!-- Selector de Idioma -->
            <!-- La clase 'active-lang' está aplicada por defecto a ES. -->
            <div class="language-selector flex items-center space-x-2 mr-4">
                <a href="?lang=es" class="language-link active-lang text-sm hover:text-red-400 transition duration-150" title="Cambiar a Español">
                    <i class="fas fa-globe"></i> 
                    <span class="font-bold">ES</span>
                </a>
                
                <a href="?lang=en" class="language-link text-sm hover:text-red-400 transition duration-150" title="Switch to English">
                    <i class="fas fa-globe"></i>
                    <span>EN</span>
                </a>
            </div>

            <!-- Acciones de Usuario (Ingresar/Salir) -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/chilean_food_app/auth/logout" class="user-action-link logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Salir</span>
                </a>
            <?php else: ?>
                <a href="/chilean_food_app/auth/login" class="user-action-link">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Ingresar</span>
                </a>
                <a href="/chilean_food_app/auth/register" class="user-action-link">
                    <i class="fas fa-user-plus"></i>
                    <span>Registrarse</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Estilos CSS para el idioma activo (marcado en rojo) -->
<style>
.language-link.active-lang {
    color: #ff0000; /* Rojo puro para destacar */
    font-weight: 800; /* Extra negrita */
    text-decoration: underline;
    /* Aplicar un pequeño efecto para que se vea más clicable/activo */
    transform: scale(1.1);
}
</style>