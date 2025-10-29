<?php
session_start();

// Si ya está logueado, redirigir según el rol
if (isset($_SESSION['user_id']) && isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'admin') {
        header("Location: ../admin/index.php");
        exit;
    } else {
        header("Location: ../user/welcome.php");
        exit;
    }
}

require_once __DIR__ . '/../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login exitoso
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['rol'] = $user['rol']; // 🔥 Guardamos el rol

            $_SESSION['alert'] = [
                'tipo' => 'success',
                'mensaje' => "¡Bienvenido de nuevo, {$user['nombre']}!"
            ];

            // Redirección según el rol
            if ($user['rol'] === 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../user/welcome.php");
            }
            exit;
        } else {
            $_SESSION['alert'] = [
                'tipo' => 'error',
                'mensaje' => "Email o contraseña incorrectos"
            ];
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['alert'] = [
            'tipo' => 'error',
            'mensaje' => "Error en la base de datos"
        ];
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameLearn | Login</title>

    <link rel="stylesheet" href="../public/css/Auth.css">
    <?php include __DIR__ . '/../includes/cdns.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body.swal2-height-auto {
            height: 100vh !important;
            overflow: hidden !important;
        }

        .swal2-container {
            z-index: 99999 !important;
        }

        .swal-game {
            border: 2px solid #a259ff;
            box-shadow: 0 0 30px #a259ff80;
            border-radius: 20px;
            animation: glow 1.5s infinite alternate;
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
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit" class="btn" id="btnPlay">Play</button>
        </form>

        <div class="extra">
            ¿Nuevo jugador? <a href="register.php">Regístrate</a>
        </div>
    </div>

    <?php
    $alert = $_SESSION['alert'] ?? null;
    unset($_SESSION['alert']);
    ?>

    <?php if ($alert): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    title: '<?= $alert['tipo'] === "success" ? "LEVEL UP! 🚀" : "ERROR" ?>',
                    html: `
      <div style="font-family: 'Orbitron', sans-serif; color: #fff; font-size:1.2rem; text-align:center;">
        <?= $alert['mensaje'] ?>
      </div>`,
                    background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
                    color: '#fff',
                    icon: '<?= $alert['tipo'] ?>',
                    iconColor: '<?= $alert['tipo'] === "success" ? "#a259ff" : "#ff4f4f" ?>',
                    showConfirmButton: true,
                    confirmButtonText: 'Continuar',
                    confirmButtonColor: '#a259ff',
                    customClass: {
                        popup: 'swal-game',
                        confirmButton: 'swal-game-btn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        <?php if ($alert['tipo'] === 'success'): ?>
                            // Redirigir según el rol
                            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                                window.location.href = "../admin/dashboard.php";
                            <?php else: ?>
                                window.location.href = "../user/welcome.php";
                            <?php endif; ?>
                        <?php else: ?>
                            window.location.href = "login.php";
                        <?php endif; ?>
                    }
                });
            });
        </script>
    <?php endif; ?>
</body>
</html>
