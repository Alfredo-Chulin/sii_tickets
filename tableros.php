<?php
// tableros.php
require 'config.php'; // Conexión a la base de datos
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

// Obtener los tickets para cada tablero
foreach ($tableros as &$tablero) {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE tablero_id = ? ORDER BY id ASC");
    $stmt->execute([$tablero['id']]);
    $tablero['tickets'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
unset($tablero); // Rompe la referencia con el último elemento
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
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">SII INTERNET</h2>
        <button class="btn btn-success crear-tablero-btn" data-bs-toggle="modal" data-bs-target="#crearTableroModal">+ Crear Tablero</button>
        <div class="scroll-container" id="tablerosContainer">
            <?php foreach ($tableros as $tablero): ?>
                <div class="card tablero-card" data-id="<?php echo $tablero['id']; ?>">
                    
                <div class="contt_title_tab">
                    <h5 class="card-title"><?php echo htmlspecialchars($tablero['nombre']); ?></h5>
                </div>

                    <div class="card-body drag-handle">
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#crearTicketModal<?php echo $tablero['id']; ?>">+ Crear Ticket</button>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarTableroModal<?php echo $tablero['id']; ?>">Editar</button>
                        </div>
                        <br>
                        <!-- Mostrar los tickets del tablero -->
                        <div class="tickets-container" data-tablero-id="<?php echo $tablero['id']; ?>" style="max-width: 100%;">

                            <?php
                                $stmt_tickets = $pdo->prepare("SELECT * FROM tickets WHERE tablero_id = ? ORDER BY posicion ASC");
                                $stmt_tickets->execute([$tablero['id']]);
                                $tickets = $stmt_tickets->fetchAll(PDO::FETCH_ASSOC);

                                if (empty($tickets)) {
                                    echo '<div class="empty-placeholder" style="min-height: 80px;"></div>'; // Zona de drop vacía
                                } else {
                                    foreach ($tickets as $ticket) {
                                        ?>
                                        <div class="ticket-card" data-id="<?php echo $ticket['id']; ?>" title="<?php echo htmlspecialchars($ticket['titulo']); ?>">
                                            
                                        <strong><?php echo htmlspecialchars($ticket['titulo']); ?></strong><br>
                                        
                                        <strong>Cliente:</strong> <?php echo htmlspecialchars($ticket['responsable']); ?><br> <br>
                                        <strong>Fecha:</strong> <?php echo htmlspecialchars($ticket['fecha_contrato']); ?> <br> <br>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarTicketModal<?php echo $ticket['id']; ?>">Editar  </button>
                                        <br>
                                        <button class="btn btn-danger btn-sm">Eliminar</button>
                                    </div>

                                        <?php
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Modal para Editar Ticket -->
                <?php foreach ($tablero['tickets'] as $ticket): ?>
                    <div class="modal fade" id="editarTicketModal<?php echo $ticket['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Ticket</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="procesar_editar_ticket.php" method="POST">
                                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Título</label>
                                            <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($ticket['titulo']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Paquete (MB de Descarga / MB de Subida)</label>
                                            <input type="text" name="paquete" class="form-control" value="<?php echo htmlspecialchars($ticket['paquete']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pago Mensual</label>
                                            <input type="text" name="pago_mensual" class="form-control" value="<?php echo htmlspecialchars($ticket['pago_mensual']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nombre de la persona responsable</label>
                                            <input type="text" name="responsable" class="form-control" value="<?php echo htmlspecialchars($ticket['responsable']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Contacto</label>
                                            <input type="text" name="contacto" class="form-control" value="<?php echo htmlspecialchars($ticket['contacto']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Dirección</label>
                                            <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($ticket['direccion']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Ubicación (Google Maps)</label>
                                            <input type="text" name="ubicacion" class="form-control" value="<?php echo htmlspecialchars($ticket['ubicacion']); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Fecha de contrato</label>
                                            <input type="date" name="fecha_contrato" class="form-control" value="<?php echo htmlspecialchars($ticket['fecha_contrato']); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Dirección IP</label>
                                            <input type="text" name="direccion_ip" class="form-control" value="<?php echo htmlspecialchars($ticket['direccion_ip']); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Modelo de antena</label>
                                            <input type="text" name="modelo_antena" class="form-control" value="<?php echo htmlspecialchars($ticket['modelo_antena']); ?>">
                                        </div>
                                        <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

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
                                    <div class="alert alert-danger" role="alert">
                                        De no tener estos datos, el servicio se tomará como incompleto.
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Título</label>
                                        <input type="text" name="titulo" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Paquete (MB de Descarga / MB de Subida)</label>
                                        <input type="text" name="paquete" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Pago Mensual</label>
                                        <input type="text" name="pago_mensual" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nombre de la persona responsable</label>
                                        <input type="text" name="responsable" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Contacto</label>
                                        <input type="text" name="contacto" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" name="direccion" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ubicación (Google Maps)</label>
                                        <input type="text" name="ubicacion" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Fecha de contrato</label>
                                        <input type="date" name="fecha_contrato" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Dirección IP</label>
                                        <input type="text" name="direccion_ip" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Modelo de antena</label>
                                        <input type="text" name="modelo_antena" class="form-control" required>
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

    <!--Script para poder mover los tickets entre tableros-->
    <script>
       
       document.addEventListener("DOMContentLoaded", function () {
            let scrollContainer = document.getElementById("tablerosContainer");
            let scrollSpeed = 10;
            let scrollStep = 5;
            let scrollInterval;

            function startAutoScroll(direction) {
                stopAutoScroll();
                scrollInterval = setInterval(() => {
                    scrollContainer.scrollLeft += direction * scrollStep;
                }, scrollSpeed);
            }

            function stopAutoScroll() {
                clearInterval(scrollInterval);
            }

            var drake = dragula([...document.querySelectorAll(".tickets-container")], {
                moves: function (el, source, handle) {
                    return true;
                },
                accepts: function (el, target) {
                    return true;
                }
            });

            drake.on("drag", function (el) {
                document.addEventListener("mousemove", function (event) {
                    let rect = scrollContainer.getBoundingClientRect();
                    let mouseX = event.clientX;

                    if (mouseX < rect.left + 50) {
                        startAutoScroll(-1);
                    } else if (mouseX > rect.right - 50) {
                        startAutoScroll(1);
                    } else {
                        stopAutoScroll();
                    }
                });
            });

            drake.on("dragend", function () {
                stopAutoScroll();
            });


    // Función para asegurarnos de que los tableros vacíos mantengan su espacio
    function verificarTablerosVacios() {
        document.querySelectorAll(".tickets-container").forEach(tablero => {
            if (tablero.children.length === 0) {
                let placeholder = document.createElement("div");
                placeholder.classList.add("empty-placeholder");
                placeholder.textContent = "Arrastra aquí";
                placeholder.style.minHeight = "80px";
                placeholder.style.lineHeight = "80px";
                placeholder.style.textAlign = "center";
                placeholder.style.border = "2px dashed #ccc";
                placeholder.style.color = "#999";
                placeholder.style.margin = "5px";
                placeholder.style.fontSize = "14px";

                tablero.appendChild(placeholder);
            }
        });
    }

    // Evento cuando se suelta un ticket en otro tablero
    drake.on("drop", function (el, target, source) {
        setTimeout(() => {
            // 1️⃣ Si el tablero de origen quedó vacío, agregamos el placeholder
            if (source.children.length === 0) {
                let placeholder = document.createElement("div");
                placeholder.classList.add("empty-placeholder");
                placeholder.textContent = "Arrastra aquí";
                placeholder.style.minHeight = "80px";
                placeholder.style.lineHeight = "80px";
                placeholder.style.textAlign = "center";
                placeholder.style.border = "2px dashed #ccc";
                placeholder.style.color = "#999";
                placeholder.style.margin = "5px";
                placeholder.style.fontSize = "14px";
                source.appendChild(placeholder);
            }

            // 2️⃣ Si el tablero destino tenía un placeholder, lo eliminamos
            let placeholders = target.getElementsByClassName("empty-placeholder");
            if (placeholders.length > 0) {
                placeholders[0].remove();
            }

            // 3️⃣ Guardar los cambios en la base de datos
            let ticketId = el.getAttribute("data-id");
            let nuevoTableroId = target.getAttribute("data-tablero-id");

            fetch("actualizar_tablero_ticket.php", {
                method: "POST",
                body: JSON.stringify({
                    ticket_id: ticketId,
                    nuevo_tablero_id: nuevoTableroId
                }),
                headers: {
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log("Respuesta del servidor:", data);
            })
            .catch(error => console.error("Error al actualizar el tablero del ticket:", error));
        }, 100);
    });

    // Ejecutar la verificación al cargar la página
    verificarTablerosVacios();
});

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>