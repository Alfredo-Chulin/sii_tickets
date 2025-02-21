<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ticket_id = $_POST['ticket_id'];
    $titulo = $_POST['titulo'];
    $paquete = $_POST['paquete'];
    $pago_mensual = $_POST['pago_mensual'];
    $responsable = $_POST['responsable'];
    $contacto = $_POST['contacto'];
    $direccion = $_POST['direccion'];
    $ubicacion = $_POST['ubicacion'];
    $fecha_contrato = $_POST['fecha_contrato'];
    $direccion_ip = $_POST['direccion_ip'];
    $modelo_antena = $_POST['modelo_antena'];

    $stmt = $pdo->prepare("UPDATE tickets SET titulo = ?, paquete = ?, pago_mensual = ?, responsable = ?, contacto = ?, direccion = ?, ubicacion = ?, fecha_contrato = ?, direccion_ip = ?, modelo_antena = ? WHERE id = ?");
    $stmt->execute([$titulo, $paquete, $pago_mensual, $responsable, $contacto, $direccion, $ubicacion, $fecha_contrato, $direccion_ip, $modelo_antena, $ticket_id]);

    header("Location: tableros.php");
    exit;
}
?>
