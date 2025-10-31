<?php
include __DIR__ . '/../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../user/login.php");
    exit;
}

// Obtener todos los productos
// Obtener todas las categorías
$stmtCat = $conn->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// Obtener recompensas con categoría (JOIN)
$stmt = $conn->query("
  SELECT r.*, c.nombre AS categoria_nombre
  FROM recompensas r
  LEFT JOIN categorias c ON r.id_categoria = c.id_categoria
  ORDER BY r.id DESC
");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda | GameLearn</title>
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
    </style>
</head>

<body>

    <?php include __DIR__ . '/../admin/includes/navbar.php'; ?>

    <main>
        <h2><i class='bx bx-store'></i> Gestión de Tienda</h2>
        <div class="btn-create-container">
            <button class="btn-create" onclick="abrirModalCrear()">
                <i class='bx bx-plus'></i> Nuevo producto
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
                            <th>stock</th>
                            <th>Puntos Requeridos</th>
                            <th>Categoría</th>

                            <th>Acciones</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p): ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td><?= htmlspecialchars($p['nombre']) ?></td>
                                <td><?= htmlspecialchars($p['descripcion']) ?></td>
                                <td><?= htmlspecialchars($p['stock']) ?></td>
                                <td><?= htmlspecialchars($p['puntos_requeridos']) ?></td>
                                <td><?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin categoría') ?></td>

                                <td>
                                    <button class="btn btn-view btn-sm"
                                        data-id="<?= $p['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>"
                                        data-descripcion="<?= htmlspecialchars($p['descripcion'], ENT_QUOTES) ?>"
                                        data-precio="<?= $p['stock'] ?>"
                                        data-puntos="<?= $p['puntos_requeridos'] ?>"
                                        onclick="verProducto(this)">
                                        <i class='bx bx-show'></i>
                                    </button>
                                    
                                   <button class="btn btn-edit btn-sm mx-1" 
  data-id="<?= $p['id'] ?>"
  data-nombre="<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>"
  data-descripcion="<?= htmlspecialchars($p['descripcion'], ENT_QUOTES) ?>"
  data-precio="<?= $p['stock'] ?>"
  data-puntos="<?= $p['puntos_requeridos'] ?>"
  data-categoria="<?= $p['id_categoria'] ?>"
  onclick="editarProducto(this)">
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

    <!-- Modal Ver -->
    <div class="modal fade" id="modalVerProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="infoProducto"></div>
            </div>
        </div>
    </div>

    <!-- Modal Crear -->
    <div class="modal fade" id="modalCrearProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCrearProducto">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" id="crearNombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea id="crearDescripcion" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" id="crearPrecio" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Categoría</label>
                            <select id="crearCategoria" class="form-select" required>
                                <option value="" selected disabled>Selecciona una categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Puntos Requeridos</label>
                            <input type="number" id="crearPuntos" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Guardar Producto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<!-- Modal Editar -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Producto</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formEditarProducto">
          <input type="hidden" id="editarId">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" id="editarNombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea id="editarDescripcion" class="form-control" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" id="editarPrecio" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Puntos Requeridos</label>
            <input type="number" id="editarPuntos" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Categoría</label>
            <select id="editarCategoria" class="form-select" required>
              <option value="" disabled>Selecciona una categoría</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-success w-100">Actualizar Producto</button>
        </form>
      </div>
    </div>
  </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="public/js/swal.js"></script>
    <script>
        function verProducto(btn) {
            const info = `
        <p><strong>ID:</strong> ${btn.dataset.id}</p>
        <p><strong>Nombre:</strong> ${btn.dataset.nombre}</p>
        <p><strong>Descripción:</strong> ${btn.dataset.descripcion}</p>
        <p><strong>Precio:</strong> ${btn.dataset.precio}</p>
        <p><strong>Puntos:</strong> ${btn.dataset.puntos}</p>
      `;
            document.getElementById('infoProducto').innerHTML = info;
            new bootstrap.Modal(document.getElementById('modalVerProducto')).show();
        }

        function abrirModalCrear() {
            document.getElementById('formCrearProducto').reset();
            new bootstrap.Modal(document.getElementById('modalCrearProducto')).show();
        }

        document.getElementById('formCrearProducto').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('action', 'crear');
            formData.append('nombre', document.getElementById('crearNombre').value);
            formData.append('descripcion', document.getElementById('crearDescripcion').value);
            formData.append('precio', document.getElementById('crearPrecio').value);
            formData.append('puntos', document.getElementById('crearPuntos').value);
            formData.append('categoria', document.getElementById('crearCategoria').value);


            fetch('back/tienda_acciones.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        SwalGame({
                            title: "¡Creado!",
                            html: "<p>Se creo el nuevo producto.</p>",
                            icon: "success",
                        });
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        SwalGame({
                            title: "¡Error!",
                            html: "<p>Error al crear.</p>",
                            icon: "error",
                        });
                        setTimeout(() => location.reload(), 1500);
                    }
                })
                .catch(err => Swal.fire('Error', 'No se pudo crear el producto', 'error'));
        });

        function editarProducto(btn) {
  document.getElementById('editarId').value = btn.dataset.id;
  document.getElementById('editarNombre').value = btn.dataset.nombre;
  document.getElementById('editarDescripcion').value = btn.dataset.descripcion;
  document.getElementById('editarPrecio').value = btn.dataset.precio;
  document.getElementById('editarPuntos').value = btn.dataset.puntos;
  document.getElementById('editarCategoria').value = btn.dataset.categoria;

  new bootstrap.Modal(document.getElementById('modalEditarProducto')).show();
}

document.getElementById('formEditarProducto').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData();
  formData.append('action', 'editar');
  formData.append('id', document.getElementById('editarId').value);
  formData.append('nombre', document.getElementById('editarNombre').value);
  formData.append('descripcion', document.getElementById('editarDescripcion').value);
  formData.append('precio', document.getElementById('editarPrecio').value);
  formData.append('puntos', document.getElementById('editarPuntos').value);
  formData.append('categoria', document.getElementById('editarCategoria').value);

  fetch('back/tienda_acciones.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        SwalGame({
                            title: "¡Actualizado!",
                            html: "<p>Se actualizo el producto.</p>",
                            icon: "success",
                        });setTimeout(() => location.reload(), 1500);
      } else {
        SwalGame('Error', data.message, 'error');
      }
    })
    .catch(err => Swal.fire('Error', 'No se pudo editar el producto', 'error'));
});

    </script>
</body>

</html>