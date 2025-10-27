<?php
include __DIR__ . '/../config/db.php';
session_start();

// 🔒 Validar sesión
if (!isset($_SESSION['user_id'])) {
  header("Location: ../user/login.php");
  exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
  echo "<p>Lección no encontrada.</p>";
  exit;
}

$id_leccion = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM leccion_teoria WHERE id_leccion = :id");
$stmt->bindParam(':id', $id_leccion, PDO::PARAM_INT);
$stmt->execute();
$leccion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$leccion) {
  echo "<p>Lección no encontrada.</p>";
  exit;
}

$stmt2 = $conn->prepare("SELECT pregunta, audio FROM preguntas WHERE id_leccion = :id");
$stmt2->bindParam(':id', $id_leccion, PDO::PARAM_INT);
$stmt2->execute();
$palabras = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($leccion['titulo']); ?> | English Game</title>

  <?php include __DIR__ . '/../includes/cdns.php'; ?>
  <link rel="stylesheet" href="../public/css/user/leccionTeoria.css">
</head>
<body>
  <?php include __DIR__ . '/../includes/navbar.php'; ?>

  <section class="lesson-hero">
    <div class="lesson-info">
      <h1><?= htmlspecialchars($leccion['titulo']); ?></h1>
      <p>¡Aprende los conceptos antes de practicar!</p>
    </div>
  </section>

  <section class="lesson-container">

    <!-- Bloque de contenido -->
    <div class="lesson-card theory">
      <h2> Contenido Teórico</h2>
      <p><?= nl2br(htmlspecialchars($leccion['contenido'])); ?></p>
    </div>

    <!-- Bloque de ejemplo -->
    <?php if (!empty($leccion['ejemplo'])): ?>
    <div class="lesson-card examples">
      <h2> Ejemplo</h2>
      <p><?= nl2br(htmlspecialchars($leccion['ejemplo'])); ?></p>
    </div>
    <?php endif; ?>

    <!-- Imagen ilustrativa -->
    <?php if (!empty($leccion['imagen'])): ?>
    <div class="lesson-card image-section">
      <h2> Imagen ilustrativa</h2>
      <img src="../assets/img/teoria/<?= htmlspecialchars($leccion['imagen']); ?>" alt="Imagen de la lección">
    </div>
    <?php endif; ?>

    <!-- Video explicativo -->
    <?php if (!empty($leccion['video_url'])): ?>
    <div class="lesson-card video-section">
      <h2>🎥 Video Explicativo</h2>
      <div class="video-wrapper">
        <iframe src="<?= htmlspecialchars($leccion['video_url']); ?>" frameborder="0" allowfullscreen></iframe>
      </div>
    </div>
    <?php endif; ?>

    <!-- Vocabulario -->
    <?php if (!empty($palabras)): ?>
    <div class="lesson-card vocab">
      <h2> Palabras Clave</h2>
      <div class="vocab-grid">
        <?php foreach ($palabras as $p): ?>
          <div class="vocab-item">
            <span><?= htmlspecialchars($p['pregunta']); ?></span>
            <?php if (!empty($p['audio'])): ?>
              <button onclick="playSound('<?= htmlspecialchars($p['audio']); ?>')" class="audio-btn">🔊</button>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Botón para continuar -->
    <div class="continue-section">
      <a href="leccion.php?id=<?= $id_leccion ?>" class="btn-next">
        ¡Estoy listo para practicar! 🚀
      </a>
    </div>
  </section>

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <script>
  function playSound(file) {
    const audio = new Audio('../../assets/audios/' + file);
    audio.play();
  }
  </script>
</body>
</html>
