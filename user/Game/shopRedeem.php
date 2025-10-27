<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['reward_id'])) {
    echo json_encode(['success' => false, 'mensaje' => 'ID de recompensa no proporcionado']);
    exit;
}

$reward_id = (int)$data['reward_id'];

// Obtener recompensa
$stmt = $conn->prepare("SELECT puntos_requeridos, nombre FROM recompensas WHERE id = :id");
$stmt->bindParam(':id', $reward_id, PDO::PARAM_INT);
$stmt->execute();
$reward = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reward) {
    echo json_encode(['success' => false, 'mensaje' => 'Recompensa no encontrada']);
    exit;
}

// Obtener puntos del usuario
$stmt2 = $conn->prepare("SELECT puntos FROM usuarios WHERE id_usuario = :id");
$stmt2->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt2->execute();
$user = $stmt2->fetch(PDO::FETCH_ASSOC);

if ($user['puntos'] < $reward['puntos_requeridos']) {
    echo json_encode(['success' => false, 'mensaje' => 'No tienes suficientes puntos']);
    exit;
}

// Restar puntos
$nuevo_puntaje = $user['puntos'] - $reward['puntos_requeridos'];
$stmt3 = $conn->prepare("UPDATE usuarios SET puntos = :puntos WHERE id_usuario = :id");
$stmt3->bindParam(':puntos', $nuevo_puntaje, PDO::PARAM_INT);
$stmt3->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt3->execute();

// Registrar canje
$stmt4 = $conn->prepare("INSERT INTO canjes (id_usuario, id_recompensa, fecha) VALUES (:user, :reward, NOW())");
$stmt4->bindParam(':user', $user_id, PDO::PARAM_INT);
$stmt4->bindParam(':reward', $reward_id, PDO::PARAM_INT);
$stmt4->execute();

// Registrar actividad
$descripcion = "Canjeó la recompensa '{$reward['nombre']}'";
$stmt5 = $conn->prepare("INSERT INTO actividad (id_usuario, tipo, descripcion, fecha) VALUES (:user, 'compra', :desc, NOW())");
$stmt5->bindParam(':user', $user_id, PDO::PARAM_INT);
$stmt5->bindParam(':desc', $descripcion, PDO::PARAM_STR);
$stmt5->execute();

// Responder con JSON
echo json_encode([
    'success' => true,
    'nuevo_puntaje' => $nuevo_puntaje,
    'mensaje' => "Has canjeado '{$reward['nombre']}' correctamente!"
]);
