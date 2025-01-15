<?php

// Consultar los trofeos con INNER JOIN a grupo y clase para obtener los nombres
$query = "
    SELECT 
        t.id_trofeo, 
        t.id_orquidea, 
        o.nombre_planta, 
        t.id_clase, 
        t.id_grupo, 
        g.nombre_grupo, 
        c.nombre_clase, 
        t.categoria, 
        t.fecha_ganador
    FROM tb_trofeo t
    INNER JOIN tb_orquidea o ON t.id_orquidea = o.id_orquidea
    LEFT JOIN grupo g ON t.id_grupo = g.id_grupo
    LEFT JOIN clase c ON t.id_clase = c.id_clase";

$trofeos = mysqli_query($conexion, $query);
?>
<div class="container mt-3" style="max-width: 60%; margin: 0 auto;">
    <!-- Resultados -->
    <div class="card" style="font-size: 0.9rem;">
        <div class="card-header bg-primary text-white">
            <h2 style="font-size: 1.5rem;">Trofeos Asignados</h2>
        </div>
        <div class="card-body" style="padding: 10px;">
            <a href="#" data-target="desig_trofeo.php" class="btn btn-dark mb-3">+ Asignar Nuevo Trofeo</a>
            
            <!-- Filtros -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="filter_orquidea" class="form-control" placeholder="Filtrar por Orquídea">
                </div>
                <div class="col-md-4">
                    <input type="text" id="filter_clase" class="form-control" placeholder="Filtrar por Clase">
                </div>
                <div class="col-md-4">
                    <input type="text" id="filter_grupo" class="form-control" placeholder="Filtrar por Grupo">
                </div>
            </div>
            
            <table class="table table-bordered table-striped table-sm" id="trofeos_table">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Trofeo</th>
                        <th>Orquídea</th>
                        <th>Clase</th>
                        <th>Grupo</th>
                        <th>Categoría</th>
                        <th>Fecha de Asignación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($trofeos && mysqli_num_rows($trofeos) > 0) {
                        while ($row = mysqli_fetch_assoc($trofeos)) { ?>
                            <tr id="trofeo_<?php echo $row['id_trofeo']; ?>">
                                <td><?php echo $row['id_trofeo']; ?></td>
                                <td><?php echo $row['nombre_planta']; ?></td>
                                <td><?php echo $row['nombre_clase']; ?></td>
                                <td><?php echo $row['nombre_grupo']; ?></td>
                                <td><?php echo $row['categoria']; ?></td>
                                <td><?php echo $row['fecha_ganador']; ?></td>
                                <td>
                                    <!-- Botón de Eliminar -->
                                    <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="<?php echo $row['id_trofeo']; ?>" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron registros.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Función para filtrar filas de la tabla
        function filterTable() {
            const orquidea = $('#filter_orquidea').val().toLowerCase();
            const clase = $('#filter_clase').val().toLowerCase();
            const grupo = $('#filter_grupo').val().toLowerCase();

            $('#trofeos_table tbody tr').each(function () {
                const rowOrquidea = $(this).find('td:nth-child(2)').text().toLowerCase();
                const rowClase = $(this).find('td:nth-child(3)').text().toLowerCase();
                const rowGrupo = $(this).find('td:nth-child(4)').text().toLowerCase();

                const matchOrquidea = rowOrquidea.includes(orquidea);
                const matchClase = rowClase.includes(clase);
                const matchGrupo = rowGrupo.includes(grupo);

                if (matchOrquidea && matchClase && matchGrupo) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Llamar a la función de filtro cada vez que se escriba en los campos
        $('#filter_orquidea, #filter_clase, #filter_grupo').on('input', filterTable);
    });
</script>
