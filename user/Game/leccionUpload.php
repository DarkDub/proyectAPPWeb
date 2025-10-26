<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'];
$id_leccion = $data['id_leccion'];
$puntos_ganados = $data['puntos_ganados'];
$palabras = $data['palabras'] ?? [];

//  Actualizar puntos del usuario
$stmt = $conn->prepare("UPDATE usuarios SET puntos = puntos + :puntos WHERE id_usuario = :id");
$stmt->bindParam(':puntos', $puntos_ganados, PDO::PARAM_INT);
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$success = $stmt->execute();

//  Guardar progreso
$stmt2 = $conn->prepare("INSERT INTO progreso (id_usuario, id_leccion, completado, puntos_obtenidos)
                         VALUES (:user, :leccion, 1, :puntos)
                         ON DUPLICATE KEY UPDATE completado=1, puntos_obtenidos=:puntos");
$stmt2->bindParam(':user', $user_id, PDO::PARAM_INT);
$stmt2->bindParam(':leccion', $id_leccion, PDO::PARAM_INT);
$stmt2->bindParam(':puntos', $puntos_ganados, PDO::PARAM_INT);
$stmt2->execute();

//  Guardar palabras aprendidas
foreach($palabras as $palabra){
    $stmt3 = $conn->prepare("INSERT INTO palabras_aprendidas (id_usuario, id_leccion, palabra) 
                             VALUES (:user, :leccion, :palabra)
                             ON DUPLICATE KEY UPDATE fecha_aprendida=NOW()");
    $stmt3->bindParam(':user', $user_id, PDO::PARAM_INT);
    $stmt3->bindParam(':leccion', $id_leccion, PDO::PARAM_INT);
    $stmt3->bindParam(':palabra', $palabra, PDO::PARAM_STR);
    $stmt3->execute();
}

// Registrar actividad
$actividad_desc = "Completó la lección $id_leccion con $puntos_ganados puntos";
$stmt4 = $conn->prepare("INSERT INTO actividad (id_usuario, tipo, descripcion) VALUES (:user, 'Lección', :desc)");
$stmt4->bindParam(':user', $user_id, PDO::PARAM_INT);
$stmt4->bindParam(':desc', $actividad_desc, PDO::PARAM_STR);
$stmt4->execute();

//  Devolver nuevo puntaje
$stmt5 = $conn->prepare("SELECT puntos FROM usuarios WHERE id_usuario = :id");
$stmt5->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt5->execute();
$nuevo_puntaje = $stmt5->fetchColumn();

echo json_encode(['success' => true, 'nuevo_puntaje' => $nuevo_puntaje]);
