<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usuario = $_POST['usuario'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM admins WHERE usuario = ?");
  $stmt->execute([$usuario]);
  $admin = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($admin && password_verify($password, $admin['password'])) {
    $_SESSION['admin_id'] = $admin['id_admin'];
    $_SESSION['admin_usuario'] = $admin['usuario'];
    header("Location: index.php");
    exit;
  } else {
    $error = "Usuario o contraseña incorrectos.";
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - Panel Admin</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="login-container">
    <h2>Panel de Administración</h2>
    <?php if (!empty($error)): ?>
      <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="usuario" placeholder="Usuario" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <button type="submit">Ingresar</button>
    </form>
  </div>
</body>
</html>
