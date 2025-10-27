<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';
$user_id = $_SESSION['user_id'];

// Obtener lecciones
$stmt = $conn->prepare("SELECT id_leccion, titulo, descripcion FROM lecciones ORDER BY id_leccion ASC");
$stmt->execute();
$lecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener progreso del usuario
$stmt2 = $conn->prepare("SELECT id_leccion, completado FROM progreso WHERE id_usuario = :id");
$stmt2->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt2->execute();
$progreso = $stmt2->fetchAll(PDO::FETCH_KEY_PAIR); // [id_leccion => completado]
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lecciones - BrainPlay</title>
  <?php include __DIR__ . '/../includes/cdns.php'; ?>
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../public/css/user/lecciones.css">
</head>
<body>

  <?php include __DIR__ . '/../includes/navbar.php'; ?>

  <section class="lecciones-section">
    <h1 class="titulo">Lecciones Disponibles</h1>
    <p class="subtitulo">Avanza paso a paso y mejora tu inglés</p>

    <div class="lecciones-grid">
      <?php foreach ($lecciones as $leccion): 
        $id = $leccion['id_leccion'];
        $completada = isset($progreso[$id]) && $progreso[$id] == 1;
      ?>
        <div class="leccion-card <?= $completada ? 'completada' : '' ?>">
          <div class="leccion-icon">
            <i class="fas <?= $completada ? 'fa-check-circle' : 'fa-book' ?>"></i>
          </div>
          <div class="leccion-info">
            <h2><?= htmlspecialchars($leccion['titulo']) ?></h2>
            <p><?= htmlspecialchars($leccion['descripcion']) ?></p>
          </div>
          <button 
            class="btn-comenzar" 
            onclick="window.location.href='leccionTeoria.php?id=<?= $id ?>'">
            <?= $completada ? 'Repetir' : 'Comenzar' ?>
          </button>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
