<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Datos no válidos']);
    exit;
}

$user_id = $data['user_id'] ?? null;
$id_leccion = $data['id_leccion'] ?? null;
$puntos_ganados = $data['puntos_ganados'] ?? 0;
$palabras = $data['palabras'] ?? [];

if (!$user_id || !$id_leccion) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    exit;
}

// 🔹 Obtener datos del usuario
$stmt = $conn->prepare("SELECT nivel_actual, nivel_actual_gramatica, puntos FROM usuarios WHERE id_usuario = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    exit;
}

$nivel_actual = (int) $user['nivel_actual'];
$nivel_gram = $user['nivel_actual_gramatica'];
$puntos_actuales = (int) $user['puntos'];

// 🔹 Definir metas y correspondencia de niveles
$metas = [
    'A1' => 1000,
    'A2' => 3000,
    'B1' => 6000,
    'B2' => 10000,
    'C1' => 15000,
    'C2' => 20000
];

$siguiente_nivel_gram = [
    'A1' => 'A2',
    'A2' => 'B1',
    'B1' => 'B2',
    'B2' => 'C1',
    'C1' => 'C2',
    'C2' => 'C2'
];

// 🔹 Si el nivel no está en el array, asumir A1
if (!isset($metas[$nivel_gram])) {
    $nivel_gram = 'A1';
}

$puntos_totales = $puntos_actuales + $puntos_ganados;
$nuevo_nivel = $nivel_actual;
$nuevo_nivel_gram = $nivel_gram;
$subio_nivel = false;

// 🔹 Comprobar si sube de nivel
if ($puntos_totales >= $metas[$nivel_gram]) {
    $subio_nivel = true;
    $nuevo_nivel = $nivel_actual + 1;
    $nuevo_nivel_gram = $siguiente_nivel_gram[$nivel_gram];
    $puntos_totales -= $metas[$nivel_gram]; // restar exceso
}

// 🔹 Actualizar usuario
$stmtUpdate = $conn->prepare("UPDATE usuarios 
    SET puntos = :puntos, nivel_actual = :nivel, nivel_actual_gramatica = :nivel_gram
    WHERE id_usuario = :id
");
$stmtUpdate->execute([
    ':puntos' => $puntos_totales,
    ':nivel' => $nuevo_nivel,
    ':nivel_gram' => $nuevo_nivel_gram,
    ':id' => $user_id
]);

// 🔹 Guardar progreso
$stmt2 = $conn->prepare("
    INSERT INTO progreso (id_usuario, id_leccion, completado, puntos_obtenidos)
    VALUES (:user, :leccion, 1, :puntos)
    ON DUPLICATE KEY UPDATE completado = 1, puntos_obtenidos = :puntos
");
$stmt2->execute([
    ':user' => $user_id,
    ':leccion' => $id_leccion,
    ':puntos' => $puntos_ganados
]);

// 🔹 Guardar palabras aprendidas
if (!empty($palabras)) {
    $stmt3 = $conn->prepare("
        INSERT INTO palabras_aprendidas (id_usuario, id_leccion, palabra)
        VALUES (:user, :leccion, :palabra)
        ON DUPLICATE KEY UPDATE fecha_aprendida = NOW()
    ");
    foreach ($palabras as $palabra) {
        $stmt3->execute([
            ':user' => $user_id,
            ':leccion' => $id_leccion,
            ':palabra' => $palabra
        ]);
    }
}

// 🔹 Registrar actividad
$actividad_desc = "Completó la lección $id_leccion y ganó $puntos_ganados puntos.";
$stmt4 = $conn->prepare("INSERT INTO actividad (id_usuario, tipo, descripcion) VALUES (:user, 'Lección', :desc)");
$stmt4->execute([
    ':user' => $user_id,
    ':desc' => $actividad_desc
]);

// 🔹 Registrar subida de nivel
if ($subio_nivel) {
    $msg = "Subió al nivel $nuevo_nivel_gram (Nivel $nuevo_nivel) 🎉";
    $stmt5 = $conn->prepare("INSERT INTO actividad (id_usuario, tipo, descripcion) VALUES (:user, 'Nivel', :desc)");
    $stmt5->execute([
        ':user' => $user_id,
        ':desc' => $msg
    ]);
}

// 🔹 Respuesta final JSON
echo json_encode([
    'success' => true,
    'nuevo_puntaje' => $puntos_totales,
    'nivel_actual' => $nuevo_nivel,
    'nivel_actual_gramatica' => $nuevo_nivel_gram,
    'subio_nivel' => $subio_nivel,
    'meta' => $metas[$nuevo_nivel_gram] ?? end($metas)
]);
exit;
