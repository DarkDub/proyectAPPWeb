<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

$user_id = $_SESSION['user_id'];

// Obtener datos del usuario
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: ../user/login.php");
    exit;
}

// Últimas actividades
$activities = $conn->prepare("SELECT descripcion, tipo, fecha FROM actividad WHERE id_usuario = :id ORDER BY fecha DESC LIMIT 5");
$activities->bindParam(':id', $user_id, PDO::PARAM_INT);
$activities->execute();
$actividades = $activities->fetchAll(PDO::FETCH_ASSOC);


// Obtener palabras aprendidas
$palabrasAprendidas = $conn->prepare("SELECT palabra FROM palabras_aprendidas WHERE id_usuario = :id ORDER BY fecha_aprendida DESC");
$palabrasAprendidas->bindParam(':id', $user_id, PDO::PARAM_INT);
$palabrasAprendidas->execute();
$palabras = $palabrasAprendidas->fetchAll(PDO::FETCH_COLUMN);
$user['palabras_aprendidas_count'] = count($palabras);



$lecciones = $conn->prepare("SELECT id_leccion, puntos_obtenidos FROM progreso WHERE id_usuario = :id ORDER BY fecha DESC");
$lecciones->bindParam(':id', $user_id, PDO::PARAM_INT);
$lecciones->execute();
$leccionesAprendidas = $lecciones->fetchAll(PDO::FETCH_COLUMN);
$user['lecciones_completadas'] = count($leccionesAprendidas);



// $lecciones = $conn->prepare("
//     SELECT 
//         p.id_leccion,
//         l.titulo AS nombre_leccion,
//         p.puntos_obtenidos
//     FROM progreso p
//     INNER JOIN lecciones l ON p.id_leccion = l.id_leccion
//     WHERE p.id_usuario = :id
//     ORDER BY p.fecha DESC
// ");
// $lecciones->bindParam(':id', $user_id, PDO::PARAM_INT);
// $lecciones->execute();
// Capturamos alerta
$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - BrainPlay</title>

    <?php include __DIR__ . '/../includes/cdns.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/global.css">
    <link rel="stylesheet" href="../public/css/user/profile.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="dashboard-container">

        <!-- Perfil del usuario -->
        <div class="profile-card">
            <div class="profile-avatar">
                <img src="../public/img/perfil.jpg" alt="Avatar Usuario">
            </div>
            <div class="profile-details">
                <h1><?= htmlspecialchars($user['nombre']) ?></h1>
                <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
                <p><i class="fas fa-star"></i> Puntos: <?= htmlspecialchars($user['puntos']) ?></p>
                <p><i class="fas fa-layer-group"></i> Nivel: <?= htmlspecialchars($user['nivel_actual']) ?></p>
            </div>
            <?php
            $nivel = $user['nivel_actual_gramatica'];
            $puntos = $user['puntos'];
            $metas = ['A1' => 1000, 'A2' => 3000, 'B1' => 6000, 'B2' => 10000, 'C1' => 15000, 'C2' => 20000];
            $meta = $metas[$nivel];
            $porcentaje = min(($puntos / $meta) * 100, 100);
            ?>
            <div class="profile-actions">
                <div class="level-progress">
                    <p>Progreso al siguiente nivel (<?= $nivel ?>)</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $porcentaje ?>%;"></div>
                    </div>
                    <small><?= $puntos ?>/<?= $meta ?> puntos</small>
                </div>
                <form action="logout.php" method="post">
                    <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
                </form>
            </div>

        </div>


        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card" id="leccionCard" style="cursor:pointer;">
                <i class="fas fa-book fa-2x"></i>
                <h2><?= $user['lecciones_completadas'] ?></h2>
                <p>Lecciones completadas</p>
            </div>
            <div class="stat-card" id="palabrasCard" style="cursor:pointer;">
                <i class="fas fa-language fa-2x"></i>
                <h2><?= $user['palabras_aprendidas_count'] ?></h2>
                <p>Palabras aprendidas</p>
            </div>

            <div class="stat-card">
                <i class="fas fa-trophy fa-2x"></i>
                <h2><?= $user['retos_completados'] ?></h2>
                <p>Retos completados</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-layer-group fa-2x"></i>
                <h2><?= $user['nivel_actual'] ?></h2>
                <p>Nivel actual</p>
            </div>
        </div>

        <!-- Actividades recientes -->
        <div class="activity-list">
            <h2>Últimas actividades</h2>
            <?php if ($actividades): ?>
                <?php foreach ($actividades as $act): ?>
                    <div class="activity-item">
                        <span><?= htmlspecialchars($act['descripcion']) ?></span>
                        <span><?= date('d/m/Y H:i', strtotime($act['fecha'])) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay actividades recientes.</p>
            <?php endif; ?>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="palabrasModal" tabindex="-1" aria-labelledby="palabrasModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="palabrasModalLabel">Palabras aprendidas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <ul id="palabrasList">
                            <?php foreach ($palabras as $palabra): ?>
                                <li><?= htmlspecialchars($palabra) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="leccionModal" tabindex="-1" aria-labelledby="leccionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="LeccionModalLabel">Lecciones aprendidas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <ul id="palabrasList">
                            <?php foreach ($leccionesAprendidas as $leccion): ?>
                                <li><?= htmlspecialchars($leccion) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <?php if ($alert): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    title: '¡Alerta!',
                    html: `<div style="font-family: 'Orbitron', sans-serif; color:#fff; font-size:1.2rem; letter-spacing:1px;"><?= htmlspecialchars($alert['mensaje'], ENT_QUOTES) ?></div>`,
                    background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
                    icon: '<?= $alert['tipo'] ?>',
                    iconColor: '<?= $alert['tipo'] === "success" ? "#a259ff" : "#ff4f4f" ?>',
                    showConfirmButton: true,
                    confirmButtonText: 'Continuar',
                    confirmButtonColor: '#a259ff',
                    customClass: {
                        popup: 'swal-game',
                        confirmButton: 'swal-game-btn'
                    },
                });
            });
        </script>
    <?php endif; ?>
    <script>
        const palabrasCard = document.getElementById('palabrasCard');
        const palabrasModal = new bootstrap.Modal(document.getElementById('palabrasModal'));
        const leccionCard = document.getElementById('leccionCard');
        const leccionModal = new bootstrap.Modal(document.getElementById('leccionModal'));
        palabrasCard.addEventListener('click', () => {
            palabrasModal.show();
        });
        leccionCard.addEventListener('click', () => {
            leccionModal.show();
        });
    </script>

</body>

</html>