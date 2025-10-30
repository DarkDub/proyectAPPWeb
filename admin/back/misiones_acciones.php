<?php
ob_start(); // Limpia cualquier salida previa
include __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');
session_start();

// 🔒 Validar sesión de admin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

// 🔹 Verificar acción enviada
$action = $_POST['action'] ?? '';

try {
    if ($action === 'crear') {
        $stmt = $conn->prepare("
            INSERT INTO misiones_diarias (nombre, descripcion, tipo, puntos, id_leccion)
            VALUES (:nombre, :descripcion, :tipo, :puntos, :id_leccion)
        ");
        $stmt->execute([
            ':nombre' => $_POST['nombre'] ?? '',
            ':descripcion' => $_POST['descripcion'] ?? '',
            ':tipo' => $_POST['tipo'] ?? '',
            ':puntos' => $_POST['puntos'] ?? 0,
            ':id_leccion' => $_POST['id_leccion'] ?? null
        ]);
        echo json_encode(['status' => 'success', 'message' => ' Misión creada correctamente']);
    }

    elseif ($action === 'editar') {
        $stmt = $conn->prepare("
            UPDATE misiones_diarias
            SET nombre=:nombre, descripcion=:descripcion, tipo=:tipo, puntos=:puntos, id_leccion=:id_leccion
            WHERE id=:id
        ");
        $stmt->execute([
            ':nombre' => $_POST['nombre'] ?? '',
            ':descripcion' => $_POST['descripcion'] ?? '',
            ':tipo' => $_POST['tipo'] ?? '',
            ':puntos' => $_POST['puntos'] ?? 0,
            ':id_leccion' => $_POST['id_leccion'] ?? null,
            ':id' => $_POST['id'] ?? 0
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Misión actualizada correctamente']);
    }

    elseif ($action === 'eliminar') {
        $stmt = $conn->prepare("DELETE FROM misiones_diarias WHERE id=:id");
        $stmt->execute([':id' => $_POST['id'] ?? 0]);
        echo json_encode(['status' => 'success', 'message' => 'Misión eliminada correctamente']);
    }

    else {
        echo json_encode(['status' => 'error', 'message' =>  'Acción inválida']);
    }

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => ' Error en la base de datos: ' . $e->getMessage()
    ]);
}

ob_end_flush();
exit;
