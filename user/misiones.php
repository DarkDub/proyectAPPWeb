<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';
$user_id = $_SESSION['user_id'];

// Puntos del usuario
$stmt = $conn->prepare("SELECT puntos FROM usuarios WHERE id_usuario=:id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Traer misiones
$stmt = $conn->prepare("SELECT * FROM misiones_diarias ORDER BY id DESC");
$stmt->execute();
$misiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Misiones completadas del usuario
$stmt2 = $conn->prepare("SELECT id_mision FROM misiones_completadas WHERE id_usuario=:user");
$stmt2->bindParam(':user', $user_id);
$stmt2->execute();
$completadas = $stmt2->fetchAll(PDO::FETCH_COLUMN);

// Progreso de lecciones del usuario
$stmt3 = $conn->prepare("SELECT id_leccion FROM progreso WHERE id_usuario=:user AND completado=1");
$stmt3->bindParam(':user', $user_id);
$stmt3->execute();
$lecciones_completadas = $stmt3->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Misiones Diarias - BrainPlay</title>
    <?php include __DIR__ . '/../includes/cdns.php'; ?>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: radial-gradient(circle at top, #1a001f, #000);
            color: #fff;
            font-family: 'Public Sans', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .misiones-section {
            text-align: center;
            padding: 4rem 1rem 6rem;
        }

        .titulo {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
            margin-top: 50px;
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            color: #a259ff;
        }

        .subtitulo {
            color: #ffffffff;
            margin-bottom: 2rem;
        }

        .misiones-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .mision-card {
            background: linear-gradient(145deg, #1a001f, #2b0040);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .mision-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 6px 18px rgba(120, 0, 255, 0.5);
        }

        .mision-card.completada {
            border: 2px solid #6a0dad;
            background: linear-gradient(145deg, #23004a, #14002b5b);
        }

        .mision-icon {
            font-size: 2rem;
            color: #6a0dad;
            margin-bottom: 1rem;
        }

        .mision-info h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        .mision-info p {
            font-size: 0.95rem;
            color: #cfcfcf;
            margin-bottom: 0.5rem;
        }

        .puntos {
            display: inline-block;
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            color: #c9a4ff;
            margin-bottom: 1rem;
        }

        .btn-mision {
            background: #6a0dad;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.6rem 1.5rem;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-mision i {
            font-size: 1rem;
        }

        .btn-mision:hover {
            background: #8e24ff;
            transform: translateY(-2px);
        }

        .btn-mision:disabled {
            background: #333;
            color: #aaa;
            cursor: not-allowed;
        }
      
       .swal-game {
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

        @media (max-width: 600px) {
            .titulo {
                font-size: 1.7rem;
                margin-top: 10px;
            }

            .mision-card {
                padding: 1rem;
            }

            .btn-mision {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="misiones-section">
        <h1 class="titulo">Misiones Diarias</h1>
        <p class="subtitulo">Completa las misiones y gana puntos extra</p>
        <p><strong>Puntos disponibles:</strong> <span class="puntos"><?= $user['puntos'] ?></span></p>

        <div class="misiones-grid">
            <?php foreach ($misiones as $m):
                $completada = in_array($m['id'], $completadas);
                $leccion_completada = !empty($m['id_leccion']) && in_array($m['id_leccion'], $lecciones_completadas);
            ?>
                <div class="mision-card <?= $completada ? 'completada' : '' ?>">
                    <div class="mision-icon">
                        <i class="fa-solid fa-flag"></i>
                    </div>
                    <div class="mision-info">
                        <h3><?= htmlspecialchars($m['nombre']) ?></h3>
                        <p><?= htmlspecialchars($m['descripcion']) ?></p>
                        <span class="puntos">Puntos: <?= $m['puntos'] ?></span>
                    </div>

                    <?php if (!empty($m['id_leccion'])): ?>
                        <?php if ($leccion_completada && !$completada): ?>
                            <button class="btn-mision" data-id="<?= $m['id'] ?>"><i class="fa-solid fa-gift"></i> Reclamar</button>
                        <?php elseif ($completada): ?>
                            <button class="btn-mision" disabled><i class="fa-solid fa-check"></i> Completada</button>
                        <?php else: ?>
                            <a class="btn-mision" href="leccion.php?id=<?= $m['id_leccion'] ?>"><i class="fa-solid fa-play"></i> Iniciar Lección</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="btn-mision" data-id="<?= $m['id'] ?>" <?= $completada ? 'disabled' : '' ?>>
                            <i class="fa-solid fa-rocket"></i> <?= $completada ? 'Completada' : 'Iniciar' ?>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script>
        document.querySelectorAll('.btn-mision').forEach(btn => {
            if (btn.tagName !== 'BUTTON') return;

            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                fetch('Game/completarMision.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'leccion_id=' + id
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            btn.innerHTML = '<i class="fa-solid fa-check"></i> Completada';
                            btn.disabled = true;

                            const puntosElem = document.querySelector('.puntos');
                            puntosElem.textContent = parseInt(puntosElem.textContent) + parseInt(data.puntos_ganados);

                            Swal.fire({
                                title: '¡Felicidades!',
                                html: `
      <div style="
        font-family: 'Orbitron', sans-serif;
        color: #fff;
        font-size: 1.2rem;
        text-align: center;
        
        padding: 20px;
        border-radius: 12px;
      ">
        Has completado la misión y ganado <span style="color:#c9a4ff; font-weight:700;">${data.puntos_ganados} puntos</span>!
      </div>
    `,
                                        background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',

                                showConfirmButton: true,
                                confirmButtonText: '¡Genial!',
                                confirmButtonColor: '#6a0dad',
                                icon: 'success',
                                iconColor: '#c9a4ff',
                                 customClass: {
                                            popup: 'swal-game',
                                            confirmButton: 'swal-game-btn'
                                        },
                                        showClass: {
                                            popup: 'animate__animated animate__fadeInDown'
                                        },
                                        hideClass: {
                                            popup: 'animate__animated animate__fadeOutUp'
                                        }
                            });

                        } else {
                            Swal.fire('Error', data.mensaje, 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'No se pudo completar la misión', 'error');
                        console.error(err);
                    });
            });
        });
    </script>
</body>

</html>