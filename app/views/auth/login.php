<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sabores de Chile</title>
    <link rel="stylesheet" href="/chilean_food_app/assets/css/style.css">
    <link rel="stylesheet" href="/chilean_food_app/assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Iniciar Sesión</h2>
            
            <?php if(isset($error)): ?>
                <div class="error">
                    <?= $error ?>
                    <!-- DEBUG: Mostrar información adicional -->
                    <div style="margin-top: 10px; font-size: 0.8em; background: rgba(255,255,255,0.1); padding: 5px; border-radius: 3px;">
                        DEBUG: Revisa los logs para más información
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/chilean_food_app/auth/login">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : 'ivan@correo.com' ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required 
                           value="ivan123">
                </div>
                
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </form>
            
            <p>¿No tienes cuenta? <a href="/chilean_food_app/auth/register">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>