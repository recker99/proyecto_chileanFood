<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reseñas - Dashboard</title>
    <link rel="stylesheet" href="/chilean_food_app/assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Sabores<span>Chile</span></h2>
                <p>Panel de Administración</p>
            </div>
            <nav class="sidebar-nav">
                <a href="/chilean_food_app/dashboard" class="nav-item">
                    <span>📊 Dashboard</span>
                </a>
                <a href="/chilean_food_app/dashboard/users" class="nav-item">
                    <span>👥 Usuarios</span>
                </a>
                <a href="/chilean_food_app/dashboard/restaurants" class="nav-item">
                    <span>🏪 Restaurantes</span>
                </a>
                <a href="/chilean_food_app/dashboard/reviews" class="nav-item active">
                    <span>⭐ Reseñas</span>
                </a>
                <a href="/chilean_food_app/dashboard/contact-messages" class="nav-item">
                    <span>📧 Mensajes</span>
                </a>
                <a href="/chilean_food_app/" class="nav-item">
                    <span>🏠 Sitio Web</span>
                </a>
                <a href="/chilean_food_app/auth/logout" class="nav-item logout">
                    <span>🚪 Cerrar Sesión</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header class="content-header">
                <h1>Gestión de Reseñas</h1>
                <div class="user-info">
                    <span>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                </div>
            </header>

            <!-- Mostrar mensajes -->
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <div class="recent-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Todas las Reseñas</h2>
                    <span class="badge badge-region">Total: <?= count($reviews) ?> reseñas</span>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Restaurante</th>
                                <th>Calificación</th>
                                <th>Comentario</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($reviews)): ?>
                                <?php foreach($reviews as $review): ?>
                                <tr>
                                    <td><?= $review['id'] ?></td>
                                    <td><?= htmlspecialchars($review['user_name']) ?></td>
                                    <td><?= htmlspecialchars($review['restaurant_name']) ?></td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <span style="color: #ffc107; font-weight: bold;"><?= $review['rating'] ?></span>
                                            <span style="color: #ffc107;">★</span>
                                        </div>
                                    </td>
                                    <td>
                                        <?= !empty($review['comment']) ? htmlspecialchars(substr($review['comment'], 0, 50)) . '...' : 'Sin comentario' ?>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></td>
                                    <td>
                                        <a href="/chilean_food_app/dashboard/reviews/delete/<?= $review['id'] ?>" 
                                           class="btn btn-delete"
                                           onclick="return confirm('¿Estás seguro de eliminar esta reseña?')">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center;">No hay reseñas registradas</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>