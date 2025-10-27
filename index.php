<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BrainPlay</title> <!-- Nombre estilo “game” -->
  <link rel="stylesheet" href="public/css/index.css">
  <?php include 'includes/cdns.php'; ?>
</head>
<body>
  <div class="background">
    <div class="bubble" style="left:10%; animation-delay:0s;"></div>
    <div class="bubble" style="left:30%; animation-delay:2s; width:60px; height:60px;"></div>
    <div class="bubble" style="left:50%; animation-delay:4s;"></div>
    <div class="bubble" style="left:70%; animation-delay:1s; width:25px; height:25px;"></div>
    <div class="bubble" style="left:90%; animation-delay:3s; width:50px; height:50px;"></div>
  </div>

  <div style="z-index: 2; text-align:center;">
    <div class="logo">BrainPlay</div>
    <h2 class="subtitle">Aprende inglés jugando</h2>

    <a href="user/login.php" class="btn-play">Jugar ahora</a>
  </div>

  <div class="footer">© 2025 BrainPlay - Proyecto educativo</div>
</body>
</html>
