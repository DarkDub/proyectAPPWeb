<?php
include __DIR__ . '/../../config/db.php';
session_start();

// Solo admin puede ejecutar estas acciones
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
  http_response_code(403);
  echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
  exit;
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? null;

if ($action === 'editar') {
  $id = $_POST['id'] ?? null;
  $nombre = trim($_POST['nombre'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $rol = trim($_POST['rol'] ?? '');

  if (!$id || !$nombre || !$email || !$rol) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
    exit;
  }

  $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id_usuario = ?");
  $ok = $stmt->execute([$nombre, $email, $rol, $id]);

  echo json_encode([
    'status' => $ok ? 'success' : 'error',
    'message' => $ok ? 'Usuario actualizado correctamente' : 'Error al actualizar usuario'
  ]);
  exit;
}

if ($action === 'eliminar') {
  $id = $_POST['id'] ?? null;

  if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
    exit;
  }

  $stmt = $conn->prepare("UPDATE usuarios SET estado = 'I'  WHERE id_usuario = ?");
  $ok = $stmt->execute([$id]);

  echo json_encode([
    'status' => $ok ? 'success' : 'error',
    'message' => $ok ? 'Usuario eliminado correctamente' : 'Error al eliminar usuario'
  ]);
  exit;
}

// Si llega algo no válido
echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
