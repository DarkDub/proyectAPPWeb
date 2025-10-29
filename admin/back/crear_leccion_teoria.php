<?php
include __DIR__ . '/../../config/db.php';
session_start();

header('Content-Type: application/json');

// 🔒 Validación de sesión
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
  echo json_encode(['status' => 'error', 'message' => 'Acceso denegado']);
  exit;
}

try {
  // Obtener datos del formulario
  $id_leccion = $_POST['id_leccion'] ?? null;
  $titulo = $_POST['titulo'] ?? null;
  $contenido = $_POST['contenido'] ?? null;
  $ejemplo = $_POST['ejemplo'] ?? null;
  $imagen = $_POST['imagen'] ?? null;
  $video_url = $_POST['video_url'] ?? null;
  $nivel = $_POST['nivel'] ?? 'A1';
  $categoria = $_POST['categoria'] ?? 'Gramática';
  $objetivo = $_POST['objetivo'] ?? null;
  $tips = $_POST['tips'] ?? null;
  $audio_url = $_POST['audio_url'] ?? null;
  $frases_utiles = $_POST['frases_utiles'] ?? null;
  $palabras_clave = $_POST['palabras_clave'] ?? null;
  $ejercicio_recomendado = $_POST['ejercicio_recomendado'] ?? null;

  // 🧩 Validar campos obligatorios
  if (!$id_leccion || !$titulo || !$contenido || !$ejercicio_recomendado) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan campos obligatorios']);
    exit;
  }

  // ✅ Insertar en la base de datos
  $stmt = $conn->prepare("
    INSERT INTO leccion_teoria 
    (id_leccion, titulo, contenido, ejemplo, imagen, video_url, nivel, categoria, objetivo, tips, audio_url, frases_utiles, palabras_clave, ejercicio_recomendado)
    VALUES 
    (:id_leccion, :titulo, :contenido, :ejemplo, :imagen, :video_url, :nivel, :categoria, :objetivo, :tips, :audio_url, :frases_utiles, :palabras_clave, :ejercicio_recomendado)
  ");

  $stmt->execute([
    ':id_leccion' => $id_leccion,
    ':titulo' => $titulo,
    ':contenido' => $contenido,
    ':ejemplo' => $ejemplo,
    ':imagen' => $imagen,
    ':video_url' => $video_url,
    ':nivel' => $nivel,
    ':categoria' => $categoria,
    ':objetivo' => $objetivo,
    ':tips' => $tips,
    ':audio_url' => $audio_url,
    ':frases_utiles' => $frases_utiles,
    ':palabras_clave' => $palabras_clave,
    ':ejercicio_recomendado' => $ejercicio_recomendado
  ]);

  echo json_encode(['status' => 'success', 'message' => 'Lección teórica creada con éxito']);
} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
