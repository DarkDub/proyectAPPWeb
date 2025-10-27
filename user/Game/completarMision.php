<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(['success'=>false,'mensaje'=>'No estás logueado']);
    exit;
}

require_once __DIR__ . '/../../config/db.php';
$user_id = $_SESSION['user_id'];
$mision_id = $_POST['leccion_id'] ?? null;

if(!$mision_id){
    echo json_encode(['success'=>false,'mensaje'=>'ID de misión inválido']);
    exit;
}

// Traer la misión
$stmt = $conn->prepare("SELECT * FROM misiones_diarias WHERE id=:id");
$stmt->bindParam(':id', $mision_id, PDO::PARAM_INT);
$stmt->execute();
$mision = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$mision){
    echo json_encode(['success'=>false,'mensaje'=>'Misión no encontrada']);
    exit;
}

// Verificar si ya se reclamó la misión
$stmtCheck = $conn->prepare("SELECT id FROM misiones_completadas WHERE id_usuario=:user AND id_mision=:mision");
$stmtCheck->bindParam(':user', $user_id, PDO::PARAM_INT);
$stmtCheck->bindParam(':mision', $mision_id, PDO::PARAM_INT);
$stmtCheck->execute();
$yaReclamada = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if($yaReclamada){
    echo json_encode(['success'=>false,'mensaje'=>'Ya reclamaste esta misión','puntos_ganados'=>0]);
    exit;
}

// Misiones con lección asociada
if(!empty($mision['id_leccion'])){
    // Verificar si el usuario completó la lección
    $stmt2 = $conn->prepare("SELECT completado FROM progreso WHERE id_usuario=:user AND id_leccion=:leccion");
    $stmt2->bindParam(':user', $user_id, PDO::PARAM_INT);
    $stmt2->bindParam(':leccion', $mision['id_leccion'], PDO::PARAM_INT);
    $stmt2->execute();
    $progreso = $stmt2->fetch(PDO::FETCH_ASSOC);

    if(!$progreso || $progreso['completado'] != 1){
        echo json_encode(['success'=>false,'mensaje'=>'Debes completar la lección antes de reclamar','puntos_ganados'=>0]);
        exit;
    }
}

// Guardar misión completada en misiones_completadas
$stmt3 = $conn->prepare("INSERT INTO misiones_completadas (id_usuario,id_mision,fecha) VALUES (:user,:mision,NOW())");
$stmt3->bindParam(':user', $user_id, PDO::PARAM_INT);
$stmt3->bindParam(':mision', $mision_id, PDO::PARAM_INT);
$stmt3->execute();

// Actualizar puntos del usuario
$stmt4 = $conn->prepare("UPDATE usuarios SET puntos = puntos + :puntos WHERE id_usuario=:user");
$stmt4->bindParam(':puntos', $mision['puntos'], PDO::PARAM_INT);
$stmt4->bindParam(':user', $user_id, PDO::PARAM_INT);
$stmt4->execute();

echo json_encode(['success'=>true,'puntos_ganados'=>$mision['puntos']]);
exit;
