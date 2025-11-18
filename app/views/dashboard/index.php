<?php
// Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sabores de Chile</title>
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
                <a href="/chilean_food_app/dashboard" class="nav-item active">
                    <span>📊 Dashboard</span>
                </a>
                <a href="/chilean_food_app/dashboard/users" class="nav-item">
                    <span>👥 Usuarios</span>
                </a>
                <a href="/chilean_food_app/dashboard/restaurants" class="nav-item">
                    <span>🏪 Restaurantes</span>
                </a>
                <a href="/chilean_food_app/dashboard/reviews" class="nav-item">
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
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                </div>
            </header>

            <!-- Estadísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3><?= $data['total_users'] ?></h3>
                        <p>Usuarios Registrados</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🏪</div>
                    <div class="stat-info">
                        <h3><?= $data['total_restaurants'] ?></h3>
                        <p>Restaurantes</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-info">
                        <h3><?= $data['total_reviews'] ?></h3>
                        <p>Reseñas</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📧</div>
                    <div class="stat-info">
                        <h3><?= $data['pending_contacts'] ?></h3>
                        <p>Mensajes de Contacto</p> <!-- Cambiado de "Mensajes Pendientes" -->
                    </div>
                </div>
            </div>

            <!-- Usuarios Recientes -->
            <div class="recent-section">
                <h2>Usuarios Recientes</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Fecha Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['recent_users'] as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><span class="badge <?= $user['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>"><?= ucfirst($user['role']) ?></span></td>
                                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Restaurantes Recientes -->
            <div class="recent-section">
                <h2>Restaurantes Recientes</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Región</th>
                                <th>Ubicación</th>
                                <th>Fecha Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['recent_restaurants'] as $restaurant): ?>
                            <tr>
                                <td><?= htmlspecialchars($restaurant['name']) ?></td>
                                <td><span class="badge badge-region"><?= ucfirst($restaurant['region']) ?></span></td>
                                <td><?= htmlspecialchars($restaurant['location']) ?></td>
                                <td><?= date('d/m/Y', strtotime($restaurant['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>