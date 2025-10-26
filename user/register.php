<?php
session_start();

// Si ya está logueado, redirigir a welcome
if (isset($_SESSION['user_id'])){
    header("Location: welcome.php");
    exit;
}


include __DIR__ . '/../config/db.php';

$mensaje = '';
$tipo = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);

    try {
        $stmt->execute();
        $mensaje = "¡Tu cuenta ha sido creada con éxito!";
        $tipo = 'success';
    } catch (PDOException $e) {
        $mensaje = "Hubo un error al registrarte";
        $tipo = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameLearn | Registro</title>

  <link rel="stylesheet" href="../public/css/Auth.css">
  <link rel="stylesheet" href="../public/css/global.css">
  <?php include __DIR__ . '/../includes/cdns.php'; ?>


  <style>
    body.swal2-height-auto {
      height: 100vh !important;
    }


    /* Estilo gamer para el popup */
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
  <div class="lights">
    <div class="light"></div>
    <div class="light"></div>
    <div class="light"></div>
  </div>

  <div class="game-box">
    <div class="logo">🎮 GameLearn</div>
    <div class="subtitle">Learn English with Fun</div>

    <form action="" method="POST">
      <input type="text" placeholder="Nombre" name="name" required>
      <input type="email" placeholder="Correo electrónico" name="email" required>
      <input type="password" placeholder="Contraseña" name="password" required>
      <button type="submit" class="btn" id="btnPlay">Registrarme</button>
    </form>

    <div class="extra">
      ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
    </div>
  </div>

  <?php if (!empty($tipo)) : ?>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      Swal.fire({
        title: '<?= $tipo === "success" ? "LEVEL UP! 🚀" : "ERROR 404" ?>',
        html: `
          <div style="
            font-family: 'Orbitron', sans-serif;
            color: #fff;
            font-size: 1.2rem;
            letter-spacing: 1px;
          ">
            <?= $mensaje ?>
          </div>
        `,
        background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
        color: '#fff',
        icon: '<?= $tipo ?>',
        iconColor: '<?= $tipo === "success" ? "#a259ff" : "#ff4f4f" ?>',
        showConfirmButton: true,
        confirmButtonText: 'Continuar',
        confirmButtonColor: '#a259ff',
        customClass: {
          popup: 'swal-game',
          confirmButton: 'swal-game-btn'
        },
        willClose: () => {
          <?php if ($tipo === 'success'): ?>
            window.location.href = "login.php";
          <?php endif; ?>
          <?php if ($tipo === 'error'): ?>
            window.location.href = "register.php";
          <?php endif; ?>
        }
      });
    });
  </script>
  <?php endif; ?>
</body>
</html>
