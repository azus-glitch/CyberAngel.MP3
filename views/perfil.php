<?php
session_start();
$host = "localhost";
$dbname = "cyber_angel_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $pdo = null;
}

$mensaje_exito = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'cerrar_sesion') {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar_cuenta' && $pdo) {
    try {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = 1");
        $stmt->execute();
        session_destroy();
        header("Location: ../login.php");
        exit();
    } catch (Exception $e) {
        $mensaje_exito = "Error al eliminar la cuenta.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo && !isset($_POST['accion'])) {
    $nuevo_nombre = $_POST['nombre'] ?? '';
    $nueva_edad = $_POST['edad'] ?? '';
    $nuevo_bio = $_POST['bio'] ?? '';
    $nuevo_mood = $_POST['mood'] ?? '';

    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['avatar_file']['tmp_name'];
        $fileName = $_FILES['avatar_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $newFileName = 'avatar_' . time() . '.' . $fileExtension;
        $uploadFileDir = '../img/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $nuevo_avatar = $dest_path;
        } else {
            $nuevo_avatar = $_POST['avatar_actual'] ?? '../img/perfil.jfif';
        }
    } else {
        $nuevo_avatar = $_POST['avatar_actual'] ?? '../img/perfil.jfif';
    }

    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, edad = ?, bio = ?, mood = ?, avatar = ? WHERE id = 1");
        $stmt->execute([$nuevo_nombre, $nueva_edad, $nuevo_bio, $nuevo_mood, $nuevo_avatar]);
        $mensaje_exito = "¡Perfil actualizado con éxito!";
    } catch (Exception $e) {
        $mensaje_exito = "Error al actualizar.";
    }
}

$usuario = [
    'nombre' => 'Angel',
    'edad' => 17,
    'bio' => 'I love: ME',
    'mood' => 'divaa <3',
    'avatar' => '../img/perfil.jfif'
];

