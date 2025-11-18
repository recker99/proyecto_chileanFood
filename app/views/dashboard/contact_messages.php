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
    <title>Mensajes de Contacto - Dashboard</title>
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
                <a href="/chilean_food_app/dashboard/reviews" class="nav-item">
                    <span>⭐ Reseñas</span>
                </a>
                <a href="/chilean_food_app/dashboard/contact-messages" class="nav-item active">
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
                <h1>Mensajes de Contacto</h1>
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
                    <h2>Todos los Mensajes</h2>
                    <span class="badge badge-region">Total: <?= count($messages) ?> mensajes</span>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Asunto</th>
                                <th>Mensaje</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($messages)): ?>
                                <?php foreach($messages as $message): ?>
                                <tr>
                                    <td><?= $message['id'] ?></td>
                                    <td><?= htmlspecialchars($message['name']) ?></td>
                                    <td><?= htmlspecialchars($message['email']) ?></td>
                                    <td><?= htmlspecialchars($message['subject']) ?></td>
                                    <td>
                                        <?= htmlspecialchars(substr($message['message'], 0, 80)) . (strlen($message['message']) > 80 ? '...' : '') ?>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($message['created_at'])) ?></td>
                                    <td>
                                        <span class="badge <?= $message['status'] === 'pending' ? 'badge-user' : ($message['status'] === 'read' ? 'badge-admin' : 'badge-region') ?>">
                                            <?= ucfirst($message['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <?php if($message['status'] === 'pending'): ?>
                                                <a href="/chilean_food_app/dashboard/contact-messages/mark-read/<?= $message['id'] ?>" class="btn btn-edit">
                                                    Marcar Leído
                                                </a>
                                            <?php endif; ?>
                                            <a href="/chilean_food_app/dashboard/contact-messages/delete/<?= $message['id'] ?>" 
                                               class="btn btn-delete"
                                               onclick="return confirm('¿Estás seguro de eliminar este mensaje?')">
                                                Eliminar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center;">No hay mensajes de contacto</td>
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