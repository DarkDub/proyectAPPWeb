<?php
include __DIR__ . '/../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

// Obtener datos
$totalUsuarios = $conn->query("SELECT COUNT(*) FROM usuarios WHERE estado = 'A'")->fetchColumn();
$totalLecciones = $conn->query("SELECT COUNT(*) FROM leccion_teoria WHERE estado = 'A'")->fetchColumn();
$totalMisiones = $conn->query("SELECT COUNT(*) FROM misiones_diarias")->fetchColumn();
$totalCompletadas = $conn->query("SELECT COUNT(*) FROM progreso WHERE completado = 1")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin | GameLearn</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
     <link rel="icon" type="image/x-icon" href="../public/uploads/icon-page.png">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --bg-dark: #0f021e;
      --bg-glass: rgba(255, 255, 255, 0.08);
      --primary: #9b5cff;
      --secondary: #00ffb3;
      --text-light: #e6e6e6;
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: radial-gradient(circle at top left, #1a0033, #0a0014);
      color: var(--text-light);
      display: flex;
      min-height: 100vh;
    }

    main {
      margin-left: 240px;
      width: calc(100% - 240px);
      padding: 40px;
      backdrop-filter: blur(10px);
    }

    h2 {
      font-weight: 700;
      font-size: 2rem;
      background: linear-gradient(90deg, var(--secondary), var(--primary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 5px;
    }

    .Admin-index-header {
      display: flex;
      justify-content: space-between;
    }
    .Admin-index-header img{
      width: 40px;
      height: 40px;
      border-radius: 100%;
    }
    .name-header-profile {
      display: flex;
      align-items: center;
    }
    .name-header-profile h3{
      font-size: 1rem;
      margin: 0px 10px;      
    }
    p {
      color: #b3b3b3;
    }

    /* Tarjetas */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
      margin-top: 35px;
    }

    .card-stat {
      background: var(--bg-glass);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 25px;
      text-align: left;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .card-stat::before {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at top left, rgba(255, 255, 255, 0.1), transparent 60%);
      transform: rotate(25deg);
      opacity: 0;
      transition: opacity 0.4s ease;
    }

    .card-stat:hover::before {
      opacity: 1;
    }

    .card-stat:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 35px rgba(0, 0, 0, 0.6);
    }

    .card-stat i {
      font-size: 2.5rem;
      color: var(--secondary);
      margin-bottom: 15px;
    }

    .stat-number {
      font-size: 2.2rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 5px;
    }

    .stat-label {
      color: #c8b8ff;
      font-size: 0.95rem;
    }

    /* Chart area */
    .chart-box {
      margin-top: 60px;
      background: var(--bg-glass);
      border-radius: 25px;
      padding: 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .chart-box h4 {
      color: #fff;
      margin-bottom: 20px;
      font-weight: 600;
    }

    canvas {
      width: 100% !important;
      height: 360px !important;
    }

    /* Animación */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card-stat {
      animation: fadeIn 0.8s ease forwards;
    }
  </style>
</head>

<body>

  <?php include __DIR__ . '/../admin/includes/navbar.php'; ?>

  <main>
    <div class="Admin-index-header">
      <h2>Panel General</h2>
      <div class="name-header-profile">
      <h3><?= htmlspecialchars($_SESSION['user_name']) ?></h3>
      <img src="../public/img/default.png" alt="perfl">

      </div>
    </div>


    <p>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋 | Estadísticas del sistema</p>

    <div class="stats-grid">
      <div class="card-stat">
        <i class='bx bx-user-circle'></i>
        <div class="stat-number"><?= $totalUsuarios ?></div>
        <div class="stat-label">Usuarios Registrados</div>
      </div>

      <div class="card-stat">
        <i class='bx bx-book-open'></i>
        <div class="stat-number"><?= $totalLecciones ?></div>
        <div class="stat-label">Lecciones Creadas</div>
      </div>

      <div class="card-stat">
        <i class='bx bx-target-lock'></i>
        <div class="stat-number"><?= $totalMisiones ?></div>
        <div class="stat-label">Misiones Disponibles</div>
      </div>

      <div class="card-stat">
        <i class='bx bx-trophy'></i>
        <div class="stat-number"><?= $totalCompletadas ?></div>
        <div class="stat-label">Lecciones Completadas</div>
      </div>
    </div>

    <div class="chart-box mt-5">
      <h4>Actividad General</h4>
      <canvas id="statsChart"></canvas>
    </div>
  </main>

  <script>
    const ctx = document.getElementById('statsChart').getContext('2d');
    const statsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Usuarios', 'Lecciones', 'Misiones', 'Completadas'],
        datasets: [{
          label: 'Totales',
          data: [<?= $totalUsuarios ?>, <?= $totalLecciones ?>, <?= $totalMisiones ?>, <?= $totalCompletadas ?>],
          fill: true,
          borderColor: '#9b5cff',
          backgroundColor: 'rgba(155, 92, 255, 0.1)',
          tension: 0.4,
          pointBackgroundColor: '#00ffb3',
          pointBorderColor: '#fff',
          pointRadius: 6,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          x: {
            ticks: {
              color: '#ccc'
            },
            grid: {
              color: 'rgba(255,255,255,0.05)'
            }
          },
          y: {
            beginAtZero: true,
            ticks: {
              color: '#ccc'
            },
            grid: {
              color: 'rgba(255,255,255,0.05)'
            }
          }
        }
      }
    });
  </script>

</body>

</html>