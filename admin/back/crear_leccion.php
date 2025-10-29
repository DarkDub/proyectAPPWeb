<?php
include __DIR__ . '/../../config/db.php';
session_start();

header('Content-Type: application/json');

try {
  // Validar que los datos existan
  if (empty($_POST['titulo']) || empty($_POST['descripcion']) ) {
    echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
    exit;
  }

  $titulo = trim($_POST['titulo']);
  $descripcion = trim($_POST['descripcion']);

  // Insertar nueva lección
  $stmt = $conn->prepare("INSERT INTO lecciones (titulo, descripcion) VALUES (:titulo, :descripcion)");
  $stmt->bindParam(':titulo', $titulo);
  $stmt->bindParam(':descripcion', $descripcion);
  if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Lección creada correctamente.']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Error al guardar la lección.']);
  }

} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
