<!DOCTYPE html>
<html lang="es">
<!--inicio del programa donde te logueas inicia llamando librerias para el uso de css
para el acceso del sistema -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema - Solución</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6a11cb;
            --secondary-color: #2575fc;
            --gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        body {
            background: var(--gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.1' d='M0,128L48,117.3C96,107,192,85,288,112C384,139,480,213,576,218.7C672,224,768,160,864,138.7C960,117,1056,139,1152,149.3C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            background-position: bottom;
            opacity: 0.2;
        }
        
        .login-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            z-index: 1;
            animation: fadeIn 0.5s ease-out;
        }
        
        .login-header {
            background: var(--gradient);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .login-header h3 {
            margin: 0;
            font-weight: 600;
        }
        
        .login-body {
            padding: 25px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(106, 17, 203, 0.25);
        }
        
        .btn-login {
            background: var(--gradient);
            border: none;
            color: white;
            width: 100%;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .password-field {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 42px;
            cursor: pointer;
            color: var(--primary-color);
        }
        
        .demo-accounts {
            margin-top: 25px;
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            font-size: 0.9rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .demo-title {
            font-weight: bold;
            margin-bottom: 12px;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }
        
        .demo-title i {
            margin-right: 8px;
        }
        
        .demo-account {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
            display: flex;
        }
        
        .demo-account:last-child {
            border-bottom: none;
        }
        
        .account-role {
            font-weight: 600;
            min-width: 90px;
            color: var(--secondary-color);
        }
        
        .alert {
            border: none;
            border-left: 4px solid #dc3545;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        
        .form-control {
            border-left: none;
            padding-left: 5px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }
        
        .shake {
            animation: shake 0.4s ease-in-out;
        }
        
        .troubleshooting {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
        
        .troubleshooting h5 {
            color: #856404;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    
    <div class="login-container">
        <div class="login-header">
            <h3><i class="fas fa-users me-2"></i>Acceso al Sistema</h3>
            <p class="mb-0 mt-2">Ingrese sus credenciales para acceder</p>
        </div>
        <div class="login-body">
            <div class="alert alert-danger" id="error-message" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <span id="error-text">Debe iniciar sesión para acceder</span>
            </div>
            
            <form id="login-form" method="POST" action="login.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Ingrese su usuario" required>
                    </div>
                </div>
                <div class="mb-3 password-field">
                    <label for="password" class="form-label">Contraseña:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña" required>
                        <span class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="btn btn-login mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                </button>
            </form>
            
            <div class="demo-accounts">
                <div class="demo-title">
                    <i class="fas fa-info-circle"></i>Usuarios del sistema:
                </div>
                <div class="demo-account">
                    <span class="account-role">admin:</span> admin / 1234 (Administrador)
                </div>
                <div class="demo-account">
                    <span class="account-role">jorge:</span> jorge / jorgemin123 (Técnico)
                </div>
                <div class="demo-account">
                    <span class="account-role">alfredo:</span> alfredo / alfredo123 (Técnico)
                </div>
                <div class="demo-account">
                    <span class="account-role">evelyn:</span> evelyn / evelyn123 (Técnico)
                </div>
            </div>
            
            <div class="troubleshooting">
                <h5><i class="fas fa-tools me-2"></i>Si no puede acceder:</h5>
                <ul class="mb-0">
                    <li>Verifique que esté usando el usuario y contraseña correctos</li>
                    <li>Asegúrese de que su teclado no tenga activado el bloqueo de mayúsculas</li>
                    <li>Si el problema persiste, contacte al administrador del sistema</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Mostrar/ocultar contraseña
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Validar el formulario antes de enviar
        document.getElementById('login-form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');
            
            // Validar campos vacíos
            if (!username || !password) {
                e.preventDefault();
                errorText.textContent = 'Por favor, complete todos los campos.';
                errorDiv.style.display = 'block';
                
                // Efecto de sacudida
                this.classList.add('shake');
                setTimeout(() => {
                    this.classList.remove('shake');
                }, 400);
            }
        });
        
        // Mostrar mensaje de error si viene por URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                const errorDiv = document.getElementById('error-message');
                const errorText = document.getElementById('error-text');
                
                const errorType = urlParams.get('error');
                if (errorType === 'invalid_credentials') {
                    errorText.textContent = 'Usuario o contraseña incorrectos.';
                } else if (errorType === 'empty_fields') {
                    errorText.textContent = 'Por favor, complete todos los campos.';
                } else {
                    errorText.textContent = 'Debe iniciar sesión para acceder.';
                }
                
                errorDiv.style.display = 'block';
            }
        });
    </script>
</body>
</html>