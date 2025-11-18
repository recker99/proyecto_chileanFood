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
    <title>Gestión de Usuarios - Dashboard</title>
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
                <a href="/chilean_food_app/dashboard/users" class="nav-item active">
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
                <h1>Gestión de Usuarios</h1>
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
                    <h2>Todos los Usuarios</h2>
                    <span class="badge badge-admin">Total: <?= count($users) ?> usuarios</span>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Fecha Registro</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($users)): ?>
                                <?php foreach($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="badge <?= $user['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <span class="<?= $user['status'] === 'active' ? 'status-active' : 'status-inactive' ?>">
                                            <?= ucfirst($user['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <a href="/chilean_food_app/dashboard/users/edit/<?= $user['id'] ?>" class="btn btn-edit">
                                                Editar
                                            </a>
                                            <?php if($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="/chilean_food_app/dashboard/users/delete/<?= $user['id'] ?>" 
                                                   class="btn btn-delete"
                                                   onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                                    Eliminar
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center;">No hay usuarios registrados</td>
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