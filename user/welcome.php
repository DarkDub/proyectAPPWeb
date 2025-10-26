<?php
session_start();

if (!isset($_SESSION['user_id'])) {
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
<title>BrainPlay</title>

<!-- Fuentes y CDN -->
<?php include __DIR__ . '/../includes/cdns.php' ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../public/css/global.css">
<link rel="stylesheet" href="../public/css/user/panel.css">

<style>
/* Estilo alerta tipo juego */
.swal-game { 
    border: 2px solid #a259ff; 
    box-shadow: 0 0 30px #a259ff80; 
    border-radius: 20px; 
    animation: glow 1.5s infinite alternate; 
}
@keyframes glow { 
    from { box-shadow: 0 0 10px #a259ff80; } 
    to { box-shadow: 0 0 30px #a259ff; } 
}
.swal-game-btn { 
    font-family: 'Orbitron', sans-serif !important; 
    text-transform: uppercase; 
    border-radius: 12px !important; 
    padding: 10px 30px !important; 
    box-shadow: 0 0 15px #a259ff; 
    transition: 0.3s ease; 
    
}
.swal-game-btn:hover { 
    transform: scale(1.05); 
    box-shadow: 0 0 25px #a259ff; 
}
</style>
</head>
<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<main class="panel-container">
  <section class="welcome-section">
    <h1>Bienvenido a <span>BrainPlay</span></h1>
    <p>Tu centro de entrenamiento en inglés. Elige un modo y comienza a jugar.</p>
  </section>

  <section class="options-section">
    <div class="option-card play">
      <i class="fa-solid fa-rocket"></i>
      <h2>Jugar</h2>
      <p>Responde preguntas, gana puntos y sube de nivel.</p>
    </div>
    <div class="option-card challenge">
      <i class="fa-solid fa-stopwatch"></i>
      <h2>Desafío</h2>
      <p>Modo contrarreloj para verdaderos maestros del inglés.</p>
    </div>
    <div class="option-card ranking">
      <i class="fa-solid fa-ranking-star"></i>
      <h2>Ranking</h2>
      <p>Compite y escala posiciones en la tabla global.</p>
    </div>
    <div class="option-card shop">
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
    title: 'LEVEL UP! 🚀',
    html: `<div style="font-family: 'Orbitron', sans-serif; color:#fff; font-size:1.2rem; letter-spacing:1px;"><?= $alert['mensaje'] ?></div>`,
    background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
    icon: '<?= $alert['tipo'] ?>',
    iconColor: '<?= $alert['tipo'] === "success" ? "#a259ff" : "#ff4f4f" ?>',
    showConfirmButton: true,
    confirmButtonText: 'Continuar',
    confirmButtonColor: '#a259ff',
    customClass: { popup: 'swal-game', confirmButton: 'swal-game-btn' },
  });
}); 
</script>
<?php endif; ?>


</body>
</html>
