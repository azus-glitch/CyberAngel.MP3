<?php
session_start();
require_once 'conexion.php';

$mensaje = '';
$accion = $_GET['action'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST['form_type'] ?? 'login';

    if ($form_type === 'login') {
        $usuario = trim($_POST['usuario'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!empty($usuario) && !empty($password)) {
            try {
                $sql = "SELECT * FROM usuarios WHERE usuario = :usuario";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['usuario' => $usuario]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && $user['password'] === $password) {
                    $token_sesion = uniqid('cyber_', true);

                    $updateStmt = $pdo->prepare("UPDATE usuarios SET session_token = ? WHERE id = ?");
                    $updateStmt->execute([$token_sesion, $user['id']]);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['usuario'];
                    $_SESSION['login_token'] = $token_sesion;

                    header("Location: views/home.php");
                    exit();
                } else {
                    $mensaje = "Usuario o contraseña incorrectos.";
                }
            } catch (PDOException $e) {
                $mensaje = "Error en la base de datos: " . $e->getMessage();
            }
        } else {
            $mensaje = "Por favor, completa todos los campos.";
        }
    } elseif ($form_type === 'register') {
        $usuario = trim($_POST['usuario'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        if (!empty($usuario) && !empty($password) && !empty($confirm_password)) {
            if ($password === $confirm_password) {
                try {
                    $checkStmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = :usuario");
                    $checkStmt->execute(['usuario' => $usuario]);
                    
                    if ($checkStmt->rowCount() > 0) {
                        $mensaje = "Ese nombre de usuario ya está en uso :(";
                    } else {
                        $sql = "INSERT INTO usuarios (usuario, password) VALUES (:usuario, :password)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            'usuario' => $usuario,
                            'password' => $password
                        ]);

                        $mensaje = "¡Registro exitoso! Ya podés iniciar sesión ✨";
                        $accion = 'login';
                    }
                } catch (PDOException $e) {
                    $mensaje = "Error en la base de datos: " . $e->getMessage();
                }
            } else {
                $mensaje = "Las contraseñas no coinciden :(";
            }
        } else {
            $mensaje = "Por favor, completa todos los campos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CyberAngel.MP3 - Acceso</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Slackey&display=swap" rel="stylesheet">
    <style>
        *, body, a, button, select, input {
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="%23ffb6c1" stroke="%23ff007f" stroke-width="1.5"><path d="M4.5 3.5L11.5 20.5L14.5 13.5L21.5 10.5L4.5 3.5Z"/></svg>'), auto !important;
        }
        body {
            background-color: #000000;
            margin: 0;
            padding: 0;
            font-family: 'Slackey', cursive, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .os-window {
            width: 580px;
            max-width: 95vw;
            background-color: #000000;
            border: 4px solid #ffb6c1;
            box-shadow: 0 0 25px rgba(255, 182, 193, 0.4);
            position: relative;
            margin-bottom: 50px;
            transition: width 0.3s ease;
        }
        .os-header {
            background-color: #ffb6c1;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ffb6c1;
        }
        .os-title-container {
            display: flex;
            align-items: center;
        }
        .os-title-text {
            font-family: 'Slackey', cursive, sans-serif;
            font-size: 20px;
            color: #d64573;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            user-select: none;
            text-shadow: 0 0 2px rgba(255, 255, 255, 0.3);
        }
        .os-controls {
            display: flex;
            gap: 6px;
        }
        .os-btn {
            width: 20px;
            height: 18px;
            background-color: #ffb6c1;
            border: 1.5px solid #000000;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        .btn-minimize {
            border-bottom: 2px solid #000000;
            height: 4px;
            margin-top: 8px;
        }
        .btn-maximize {
            width: 10px;
            height: 10px;
            border: 1.5px solid #000000;
        }
        .btn-close {
            font-size: 13px;
            font-weight: bold;
            color: #000000;
            line-height: 1;
        }
        .login-body {
            padding: 25px 20px 45px 20px;
            text-align: center;
        }
        .user-icon-container {
            width: 60px;
            height: 60px;
            border: 3px solid #ffb6c1;
            border-radius: 50%;
            margin: 0 auto 15px auto;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #000;
        }
        .user-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .login-title-container {
            margin-bottom: 20px;
        }
        .login-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .input-box {
            width: 70%;
            position: relative;
            display: flex;
            align-items: center;
        }
        .login-input {
            width: 100%;
            padding: 10px 45px 10px 15px;
            background-color: #000;
            border: 2px solid #ffb6c1;
            color: #ffb6c1;
            text-align: center;
            font-family: inherit;
            font-size: 13px;
            outline: none;
            box-sizing: border-box;
        }
        .login-input::placeholder {
            color: #ffb6c1;
            opacity: 0.8;
        }
        .eye-toggle-btn {
            position: absolute;
            right: 8px;
            background: #000;
            border: 2px solid #ffb6c1;
            padding: 0;
            cursor: pointer;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 5px rgba(255, 182, 193, 0.4);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .eye-toggle-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .login-submit-btn {
            background-color: #ffb6c1;
            color: #000;
            border: 2px solid #ff007f;
            padding: 8px 20px;
            font-family: inherit;
            font-size: 12px;
            cursor: pointer;
            border-radius: 8px;
            margin-top: 5px;
            transition: transform 0.1s ease, background-color 0.2s ease;
        }
        .login-submit-btn:hover {
            background-color: #ff007f;
            color: #fff;
            transform: scale(1.05);
        }
        .forgot-link {
            color: #ffb6c1;
            font-size: 11px;
            text-decoration: underline;
            cursor: pointer;
            margin-top: 5px;
            display: inline-block;
        }
        .forgot-link:hover {
            color: #ff007f;
        }
        .custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        .custom-modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .custom-modal {
            width: 380px;
            max-width: 90vw;
            background-color: #000000;
            border: 3px solid #ffb6c1;
            box-shadow: 0 0 20px rgba(255, 182, 193, 0.6);
        }
        .custom-modal-header {
            background-color: #ffb6c1;
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .custom-modal-title {
            font-size: 14px;
            color: #d64573;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .custom-modal-body {
            padding: 25px 20px;
            text-align: center;
            color: #ffb6c1;
            font-size: 13px;
        }
        .custom-modal-btn {
            background-color: #ffb6c1;
            color: #000;
            border: 2px solid #ff007f;
            padding: 6px 20px;
            font-family: inherit;
            font-size: 12px;
            cursor: pointer;
            border-radius: 6px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="os-window">
        <div class="os-header">
            <div class="os-title-container">
                <span class="os-title-text"><?php echo ($accion === 'register') ? 'Register' : 'Login'; ?></span>
            </div>
            <div class="os-controls">
                <div class="os-btn" onclick="minimizeWindow()"><div class="btn-minimize"></div></div>
                <div class="os-btn" onclick="maximizeWindow()"><div class="btn-maximize"></div></div>
                <div class="os-btn" onclick="closeWindow()"><span class="btn-close">×</span></div>
            </div>
        </div>
        <div class="login-body">
            <div class="user-icon-container">
                <img src="img/logo.jfif" alt="User Profile" class="user-avatar-img">
            </div>
            <div class="login-title-container">
                <h2 style="color: #ffb6c1; font-size: 18px; margin: 0; text-transform: uppercase;">
                    <?php echo ($accion === 'register') ? 'Register' : 'Login'; ?>
                </h2>
            </div>

            <?php if ($accion === 'login'): ?>
                <form class="login-form" action="login.php" method="POST">
                    <input type="hidden" name="form_type" value="login">
                    <div class="input-box">
                        <input type="text" name="usuario" placeholder="Username" class="login-input" required>
                    </div>
                    <div class="input-box">
                        <input type="password" name="password" id="passwordField" placeholder="Password" class="login-input" required>
                        <button type="button" class="eye-toggle-btn" onclick="togglePassword('passwordField', 'eyeIcon1')">
                            <img src="img/ojo.jfif" alt="Mostrar" id="eyeIcon1">
                        </button>
                    </div>
                    <button type="submit" class="login-submit-btn">ENTER</button>

                    <a href="login.php?action=register" class="forgot-link">¿No tenés cuenta? Registrate acá ✨</a>
                    <span class="forgot-link" onclick="openForgotModal()">¿no te acordas la contraseña?:( que mal</span>

                    <?php if (!empty($mensaje)): ?>
                        <div style="color: #ff3366; font-size: 11px; margin-top: 5px;"><?php echo htmlspecialchars($mensaje); ?></div>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <form class="login-form" action="login.php?action=register" method="POST">
                    <input type="hidden" name="form_type" value="register">
                    <div class="input-box">
                        <input type="text" name="usuario" placeholder="Choose a Username" class="login-input" required>
                    </div>
                    <div class="input-box">
                        <input type="password" name="password" id="regPassField" placeholder="Password" class="login-input" required>
                        <button type="button" class="eye-toggle-btn" onclick="togglePassword('regPassField', 'eyeIcon2')">
                            <img src="img/ojo.jfif" alt="Mostrar" id="eyeIcon2">
                        </button>
                    </div>
                    <div class="input-box">
                        <input type="password" name="confirm_password" id="confirmPassField" placeholder="Confirm Password" class="login-input" required>
                        <button type="button" class="eye-toggle-btn" onclick="togglePassword('confirmPassField', 'eyeIcon3')">
                            <img src="img/ojo.jfif" alt="Mostrar" id="eyeIcon3">
                        </button>
                    </div>
                    <button type="submit" class="login-submit-btn">REGISTER</button>

                    <a href="login.php" class="forgot-link">¿Ya tenés cuenta? Iniciar Sesión 💖</a>

                    <?php if (!empty($mensaje)): ?>
                        <div style="color: #ff3366; font-size: 11px; margin-top: 5px;"><?php echo htmlspecialchars($mensaje); ?></div>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="custom-modal-overlay" id="forgotModal">
        <div class="custom-modal">
            <div class="custom-modal-header">
                <span class="custom-modal-title">RECUPERAR CONTRASEÑA 💔</span>
                <div class="os-btn" onclick="closeForgotModal()"><span class="btn-close">×</span></div>
            </div>
            <div class="custom-modal-body">
                <p style="margin-bottom: 15px; font-size: 12px; line-height: 1.4;">
                    ¿no te acordas la contraseña?:( que mal<br>
                    <span style="color: #ff007f; font-size: 11px;">Ingresá tu correo electrónico para enviarte un link de recuperación:</span>
                </p>
                <input type="email" id="recoveryEmailInput" placeholder="tu_email@ejemplo.com" class="login-input" style="width: 100%; font-size: 11px; margin-bottom: 10px;" required>
               
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button class="custom-modal-btn" onclick="closeForgotModal()" style="background: transparent; color: #ffb6c1; border-color: #ffb6c1;">CANCELAR</button>
                    <button class="custom-modal-btn" onclick="sendRecoveryEmail()">ENVIAR LINK ✨</button>
                </div>
            </div>
        </div>
    </div>

    <div class="custom-modal-overlay" id="customModal">
        <div class="custom-modal">
            <div class="custom-modal-header">
                <span class="custom-modal-title">CyberAngel.MP3</span>
                <div class="os-btn" onclick="closeCustomAlert()"><span class="btn-close">×</span></div>
            </div>
            <div class="custom-modal-body">
                <p id="modalMessage">Mensaje aquí</p>
                <button class="custom-modal-btn" onclick="closeCustomAlert()">ACEPTAR</button>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
        }
        function showCustomAlert(message) {
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('customModal').classList.add('active');
        }
        function closeCustomAlert() {
            document.getElementById('customModal').classList.remove('active');
        }
        function openForgotModal() {
            document.getElementById('forgotModal').classList.add('active');
        }
        function closeForgotModal() {
            document.getElementById('forgotModal').classList.remove('active');
        }
        function sendRecoveryEmail() {
            const email = document.getElementById('recoveryEmailInput').value.trim();
            if (email !== '') {
                closeForgotModal();
                showCustomAlert('¡Listo! Te enviamos las instrucciones de recuperación a ' + email + ' 💖✨');
                document.getElementById('recoveryEmailInput').value = '';
            } else {
                alert('Por favor, ingresá un correo electrónico válido. <3');
            }
        }
        function minimizeWindow() {
            showCustomAlert('Ventana minimizada.');
        }
        function maximizeWindow() {
            const win = document.querySelector('.os-window');
            win.style.width = win.style.width === '90vw' ? '580px' : '90vw';
        }
        function closeWindow() {
            showCustomAlert('Acción no permitida en esta DEMO');
        }
    </script>
</body>
</html>