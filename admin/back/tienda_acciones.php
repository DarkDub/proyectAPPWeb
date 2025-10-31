<?php
include __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
  echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
  exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'crear') {
  $nombre = trim($_POST['nombre'] ?? '');
  $descripcion = trim($_POST['descripcion'] ?? '');
  $stock = intval($_POST['precio'] ?? 0);
  $puntos = intval($_POST['puntos'] ?? 0);
  $id_categoria = intval($_POST['categoria'] ?? 0);

  if (!$nombre || !$descripcion || !$stock || !$puntos || !$id_categoria) {
    echo json_encode(['status' => 'error', 'message' => 'Completa todos los campos']);
    exit;
  }

  try {
    $stmt = $conn->prepare("INSERT INTO recompensas (nombre, descripcion, stock, puntos_requeridos, id_categoria) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $descripcion, $stock, $puntos, $id_categoria]);
    echo json_encode(['status' => 'success', 'message' => 'Producto creado correctamente']);
  } catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al crear producto: ' . $e->getMessage()]);
  }
}
if ($action === 'editar') {
  $id = intval($_POST['id'] ?? 0);
  $nombre = trim($_POST['nombre'] ?? '');
  $descripcion = trim($_POST['descripcion'] ?? '');
  $stock = intval($_POST['precio'] ?? 0);
  $puntos = intval($_POST['puntos'] ?? 0);
  $id_categoria = intval($_POST['categoria'] ?? 0);

  if (!$id || !$nombre || !$descripcion || !$stock || !$puntos || !$id_categoria) {
    echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios']);
    exit;
  }

  try {
    $stmt = $conn->prepare("
      UPDATE recompensas 
      SET nombre = ?, descripcion = ?, stock = ?, puntos_requeridos = ?, id_categoria = ? 
      WHERE id = ?
    ");
    $stmt->execute([$nombre, $descripcion, $stock, $puntos, $id_categoria, $id]);
    echo json_encode(['status' => 'success', 'message' => 'Producto actualizado correctamente']);
  } catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar: ' . $e->getMessage()]);
  }
}
