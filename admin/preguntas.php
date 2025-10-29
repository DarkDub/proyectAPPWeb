<?php
include __DIR__ . '/../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../user/login.php");
    exit;
}

// Obtener todas las preguntas con su lección
$stmt = $conn->query("
    SELECT p.*, l.titulo AS titulo_leccion
    FROM preguntas p
    LEFT JOIN lecciones l ON p.id_leccion = l.id_leccion
    ORDER BY p.id_pregunta DESC
");
$preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de lecciones para el select en editar
$lista_lecciones = $conn->query("SELECT id_leccion, titulo FROM lecciones ORDER BY id_leccion ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Preguntas | GameLearn</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="public/css/lecciones.css">
</head>
<body>
<?php include __DIR__ . '/../admin/includes/navbar.php'; ?>

<main class="container mt-4">
    <h2><i class='bx bx-question-mark'></i> Gestión de Preguntas</h2>

    <div class="card mt-3">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pregunta</th>
                        <th>Opciones</th>
                        <th>Correcta</th>
                        <th>Lección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($preguntas as $p): ?>
                    <tr>
                        <td><?= $p['id_pregunta'] ?></td>
                        <td><?= htmlspecialchars($p['pregunta']) ?></td>
                        <td>
                            A: <?= htmlspecialchars($p['opcion_a']) ?><br>
                            B: <?= htmlspecialchars($p['opcion_b']) ?><br>
                            C: <?= htmlspecialchars($p['opcion_c']) ?><br>
                            D: <?= htmlspecialchars($p['opcion_d']) ?>
                        </td>
                        <td><?= strtoupper($p['correcta']) ?></td>
                        <td><?= htmlspecialchars($p['titulo_leccion'] ?? 'Sin lección') ?></td>
                        <td>
                            <button class="btn btn-view btn-sm"
                                data-id="<?= $p['id_pregunta'] ?>"
                                data-pregunta="<?= htmlspecialchars($p['pregunta'], ENT_QUOTES) ?>"
                                data-opcion_a="<?= htmlspecialchars($p['opcion_a'], ENT_QUOTES) ?>"
                                data-opcion_b="<?= htmlspecialchars($p['opcion_b'], ENT_QUOTES) ?>"
                                data-opcion_c="<?= htmlspecialchars($p['opcion_c'], ENT_QUOTES) ?>"
                                data-opcion_d="<?= htmlspecialchars($p['opcion_d'], ENT_QUOTES) ?>"
                                data-correcta="<?= $p['correcta'] ?>"
                                data-leccion="<?= $p['id_leccion'] ?>"
                                onclick="verPregunta(this)">
                                <i class='bx bx-show'></i>
                            </button>

                            <button class="btn btn-edit btn-sm"
                                data-id="<?= $p['id_pregunta'] ?>"
                                data-pregunta="<?= htmlspecialchars($p['pregunta'], ENT_QUOTES) ?>"
                                data-opcion_a="<?= htmlspecialchars($p['opcion_a'], ENT_QUOTES) ?>"
                                data-opcion_b="<?= htmlspecialchars($p['opcion_b'], ENT_QUOTES) ?>"
                                data-opcion_c="<?= htmlspecialchars($p['opcion_c'], ENT_QUOTES) ?>"
                                data-opcion_d="<?= htmlspecialchars($p['opcion_d'], ENT_QUOTES) ?>"
                                data-correcta="<?= $p['correcta'] ?>"
                                data-leccion="<?= $p['id_leccion'] ?>"
                                onclick="editarPregunta(this)">
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

<!-- Modal Ver Pregunta -->
<div class="modal fade" id="modalVerPregunta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Pregunta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="infoPregunta"></div>
        </div>
    </div>
</div>

<!-- Modal Editar Pregunta -->
<div class="modal fade" id="modalEditarPregunta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Pregunta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarPregunta">
                    <input type="hidden" id="editPreguntaId">
                    <div class="mb-3">
                        <label class="form-label">Pregunta</label>
                        <textarea class="form-control" id="editPreguntaTexto" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opción A</label>
                        <input type="text" class="form-control" id="editOpcionA" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opción B</label>
                        <input type="text" class="form-control" id="editOpcionB" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opción C</label>
                        <input type="text" class="form-control" id="editOpcionC" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opción D</label>
                        <input type="text" class="form-control" id="editOpcionD" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correcta</label>
                        <select class="form-select" id="editCorrecta">
                            <option value="a">A</option>
                            <option value="b">B</option>
                            <option value="c">C</option>
                            <option value="d">D</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lección</label>
                        <select class="form-select" id="editLeccion">
                            <?php foreach($lista_lecciones as $l): ?>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="public/js/preguntas.js"></script>

</body>
</html>
