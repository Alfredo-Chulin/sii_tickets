<?php
// tableros.php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Obtener los tableros del usuario
$stmt = $pdo->prepare("SELECT * FROM tableros WHERE usuario_id = ? ORDER BY posicion ASC");
$stmt->execute([$user_id]);
$tableros = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tableros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- Librería Dragula.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.js"></script>
<style>
    .scroll-container {
        display: flex;
        align-items: center;
        overflow-x: auto;
        white-space: nowrap;
        padding: 10px;
        min-height: 150px;
    }
    .tablero-card {
        min-width: 250px;
        margin-right: 10px;
        display: inline-block;
        cursor: grab;
        transition: transform 0.2s ease-in-out;
        position: relative;
    }
</style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Mis Tableros</h2>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#crearTableroModal">+ Crear Tablero</button>
        <div class="scroll-container" id="tablerosContainer">
            <?php foreach ($tableros as $tablero): ?>
                <div class="card tablero-card" data-id="<?php echo $tablero['id']; ?>">
                    <div class="card-body drag-handle">
                        <h5 class="card-title"> <?php echo htmlspecialchars($tablero['nombre']); ?> </h5>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#crearTicketModal<?php echo $tablero['id']; ?>">+ Crear Ticket</button>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarTableroModal<?php echo $tablero['id']; ?>">Editar</button>
                        </div>
                    </div>
                </div>

                <!-- Modal para crear ticket -->
                <div class="modal fade" id="crearTicketModal<?php echo $tablero['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Crear Ticket</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form action="procesar_ticket.php" method="POST">
                                    <input type="hidden" name="tablero_id" value="<?php echo $tablero['id']; ?>">
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

                <!-- Modal para editar tablero -->
                <div class="modal fade" id="editarTableroModal<?php echo $tablero['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Tablero</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form action="procesar_tablero.php" method="POST">
                                    <input type="hidden" name="tablero_id" value="<?php echo $tablero['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Nuevo Nombre</label>
                                        <input type="text" name="nuevo_nombre" class="form-control" value="<?php echo htmlspecialchars($tablero['nombre']); ?>" required>
                                    </div>
                                    <button type="submit" name="editar_tablero" class="btn btn-warning">Guardar Cambios</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal para crear tablero -->
    <div class="modal fade" id="crearTableroModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Tablero</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="procesar_tablero.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Tablero</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Crear</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var container = document.getElementById("tablerosContainer");

        var drake = dragula([container], {
            direction: "horizontal",
            moves: function (el, source, handle) {
                return handle.classList.contains("drag-handle");
            }
        });

        drake.on("drop", function () {
            var orden = [];
            document.querySelectorAll("#tablerosContainer .tablero-card").forEach(function (tablero, index) {
                orden.push({ id: tablero.getAttribute("data-id"), posicion: index });
            });

            fetch("actualizar_orden_tableros.php", {
                method: "POST",
                body: JSON.stringify({ orden: orden.map(item => item.id) }),
                headers: {
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.text())
            .then(data => console.log("Orden actualizado:", data))
            .catch(error => console.error("Error:", error));
        });
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
