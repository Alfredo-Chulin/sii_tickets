<?php
require 'config.php'; // Asegúrate de que este archivo contiene la conexión a la base de datos

// Recibir los datos del fetch en formato JSON
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['ticket_id']) && isset($data['nuevo_tablero_id'])) {
    $ticket_id = $data['ticket_id'];
    $nuevo_tablero_id = $data['nuevo_tablero_id'];

    // Actualizar la base de datos con el nuevo tablero_id
    $stmt = $pdo->prepare("UPDATE tickets SET tablero_id = ? WHERE id = ?");
    if ($stmt->execute([$nuevo_tablero_id, $ticket_id])) {
        echo json_encode(["status" => "success", "message" => "Ticket actualizado correctamente"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo actualizar el ticket"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Datos inválidos"]);
}

?>
