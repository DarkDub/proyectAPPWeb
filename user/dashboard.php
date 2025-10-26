<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Font: Public Sans -->
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600&display=swap" rel="stylesheet">
  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Public Sans', sans-serif;
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      background-color: #fff;
      border-right: 1px solid #dee2e6;
      padding-top: 20px;
    }
    .sidebar a {
      display: block;
      padding: 15px 20px;
      color: #333;
      text-decoration: none;
      transition: background 0.3s, color 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #0d6efd;
      color: #fff;
      border-radius: 8px;
    }
    .content {
      padding: 30px;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      transition: transform 0.2s;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .profile-img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
    }
  </style>
</head>
<body>

<div class="d-flex">
  <!-- Sidebar -->
  <div class="sidebar d-flex flex-column p-2">
    <h4 class="text-center">Mi App</h4>
    <a href="#" class="active"><i class="fas fa-home me-2"></i>Inicio</a>
    <a href="#"><i class="fas fa-user me-2"></i>Perfil</a>
    <a href="#"><i class="fas fa-cogs me-2"></i>Configuración</a>
    <a href="#" id="logoutBtn"><i class="fas fa-sign-out-alt me-2"></i>Salir</a>
  </div>

  <!-- Main Content -->
  <div class="content flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Bienvenido, <span id="username">Usuario</span></h2>
      <img src="https://via.placeholder.com/50" alt="Perfil" class="profile-img">
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="card p-3">
          <h5>Solicitudes</h5>
          <p>5 nuevas solicitudes pendientes.</p>
          <button class="btn btn-primary" onclick="showAlert('Solicitudes')">Ver más</button>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-3">
          <h5>Mensajes</h5>
          <p>Tienes 2 mensajes nuevos.</p>
          <button class="btn btn-primary" onclick="showAlert('Mensajes')">Ver más</button>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-3">
          <h5>Estadísticas</h5>
          <p>Tu actividad esta semana ha aumentado un 20%.</p>
          <button class="btn btn-primary" onclick="showAlert('Estadísticas')">Ver más</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Simulación de nombre de usuario
  document.getElementById('username').textContent = 'Luis Duncan';

  // SweetAlert para tarjetas
  function showAlert(section) {
    Swal.fire({
      title: section,
      text: 'Sección en desarrollo...',
      icon: 'info',
      confirmButtonText: 'Ok'
    });
  }

  // Logout
  document.getElementById('logoutBtn').addEventListener('click', function() {
    Swal.fire({
      title: 'Cerrar sesión?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, salir',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if(result.isConfirmed) {
        // Redirigir al login
        window.location.href = 'login.html';
      }
    });
  });
</script>

</body>
</html>
