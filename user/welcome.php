<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../user/login.php");
  exit;
}
require_once __DIR__ . '/../config/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT nombre, puntos, nivel_actual FROM usuarios WHERE id_usuario = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  session_destroy();
  header("Location: ../user/login.php");
  exit;
}


// Capturamos alerta  
$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']); // Limpiar después de mostrar
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BrainPlay | Inicio</title>

  <!-- Fuentes y CDN -->
  <?php include __DIR__ . '/../includes/cdns.php' ?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../public/css/global.css">
  <link rel="stylesheet" href="../public/css/user/panel.css">

</head>

<body>

  <?php include __DIR__ . '/../includes/navbar.php'; ?>

  <main class="panel-container">

    <!-- Bienvenida y estadísticas -->
    <section class="welcome-section">
      <h1>Bienvenido, <?= htmlspecialchars($user['nombre']) ?>!</h1>
      <p>Nivel <?= $user['nivel_actual'] ?> | Puntos: <?= $user['puntos'] ?></p>
      <p>Tu centro de entrenamiento en inglés. Elige un modo y comienza a jugar.</p>
    </section>

    <!-- Opciones / Cards -->
    <section class="options-section">
      <div class="option-card play" onclick="location.href='lecciones.php'">
        <i class="fa-solid fa-rocket"></i>
        <h2>Jugar</h2>
        <p>Responde preguntas, gana puntos y sube de nivel.</p>
      </div>
      <div class="option-card challenge" onclick="location.href='misiones.php'">
        <i class="fa-solid fa-stopwatch"></i>
        <h2>Misiones Diarias</h2>
        <p>Modo Mision para verdaderos maestros del inglés.</p>
      </div>
      <div class="option-card ranking" onclick="location.href='ranking.php'">
        <i class="fa-solid fa-ranking-star"></i>
        <h2>Ranking</h2>
        <p>Compite y escala posiciones en la tabla global.</p>
      </div>
      <div class="option-card shop" onclick="location.href='shop.php'">
        <i class="fa-solid fa-store"></i>
        <h2>Tienda</h2>
        <p>Canjea tus puntos por recompensas exclusivas.</p>
      </div>
    </section>

  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <?php if ($alert): ?>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
          title: '¡Welcome!',
          titleColor: '#fff',
          html: `<div style="font-family: 'Orbitron', sans-serif; color:#fff; font-size:1.2rem; letter-spacing:1px;">
            <?= htmlspecialchars($alert['mensaje'], ENT_QUOTES) ?>
           </div>`,
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
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.reload(); // O redirigir a otra página si quieres
          }
        });
      });
    </script>
  <?php endif; ?>

</body>

</html>