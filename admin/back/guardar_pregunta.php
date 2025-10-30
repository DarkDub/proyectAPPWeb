<?php
include __DIR__ . '/../../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['pregunta']) || empty($data['id_leccion'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
    exit;
}

try {
    $stmt = $conn->prepare("
        INSERT INTO preguntas (id_leccion, pregunta, opcion_a, opcion_b, opcion_c, opcion_d, correcta)
        VALUES (:id_leccion, :pregunta, :a, :b, :c, :d, :correcta)
    ");
    $stmt->execute([
        ':id_leccion' => $data['id_leccion'],
        ':pregunta' => $data['pregunta'],
        ':a' => $data['opcion_a'],
        ':b' => $data['opcion_b'],
        ':c' => $data['opcion_c'],
        ':d' => $data['opcion_d'],
        ':correcta' => $data['correcta']
    ]);

    echo json_encode(['success' => true, 'message' => 'Pregunta guardada correctamente']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
