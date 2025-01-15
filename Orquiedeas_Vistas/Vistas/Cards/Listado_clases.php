<?php
include './conexion_db.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Listado de Orquídeas por Clases</h3>
        <p>Genera un reporte de orquídeas clasificadas por categorías en el período seleccionado.</p>
    </div>
    <div class="card-body">
        <form method="GET" action="../Vistas/Documentos/pdf/Listado_por_clases.php" target="_blank">
            <label for="start-date">Fecha de inicio:</label>
            <input type="date" id="start-date-clases" name="start_date" class="form-control" required>
            <br>
            <label for="end-date">Fecha de fin:</label>
            <input type="date" id="end-date-clases" name="end_date" class="form-control" required>
            <br>
            <label for="class-filter">Clase:</label>
            <select id="class-filter" name="class_filter" class="form-control">
                <option value="">Todas las clases</option>
                <?php
                // Obtener clases de la base de datos
                $queryClases = "SELECT id_clase, nombre_clase FROM clase";
                $resultClases = $conexion->query($queryClases);
                while ($row = $resultClases->fetch_assoc()) {
                    echo '<option value="' . $row['id_clase'] . '">' . $row['nombre_clase'] . '</option>';
                }
                ?>
            </select>
            <br><br>
            <button type="submit" class="btn btn-primary">Generar Reporte</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Limitar la fecha de fin a la fecha actual
        const endDate = document.getElementById("end-date-clases");
        const today = new Date().toISOString().split("T")[0];
        endDate.setAttribute("max", today);
    });
</script>