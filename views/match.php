<?php
$host = "localhost";
$dbname = "cyber_angel_db";
$username = "root";
$password = "";

$perfiles = [];
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT * FROM match_perfiles ORDER BY RAND()");
    $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

if (empty($perfiles)) {
    $perfiles = [
        ['id' => 1, 'nombre' => 'Kamila', 'imagen' => '../img/kamila.jpg'],
        ['id' => 2, 'nombre' => 'Nahi', 'imagen' => '../img/nahi.jpg'],
        ['id' => 3, 'nombre' => 'May', 'imagen' => '../img/may.jpg'],
        ['id' => 4, 'nombre' => 'Martin', 'imagen' => '../img/martin.jpg'],
        ['id' => 5, 'nombre' => 'Jhonatan', 'imagen' => '../img/jhonatan.jpg'],
        ['id' => 6, 'nombre' => 'Enrique', 'imagen' => '../img/enrique.jpg'],
        ['id' => 7, 'nombre' => 'Rosi', 'imagen' => '../img/rosi.jpg'],
        ['id' => 8, 'nombre' => 'Miriam', 'imagen' => '../img/miriam.jpg'],
        ['id' => 9, 'nombre' => 'Matias', 'imagen' => '../img/matias.jpg']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Angel - Match</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    
    <style>
        *, body, a, button, select, input {
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="%23ffb6c1" stroke="%23ff007f" stroke-width="1.5"><path d="M4.5 3.5L11.5 20.5L14.5 13.5L21.5 10.5L4.5 3.5Z"/></svg>'), auto !important;
        }

        .sparkle {
            position: fixed;
            pointer-events: none;
            width: 12px;
            height: 12px;
            background-color: #ff007f;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            z-index: 9999;
            transform: translate(-50%, -50%) scale(1);
            animation: sparkle-anim 0.8s forwards linear;
        }

        @keyframes sparkle-anim {
            0% { opacity: 1; transform: translate(-50%, -50%) scale(1.2) rotate(0deg); }
            100% { opacity: 0; transform: translate(-50%, -50%) scale(0.2) translateY(20px) rotate(180deg); }
        }

        .falling-item {
            position: fixed;
            top: -50px;
            pointer-events: none;
            z-index: 1;
            user-select: none;
            animation: fall linear forwards;
        }
        .falling-item svg { width: 100%; height: 100%; display: block; }
        @keyframes fall {
            0% { transform: translateY(0) rotate(0deg); opacity: 0.95; }
            100% { transform: translateY(105vh) rotate(360deg); opacity: 0; }
        }

        html { overflow-y: scroll; }

        ::-webkit-scrollbar { width: 16px; background-color: #ffe4e1; }
        ::-webkit-scrollbar-track { background: #ffd0e3; border-left: 2px solid #ff007f; }
        ::-webkit-scrollbar-thumb { background: #ff66b2; border: 2px solid #ffffff; outline: 1px solid #ff007f; border-radius: 6px; }
        ::-webkit-scrollbar-thumb:hover { background: #ff007f; }

        body {
            margin: 0;
            padding: 0;
            background-image: url('../img/animal.jfif');
            background-repeat: repeat;
            background-size: 500px auto;
            font-family: 'Courier New', monospace;
            color: #ffffff;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #000000;
        }

        .window-container {
            width: 100%;
            max-width: 800px;
            background-color: #000000;
            min-height: calc(100vh - 56px);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 20px 100px 20px;
            box-sizing: border-box;
            border-left: 12px solid #ff007f;
            border-right: 12px solid #ff007f;
            box-shadow: inset 0 0 30px rgba(255, 0, 127, 0.3);
            z-index: 2;
        }

        .window-container::after {
            content: " ";
            display: block;
            position: absolute;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%);
            background-size: 100% 4px;
            z-index: 50;
            pointer-events: none;
            opacity: 0.5;
        }

        .screen-glow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,0,127,0.8) 0%, rgba(255,182,193,0.4) 50%, rgba(0,0,0,0) 90%);
            opacity: 0;
            pointer-events: none;
            z-index: 35;
            transition: opacity 0.2s ease;
        }

        .screen-glow.active {
            animation: fullScreenGlow 0.7s ease;
        }

        @keyframes fullScreenGlow {
            0% { opacity: 0; transform: scale(0.9); }
            40% { opacity: 1; transform: scale(1.05); }
            100% { opacity: 0; transform: scale(1); }
        }

        .bg-decor-text {
            position: absolute;
            font-family: 'Great Vibes', cursive;
            color: rgba(255, 102, 178, 0.25);
            font-size: 28px;
            pointer-events: none;
            text-shadow: 1px 1px #ff007f;
            z-index: 1;
        }

        .t1 { top: 20px; left: 30px; transform: rotate(-10deg); }
        .t2 { top: 30px; right: 40px; transform: rotate(15deg); font-size: 34px; }
        .t3 { bottom: 120px; left: 40px; transform: rotate(12deg); }
        .t4 { bottom: 100px; right: 40px; transform: rotate(-8deg); font-size: 32px; }

        .floating-marquee-container {
            width: 100%;
            overflow: hidden;
            z-index: 30;
            pointer-events: none;
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .floating-marquee {
            display: inline-block;
            white-space: nowrap;
            background: rgba(0, 0, 0, 0.85);
            border: 1px dashed #ff007f;
            color: #ff99cc;
            padding: 6px 20px;
            font-size: 13px;
            letter-spacing: 1.5px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(255, 0, 127, 0.4);
            animation: floatBanner 26s linear infinite;
        }

        @keyframes floatBanner {
            0% { transform: translateX(100vw); }
            100% { transform: translateX(-100vw); }
        }

        .counter-badge {
            position: absolute;
            top: 20px;
            right: 25px;
            background: rgba(255, 0, 127, 0.2);
            border: 1px solid #ff007f;
            color: #ff99cc;
            padding: 4px 10px;
            font-size: 12px;
            border-radius: 6px;
            z-index: 30;
            pointer-events: none;
        }

        .top-left-title {
            position: absolute;
            top: 20px;
            left: 25px;
            font-family: 'Great Vibes', cursive;
            font-size: 26px;
            color: #ff99cc;
            line-height: 1.1;
            text-shadow: 1px 1px #ff007f;
            z-index: 20;
            pointer-events: none;
            letter-spacing: 1px;
        }

        .match-workspace {
            position: relative;
            width: 100%;
            min-height: 420px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
            z-index: 20;
        }

        .deck-container {
            position: relative;
            width: 280px;
            height: 340px;
        }

        .profile-card {
            position: absolute;
            width: 280px;
            height: 320px;
            background-size: cover;
            background-position: center;
            border-radius: 4px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.8);
            transition: transform 0.4s ease, opacity 0.4s ease;
            border: 2px solid #ff007f;
        }

        .action-button-left, .action-button-right {
            position: absolute;
            width: 110px;
            height: 110px;
            background-color: #000000;
            background-repeat: no-repeat;
            background-position: center;
            background-size: 85%;
            border: 2px solid #ff007f;
            box-shadow: 0 0 10px rgba(255, 0, 127, 0.5);
            cursor: pointer;
            z-index: 25;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 4px;
            top: 50%;
            transform: translateY(-50%);
        }

        .action-button-left {
            left: 5px;
            background-image: url('../img/match.jpg');
        }

        .action-button-right {
            right: 5px;
            background-image: url('../img/nomatch.jpg');
        }

        .action-button-left:hover, .action-button-right:hover {
            transform: translateY(-50%) scale(1.08);
            box-shadow: 0 0 18px #ff007f;
        }

        .action-button-left:active, .action-button-right:active {
            transform: translateY(-50%) scale(0.95);
        }

        .screen-kiss-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255, 0, 127, 0.2);
            backdrop-filter: blur(2px);
            z-index: 150;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .screen-kiss-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .giant-screen-kiss {
            font-size: 15vw;
            color: #ff007f;
            filter: drop-shadow(0 0 35px #ffffff) drop-shadow(0 0 60px #ff007f);
            transform: scale(0.1);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.4);
            user-select: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .screen-kiss-overlay.active .giant-screen-kiss {
            transform: scale(1);
            opacity: 1;
            animation: kissPulse 0.5s infinite alternate ease-in-out;
        }

        @keyframes kissPulse {
            0% { transform: scale(1) rotate(-3deg); }
            100% { transform: scale(1.1) rotate(3deg); }
        }

        .kiss-text {
            font-family: 'Great Vibes', cursive;
            font-size: 45px;
            color: #ffffff;
            text-shadow: 2px 2px #ff007f, 0 0 20px #ff007f;
            white-space: nowrap;
        }

        .match-success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(4px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 100;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .match-success-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .match-box-y2k {
            width: 360px;
            background: #000000;
            border: 2px solid #ff007f;
            box-shadow: 0 0 25px rgba(255, 0, 127, 0.7), inset 0 0 15px rgba(255, 182, 193, 0.2);
            padding: 15px;
            text-align: center;
            font-family: 'Courier New', monospace;
            position: relative;
            border-radius: 6px;
        }

        .match-marquee-top {
            background: #ff007f;
            color: #fff;
            font-size: 11px;
            padding: 4px;
            overflow: hidden;
            white-space: nowrap;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .match-title {
            font-family: 'Great Vibes', cursive;
            font-size: 32px;
            color: #ff99cc;
            margin: 0 0 15px 0;
            text-shadow: 1px 1px #ff007f;
        }

        .match-avatars-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            border: 2px solid #ff007f;
            box-shadow: 0 0 10px #ff007f;
        }

        .pixel-heart-beat {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: heartBeat 0.8s infinite alternate;
        }

        @keyframes heartBeat {
            0% { transform: scale(1); }
            100% { transform: scale(1.3); }
        }

        .match-subtitle {
            color: #ffb6c1;
            font-size: 12px;
            margin-bottom: 20px;
            font-style: italic;
        }

        .match-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .match-chat-input {
            background: #111;
            border: 1px dashed #ff007f;
            color: #ff99cc;
            padding: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            text-align: center;
            outline: none;
        }

        .match-buttons-row {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .y2k-btn {
            background: #ff007f;
            color: #fff;
            border: 1px solid #fff;
            padding: 6px 12px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 2px 2px 0px #ffb6c1;
            transition: transform 0.1s;
        }

        .y2k-btn.secondary {
            background: #000;
            color: #ff99cc;
            border: 1px solid #ff007f;
        }

        .y2k-btn:active {
            transform: translate(2px, 2px);
            box-shadow: none;
        }

        .bottom-bar {
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

        .nav-item {
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

        .nav-item img {
            width: 36px;
            height: 36px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ff007f;
            box-shadow: 0 0 6px rgba(255, 0, 127, 0.4);
            pointer-events: none;
        }

        .nav-item:hover {
            transform: scale(1.15) translateY(-2px);
            filter: drop-shadow(0 0 8px #ff007f);
        }

        .nav-item.active img {
            border: 3px solid #ffffff;
            box-shadow: 0 0 12px #ff007f;
            transform: scale(1.1);
        }
    </style>
</head>
<body>

    <div class="window-container">
        
        <audio id="soundMatch" src="../img/sonido-de-brillo.mp3" preload="auto"></audio>
        <audio id="soundNoMatch" src="../img/mouse-click-sound.mp3" preload="auto"></audio>

        <div class="screen-glow" id="screenGlow"></div>

        <div class="bg-decor-text t1">MATCH !!!<br>\(>v<)/</div>
        <div class="bg-decor-text t2">Diva 💋</div>
        <div class="bg-decor-text t3">\(>v<)/</div>
        <div class="bg-decor-text t4">MATCH !!!<br>Diva 💋</div>

        <div class="floating-marquee-container">
            <div class="floating-marquee">✨ nota: tu brillo es verdadero el del resto es solo sudor! ✨</div>
        </div>

        <div class="counter-badge" id="counterBadge">Perfiles: <?php echo count($perfiles); ?></div>

        <div class="top-left-title">Cyber<br>Angel<br>.mp3</div>

        <div class="match-workspace">
            <button class="action-button-left" onclick="handleMatch('match')" title="Match!"></button>

            <div class="deck-container" id="card-deck">
                <?php foreach ($perfiles as $index => $p): ?>
                    <div class="profile-card" 
                         data-id="<?php echo htmlspecialchars($p['id']); ?>" 
                         data-nombre="<?php echo htmlspecialchars($p['nombre']); ?>" 
                         style="background-image: url('<?php echo htmlspecialchars($p['imagen']); ?>'); z-index: <?php echo count($perfiles) - $index; ?>; transform: rotate(<?php echo ($index * 3) - 3; ?>deg) translate(<?php echo $index * 5; ?>px, <?php echo $index * 4; ?>px); filter: brightness(<?php echo 1 - ($index * 0.2); ?>);"></div>
                <?php endforeach; ?>
            </div>

            <button class="action-button-right" onclick="handleMatch('nomatch')" title="No Match!"></button>
        </div>

        <div class="screen-kiss-overlay" id="screenKissOverlay">
            <div class="giant-screen-kiss">
                💋
                <div class="kiss-text">¡Muuuua! La próxima será, amigx</div>
            </div>
        </div>

        <div class="match-success-overlay" id="matchSuccessOverlay">
            <div class="match-box-y2k">
                <div class="match-marquee-top">
                    <span> ★ IT'S A MATCH! ★  :: CYBER ANGEL CONNECTION :: </span>
                </div>
                <h2 class="match-title">¡Nuevo Vínculo Encontrado!</h2>
                
                <div class="match-avatars-container">
                    <div class="avatar-circle" style="background-image: url('../img/perfil.jfif');"></div>
                    
                    <div class="pixel-heart-beat">
                        <svg width="42" height="42" viewBox="0 0 100 100">
                            <defs>
                                <radialGradient id="pinkGloss" cx="35%" cy="30%" r="70%">
                                    <stop offset="0%" stop-color="#ffb6c1" />
                                    <stop offset="50%" stop-color="#ff007f" />
                                    <stop offset="100%" stop-color="#800040" />
                                </radialGradient>
                                <filter id="glowEffect" x="-20%" y="-20%" width="140%" height="140%">
                                    <feGaussianBlur stdDeviation="3" result="blur" />
                                    <feComposite in="SourceGraphic" in2="blur" operator="over" />
                                </filter>
                            </defs>
                            <path d="M 50 88 C 20 65, 5 45, 5 28 C 5 12, 18 3, 33 3 C 42 3, 47 8, 50 14 C 53 8, 58 3, 67 3 C 82 3, 95 12, 95 28 C 95 45, 80 65, 50 88 Z" fill="url(#pinkGloss)" stroke="#ffffff" stroke-width="3" filter="url(#glowEffect)" />
                            <path d="M 22 18 C 16 26, 16 34, 22 40" fill="none" stroke="#ffffff" stroke-width="4" stroke-linecap="round" opacity="0.7" />
                            <path d="M 75 18 L 78 25 L 85 28 L 78 31 L 75 38 L 72 31 L 65 28 L 72 25 Z" fill="#ffee00" stroke="#ffffff" stroke-width="1" />
                            <path d="M 24 64 L 26 69 L 31 71 L 26 73 L 24 78 L 22 73 L 17 71 L 22 69 Z" fill="#ffee00" stroke="#ffffff" stroke-width="1" />
                        </svg>
                    </div>

                    <div class="avatar-circle" id="matchedAvatar" style="background-image: url('');"></div>
                </div>

                <p class="match-subtitle">"Agregado a tus Top Friends ♫"</p>
                <div class="match-actions">
                    <input type="text" class="match-chat-input" readonly value="¡Hola! Me encantó tu perfil :3">
                    <div class="match-buttons-row">
                        <button class="y2k-btn" onclick="sendGreeting()">Enviar Mensaje</button>
                        <!-- CORREGIDO: Ahora redirige al perfil del usuario con el que se hizo match -->
                        <button class="y2k-btn secondary" onclick="viewMatchedProfile()">Ver Perfil</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    <div class="bottom-bar">
        <button class="nav-item" onclick="window.location.href='perfil.php'" title="Perfil">
            <img src="../img/perfil.jfif" alt="Perfil">
        </button>
        <button class="nav-item active" onclick="window.location.href='match.php'" title="Match">
            <img src="../img/match.jfif" alt="Match">
        </button>
        <button class="nav-item" onclick="window.location.href='agregar.php'" title="Agregar">
            <img src="../img/simbolomas.jfif" alt="Agregar">
        </button>
        <button class="nav-item" onclick="window.location.href='mensajes.php'" title="Mensajes">
            <img src="../img/mensajes.jfif" alt="Mensajes">
        </button>
        <button class="nav-item" onclick="window.location.href='notificaciones.php'" title="Notificaciones">
            <img src="../img/notificaciones.jfif" alt="Notificaciones">
        </button>
    </div>

    <script>
        let currentMatchedId = null;

        function handleMatch(type) {
            const deck = document.getElementById('card-deck');
            const topCard = deck.querySelector('.profile-card');
            const glow = document.getElementById('screenGlow');
            const kissOverlay = document.getElementById('screenKissOverlay');
            const counterBadge = document.getElementById('counterBadge');
            const matchOverlay = document.getElementById('matchSuccessOverlay');
            
            const soundMatch = document.getElementById('soundMatch');
            const soundNoMatch = document.getElementById('soundNoMatch');
            
            if (topCard) {
                // Guardamos el ID del perfil actual antes de animarlo o removerlo
                currentMatchedId = topCard.getAttribute('data-id');
                const cardBg = topCard.style.backgroundImage;

                topCard.style.transition = 'transform 0.4s ease, opacity 0.4s ease';
                
                if (type === 'match') {
                    soundMatch.currentTime = 0;
                    soundMatch.play().catch(e => console.log("Audio play prevented:", e));

                    topCard.style.transform = 'translateX(250px) rotate(25deg)';
                    topCard.style.opacity = '0';

                    glow.classList.remove('active');
                    void glow.offsetWidth; 
                    glow.classList.add('active');

                    // Sincronizamos la foto de la tarjeta en el cartel de éxito
                    document.getElementById('matchedAvatar').style.backgroundImage = cardBg;

                    setTimeout(() => {
                        matchOverlay.classList.add('active');
                    }, 300);

                } else {
                    soundNoMatch.currentTime = 0;
                    soundNoMatch.play().catch(e => console.log("Audio play prevented:", e));

                    topCard.style.transform = 'translateX(-250px) rotate(-25deg)';
                    topCard.style.opacity = '0';

                    kissOverlay.classList.add('active');
                    
                    setTimeout(() => {
                        kissOverlay.classList.remove('active');
                        removeCardAndCheck(topCard, counterBadge, deck);
                    }, 1400);
                }
            }
        }

        function viewMatchedProfile() {
            if (currentMatchedId) {
                window.location.href = 'perfil.php?id=' + currentMatchedId;
            } else {
                window.location.href = 'perfil.php';
            }
        }

        function closeMatchScreen() {
            const matchOverlay = document.getElementById('matchSuccessOverlay');
            const deck = document.getElementById('card-deck');
            const counterBadge = document.getElementById('counterBadge');
            const topCard = deck.querySelector('.profile-card');

            matchOverlay.classList.remove('active');

            if (topCard) {
                removeCardAndCheck(topCard, counterBadge, deck);
            }
        }

        function removeCardAndCheck(card, badge, deck) {
            if (card) card.remove();
            const remaining = deck.children.length;
            badge.textContent = `Perfiles: ${remaining}`;
            
            if (remaining === 0) {
                location.reload();
            }
        }

        function sendGreeting() {
            alert("💌 ¡Mensaje enviado con éxito al Top Friends!");
            closeMatchScreen();
        }

        const Y2K_ITEMS = [
            `<svg viewBox="0 0 24 24" fill="#ff007f"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>`,
            `<svg viewBox="0 0 24 24" fill="#ffb6c1"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>`,
            `<svg viewBox="0 0 24 24" fill="#ff007f"><path d="M12 0L14.5 9.5L24 12L14.5 14.5L12 24L9.5 14.5L0 12L9.5 9.5Z"/></svg>`
        ];

        function createFallingItem() {
            const item = document.createElement('div');
            item.className = 'falling-item';
            item.innerHTML = Y2K_ITEMS[Math.floor(Math.random() * Y2K_ITEMS.length)];
            item.style.left = Math.random() * 100 + 'vw';
            const size = Math.random() * 10 + 16;
            item.style.width = size + 'px';
            item.style.height = size + 'px';
            const duration = Math.random() * 3 + 4;
            item.style.animationDuration = duration + 's';
            document.body.appendChild(item);
            setTimeout(() => item.remove(), duration * 1000);
        }
        setInterval(createFallingItem, 400);

        const colors = ['#ff007f', '#ff66b2', '#ffffff', '#ffb6c1'];
        let lastSparkleTime = 0;
        document.addEventListener('mousemove', function(e) {
            const now = Date.now();
            if (now - lastSparkleTime > 50) {
                const sparkle = document.createElement('div');
                sparkle.className = 'sparkle';
                sparkle.style.left = e.clientX + 'px';
                sparkle.style.top = e.clientY + 'px';
                sparkle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                document.body.appendChild(sparkle);
                setTimeout(() => sparkle.remove(), 800);
                lastSparkleTime = now;
            }
        });
    </script>
    <?php include 'cookies.php'; ?>
</body>
</html>
