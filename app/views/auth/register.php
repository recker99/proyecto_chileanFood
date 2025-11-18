<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sabores de Chile</title>
    <link rel="stylesheet" href="./../assets/css/style.css">
    <link rel="stylesheet" href="./../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Crear Cuenta</h2>
            <p style="text-align: center; color: #6c757d; margin-bottom: 2rem;">Únete a nuestra comunidad gastronómica</p>
            
            <?php if(isset($error)): ?>
            <!--    <div class="error"><?= $error ?></div> -->
            <?php endif; ?>
            
            <form method="POST" id="registerForm">
                <div class="form-group">
                    <label for="name">Nombre completo:</label>
                    <input type="text" id="name" name="name" required 
                           placeholder="Ingresa tu nombre completo"
                           value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="tu@email.com"
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Mínimo 6 caracteres" minlength="6"
                           oninput="checkPasswordStrength(this.value)">
                    <div id="passwordStrength" class="password-strength"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           placeholder="Repite tu contraseña" minlength="6"
                           oninput="checkPasswordMatch()">
                    <div id="passwordMatch" class="password-strength"></div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">Crear Cuenta</button>
            </form>
            
            <p>¿Ya tienes cuenta? <a href="/chilean_food_app/auth/login">Inicia sesión aquí</a></p>
        </div>
    </div>

    <script>
        function checkPasswordStrength(password) {
            const strengthElement = document.getElementById('passwordStrength');
            let strength = '';
            let strengthClass = '';
            
            if (password.length === 0) {
                strengthElement.textContent = '';
                return;
            }
            
            if (password.length < 6) {
                strength = 'Débil - Mínimo 6 caracteres';
                strengthClass = 'strength-weak';
            } else if (password.length < 8) {
                strength = 'Media';
                strengthClass = 'strength-medium';
            } else {
                // Check for complexity
                const hasUpperCase = /[A-Z]/.test(password);
                const hasLowerCase = /[a-z]/.test(password);
                const hasNumbers = /\d/.test(password);
                const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
                
                const complexity = [hasUpperCase, hasLowerCase, hasNumbers, hasSpecial].filter(Boolean).length;
                
                if (complexity >= 3) {
                    strength = 'Fuerte';
                    strengthClass = 'strength-strong';
                } else if (complexity >= 2) {
                    strength = 'Media';
                    strengthClass = 'strength-medium';
                } else {
                    strength = 'Débil';
                    strengthClass = 'strength-weak';
                }
            }
            
            strengthElement.textContent = strength;
            strengthElement.className = 'password-strength ' + strengthClass;
            checkPasswordMatch();
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchElement = document.getElementById('passwordMatch');
            const submitBtn = document.getElementById('submitBtn');
            
            if (confirmPassword.length === 0) {
                matchElement.textContent = '';
                submitBtn.disabled = false;
                return;
            }
            
            if (password === confirmPassword) {
                matchElement.textContent = '✓ Las contraseñas coinciden';
                matchElement.className = 'password-strength strength-strong';
                submitBtn.disabled = false;
            } else {
                matchElement.textContent = '✗ Las contraseñas no coinciden';
                matchElement.className = 'password-strength strength-weak';
                submitBtn.disabled = true;
            }
        }
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>