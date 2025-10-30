<?php
include __DIR__ . '/../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../user/login.php");
    exit;
}

$stmt = $conn->query("SELECT id_usuario, nombre, email, rol, fecha_registro FROM usuarios WHERE estado = 'A' ORDER BY fecha_registro DESC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | GameLearn</title>
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

        .btn-delete {
            background: #e63946;
        }

        .btn-delete:hover {
            background: #b91c1c;
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

        /* ✅ Personalización visual de las opciones */
        .form-select option {
            background: #1a0033;
            /* Fondo oscuro visible */
            color: #fff;
            /* Texto claro */
            padding: 8px;
        }

        /* ✅ Indicador de flecha más visible */
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
    </style>
</head>

<body>

    <?php include __DIR__ . '/../admin/includes/navbar.php'; ?>

    <main>
        <h2><i class='bx bx-user'></i> Gestión de Usuarios</h2>

        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['id_usuario']) ?></td>
                                <td><?= htmlspecialchars($u['nombre']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($u['rol'])) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y', strtotime($u['fecha_registro']))) ?></td>
                                <td>
                                    <button class="btn btn-view btn-sm"
                                        data-id="<?= $u['id_usuario'] ?>"
                                        data-nombre="<?= htmlspecialchars($u['nombre']) ?>"
                                        data-email="<?= htmlspecialchars($u['email']) ?>"
                                        data-rol="<?= htmlspecialchars($u['rol']) ?>"
                                        data-fecha="<?= htmlspecialchars($u['fecha_registro']) ?>"
                                        onclick="verUsuario(this)">
                                        <i class='bx bx-show'></i>
                                    </button>

                                    <button class="btn btn-edit btn-sm"
                                        data-id="<?= $u['id_usuario'] ?>"
                                        data-nombre="<?= htmlspecialchars($u['nombre']) ?>"
                                        data-email="<?= htmlspecialchars($u['email']) ?>"
                                        data-rol="<?= htmlspecialchars($u['rol']) ?>"
                                        onclick="editarUsuario(this)">
                                        <i class='bx bx-edit'></i>
                                    </button>

                                    <button class="btn btn-delete btn-sm" data-id="<?= $u['id_usuario'] ?>" onclick="eliminarUsuario(this)">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Ver Usuario -->
    <div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="infoUsuario"></div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditar">
                        <input type="hidden" id="editId">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editNombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select id="editRol" class="form-select">
                                <option value="user">Usuario</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>


                        <button type="submit" class="btn btn-success w-100">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="public/js/swal.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ver usuario
        function verUsuario(btn) {
            const info = `
      <p><strong>ID:</strong> ${btn.dataset.id}</p>
      <p><strong>Nombre:</strong> ${btn.dataset.nombre}</p>
      <p><strong>Email:</strong> ${btn.dataset.email}</p>
      <p><strong>Rol:</strong> ${btn.dataset.rol}</p>
      <p><strong>Registro:</strong> ${btn.dataset.fecha}</p>
    `;
            document.getElementById('infoUsuario').innerHTML = info;
            new bootstrap.Modal(document.getElementById('modalVer')).show();
        }

        // Editar usuario (abrir modal)
        function editarUsuario(btn) {
            document.getElementById('editId').value = btn.dataset.id;
            document.getElementById('editNombre').value = btn.dataset.nombre;
            document.getElementById('editEmail').value = btn.dataset.email;

            const selectRol = document.getElementById('editRol');
            selectRol.value = btn.dataset.rol || 'user'; // si no hay valor, deja 'user'

            const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
            modal.show();
        }


        // Guardar cambios vía AJAX
        document.getElementById('formEditar').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('action', 'editar');
            formData.append('id', document.getElementById('editId').value);
            formData.append('nombre', document.getElementById('editNombre').value);
            formData.append('email', document.getElementById('editEmail').value);
            formData.append('rol', document.getElementById('editRol').value);

            fetch('back/usuarios_acciones.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Editado con exito',
                            text: 'editaste correctamente al usuario.',
                            icon: 'success',
                            confirmButtonColor: '#a259ff',
                            confirmButtonText: 'aceptar',
                            background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
                            color: '#fff',
                        }).then(() => location.reload());
                    } else {
                        SwalGame('Error', data.message, 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Ocurrió un error al procesar la solicitud', 'error'));
        });



        function eliminarUsuario(btn) {
            const id = btn.dataset.id;
            Swal.fire({
                title: '¿Eliminar usuario?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#a259ff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
                color: '#fff',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('back/usuarios_acciones.php', {
                            method: 'POST',
                            body: new URLSearchParams({
                                action: 'eliminar',
                                id
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.status === 'success') {
                                SwalGame({
                                    title: '¡Eliminado!',
                                    html: '<p>El usuario ha sido eliminado correctamente.</p>',
                                    icon: 'success',
                                    confirmText: 'Aceptar',
                                });
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                SwalGame({
                                    title: 'Error',
                                    html: `<p>${data.message}</p>`,
                                    icon: 'error'
                                });
                            }
                        });
                }
            });
        }
    </script>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>