<?php
// Detectar archivo actual
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
  <div class="nav-left">
    <h1 class="nav-logo">
      <i class="fa-solid fa-bolt"></i> <span>BrainPlay</span>
    </h1>
  </div>

  <div class="nav-right">
    <ul class="nav-menu">
      <li>
        <a href="welcome.php" class="<?= $current_page == 'welcome.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-house"></i><span>Inicio</span>
        </a>
      </li>
      <li>
        <a href="lecciones.php" class="<?= $current_page == 'lecciones.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-gamepad"></i><span>Jugar</span>
        </a>
      </li>
      <li>
        <a href="ranking.php" class="<?= $current_page == 'ranking.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-trophy"></i><span>Ranking</span>
        </a>
      </li>
      <li>
        <a href="profile.php" class="<?= $current_page == 'profile.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-user"></i><span>Perfil</span>
        </a>
      </li>
    </ul>
  </div>
</nav>
