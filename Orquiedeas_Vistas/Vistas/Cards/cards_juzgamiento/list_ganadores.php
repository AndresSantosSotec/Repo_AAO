<?php
if (isset($_GET['mensaje'])) {
    if ($_GET['mensaje'] == 'eliminado') {
        echo '<div class="alert alert-success">Ganador eliminado exitosamente.</div>';
    } elseif ($_GET['mensaje'] == 'error') {
        echo '<div class="alert alert-danger">Error al intentar eliminar el ganador.</div>';
    }
}

// Validar la conexión
if (!$conexion) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

// Consultar los ganadores y ordenar por categoría (grupo, clase) y posición
$query = "
    SELECT 
        g.id_ganador, 
        o.nombre_planta AS nombre_orquidea, 
        gr.nombre_grupo AS nombre_grupo, 
        c.nombre_clase AS nombre_clase, 
        i.correlativo, 
        g.posicion, 
        g.empate, 
        g.fecha_ganador
    FROM tb_ganadores g
    INNER JOIN tb_orquidea o ON g.id_orquidea = o.id_orquidea
    INNER JOIN grupo gr ON g.id_grupo = gr.id_grupo
    INNER JOIN clase c ON g.id_clase = c.id_clase
    INNER JOIN tb_inscripcion i ON g.id_orquidea = i.id_orquidea
    ORDER BY gr.nombre_grupo, c.nombre_clase, g.posicion";

$ganadores = mysqli_query($conexion, $query);

if (!$ganadores) {
    die("Error en la consulta: " . mysqli_error($conexion));
}
?>

<div class="container mt-3" style="max-width: 80%; margin: 0 auto;">
    <!-- Filtros de búsqueda -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="search_orquidea">Buscar Orquídea:</label>
            <input type="text" id="search_orquidea" class="form-control" placeholder="Nombre de la orquídea">
        </div>
        <div class="col-md-3">
            <label for="search_grupo">Buscar Grupo:</label>
            <input type="text" id="search_grupo" class="form-control" placeholder="Grupo (Ej: Grupo A)">
        </div>
        <div class="col-md-3">
            <label for="search_clase">Buscar Clase:</label>
            <input type="text" id="search_clase" class="form-control" placeholder="Clase">
        </div>
        <div class="col-md-3">
            <label for="search_correlativo">Buscar Correlativo:</label>
            <input type="text" id="search_correlativo" class="form-control" placeholder="Correlativo">
        </div>
    </div>

    <!-- Resultados -->
    <div class="card" style="font-size: 0.9rem;">
        <div class="card-header bg-primary text-white">
            <h2 style="font-size: 1.5rem;">Ganadores Registrados</h2>
        </div>
        <div class="card-body" style="padding: 10px;">
            <table class="table table-bordered table-striped table-sm" id="ganadores_table">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Ganador</th>
                        <th>Nombre Orquídea</th>
                        <th>Grupo</th>
                        <th>Clase</th>
                        <th>Correlativo</th>
                        <th>Posición</th>
                        <th>Empate</th>
                        <th>Fecha de Ganador</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($ganadores) > 0) {
                        while ($row = mysqli_fetch_assoc($ganadores)) { ?>
                            <tr id="ganador_<?php echo $row['id_ganador']; ?>">
                                <td><?php echo $row['id_ganador']; ?></td>
                                <td><?php echo $row['nombre_orquidea']; ?></td>
                                <td><?php echo $row['nombre_grupo']; ?></td>
                                <td><?php echo $row['nombre_clase']; ?></td>
                                <td><?php echo $row['correlativo']; ?></td>
                                <td><?php echo $row['posicion']; ?>° Lugar</td>
                                <td><?php echo $row['empate'] ? 'Sí' : 'No'; ?></td>
                                <td><?php echo $row['fecha_ganador']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm btn-editar" data-id="<?php echo $row['id_ganador']; ?>" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="#" class="btn btn-danger btn-sm btn-eliminar" data-id="<?php echo $row['id_ganador']; ?>">🗑️</a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="9" class="text-center">No se encontraron registros.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Incluir la librería de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Filtrar ganadores en tiempo real
    const filters = ['search_orquidea', 'search_grupo', 'search_clase', 'search_correlativo'];
    filters.forEach(filter => {
        document.getElementById(filter).addEventListener('input', function() {
            const searchOrquidea = document.getElementById('search_orquidea').value.toLowerCase();
            const searchGrupo = document.getElementById('search_grupo').value.toLowerCase();
            const searchClase = document.getElementById('search_clase').value.toLowerCase();
            const searchCorrelativo = document.getElementById('search_correlativo').value.toLowerCase();

            const rows = document.querySelectorAll('#ganadores_table tbody tr');
            rows.forEach(row => {
                const orquidea = row.children[1].textContent.toLowerCase();
                const grupo = row.children[2].textContent.toLowerCase();
                const clase = row.children[3].textContent.toLowerCase();
                const correlativo = row.children[4].textContent.toLowerCase();

                row.style.display = 
                    orquidea.includes(searchOrquidea) &&
                    grupo.includes(searchGrupo) &&
                    clase.includes(searchClase) &&
                    correlativo.includes(searchCorrelativo)
                    ? '' : 'none';
            });
        });
    });

    // Manejo de la eliminación de ganadores
    $(document).on('click', '.btn-eliminar', function() {
        var idGanador = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../Backend/eliminar_ganador.php',
                    type: 'POST',
                    data: { id: idGanador },
                    success: function(response) {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.status === 'success') {
                            Swal.fire('Eliminado!', 'El ganador ha sido eliminado.', 'success');
                            $('#ganador_' + idGanador).remove();
                        } else {
                            Swal.fire('Error!', jsonResponse.message, 'error');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error!', 'No se pudo eliminar el registro.', 'error');
                    }
                });
            }
        });
    });

    // Manejo de la edición
    $(document).on('click', '.btn-editar', function() {
        var idGanador = $(this).data('id');

        $.ajax({
            url: '../Vistas/Cards/Edit_ganador.php',
            type: 'GET',
            data: { id: idGanador },
            success: function(response) {
                $('#contenido-principal').html(response);
            },
            error: function(err) {
                console.error('Error al cargar la página de edición:', err);
            }
        });
    });
</script>
