<?php
// procesar_tablero.php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['editar_tablero']) && isset($_POST['tablero_id']) && isset($_POST['nuevo_nombre'])) {
        // Editar tablero
        $tablero_id = $_POST['tablero_id'];
        $nuevo_nombre = trim($_POST['nuevo_nombre']);
        
        if (!empty($nuevo_nombre)) {
            try {
                $stmt = $pdo->prepare("UPDATE tableros SET nombre = :nuevo_nombre WHERE id = :tablero_id AND usuario_id = :usuario_id");
                $stmt->execute(['nuevo_nombre' => $nuevo_nombre, 'tablero_id' => $tablero_id, 'usuario_id' => $_SESSION['user_id']]);
                header("Location: tableros.php");
                exit;
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger text-center'>Error: " . $e->getMessage() . "</div>";
            }
        } else {
            echo "<div class='alert alert-warning text-center'>El nuevo nombre del tablero es obligatorio.</div>";
        }
    } elseif (isset($_POST['nombre'])) {
        // Crear tablero
        $nombre = trim($_POST['nombre']);
        $usuario_id = $_SESSION['user_id'];

        if (!empty($nombre)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO tableros (nombre, usuario_id) VALUES (:nombre, :usuario_id)");
                $stmt->execute(['nombre' => $nombre, 'usuario_id' => $usuario_id]);
                header("Location: tableros.php");
                exit;
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger text-center'>Error: " . $e->getMessage() . "</div>";
            }
        } else {
            echo "<div class='alert alert-warning text-center'>El nombre del tablero es obligatorio.</div>";
        }
    }
} else {
    header("Location: tableros.php");
    exit;
}
?>
