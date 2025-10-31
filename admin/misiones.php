<?php
include __DIR__ . '/../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../user/login.php");
    exit;
}

// Obtener todas las misiones con su lección
$stmt = $conn->query("
    SELECT m.*, l.titulo AS titulo_leccion
    FROM misiones_diarias m
    LEFT JOIN lecciones l ON m.id_leccion = l.id_leccion
    ORDER BY m.id DESC
");
$misiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de lecciones para selects
$lista_lecciones = $conn->query("SELECT id_leccion, titulo FROM lecciones ORDER BY id_leccion ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Misiones | GameLearn</title>
    <?php include __DIR__ . '/../admin/includes/cdns.php'; ?>

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at center, #2b0056 0%, #0e001a 100%);
            color: #eae0ff;
            display: flex;
        }

        main {
            margin-left: 240px;
            padding: 30px;
            width: calc(100% - 240px);
            min-height: 100vh;
            animation: fadeIn 0.6s ease;
        }

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

        h2 {
            font-family: 'Orbitron', sans-serif;
            color: #c89cff;
            margin-bottom: 20px;
            text-shadow: 0 0 8px rgba(200, 156, 255, 0.6);
        }

        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(6px);
        }
.btn-create-container {
            display: flex;
            justify-content: flex-end;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: #eae0ff;
        }

        thead {
            background: rgba(159, 98, 255, 0.1);
        }

        th,
        td {
            padding: 12px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.95rem;
        }

        th {
            color: #c89cff;
            text-transform: uppercase;
            font-weight: 600;
            font-size: 0.85rem;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.05);
            transition: 0.3s;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 6px 12px;
            color: #fff;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-edit {
            background: #38b000;
        }

        .btn-edit:hover {
            background: #2d8a00;
        }

        .btn-view {
            background: #3a0ca3;
        }

        .btn-view:hover {
            background: #2a0087;
        }

        .modal-content {
            background: #2b0056;
            color: #fff;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
 .btn-create {
            background: #7209b7;
            border: none;
            color: #fff;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
            margin-bottom: 15px;
        }

        .btn-create:hover {
            background: #560bad;
        }
        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 2px #9f62ff;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 2px #9f62ff;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 2px #9f62ff;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 8px;
            padding: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #9f62ff;
            box-shadow: 0 0 0 2px #9f62ff;
            color: #fff;
        }

        /* Personalización visual de las opciones */
        .form-select option {
            background: #1a0033;
            /* Fondo oscuro visible */
            color: #fff;
            /* Texto claro */
            padding: 8px;
        }

        /* Indicador de flecha más visible */
        .form-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='%23c89cff' viewBox='0 0 16 16'><path d='M1.5 5.5l6 6 6-6H1.5z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 14px;
            padding-right: 35px;
        } 
        .form-control::placeholder {
            color: #e1e1e168;
        }
        .container-btn {
            padding: 37px 0px;
        }
    </style>
</head>

<body>

    <?php include __DIR__ . '/../admin/includes/navbar.php'; ?>

    <main>
        <h2><i class='bx bx-flag'></i> Gestión de Misiones</h2>
        <div class="btn-create-container">
<button class="btn-create" onclick="abrirModalCrear()">
  <i class='bx bx-plus'></i> Nueva misión
