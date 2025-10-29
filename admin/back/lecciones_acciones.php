<?php
include __DIR__ . '/../../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'editar') {
        $id = $_POST['id'] ?? null;
        $titulo = trim($_POST['titulo'] ?? '');
        $contenido = trim($_POST['contenido'] ?? '');
        $ejemplo = trim($_POST['ejemplo'] ?? '');
        $ejercicio_recomendado = $_POST['ejercicio_recomendado'] ?? null;

        if (!$id || !$titulo || !$contenido) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan campos obligatorios']);
            exit;
        }

        $stmt = $conn->prepare("
            UPDATE leccion_teoria 
            SET titulo = :titulo, 
                contenido = :contenido, 
                ejemplo = :ejemplo, 
                ejercicio_recomendado = :ejercicio_recomendado
            WHERE id_teoria = :id
        ");

        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':ejemplo', $ejemplo);
        $stmt->bindParam(':ejercicio_recomendado', $ejercicio_recomendado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Lección actualizada correctamente']);
        exit;
    }

    if ($action === 'eliminar') {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            exit;
        }

        // Eliminación lógica
        $stmt = $conn->prepare("UPDATE leccion_teoria SET estado = 'I' WHERE id_teoria = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Lección eliminada correctamente']);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
