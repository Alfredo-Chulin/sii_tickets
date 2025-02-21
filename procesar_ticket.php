<?php
require 'config.php'; // Conexión a la base de datos
session_start();

// Verificar si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar los datos del formulario
    $tablero_id = $_POST['tablero_id'];
    $titulo = htmlspecialchars(trim($_POST['titulo']));
    $paquete = htmlspecialchars(trim($_POST['paquete']));
    $pago_mensual = trim($_POST['pago_mensual']);
    $responsable = htmlspecialchars(trim($_POST['responsable']));
    $contacto = htmlspecialchars(trim($_POST['contacto']));
    $direccion = htmlspecialchars(trim($_POST['direccion']));
    $ubicacion = htmlspecialchars(trim($_POST['ubicacion']));
    $fecha_contrato = !empty($_POST['fecha_contrato']) ? $_POST['fecha_contrato'] : NULL;
    $direccion_ip = htmlspecialchars(trim($_POST['direccion_ip']));
    $modelo_antena = htmlspecialchars(trim($_POST['modelo_antena']));

    // Validación de campos obligatorios
    if (empty($tablero_id) || empty($titulo) || empty($paquete) || empty($pago_mensual) || empty($responsable) || empty($contacto) || empty($direccion)) {
        $_SESSION['error'] = "Todos los campos obligatorios deben estar llenos.";
        header("Location: tableros.php");
        exit;
    }

    // Validar que el tablero exista
    $stmt = $pdo->prepare("SELECT id FROM tableros WHERE id = ?");
    $stmt->execute([$tablero_id]);
    if (!$stmt->fetch()) {
        $_SESSION['error'] = "El tablero seleccionado no existe.";
        header("Location: tableros.php");
        exit;
    }

    // Obtener la posición más alta en el tablero actual
    $stmt = $pdo->prepare("SELECT MAX(posicion) AS max_pos FROM tickets WHERE tablero_id = ?");
    $stmt->execute([$tablero_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $nueva_posicion = ($row['max_pos'] !== null) ? $row['max_pos'] + 1 : 1;

    // Insertar el ticket en la base de datos
    try {
        $stmt = $pdo->prepare("INSERT INTO tickets (tablero_id, titulo, paquete, pago_mensual, responsable, contacto, direccion, ubicacion, fecha_contrato, direccion_ip, modelo_antena, posicion) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$tablero_id, $titulo, $paquete, $pago_mensual, $responsable, $contacto, $direccion, $ubicacion, $fecha_contrato, $direccion_ip, $modelo_antena, $nueva_posicion]);

        // Redirigir al usuario de vuelta a la página del tablero
        $_SESSION['success'] = "El ticket se creó correctamente.";
        header("Location: tableros.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al crear el ticket: " . $e->getMessage();
        header("Location: tableros.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Acceso denegado.";
    header("Location: tableros.php");
    exit;
}
?>