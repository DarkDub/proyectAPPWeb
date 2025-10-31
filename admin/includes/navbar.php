<!-- admin/navbar.php -->
<?php
// Detectar el nombre del archivo actual
$archivo_actual = basename($_SERVER['PHP_SELF']);
?>



<nav class="sidebar">
  <div class="logo">
    <i class='bx bx-rocket'></i>
    <span>GameLearn</span>
  </div>
  <ul class="nav-links">
    <li><a href="index.php" class="<?= ($archivo_actual == 'index.php') ? 'active' : '' ?>"><i class='bx bx-home'></i> Dashboard</a></li>
    <li><a href="usuarios.php" class="<?= ($archivo_actual == 'usuarios.php') ? 'active' : '' ?>"><i class='bx bx-user'></i> Usuarios</a></li>
    <li><a href="lecciones.php" class="<?= ($archivo_actual == 'lecciones.php') ? 'active' : '' ?>"><i class='bx bx-book'></i> Lecciones</a></li>
    <li>
      <a href="preguntas.php" class="<?= ($archivo_actual == 'preguntas.php') ? 'active' : '' ?>">
        <i class='bx bx-question-mark'></i> Preguntas
      </a>
    </li>
    <li><a href="misiones.php" class="<?= ($archivo_actual == 'misiones.php') ? 'active' : '' ?>"><i class='bx bx-target-lock'></i> Misiones</a></li>
    <li>
  <a href="shop.php" class="<?= ($archivo_actual == 'shop.php') ? 'active' : '' ?>">
    <i class='bx bx-store'></i> Tienda
  </a>
</li>



    <li><a href="#" class="<?= ($archivo_actual == 'ajustes.php') ? 'active' : '' ?>"><i class='bx bx-cog'></i> Ajustes</a></li>

    <!-- BOTÓN CERRAR SESIÓN (arriba del footer) -->
    <li style="margin-top: 1.6rem;">
      <a href="logout.php" id="btnLogout">
        <i class='bx bx-log-out'></i> Cerrar sesión
      </a>
    </li>
  </ul>
</nav>

<style>
  /* (aquí va tu CSS existente...) */
  @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap');

  #swal2-title {
    color: white !important;
  }

  /* Estilo específico para el logout (sutil, distintivo) */
  .sidebar .nav-links a#btnLogout {
    background: linear-gradient(90deg, rgba(255, 0, 100, 0.08), rgba(159, 98, 255, 0.04));
    color: #ffdede;
    border: 1px solid rgba(255, 255, 255, 0.03);
    margin-top: 170px;
  }

  .sidebar .nav-links a#btnLogout i {
    color: #ff7b7b;
  }

  .sidebar .nav-links a#btnLogout:hover {
    background: linear-gradient(90deg, #ff3b6b, #9f62ff);
    color: #fff;
    transform: translateX(6px);
    box-shadow: 0 0 12px rgba(255, 59, 107, 0.25);
  }

  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 240px;
    background: radial-gradient(circle at top left, #2b0056 0%, #0e001a 100%);
    box-shadow: 6px 0 25px rgba(0, 0, 0, 0.4);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 1rem;
    font-family: 'Orbitron', sans-serif;
    border-right: 1px solid rgba(255, 255, 255, 0.08);
    z-index: 10;
  }

  /* LOGO */
  .sidebar .logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.4rem;
    font-weight: 700;
    color: #00ffb3;
    text-shadow: 0 0 8px rgba(200, 156, 255, 0.8);
    margin-bottom: 2rem;
  }

  .sidebar .logo i {
    font-size: 1.8rem;
    color: #00ffb3;
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.4);
    animation: floatIcon 2s ease-in-out infinite;
  }

  @keyframes floatIcon {

    0%,
    100% {
      transform: translateY(0);
    }

    50% {
      transform: translateY(-3px);
    }
  }

  /* LINKS */
  .sidebar .nav-links {
    list-style: none;
    padding: 0;
    width: 100%;
  }

  .sidebar .nav-links li {
    width: 100%;
  }

  .sidebar .nav-links a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #ffffffff;
    text-decoration: none;
    transition: all 0.25s ease;
    font-weight: 600;
    border-radius: 10px;
    margin: 5px 12px;
    letter-spacing: 0.5px;
  }

  .sidebar .nav-links a i {
    margin-right: 10px;
    font-size: 1.3rem;
    color: #00ffb3;
    transition: color 0.25s ease, transform 0.25s ease;
  }

  .sidebar .nav-links a:hover,
  .sidebar .nav-links a.active {
    background: linear-gradient(90deg, #4c00b0, #9f62ff);
    color: #fff;
    transform: translateX(6px);
    box-shadow: 0 0 12px rgba(159, 98, 255, 0.5);
  }

  .sidebar .nav-links a:hover i,
  .sidebar .nav-links a.active i {
    color: #fff;
    transform: scale(1.1);
  }

  /* SCROLL COOL */
  .sidebar::-webkit-scrollbar {
    width: 6px;
  }

  .sidebar::-webkit-scrollbar-thumb {
    background: #5a1bd6;
    border-radius: 4px;
  }
</style>

<!-- Confirmación con SweetAlert2 (opcional) -->
<!-- Incluye SweetAlert2 si no lo tienes: -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.getElementById('btnLogout').addEventListener('click', function(e) {
    e.preventDefault();
    const href = this.getAttribute('href');

    // SweetAlert confirmation
    Swal.fire({
      title: '¿Cerrar sesión?',
      text: 'Se cerrará la sesión actual.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, cerrar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#a259ff',
      background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
      color: '#fff'
    }).then((res) => {
      if (res.isConfirmed) {
        // redirigir al script que destruye la sesión
        window.location.href = href;
      }
    });
  });
</script>