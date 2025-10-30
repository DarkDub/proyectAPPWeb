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

// Obtener lista de lecciones para el select
$lista_lecciones = $conn->query("SELECT id_leccion, titulo FROM lecciones ORDER BY id_leccion ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntas | GameLearn</title>
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

        .btn-create-container {
            display: flex;
            justify-content: flex-end;
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
        .form-control::placeholder {
            color: #e1e1e168;
        }
    </style>
</head>

<body>

    <?php include __DIR__ . '/../admin/includes/navbar.php'; ?>

    <main>
        <h2><i class='bx bx-question-mark'></i> Gestión de Preguntas</h2>
        <div class="btn-create-container">

            <button class="btn-create" data-bs-toggle="modal" data-bs-target="#modalCrearPregunta">
                <i class='bx bx-plus'></i> Nueva Pregunta
            </button>
        </div>

        <div class="card">
            <div class="table-container">
                <table>
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
                        <?php foreach ($preguntas as $p): ?>
                            <tr>
                                <td><?= $p['id_pregunta'] ?></td>
                                <td><?= htmlspecialchars($p['pregunta']) ?></td>
                                <td>
                                    A: <?= htmlspecialchars($p['opcion_a']) ?><br>
                                    B: <?= htmlspecialchars($p['opcion_b']) ?><br>
                                    C: <?= htmlspecialchars($p['opcion_c']) ?><br>
                                    D: <?= htmlspecialchars($p['opcion_d']) ?>
                                </td>
                                <td><strong><?= strtoupper($p['correcta']) ?></strong></td>
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
                                        data-leccion="<?= htmlspecialchars($p['titulo_leccion'] ?? 'Sin lección', ENT_QUOTES) ?>"
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

    <!-- Modal Ver -->
    <div class="modal fade" id="modalVerPregunta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Pregunta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="infoPregunta"></div>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="modalEditarPregunta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Pregunta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
    <div class="modal fade" id="modalCrearPregunta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nueva Pregunta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCrearPregunta">
                        <div class="mb-3">
                            <label class="form-label">Pregunta</label>
                            <textarea class="form-control" id="preguntaTexto" placeholder="¿Cómo se dice Beber en inglés?" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Opción A</label>
                            <input type="text" class="form-control" id="opcionA" required>
                        </div>
                        <div class="mb-3">
                            <label>Opción B</label>
                            <input type="text" class="form-control" id="opcionB" required>
                        </div>
                        <div class="mb-3">
                            <label>Opción C</label>
                            <input type="text" class="form-control" id="opcionC" required>
                        </div>
                        <div class="mb-3">
                            <label>Opción D</label>
                            <input type="text" class="form-control" id="opcionD" required>
                        </div>
                        <div class="mb-3">
                            <label>Respuesta Correcta</label>
                            <select class="form-select" id="respuestaCorrecta" required>
                                <option value="a">A</option>
                                <option value="b">B</option>
                                <option value="c">C</option>
                                <option value="d">D</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Lección</label>
                            <select class="form-select" id="idLeccion" required>
                                <option value="">Seleccione una lección</option>
                                <?php foreach ($lista_lecciones as $l): ?>
                                    <option value="<?= $l['id_leccion'] ?>"><?= htmlspecialchars($l['titulo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Guardar Pregunta</button>
                    </form>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.getElementById('formCrearPregunta').addEventListener('submit', async (e) => {
                e.preventDefault();

                const data = {
                    pregunta: document.getElementById('preguntaTexto').value.trim(),
                    opcion_a: document.getElementById('opcionA').value.trim(),
                    opcion_b: document.getElementById('opcionB').value.trim(),
                    opcion_c: document.getElementById('opcionC').value.trim(),
                    opcion_d: document.getElementById('opcionD').value.trim(),
                    correcta: document.getElementById('respuestaCorrecta').value,
                    id_leccion: document.getElementById('idLeccion').value
                };

                const res = await fetch('back/guardar_pregunta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                Swal.fire({
                    title: result.success ? '¡Guardado!' : 'Error',
                    text: result.message,
                    icon: result.success ? 'success' : 'error',
                    background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
                    color: '#fff'
                }).then(() => {
                    if (result.success) location.reload();
                });
            });

            // Ver Pregunta
            function verPregunta(btn) {
                const info = `
        <p><strong>ID:</strong> ${btn.dataset.id}</p>
        <p><strong>Pregunta:</strong> ${btn.dataset.pregunta}</p>
        <p><strong>Opciones:</strong><br>
        A: ${btn.dataset.opcion_a}<br>
        B: ${btn.dataset.opcion_b}<br>
        C: ${btn.dataset.opcion_c}<br>
        D: ${btn.dataset.opcion_d}</p>
        <p><strong>Correcta:</strong> ${btn.dataset.correcta.toUpperCase()}</p>
        <p><strong>Lección:</strong> ${btn.dataset.leccion}</p>
    `;
                document.getElementById('infoPregunta').innerHTML = info;
                new bootstrap.Modal(document.getElementById('modalVerPregunta')).show();
            }

            // Editar Pregunta
            function editarPregunta(btn) {
                document.getElementById('editPreguntaId').value = btn.dataset.id;
                document.getElementById('editPreguntaTexto').value = btn.dataset.pregunta;
                document.getElementById('editOpcionA').value = btn.dataset.opcion_a;
                document.getElementById('editOpcionB').value = btn.dataset.opcion_b;
                document.getElementById('editOpcionC').value = btn.dataset.opcion_c;
                document.getElementById('editOpcionD').value = btn.dataset.opcion_d;
                document.getElementById('editCorrecta').value = btn.dataset.correcta;
                document.getElementById('editLeccion').value = btn.dataset.leccion;
            new bootstrap.Modal(document.getElementById('modalEditarPregunta')).show();
                
// Formulario editar pregunta
document.getElementById('formEditarPregunta').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('id', document.getElementById('editPreguntaId').value);
    formData.append('pregunta', document.getElementById('editPreguntaTexto').value);
    formData.append('opcion_a', document.getElementById('editOpcionA').value);
    formData.append('opcion_b', document.getElementById('editOpcionB').value);
    formData.append('opcion_c', document.getElementById('editOpcionC').value);
    formData.append('opcion_d', document.getElementById('editOpcionD').value);
    formData.append('correcta', document.getElementById('editCorrecta').value);
    formData.append('leccion', document.getElementById('editLeccion').value);

    fetch('back/editar_pregunta.php', {
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
            Swal.fire({
                title: 'Error',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#a259ff',
                background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
                color: '#fff',
            });
        }
    })
    .catch(() => Swal.fire('Error', 'Ocurrió un error al procesar la solicitud', 'error'));
});

                
            }

            
        </script>

</body>

</html>