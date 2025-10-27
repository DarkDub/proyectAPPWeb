<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

$user_id = $_SESSION['user_id'];
if (isset($_GET['id'])) {
    $id_leccion = intval($_GET['id']); // lo convertimos a número para evitar inyección
} else {
    die("❌ ID de lección no proporcionado");
}

// Obtener datos del usuario
$stmt = $conn->prepare("SELECT nombre, puntos FROM usuarios WHERE id_usuario = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener preguntas de la lección
$stmt = $conn->prepare("SELECT * FROM preguntas WHERE id_leccion = :id_leccion ORDER BY id_pregunta ASC");
$stmt->bindParam(':id_leccion', $id_leccion, PDO::PARAM_INT);
$stmt->execute();
$preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_preguntas = count($preguntas);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lección - BrainPlay</title>
    <?php include __DIR__ . '/../includes/cdns.php'; ?>
    <link rel="stylesheet" href="../public/css/user/leccion.css">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        .swal-game1 {
            border: 2px solid #a259ff;
            box-shadow: 0 0 30px #a259ff80;
            border-radius: 20px;
        }

        @keyframes glow {
            from {
                box-shadow: 0 0 10px #a259ff80;
            }

            to {
                box-shadow: 0 0 30px #a259ff;
            }
        }

        .swal-game-btn {
            font-family: 'Orbitron', sans-serif !important;
            text-transform: uppercase;
            border-radius: 12px !important;
            padding: 10px 30px !important;
            box-shadow: 0 0 15px #a259ff;
            transition: 0.3s ease;
        }
        #swal2-title {
            color: white !important;
        }
        .swal-game-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 25px #a259ff;
        }

        .leccion-title{
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            color: #a259ff;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="leccion-container">
        <h1 class="leccion-title">Lección <?= $id_leccion ?></h1>
        <p class="user-points">Puntos: <span id="userPoints"><?= htmlspecialchars($user['puntos']) ?></span></p>

        <div class="card-container">
            <?php foreach ($preguntas as $index => $pregunta): ?>
                <div class="question-card" data-correct="<?= $pregunta['correcta'] ?>" data-palabra="<?= htmlspecialchars($pregunta['pregunta']) ?>">
                    <?php if (!empty($pregunta['imagen'])): ?>
                        <img src="<?= $pregunta['imagen'] ?>" alt="Imagen pregunta" class="question-img">
                    <?php endif; ?>
                    <h2 class="question-text"><?= htmlspecialchars($pregunta['pregunta']) ?></h2>
                    <div class="options">
                        <button class="option-btn" data-option="a"><?= htmlspecialchars($pregunta['opcion_a']) ?></button>
                        <button class="option-btn" data-option="b"><?= htmlspecialchars($pregunta['opcion_b']) ?></button>
                        <button class="option-btn" data-option="c"><?= htmlspecialchars($pregunta['opcion_c']) ?></button>
                        <button class="option-btn" data-option="d"><?= htmlspecialchars($pregunta['opcion_d']) ?></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="progress-bar-container">
            <div class="progress-bar-fill"></div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script>
        const cards = document.querySelectorAll('.question-card');
        const progressFill = document.querySelector('.progress-bar-fill');
        const userPointsSpan = document.getElementById('userPoints');
        let current = 0;
        let score = 0;
        let palabrasAprendidas = [];

        function showCard(index) {
            cards.forEach((c, i) => c.style.display = i === index ? 'block' : 'none');
            updateProgress(index);
        }

        function updateProgress(index) {
            const percent = ((index) / cards.length) * 100;
            progressFill.style.width = percent + '%';
        }

        cards.forEach((card, idx) => {
            const buttons = card.querySelectorAll('.option-btn');
            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const selected = btn.dataset.option;
                    const correct = card.dataset.correct;
                    const palabra = card.dataset.palabra;

                    palabrasAprendidas.push(palabra);

                    if (selected === correct) {
                        btn.classList.add('correct');
                        score++;
                    } else {
                        btn.classList.add('wrong');
                        card.querySelector(`.option-btn[data-option='${correct}']`).classList.add('correct');
                    }

                    setTimeout(() => {
                        current++;
                        if (current < cards.length) {
                            showCard(current);
                        } else {
                            // Fin de la lección: enviar datos al backend
                            fetch('Game/leccionUpload.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        user_id: <?= $user_id ?>,
                                        id_leccion: <?= $id_leccion ?>,
                                        puntos_ganados: score,
                                        palabras: palabrasAprendidas
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        userPointsSpan.textContent = data.nuevo_puntaje;
                                    }

                                    Swal.fire({
                                        title: '¡Felicidades!',
                                        titleColor: '#fff',

                                        html: `
        <div style="font-family: 'Orbitron', sans-serif; color:#fff; font-size:1.2rem; letter-spacing:1px;">
            <p>Terminaste la lección 🎉</p>
            <p>Puntos ganados: ${score}</p>
            <p>Palabras aprendidas: ${palabrasAprendidas.length}</p>
        </div>
    `,
                                        icon: 'success',
                                        iconColor: '#a259ff', // Color personalizado para success
                                        background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
                                        showConfirmButton: true,
                                        confirmButtonText: 'Continuar',
                                        confirmButtonColor: '#a259ff',
                                        customClass: {
                                            popup: 'swal-game1',
                                            confirmButton: 'swal-game-btn'
                                        },
                                        showClass: {
                                            popup: 'animate__animated animate__fadeInDown'
                                        },
                                        hideClass: {
                                            popup: 'animate__animated animate__fadeOutUp'
                                        }
                                    }).then(() => {
                                        window.location.href = "lecciones.php";
                                    });






                                })
                                .catch(err => console.error(err));
                        }
                    }, 800);
                });
            });
        });

        showCard(0);
    </script>
</body>

</html>