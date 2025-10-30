<?php
header('Content-Type: application/json');
session_start();
include __DIR__ . '/../../config/db.php'; // Ajusta la ruta según tu estructura

// Validar sesión
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

// Verificar que se envíen los datos
if (!isset($_POST['id'], $_POST['pregunta'], $_POST['opcion_a'], $_POST['opcion_b'], $_POST['opcion_c'], $_POST['opcion_d'], $_POST['correcta'], $_POST['leccion'])) {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
    exit;
}

// Recibir datos
$id = intval($_POST['id']);
$pregunta = trim($_POST['pregunta']);
$opcion_a = trim($_POST['opcion_a']);
$opcion_b = trim($_POST['opcion_b']);
$opcion_c = trim($_POST['opcion_c']);
$opcion_d = trim($_POST['opcion_d']);
$correcta = strtolower($_POST['correcta']);
$leccion = intval($_POST['leccion']);

try {
    $stmt = $conn->prepare("
        UPDATE preguntas SET
        pregunta = :pregunta,
        opcion_a = :opcion_a,
        opcion_b = :opcion_b,
        opcion_c = :opcion_c,
        opcion_d = :opcion_d,
        correcta = :correcta,
        id_leccion = :leccion
        WHERE id_pregunta = :id
    ");
    $stmt->execute([
        ':pregunta' => $pregunta,
        ':opcion_a' => $opcion_a,
        ':opcion_b' => $opcion_b,
        ':opcion_c' => $opcion_c,
        ':opcion_d' => $opcion_d,
        ':correcta' => $correcta,
        ':leccion' => $leccion,
        ':id' => $id
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Pregunta editada correctamente']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar: '.$e->getMessage()]);
}
