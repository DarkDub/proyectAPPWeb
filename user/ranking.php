<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';
$user_id = $_SESSION['user_id'];

// Consultar top 10 ranking
$stmt = $conn->prepare("
  SELECT id_usuario, nombre, puntos, avatar
  FROM usuarios
  ORDER BY puntos DESC
  LIMIT 10
");
$stmt->execute();
$ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ranking - BrainPlay</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php include __DIR__ . '/../includes/cdns.php'; ?>

  <style>
    body {
      background: radial-gradient(circle at top, #1a001f, #000);
      color: #fff;
      font-family: 'Public Sans', sans-serif;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }

    .ranking-section {
      max-width: 850px;
      margin: 50px auto;
      padding: 0 20px;
      margin-top: 100px;
    }

    .ranking-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .ranking-header h1 {
      font-family: 'Orbitron', sans-serif;
      font-size: 2.4rem;
      color: #c9a4ff;
      margin: 0;
    }

    .ranking-header p {
      color: #aaa;
      margin: 10px 0 0;
    }

    .ranking-table {
      width: 100%;
      border-collapse: collapse;
    }

    .ranking-table th, .ranking-table td {
      padding: 12px 15px;
      text-align: left;
    }

    .ranking-table th {
      font-size: 0.9rem;
      color: #c9a4ff;
      text-transform: uppercase;
      letter-spacing: 1px;
      border-bottom: 1px solid rgba(255,255,255,0.15);
    }

    .ranking-table tbody tr {
      background: linear-gradient(145deg, #1a001f, #2b0040);
      margin-bottom: 10px;
      border-radius: 12px;
      transition: transform 0.2s, background 0.2s;
    }

    .ranking-table tbody tr:hover {
      background: linear-gradient(145deg, #23004a, #2d0055);
      transform: translateY(-2px);
    }

    .pos {
      width: 50px;
      font-weight: bold;
      text-align: center;
    }

    .pos.first { color: gold; }
    .pos.second { color: silver; }
    .pos.third { color: #cd7f32; }

    .user-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .avatar {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #c9a4ff;
      box-shadow: 0 0 10px rgba(200, 150, 255, 0.3);
    }

    .user-name {
      font-weight: 600;
      color: #fff;
    }

    .user-points {
      font-weight: 600;
      color: #c9a4ff;
      text-align: right;
    }

    @media (max-width: 600px) {
      .ranking-header h1 {
        font-size: 1.8rem;
      }
      .ranking-table th, .ranking-table td {
        padding: 10px 8px;
      }
      .avatar {
        width: 38px;
        height: 38px;
      }
      .ranking-header{
      margin-bottom: 70px;

      }
    }
  </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

  <div class="ranking-section">
    <div class="ranking-header">
      <h1><i class="fa-solid fa-trophy"></i> Ranking Global</h1>
      <p>Los mejores jugadores por puntos</p>
    </div>

    <table class="ranking-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Jugador</th>
          <th>Puntos</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $pos = 1;
        foreach($ranking as $row):
          $avatar = !empty($row['avatar']) 
            ? '../uploads/avatars/' . htmlspecialchars($row['avatar'])
            : 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; // avatar por defecto
        ?>
          <tr>
            <td class="pos <?= $pos == 1 ? 'first' : ($pos == 2 ? 'second' : ($pos == 3 ? 'third' : '')) ?>">
              <?= $pos ?>
            </td>
            <td>
              <div class="user-info">
                <img src="<?= $avatar ?>" alt="Avatar" class="avatar">
                <span class="user-name"><?= htmlspecialchars($row['nombre']) ?></span>
              </div>
            </td>
            <td class="user-points"><?= htmlspecialchars($row['puntos']) ?></td>
          </tr>
        <?php 
        $pos++;
        endforeach; ?>
      </tbody>
    </table>
  </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
