<?php
include __DIR__ . '/../config/db.php';
session_start();

// 🔒 Validar sesión
if (!isset($_SESSION['user_id'])) {
  header("Location: ../user/login.php");
  exit;
}

//  Obtener ID de lección
if (!isset($_GET['id']) || empty($_GET['id'])) {
  echo "<p>Lección no encontrada.</p>";
  exit;
}

$id_leccion = (int)$_GET['id'];

// Consultar lección teórica
$stmt = $conn->prepare("SELECT * FROM leccion_teoria WHERE id_leccion = :id");
$stmt->bindParam(':id', $id_leccion, PDO::PARAM_INT);
$stmt->execute();
$leccion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$leccion) {
  echo "<p>Lección no encontrada.</p>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($leccion['titulo']); ?> | English Game</title>
  <?php include __DIR__ . '/../includes/cdns.php'; ?>
  <link rel="stylesheet" href="../public/css/user/leccionTeoria.css">

  <style>
    .lesson-hero {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-family: 'Public Sans', sans-serif;
      text-align: center;
      overflow: hidden;
      border-radius: 15px;
      margin-bottom: 30px;
      background: url('../public/img/hero-background.jpg') center center / cover no-repeat;
      padding: 70px 20px;
      /* espacio interno flexible */
    }

    .hero-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, rgb(0 0 0 / 86%), rgb(0 0 0 / 83%));
      z-index: 1;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      max-width: 900px;
      margin-top: 30px;
    }

    .lesson-hero h1 {
      font-size: 2.5rem;
      font-weight: 700;
      text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
      margin-bottom: 10px;
    }

    .lesson-hero p {
      font-size: 1.2rem;
      margin-bottom: 15px;
      text-shadow: 1px 1px 6px rgba(0, 0, 0, 0.6);
    }

    .lesson-level {
      font-size: 1rem;
      font-weight: 600;
      background: rgba(255, 255, 255, 0.2);
      padding: 6px 12px;
      border-radius: 12px;
      display: inline-block;
    }

    /* Responsive */
    @media (min-width: 768px) {
      .lesson-hero {
        height: 200px;
        padding: 0 50px;
      }

      .lesson-hero h1 {
        font-size: 3rem;
      }

      .lesson-hero p {
        font-size: 1.3rem;
      }
    }

    @media (min-width: 1200px) {
      .lesson-hero {
        height: 300px;
        padding: 0 80px;
      }

      .lesson-hero h1 {
        font-size: 3.5rem;
      }

      .lesson-hero p {
        font-size: 1.5rem;
      }
    }
  </style>
  </style>
</head>

<body>
  <?php include __DIR__ . '/../includes/navbar.php'; ?>

  <section class="lesson-hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1><?= htmlspecialchars($leccion['titulo']); ?></h1>
      <p><?= htmlspecialchars($leccion['objetivo'] ?? 'Aprende inglés de forma divertida.'); ?></p>
      <span class="lesson-level">Nivel: <?= htmlspecialchars($leccion['nivel']); ?> · <?= htmlspecialchars($leccion['categoria']); ?></span>
    </div>
  </section>




  <section class="lesson-container">

    <!-- Teoría principal -->
    <div class="lesson-card theory">
      <h2> Contenido Teórico</h2>
      <p><?= nl2br(htmlspecialchars($leccion['contenido'])); ?></p>
    </div>

    <!-- Tips -->
    <?php if (!empty($leccion['tips'])): ?>
      <div class="lesson-card tips">
        <h2> Tips útiles</h2>
        <p><?= nl2br(htmlspecialchars($leccion['tips'])); ?></p>
      </div>
    <?php endif; ?>

    <!-- Frases útiles -->
    <?php if (!empty($leccion['frases_utiles'])): ?>
      <div class="lesson-card phrases">
        <h2> Frases útiles</h2>
        <ul>
          <?php
          $frases = json_decode($leccion['frases_utiles'], true);
          if (is_array($frases)):
            foreach ($frases as $frase): ?>
              <li>
                <strong><?= htmlspecialchars($frase['en']); ?></strong>
                <span><?= htmlspecialchars($frase['es']); ?></span>
              </li>
          <?php endforeach;
          endif;
          ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- Imagen -->
    <?php if (!empty($leccion['imagen'])): ?>
      <div class="lesson-card image-section">
        <h2>Imagen ilustrativa</h2>
        <img src="../public/img/<?= htmlspecialchars($leccion['imagen']); ?>" alt="Imagen de la lección">
      </div>
    <?php endif; ?>

    <!-- Audio -->
    <?php if (!empty($leccion['audio_url'])): ?>
      <div class="lesson-card audio-section">
        <h2>Escucha la explicación</h2>
        <audio controls>
          <source src="../assets/audios/<?= htmlspecialchars($leccion['audio_url']); ?>" type="audio/mp3">
          Tu navegador no soporta el audio.
        </audio>
      </div>
    <?php endif; ?>

    <!-- Video -->
    <?php if (!empty($leccion['video_url'])): ?>
      <div class="lesson-card video-section">
        <h2> Video explicativo</h2>
        <div class="video-wrapper">
          <iframe src="<?= htmlspecialchars($leccion['video_url']); ?>" allowfullscreen></iframe>
        </div>
      </div>
    <?php endif; ?>

    <!-- Ejemplo -->
    <?php if (!empty($leccion['ejemplo'])): ?>
      <div class="lesson-card examples">
        <h2> Ejemplo</h2>
        <p><?= nl2br(htmlspecialchars($leccion['ejemplo'])); ?></p>
      </div>
    <?php endif; ?>

    <!-- Palabras clave -->
    <?php if (!empty($leccion['palabras_clave'])): ?>
      <div class="lesson-card vocab">
        <h2>Palabras Clave</h2>
        <div class="vocab-grid">
          <?php
          $keywords = explode(',', $leccion['palabras_clave']);
          foreach ($keywords as $word): ?>
            <div class="vocab-item"><?= htmlspecialchars(trim($word)); ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Continuar -->
    <div class="continue-section">
      <?php if (!empty($leccion['ejercicio_recomendado'])): ?>
  <a href="leccion.php?id=<?= htmlspecialchars($leccion['ejercicio_recomendado']) ?>" class="btn-next">
    ¡Estoy listo para practicar! 🚀
  </a>
<?php else: ?>
  <button class="btn-next" disabled style="opacity: 0.6; cursor: not-allowed;">
    No hay ejercicio recomendado ❌
  </button>
<?php endif; ?>

    </div>

  </section>

  <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>