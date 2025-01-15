<?php 
include 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Estado de Orquídea</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-3" style="max-width: 60%; margin: 0 auto;">
        <!-- Card para agregar el estado -->
        <div class="card" style="font-size: 0.9rem;">
            <div class="card-header bg-primary text-white">
                <h2 style="font-size: 1.5rem;">Agregar Estado a Orquídea</h2>
            </div>
            <div class="card-body" style="padding: 10px;">
                <!-- Mensaje explicativo -->
                <p class="text-muted">
                    Puede buscar orquídeas por <strong>nombre del participante</strong> o <strong>clase</strong>. 
                    Escriba parte del nombre o clase para filtrar las opciones.
                </p>

                <!-- Campo de búsqueda -->
                <div class="mb-3">
                    <label for="search_orquidea" class="form-label">Buscar Orquídea:</label>
                    <input type="text" id="search_orquidea" class="form-control" placeholder="Escriba el nombre o clase para filtrar...">
                </div>

                <form action="../Backend/add_estado.php" method="POST">
                    <div class="mb-3">
                        <label for="id_orquidea" class="form-label">Orquídea</label>
                        <select name="id_orquidea" id="id_orquidea" class="form-select" required>
                            <option value="">Selecciona una orquídea</option>
                            <?php
                            // Consultar las orquídeas disponibles
                            $query = "
                                SELECT o.id_orquidea, o.nombre_planta, c.id_clase, p.nombre AS nombre_participante
                                FROM tb_orquidea o
                                INNER JOIN tb_participante p ON o.id_participante = p.id
                                INNER JOIN clase c ON o.id_clase = c.id_clase
                                WHERE o.id_orquidea NOT IN (SELECT id_orquidea FROM tb_almacenadas)"; // Excluir orquídeas ya almacenadas
                            $orquideas = mysqli_query($conexion, $query);
                            if ($orquideas && mysqli_num_rows($orquideas) > 0) {
                                while ($row = mysqli_fetch_assoc($orquideas)) {
                                    echo '<option value="' . $row['id_orquidea'] . '">Orquídea: ' . $row['nombre_planta'] . ' - Clase: ' . $row['id_clase'] . ' - Participante: ' . $row['nombre_participante'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay orquídeas disponibles</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select name="estado" id="estado" class="form-select" required>
                            <option value="">Selecciona un estado</option>
                            <option value="participando">Participando</option>
                            <option value="almacenada">Almacenada</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo (opcional)</label>
                        <input type="text" name="motivo" id="motivo" class="form-control" placeholder="Motivo del cambio de estado">
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar Estado</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS y Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Filtro dinámico para las opciones del select
        document.getElementById('search_orquidea').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const orquideaOptions = document.getElementById('id_orquidea').options;

            for (let i = 0; i < orquideaOptions.length; i++) {
                const optionText = orquideaOptions[i].textContent.toLowerCase();
                if (optionText.includes(searchValue)) {
                    orquideaOptions[i].style.display = '';
                } else {
                    orquideaOptions[i].style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>
