<?php
//include 'auth.php';
include 'conexion.php';

// Verificar si el parámetro "id" está presente en la URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$ticketId = $_GET['id'];

// Obtener la información del ticket
$stmt = $conn->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$ticketId]);
$ticket = $stmt->fetch();

if (!$ticket) {
    header("Location: dashboard.php");
    exit();
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $prioridad = $_POST['prioridad'];
    $estado = $_POST['estado'];
    $asignado_a = $_POST['asignado_a'];

    // Actualizar el ticket en la base de datos
    $stmt = $conn->prepare("UPDATE tickets SET titulo = ?, descripcion = ?, prioridad = ?, estado = ?, asignado_a = ? WHERE id = ?");
    $stmt->execute([$titulo, $descripcion, $prioridad, $estado, $asignado_a, $ticketId]);

    header("Location: dashboard.php");
    exit();
}

// Obtener la lista de técnicos para asignar el ticket
$stmt = $conn->query("SELECT id, nombre FROM usuarios WHERE rol_id = 2"); // Rol 2 = Técnico
$tecnicos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
        <h1>Editar Ticket</h1>
        <form id="editarTicketForm" method="POST">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo $ticket['titulo']; ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo $ticket['descripcion']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="prioridad">Prioridad:</label>
                <select class="form-control" id="prioridad" name="prioridad" required>
                    <option value="Alta" <?php echo ($ticket['prioridad'] == 'Alta') ? 'selected' : ''; ?>>Alta</option>
                    <option value="Media" <?php echo ($ticket['prioridad'] == 'Media') ? 'selected' : ''; ?>>Media</option>
                    <option value="Baja" <?php echo ($ticket['prioridad'] == 'Baja') ? 'selected' : ''; ?>>Baja</option>
                </select>
            </div>
            <div class="form-group">
                <label for="estado">Estado:</label>
                <select class="form-control" id="estado" name="estado" required>
                    <option value="Pendiente" <?php echo ($ticket['estado'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="En progreso" <?php echo ($ticket['estado'] == 'En progreso') ? 'selected' : ''; ?>>En progreso</option>
                    <option value="En revisión" <?php echo ($ticket['estado'] == 'En revisión') ? 'selected' : ''; ?>>En revisión</option>
                    <option value="Resuelto" <?php echo ($ticket['estado'] == 'Resuelto') ? 'selected' : ''; ?>>Resuelto</option>
                </select>
            </div>
            <div class="form-group">
                <label for="asignado_a">Asignado a:</label>
                <select class="form-control" id="asignado_a" name="asignado_a" required>
                    <?php foreach ($tecnicos as $tecnico): ?>
                    <option value="<?php echo $tecnico['id']; ?>" <?php echo ($ticket['asignado_a'] == $tecnico['id']) ? 'selected' : ''; ?>>
                        <?php echo $tecnico['nombre']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>