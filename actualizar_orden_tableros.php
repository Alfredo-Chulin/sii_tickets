<?php
require 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data["orden"]) || !is_array($data["orden"])) {
        echo "Error: Datos inválidos.";
        exit;
    }

    foreach ($data["orden"] as $posicion => $id_tablero) {
        $stmt = $pdo->prepare("UPDATE tableros SET posicion = ? WHERE id = ?");
        $stmt->execute([$posicion, $id_tablero]);
    }

    echo "Orden actualizado correctamente.";
}
?>
