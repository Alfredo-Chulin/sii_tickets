<?php
require 'config.php';
session_start();

if (!isset($_GET['tablero_id'])) {
    echo "Error: No se ha especificado un tablero.";
    exit;
}

$tablero_id = $_GET['tablero_id'];

// Obtener el tablero
$stmt = $pdo->prepare("SELECT * FROM tableros WHERE id = ?");
$stmt->execute([$tablero_id]);
$tablero = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tablero) {
    echo "Error: Tablero no encontrado.";
    exit;
}

// Obtener los tickets del tablero
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE tablero_id = ? ORDER BY posicion ASC");
$stmt->execute([$tablero_id]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tablero['nombre']); ?> - Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center"><?php echo htmlspecialchars($tablero['nombre']); ?></h2>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#crearTicketModal">+ Crear Ticket</button>

        <div id="ticketsContainer" class="row">
            <?php foreach ($tickets as $ticket): ?>
                <div class="col-md-3">
                    <div class="card ticket-card" data-id="<?php echo $ticket['id']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($ticket['titulo']); ?></h5>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($ticket['descripcion'])); ?></p>
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editarTicketModal<?php echo $ticket['id']; ?>">Editar</button>
                            <a href="eliminar_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-danger">Eliminar</a>
                        </div>
                    </div>
                </div>

                <!-- Modal para editar ticket -->
                <div class="modal fade" id="editarTicketModal<?php echo $ticket['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Ticket</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form action="procesar_ticket.php" method="POST">
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Título</label>
                                        <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($ticket['titulo']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Descripción</label>
                                        <textarea name="descripcion" class="form-control"><?php echo htmlspecialchars($ticket['descripcion']); ?></textarea>
                                    </div>
                                    <button type="submit" name="editar_ticket" class="btn btn-warning">Guardar Cambios</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal para crear ticket -->
    <div class="modal fade" id="crearTicketModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="procesar_ticket.php" method="POST">
                        <input type="hidden" name="tablero_id" value="<?php echo $tablero_id; ?>">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Crear</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