if ($pdo) {
    $query = $pdo->query("SELECT * FROM usuarios LIMIT 1");
    $resultado = $query->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        $usuario = array_merge($usuario, $resultado);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Angel - Perfil</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Slackey&display=swap" rel="stylesheet">
    <style>
        *, body, a, button, select, input, textarea {
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="%23ffb6c1" stroke="%23ff007f" stroke-width="1.5"><path d="M4.5 3.5L11.5 20.5L14.5 13.5L21.5 10.5L4.5 3.5Z"/></svg>'), auto !important;
        }

        .sparkle {
            position: fixed;
            pointer-events: none;
            width: 14px;
            height: 14px;
            background-color: #ff007f;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            z-index: 9999;
            transform: translate(-50%, -50%) scale(1);
            animation: sparkle-anim 0.7s forwards linear;
            box-shadow: 0 0 8px #ff66b2;
        }

        @keyframes sparkle-anim {
            0% { opacity: 1; transform: translate(-50%, -50%) scale(1.4) rotate(0deg); }
            100% { opacity: 0; transform: translate(-50%, -50%) scale(0.1) translateY(25px) rotate(220deg); }
        }

        .falling-item {
            position: fixed;
            top: -50px;
            pointer-events: none;
            z-index: 1;
            user-select: none;
            animation: fall linear forwards;
        }

        .falling-item svg { width: 100%; height: 100%; display: block; filter: drop-shadow(0 0 4px #ff007f); }

        @keyframes fall {
            0% { transform: translateY(0) rotate(0deg); opacity: 0.95; }
            100% { transform: translateY(105vh) rotate(360deg); opacity: 0; }
        }

        @keyframes floatY {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        @keyframes neonPulse {
            0% { box-shadow: 4px 4px 0px #ff007f, 0 0 5px rgba(255,0,127,0.4); }
            50% { box-shadow: 4px 4px 0px #ff007f, 0 0 15px rgba(255,0,127,0.9); }
            100% { box-shadow: 4px 4px 0px #ff007f, 0 0 5px rgba(255,0,127,0.4); }
        }

        html { overflow-y: scroll; }
        ::-webkit-scrollbar { width: 16px; background-color: #000000; }
        ::-webkit-scrollbar-track { background: #111111; border-left: 2px solid #ff007f; }
        ::-webkit-scrollbar-thumb { background: #ff007f; border: 2px solid #000000; border-radius: 6px; }

        body {
            margin: 0;
            padding: 0;
            background-image: url('../img/fondo.jfif');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Comic Sans MS', 'Courier New', sans-serif;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 48px;
            background: rgba(0, 0, 0, 0.92);
            backdrop-filter: blur(6px);
            border-bottom: 3px solid #ff007f;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
            box-sizing: border-box;
            padding: 0 15px;
        }

        .top-nav-left {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Slackey', cursive;
            font-size: 13px;
            color: #ff66b2;
        }

        .top-nav-left img {
            width: 30px;
            height: 30px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ff007f;
        }

        .top-nav-right {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .top-nav-btn {
            background: rgba(255, 0, 127, 0.2);
            border: 1px solid #ff007f;
            color: #ffffff;
            padding: 4px 10px;
            font-size: 11px;
            font-family: 'Slackey', cursive;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s;
        }

        .top-nav-btn:hover {
            background: #ff007f;
            color: #000000;
            box-shadow: 0 0 8px #ff007f;
        }

        .main-container {
            width: 100%;
            max-width: 950px;
            padding: 70px 20px 90px 20px;
            box-sizing: border-box;
            position: relative;
            z-index: 2;
        }

        .profile-layout {
            display: grid;
            grid-template-columns: 280px 180px 290px;
            justify-content: center;
            gap: 18px;
            margin-top: 10px;
        }

        .col-left, .col-right { display: flex; flex-direction: column; gap: 15px; }

        .box-card {
            background: rgba(0, 0, 0, 0.88);
            border: 3px solid #ff007f;
            padding: 12px;
            animation: neonPulse 4s infinite ease-in-out;
            position: relative;
        }

        .profile-img-wrap { position: relative; width: 100%; }
        .profile-img { width: 100%; height: 250px; object-fit: cover; border: 2px solid #ff007f; display: block; }
       
        .change-avatar-btn {
            position: absolute;
            top: 8px;
            left: 8px;
            background: rgba(0, 0, 0, 0.85);
            border: 1px solid #ff007f;
            color: #ff66b2;
            font-family: 'Slackey', cursive;
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.2s;
            z-index: 10;
        }

        .change-avatar-btn:hover {
            background: #ff007f;
            color: #ffffff;
            box-shadow: 0 0 8px #ff007f;
        }

        .match-badge {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: #ff007f;
            color: #ffffff;
            font-family: 'Slackey', cursive;
            padding: 6px 14px;
            font-size: 14px;
            border: 2px solid #ffffff;
            box-shadow: 2px 2px 0px #000000;
        }

        .header-tag {
            background: url('../img/glitter.jfif') center/cover !important;
            color: #ffffff;
            font-family: 'Slackey', cursive;
            font-size: 14px;
            padding: 6px 8px;
            text-align: center;
            border: 2px solid #ffffff;
            box-shadow: 2px 2px 0px #000000;
            margin-bottom: 8px;
            text-shadow: 1px 1px 2px #000000;
        }

        .zebra-tag {
            background: url('../img/cebra.jfif') center/cover !important;
        }

        .col-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 22px;
            padding-top: 25px;
        }

        .friend-bubble-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: floatY 3s ease-in-out infinite;
            filter: drop-shadow(0 4px 6px rgba(255, 0, 127, 0.4));
        }

        .friend-bubble-wrap:nth-child(2) { animation-delay: 0.4s; }
        .friend-bubble-wrap:nth-child(3) { animation-delay: 0.8s; }
        .friend-bubble-wrap:nth-child(4) { animation-delay: 1.2s; }

        .friend-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffffff;
            box-shadow: 0 0 10px #ff007f, 3px 3px 0px #ff007f;
            background: #000;
            transition: transform 0.2s;
        }

        .friend-avatar:hover { transform: scale(1.1) rotate(3deg); }

        .friend-name {
            font-family: 'Slackey', cursive;
            font-size: 10px;
            color: #ff66b2;
            background: rgba(0, 0, 0, 0.9);
            padding: 2px 8px;
            border: 1px solid #ff007f;
            margin-top: 4px;
            text-shadow: 1px 1px 1px #000;
        }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
        .grid-img { width: 100%; height: 85px; object-fit: cover; border: 2px solid #ff007f; transition: 0.2s; }
        .grid-img:hover { filter: brightness(1.2); transform: scale(1.02); }
        .grid-img-large { grid-column: span 2; height: 110px; }

        .form-edit input, .form-edit textarea {
            width: 100%;
            background: #111;
            border: 1px solid #ff007f;
            color: #fff;
            padding: 5px;
            margin-top: 4px;
            margin-bottom: 8px;
            font-family: 'Courier New', monospace;
            box-sizing: border-box;
            font-size: 11px;
        }

        .form-edit button {
            background: #ff007f;
            color: #fff;
            border: 1px solid #fff;
            font-family: 'Slackey', cursive;
            padding: 5px 10px;
            font-size: 10px;
            cursor: pointer;
            width: 100%;
        }

        .form-edit button:hover { background: #ff66b2; }

        .logout-btn {
            background: #4a154b !important;
            border: 1px solid #ff66b2 !important;
            color: #ffb6c1 !important;
            margin-top: 6px;
        }
        .logout-btn:hover {
            background: #ff66b2 !important;
            color: #000 !important;
        }

        .delete-account-btn {
            background: #330011 !important;
            border: 1px dashed #ff3366 !important;
            color: #ff80aa !important;
            margin-top: 6px;
        }
        .delete-account-btn:hover {
            background: #ff3366 !important;
            color: #fff !important;
        }

        .custom-modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.75);
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
            width: 340px;
            max-width: 90vw;
            background-color: #000000;
            border: 3px solid #ff007f;
            box-shadow: 0 0 20px rgba(255, 0, 127, 0.6);
        }
        .custom-modal-header {
            background-color: #ff007f;
            padding: 6px 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
            font-family: 'Slackey', cursive;
            font-size: 11px;
        }
        .custom-modal-body {
            padding: 20px;
            text-align: center;
            color: #ffb6c1;
            font-size: 12px;
        }

        .youtube-hidden {
            width: 1px; height: 1px; position: absolute; bottom: 0; left: 0; opacity: 0.01; overflow: hidden;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 56px;
            background: rgba(255, 182, 193, 0.95);
            backdrop-filter: blur(6px);
            border-top: 3px solid #ff007f;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 99;
            box-sizing: border-box;
            padding: 0 10px;
        }

        .nav-btn {
            background: transparent;
            border: none;
            padding: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.15s ease-in-out;
            border-radius: 50%;
            cursor: pointer;
        }

        .nav-btn img {
            width: 36px; height: 36px; object-fit: cover; border-radius: 50%; border: 2px solid #ff007f; box-shadow: 0 0 8px rgba(255, 0, 127, 0.6); pointer-events: none;
        }

        .nav-btn:hover { transform: scale(1.15) translateY(-2px); filter: drop-shadow(0 0 8px #ff007f); }
    </style>
</head>
<body>

    <div class="top-navbar">
        <div class="top-nav-left">
            <img src="<?php echo htmlspecialchars($usuario['avatar']); ?>" alt="Avatar" onerror="this.src='../img/perfil.jfif';">
            <span><?php echo htmlspecialchars($usuario['nombre']); ?></span>
        </div>
        <div class="top-nav-right">
            <a href="home.php" class="top-nav-btn">★ MATCH</a>
            <a href="perfil.php" class="top-nav-btn">PERFIL</a>
        </div>
    </div>

    <div class="main-container">
        <div class="profile-layout">
           
            <div class="col-left">
                <div style="font-size: 13px; color: #ff66b2; font-weight: bold; text-shadow: 0 0 5px #ff007f;">@angels4cyberlife_</div>
               
                <div class="box-card" style="padding: 6px;">
                    <div class="profile-img-wrap">
                        <button type="button" class="change-avatar-btn" onclick="document.getElementById('avatar_file_input').click();">📷 cambiar foto</button>
                        <img src="<?php echo htmlspecialchars($usuario['avatar']); ?>" class="profile-img" alt="Angel" id="preview-avatar" onerror="this.src='../img/perfil.jfif';">
                        <div class="match-badge">MATCH</div>
                    </div>
                    <div style="margin-top: 8px; font-size: 12px; color: #ff66b2; text-align: center;">mood: <?php echo htmlspecialchars($usuario['mood']); ?></div>
                </div>

                <div class="header-tag zebra-tag" style="text-align: center; font-size: 13px;">
                    the princess diary
                </div>

                <div style="background: rgba(0, 0, 0, 0.9); border: 2px solid #ff007f; padding: 10px; font-size: 11px; word-break: break-all; text-align: center; box-shadow: 3px 3px 0px #ff007f;">
                    url perfil:<br><span style="color: #ff66b2;">https://cyberangel.mp3.com/@angels4cyberlife</span>
                </div>
            </div>

            <div class="col-center">
                <div class="friend-bubble-wrap">
                    <img src="../img/banner1.jfif" class="friend-avatar" alt="Sofita">
                    <span class="friend-name">sofita</span>
                </div>
                <div class="friend-bubble-wrap">
                    <img src="../img/banner2.jfif" class="friend-avatar" alt="Azuu">
                    <span class="friend-name">azuu</span>
                </div>
                <div class="friend-bubble-wrap">
                    <img src="../img/banner7.jfif" class="friend-avatar" alt="Lauu">
                    <span class="friend-name">lauu</span>
                </div>
                <div class="friend-bubble-wrap">
                    <img src="../img/banner9.jfif" class="friend-avatar" alt="Maiite">
                    <span class="friend-name">maiite</span>
                </div>
            </div>
           
            <div class="col-right">
                <div class="header-tag" style="text-align: center;">
                    EDITAR PERFIL
                </div>

                <div class="box-card form-edit" style="font-size: 12px; line-height: 1.4;">
                    <div style="color: #ff007f; font-family: 'Slackey', cursive; font-size: 11px; margin-bottom: 6px;">Angel's blogg!! &lt;3</div>
                   
                    <?php if(!empty($mensaje_exito)): ?>
                        <div style="background: #ff007f; color: #fff; padding: 4px; font-size: 10px; text-align: center; margin-bottom: 6px;"><?php echo $mensaje_exito; ?></div>
                    <?php endif; ?>

                    <form action="perfil.php" method="POST" enctype="multipart/form-data">
                        <input type="file" id="avatar_file_input" name="avatar_file" accept="image/*" style="display: none;" onchange="previewImage(event)">
                        <input type="hidden" name="avatar_actual" value="<?php echo htmlspecialchars($usuario['avatar']); ?>">

                        <label style="color: #ff66b2; font-weight: bold;">Name:</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>

                        <label style="color: #ff66b2; font-weight: bold;">Age:</label>
                        <input type="number" name="edad" value="<?php echo htmlspecialchars($usuario['edad']); ?>" required>

                        <label style="color: #ff66b2; font-weight: bold;">Mood:</label>
                        <input type="text" name="mood" value="<?php echo htmlspecialchars($usuario['mood'] ?? 'divaa <3'); ?>" required>

                        <label style="color: #ff66b2; font-weight: bold;">About Me / Bio:</label>
                        <textarea name="bio" rows="2" required><?php echo htmlspecialchars($usuario['bio']); ?></textarea>

                        <button type="submit">GUARDAR CAMBIOS</button>
                    </form>

                    <form action="perfil.php" method="POST">
                        <input type="hidden" name="accion" value="cerrar_sesion">
                        <button type="submit" class="logout-btn">CERRAR SESIÓN 🚪</button>
                    </form>

                    <button type="button" class="delete-account-btn" onclick="openDeleteModal()">ELIMINAR CUENTA 💔</button>
                </div>

                <div>
                    <div class="header-tag">fotos / "amigos/matchs"</div>
                    <div class="box-card" style="padding: 6px;">
                        <div class="grid-2">
                            <img src="../img/banner5.gif" class="grid-img grid-img-large" alt="Foto">
                            <img src="../img/banner2.jfif" class="grid-img" alt="Foto">
                            <img src="../img/banner1.jfif" class="grid-img" alt="Foto">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="header-tag">musica // my_profile_playlist.mp3</div>
                    <div class="box-card" style="padding: 8px;">
                        <div style="display: flex; gap: 10px; align-items: center; background: #0a0a0a; border: 2px solid #ff007f; padding: 8px; box-shadow: 2px 2px 0px #ff007f; margin-bottom: 8px;">
                            <img id="player-cover" src="../img/cancion.jfif" alt="Cover" style="width: 65px; height: 65px; object-fit: cover; border: 2px solid #ff007f;">
                            <div style="flex-grow: 1; overflow: hidden;">
                                <div id="player-title" style="font-family: 'Slackey', cursive; font-size: 11px; color: #ff007f; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Genie in a Bottle</div>
                                <div id="player-artist" style="font-size: 10px; color: #ffb6c1; margin-bottom: 4px;">Christina Aguilera</div>
                                <div style="background: #222; height: 5px; border: 1px solid #ff007f; width: 100%;">
                                    <div style="background: #ff007f; width: 50%; height: 100%; box-shadow: 0 0 5px #ff007f;"></div>
                                </div>
                            </div>
                        </div>
                       
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <div onclick="changeTrack('kIDWG7Xar4E', 'Genie in a Bottle', 'Christina Aguilera', '../img/cancion.jfif')" style="display: flex; align-items: center; gap: 8px; background: rgba(255,0,127,0.15); border: 1px solid #ff007f; padding: 4px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='rgba(255,0,127,0.4)'" onmouseout="this.style.background='rgba(255,0,127,0.15)'">
                                <img src="../img/cancion.jfif" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #ff007f;" alt="1">
                                <div style="font-size: 10px; line-height: 1.1;">
                                    <span style="color: #ffffff; font-weight: bold;">Genie in a Bottle</span><br>
                                    <span style="color: #ff66b2;">Christina Aguilera</span>
                                </div>
                                <div style="margin-left: auto; color: #ff007f; font-size: 11px; padding-right: 4px;">▶</div>
                            </div>

                            <div onclick="changeTrack('LOZuxwoc7B8', 'Toxic', 'Britney Spears', '../img/toxic.jfif')" style="display: flex; align-items: center; gap: 8px; background: rgba(255,0,127,0.15); border: 1px solid #ff007f; padding: 4px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='rgba(255,0,127,0.4)'" onmouseout="this.style.background='rgba(255,0,127,0.15)'">
                                <img src="../img/toxic.jfif" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #ff007f;" alt="2">
                                <div style="font-size: 10px; line-height: 1.1;">
                                    <span style="color: #ffffff; font-weight: bold;">Toxic</span><br>
                                    <span style="color: #ff66b2;">Britney Spears</span>
                                </div>
                                <div style="margin-left: auto; color: #ff007f; font-size: 11px; padding-right: 4px;">▶</div>
                            </div>

                            <div onclick="changeTrack('98WtmW-lfeE', 'Teenage Dream', 'Katy Perry', '../img/teenage.jfif')" style="display: flex; align-items: center; gap: 8px; background: rgba(255,0,127,0.15); border: 1px solid #ff007f; padding: 4px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='rgba(255,0,127,0.4)'" onmouseout="this.style.background='rgba(255,0,127,0.15)'">
                                <img src="../img/teenage.jfif" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #ff007f;" alt="3">
                                <div style="font-size: 10px; line-height: 1.1;">
                                    <span style="color: #ffffff; font-weight: bold;">Teenage Dream</span><br>
                                    <span style="color: #ff66b2;">Katy Perry</span>
                                </div>
                                <div style="margin-left: auto; color: #ff007f; font-size: 11px; padding-right: 4px;">▶</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="header-tag">posts</div>
                    <div class="box-card" style="padding: 6px;">
                        <div class="grid-2">
                            <img src="../img/banner4.gif" class="grid-img" style="height: 95px;" alt="Post">
                            <img src="../img/banner9.jfif" class="grid-img" style="height: 95px;" alt="Post">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
   
    <div class="custom-modal-overlay" id="deleteModal">
        <div class="custom-modal">
            <div class="custom-modal-header">
                <span>AVISO DEL SISTEMA ⚠️</span>
                <span style="cursor:pointer;" onclick="closeDeleteModal()">✕</span>
            </div>
            <div class="custom-modal-body">
                <p style="margin-bottom: 15px; line-height: 1.4;">¿Estás segura de que querés eliminar tu cuenta para siempre?:(</p>
                <form action="perfil.php" method="POST">
                    <input type="hidden" name="accion" value="eliminar_cuenta">
                    <div style="display: flex; gap: 8px; justify-content: center;">
                        <button type="button" onclick="closeDeleteModal()" style="background: transparent; border: 1px solid #ffb6c1; color: #ffb6c1; padding: 5px 12px; font-family: 'Slackey', cursive; font-size: 10px; cursor: pointer;">CANCELAR</button>
                        <button type="submit" style="background: #ff007f; border: 1px solid #fff; color: #fff; padding: 5px 12px; font-family: 'Slackey', cursive; font-size: 10px; cursor: pointer;">SÍ, BORRAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="youtube-hidden">
        <div id="player"></div>
    </div>
   
    <div class="bottom-nav">
        <button class="nav-btn" id="profile-btn" title="Perfil">
            <img src="../img/perfil.jfif" alt="Perfil">
        </button>
        <button class="nav-btn" id="match-btn" title="Match">
            <img src="../img/match.jfif" alt="Match">
        </button>
        <button class="nav-btn" id="add-btn" title="Agregar">
            <img src="../img/simbolomas.jfif" alt="Agregar">
        </button>
        <button class="nav-btn" id="messages-btn" title="Mensajes">
            <img src="../img/mensajes.jfif" alt="Mensajes">
        </button>
        <button class="nav-btn" id="notifications-btn" title="Notificaciones">
            <img src="../img/notificaciones.jfif" alt="Notificaciones">
        </button>
    </div>

    <script>
    let player;
    let apiReady = false;

    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    function onYouTubeIframeAPIReady() {
      player = new YT.Player("player", {
        height: "1",
        width: "1",
        videoId: "kIDWG7Xar4E",
        playerVars: { autoplay: 0, controls: 0, disablekb: 1, fs: 0, rel: 0, modestbranding: 1 },
        events: { 'onReady': () => { apiReady = true; } }
      });
    }

    function changeTrack(videoId, title, artist, coverUrl) {
      if (apiReady && player && player.loadVideoById) {
        player.loadVideoById(videoId);
        player.playVideo();
      }
      document.getElementById('player-cover').src = coverUrl;
      document.getElementById('player-title').innerText = title;
      document.getElementById('player-artist').innerText = artist;
    }

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('preview-avatar');
            if (output) output.src = reader.result;
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    function openDeleteModal() {
        document.getElementById('deleteModal').classList.add('active');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }

    document.getElementById('profile-btn').addEventListener('click', () => {
        window.location.href = 'perfil.php';
    });

    document.getElementById('match-btn').addEventListener('click', () => {
        window.location.href = 'home.php';
    });

    document.getElementById('add-btn').addEventListener('click', () => {
        window.location.href = 'agregar.php';
    });

    document.getElementById('messages-btn').addEventListener('click', () => {
        window.location.href = 'mensajes.php';
    });

    document.getElementById('notifications-btn').addEventListener('click', () => {
        window.location.href = 'notificaciones.php';
    });

    const items = [
        '<svg viewBox="0 0 24 24" fill="#ff007f"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>',
        '<svg viewBox="0 0 24 24" fill="#ff007f"><path d="M12 0L14.5 9.5L24 12L14.5 14.5L12 24L9.5 14.5L0 12L9.5 9.5Z"/></svg>'
    ];

    setInterval(() => {
        const el = document.createElement('div');
        el.className = 'falling-item';
        el.innerHTML = items[Math.floor(Math.random() * items.length)];
        el.style.left = Math.random() * 100 + 'vw';
        const s = Math.random() * 12 + 14;
        el.style.width = s + 'px'; el.style.height = s + 'px';
        const d = Math.random() * 3 + 3;
        el.style.animationDuration = d + 's';
        document.body.appendChild(el);
        setTimeout(() => el.remove(), d * 1000);
    }, 300);

    let lastTime = 0;
    document.addEventListener('mousemove', e => {
        const now = Date.now();
        if (now - lastTime > 35) {
            const sp = document.createElement('div');
            sp.className = 'sparkle';
            sp.style.left = (e.clientX + (Math.random() - 0.5) * 15) + 'px';
            sp.style.top = (e.clientY + (Math.random() - 0.5) * 15) + 'px';
            document.body.appendChild(sp);
            setTimeout(() => sp.remove(), 700);
            lastTime = now;
        }
    });
    </script>
    <?php include '../cookies.php'; ?>
</body>
</html>