</button>
           
        </div>


        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Puntos</th>
                            <th>Lección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($misiones as $m): ?>
                            <tr>
                                <td><?= $m['id'] ?></td>
                                <td><?= htmlspecialchars($m['nombre']) ?></td>
                                <td><?= htmlspecialchars($m['descripcion']) ?></td>
                                <td><?= htmlspecialchars($m['tipo']) ?></td>
                                <td><?= htmlspecialchars($m['puntos']) ?></td>
                                <td><?= htmlspecialchars($m['titulo_leccion'] ?? 'Sin lección') ?></td>
                                <td class="d-flex container-btn">
                                    <button class="btn btn-view btn-sm mx-2"
                                        data-id="<?= $m['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($m['nombre'], ENT_QUOTES) ?>"
                                        data-descripcion="<?= htmlspecialchars($m['descripcion'], ENT_QUOTES) ?>"
                                        data-tipo="<?= htmlspecialchars($m['tipo'], ENT_QUOTES) ?>"
                                        data-puntos="<?= $m['puntos'] ?>"
                                        data-leccion="<?= htmlspecialchars($m['titulo_leccion'] ?? 'Sin lección', ENT_QUOTES) ?>"
                                        onclick="verMision(this)">
                                        <i class='bx bx-show'></i>
                                    </button>
                                    <button class="btn btn-edit btn-sm"
                                        data-id="<?= $m['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($m['nombre'], ENT_QUOTES) ?>"
                                        data-descripcion="<?= htmlspecialchars($m['descripcion'], ENT_QUOTES) ?>"
                                        data-tipo="<?= htmlspecialchars($m['tipo'], ENT_QUOTES) ?>"
                                        data-puntos="<?= $m['puntos'] ?>"
                                        data-leccion="<?= $m['id_leccion'] ?>"
                                        onclick="editarMision(this)">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Ver Misión -->
    <div class="modal fade" id="modalVerMision" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Misión</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="infoMision"></div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Misión -->
    <div class="modal fade" id="modalEditarMision" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Misión</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarMision">
                        <input type="hidden" id="editMisionId" name="id">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editMisionNombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="editMisionDescripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" id="editMisionTipo" name="tipo">
                                <option value="select">Select</option>
                                <option value="click">Click</option>
                                <option value="leccion">Lección</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Puntos</label>
                            <input type="number" class="form-control" id="editMisionPuntos" name="puntos" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lección</label>
                            <select class="form-select" id="editMisionLeccion" name="id_leccion">
                                <?php foreach ($lista_lecciones as $l): ?>
                                    <option value="<?= $l['id_leccion'] ?>"><?= htmlspecialchars($l['titulo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Crear Misión -->
<div class="modal fade" id="modalCrearMision" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear nueva misión</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formCrearMision">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" id="crearMisionNombre" placeholder="Participa en mini-juego	" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" id="crearMisionDescripcion" placeholder="Juega un mini-juego del día	" rows="3" required></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Tipo</label>
            <select class="form-select" id="crearMisionTipo" required>
              <option value="select" selected></option>
              <option value="click">Click</option>
              <option value="leccion">Lección</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Puntos</label>
            <input type="number" class="form-control" id="crearMisionPuntos" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Lección</label>
            <select class="form-select" id="crearMisionLeccion" required>
              <?php foreach ($lista_lecciones as $l): ?>
                <option value="<?= $l['id_leccion'] ?>"><?= htmlspecialchars($l['titulo']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <button type="submit" class="btn btn-success w-100">
            <i class='bx bx-save'></i> Guardar misión
          </button>
        </form>
      </div>
    </div>
  </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Ver Misión
        function verMision(btn) {
            const info = `
        <p><strong>ID:</strong> ${btn.dataset.id}</p>
        <p><strong>Nombre:</strong> ${btn.dataset.nombre}</p>
        <p><strong>Descripción:</strong> ${btn.dataset.descripcion}</p>
        <p><strong>Tipo:</strong> ${btn.dataset.tipo}</p>
        <p><strong>Puntos:</strong> ${btn.dataset.puntos}</p>
        <p><strong>Lección:</strong> ${btn.dataset.leccion}</p>
    `;
            document.getElementById('infoMision').innerHTML = info;
            new bootstrap.Modal(document.getElementById('modalVerMision')).show();
        }

        // Editar Misión
        function editarMision(btn) {
            document.getElementById('editMisionId').value = btn.dataset.id;
            document.getElementById('editMisionNombre').value = btn.dataset.nombre;
            document.getElementById('editMisionDescripcion').value = btn.dataset.descripcion;
            document.getElementById('editMisionTipo').value = btn.dataset.tipo; // asigna la opción correcta
            document.getElementById('editMisionPuntos').value = btn.dataset.puntos;
            document.getElementById('editMisionLeccion').value = btn.dataset.leccion;
            new bootstrap.Modal(document.getElementById('modalEditarMision')).show();

        }
        // Ejemplo editar misión
        document.getElementById('formEditarMision').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'editar');

            fetch('back/misiones_acciones.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                title: '¡Editada!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#a259ff',
                background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
                color: '#fff',
            }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                    console.log(data)
                })
                .catch(() => Swal.fire('Error', 'Ocurrió un error', 'error'));
        });


        function abrirModalCrear() {
  document.getElementById('formCrearMision').reset();
  new bootstrap.Modal(document.getElementById('modalCrearMision')).show();
}

document.getElementById('formCrearMision').addEventListener('submit', function(e){
  e.preventDefault();

  const formData = new FormData();
  formData.append('action', 'crear');
  formData.append('nombre', document.getElementById('crearMisionNombre').value);
  formData.append('descripcion', document.getElementById('crearMisionDescripcion').value);
  formData.append('tipo', document.getElementById('crearMisionTipo').value);
  formData.append('puntos', document.getElementById('crearMisionPuntos').value);
  formData.append('id_leccion', document.getElementById('crearMisionLeccion').value);

  fetch('back/misiones_acciones.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      Swal.fire('¡Éxito!', data.message, 'success').then(() => location.reload());
    } else {
      Swal.fire('Error', data.message, 'error');
    }
  })
  .catch(err => {
    console.error(err);
    Swal.fire('Error', 'No se pudo crear la misión', 'error');
  });
});

    </script>

</body>

</html>