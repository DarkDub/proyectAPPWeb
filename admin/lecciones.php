<?php
include __DIR__ . '/../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../user/login.php");
    exit;
}

// Obtener todas las lecciones teóricas activas
$stmt = $conn->query("
    SELECT 
        lt.id_teoria,
        lt.titulo,
        lt.contenido,
        lt.ejemplo,
        lt.imagen,
        lt.ejercicio_recomendado,
        l.titulo AS titulo_leccion,
        lr.titulo AS titulo_ejercicio_recomendado
    FROM leccion_teoria lt
    LEFT JOIN lecciones l ON lt.id_leccion = l.id_leccion
    LEFT JOIN lecciones lr ON lt.ejercicio_recomendado = lr.id_leccion
    WHERE lt.estado = 'A'
    ORDER BY lt.id_teoria DESC
");
$lecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt2 = $conn->query("SELECT id_leccion, titulo FROM lecciones ORDER BY id_leccion ASC");
$lista_lecciones = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecciones | GameLearn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="public/css/lecciones.css">

</head>

<body>

    <?php include __DIR__ . '/../admin/includes/navbar.php'; ?>

    <main>
        <h2><i class='bx bx-book'></i> Gestión de Lecciones</h2>
        <div class="d-flex justify-content-end mb-3 gap-2">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearLeccion">
                <i class='bx bx-plus'></i> Nueva Lección
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearTeoria">
                <i class='bx bx-book-add'></i> Nueva Lección Teórica
            </button>
        </div>

        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Contenido</th>
                            <th>Ejemplo</th>
                            <th>Lección Relacionada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lecciones as $l): ?>
                            <tr>
                                <td><?= htmlspecialchars($l['id_teoria']) ?></td>
                                <td><?= htmlspecialchars($l['titulo']) ?></td>
                                <td><?= htmlspecialchars(substr($l['contenido'], 0, 60)) ?>...</td>
                                <td><?= htmlspecialchars($l['ejemplo']) ?></td>
                                <td><?= htmlspecialchars($l['titulo_ejercicio_recomendado'] ?? 'Sin ejercicio') ?></td>

                                <td>
                                    <button class="btn btn-view btn-sm"
                                        data-id="<?= $l['id_teoria'] ?>"
                                        data-titulo="<?= htmlspecialchars($l['titulo']) ?>"
                                        data-contenido="<?= htmlspecialchars($l['contenido']) ?>"
                                        data-ejemplo="<?= htmlspecialchars($l['ejemplo']) ?>"
                                        data-leccion="<?= htmlspecialchars($l['titulo_leccion'] ?? '') ?>"
                                        onclick="verLeccion(this)">
                                        <i class='bx bx-show'></i>
                                    </button>

                                    <button class="btn btn-edit btn-sm"
                                        data-id="<?= $l['id_teoria'] ?>"
                                        data-titulo="<?= htmlspecialchars($l['titulo']) ?>"
                                        data-contenido="<?= htmlspecialchars($l['contenido']) ?>"
                                        data-ejemplo="<?= htmlspecialchars($l['ejemplo']) ?>"
                                        data-ejercicio_recomendado="<?= htmlspecialchars($l['ejercicio_recomendado'] ?? '') ?>"
                                        onclick="editarLeccion(this)">
                                        <i class='bx bx-edit'></i>
                                    </button>

                                    <button class="btn btn-delete btn-sm" data-id="<?= $l['id_teoria'] ?>" onclick="eliminarLeccion(this)">
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

    <!-- Modal Ver Lección -->
    <div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Lección</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="infoLeccion"></div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Lección -->

    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Lección</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditar">
                        <input type="hidden" id="editId">

                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" id="editTitulo" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contenido</label>
                            <textarea class="form-control" id="editContenido" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ejemplo</label>
                            <input type="text" class="form-control" id="editEjemplo">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ejercicio Recomendado</label>
                            <select class="form-select" id="editEjercicioRecomendado">
                                <option value="">— Selecciona una lección —</option>
                                <?php
                                $allLecciones = $conn->query("SELECT id_leccion, titulo FROM lecciones ORDER BY id_leccion ASC")->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($allLecciones as $lec) {
                                    echo "<option value='{$lec['id_leccion']}'>" . htmlspecialchars($lec['titulo']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>




                        <button type="submit" class="btn btn-success w-100">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCrearLeccion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nueva Lección</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCrearLeccion" class="p-3">
                        <div class="mb-3">
                            <label for="titulo" class="form-label text-light">Título</label>
                            <input type="text" class="form-control" id="nuevaLeccionTitulo" name="titulo" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label text-light">Descripción</label>
                            <textarea class="form-control" id="nuevaLeccionDescripcion" name="descripcion" rows="3" required></textarea>
                        </div>


                        <button type="submit" class="btn btn-primary w-100 mt-3">Guardar Lección</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Lección Teórica -->
    <div class="modal fade" id="modalCrearTeoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nueva Lección Teórica</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCrearTeoria">

                        <div class="mb-3">
                            <label class="form-label">Lección base</label>
                            <select class="form-select" id="teoriaLeccion" required>
                                <option value="">— Selecciona una lección —</option>
                                <?php
                                foreach ($lista_lecciones as $lec) {
                                    echo "<option value='{$lec['id_leccion']}'>" . htmlspecialchars($lec['titulo']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" id="teoriaTitulo" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contenido</label>
                            <textarea class="form-control" id="teoriaContenido" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ejemplo</label>
                            <input type="text" class="form-control" id="teoriaEjemplo">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ejercicio Recomendado</label>
                            <select class="form-select" id="teoriaEjercicioRecomendado" required>
                                <option value="">— Selecciona una lección —</option>
                                <?php
                                foreach ($lista_lecciones as $lec) {
                                    echo "<option value='{$lec['id_leccion']}'>" . htmlspecialchars($lec['titulo']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Crear Lección Teórica</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="public/js/swal.js"></script>
    <script src="public/js/lecciones.js"></script>


</body>

</html>