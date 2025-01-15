<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción de Orquídeas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="text-center">Inscripción de Orquídeas</h3>
            </div>
            <div class="card-body">
                <!-- Filtro y selector de Participante -->
                <div class="mb-3">
                    <label for="buscarParticipante" class="form-label">Buscar Participante</label>
                    <input type="text" class="form-control" id="buscarParticipante" placeholder="Escribe el nombre del participante...">
                </div>
                
                <div class="mb-3">
                    <label for="selectParticipante" class="form-label">Selecciona Participante</label>
                    <select class="form-select" id="selectParticipante">
                        <option value="">Selecciona un participante</option>
                        <?php
                        include 'Conexion_bd.php';
                        $query_participantes = "SELECT `id`, `nombre` FROM `tb_participante`";
                        $result_participantes = mysqli_query($conexion, $query_participantes);
                        while ($row = mysqli_fetch_assoc($result_participantes)) {
                            echo '<option value="' . $row['id'] . '">' . $row['nombre'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="alert alert-warning alert-dismissible fade show" role="alert" style="color: #6c5302;">
                    <strong>⚠ Recuerda:</strong> al finalizar la inscripción de tus orquídeas, descarga el comprobante de preinscripción y entrégalo a la asociación.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Filtro y selector de Orquídea -->
                <div id="orquideasContainer" class="mb-3" style="display: none;">
                    <label for="buscarOrquidea" class="form-label">Buscar Orquídea</label>
                    <input type="text" class="form-control" id="buscarOrquidea" placeholder="Escribe el nombre de la orquídea...">
                    
                    <label for="selectOrquidea" class="form-label mt-2">Orquídeas Inscritas</label>
                    <div class="input-group">
                        <select class="form-select" id="selectOrquidea">
                            <option value="">Selecciona una orquídea</option>
                        </select>
                        <input type="number" class="form-control" id="correlativo" placeholder="Correlativo">
                        <button class="btn btn-success" type="button" id="agregarOrquidea">Add</button>
                    </div>
                </div>

                <div id="tablaOrquideas" style="display: none;">
                    <h5 class="mt-3">Orquídeas Seleccionadas</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre de la Orquídea</th>
                                <th>Correlativo</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="listaOrquideas">
                        </tbody>
                    </table>
                </div>

                <button id="registrarInscripcion" class="btn btn-primary w-100" style="display: none;">Finalizar Inscripción</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Filtro en tiempo real para el selector de participantes
            $('#buscarParticipante').on('input', function() {
                const filtro = $(this).val().toLowerCase();
                $('#selectParticipante option').each(function() {
                    const participante = $(this).text().toLowerCase();
                    $(this).toggle(participante.includes(filtro) || !filtro);
                });
            });

            // Filtro en tiempo real para el selector de orquídeas
            $('#buscarOrquidea').on('input', function() {
                const filtro = $(this).val().toLowerCase();
                $('#selectOrquidea option').each(function() {
                    const orquidea = $(this).text().toLowerCase();
                    $(this).toggle(orquidea.includes(filtro) || !filtro);
                });
            });

            // Cargar orquídeas al seleccionar un participante
            $('#selectParticipante').change(function() {
                const participanteId = $(this).val();
                if (participanteId) {
                    $.ajax({
                        url: '../Backend/orquideas_inscripcion.php',
                        type: 'POST',
                        data: {
                            action: 'get_orquideas',
                            id_participante: participanteId
                        },
                        success: function(data) {
                            const response = JSON.parse(data);
                            $('#selectOrquidea').html(response.options);
                            $('#orquideasContainer').show();
                        },
                        error: function() {
                            Swal.fire('Error', 'No se pudieron cargar las orquídeas.', 'error');
                        }
                    });
                }
            });

            // Agregar orquídea a la lista de inscripción
            $('#agregarOrquidea').click(function() {
                const orquideaId = $('#selectOrquidea').val();
                const orquideaNombre = $('#selectOrquidea option:selected').text();
                const correlativo = $('#correlativo').val();

                if (orquideaId && correlativo) {
                    const fila = `
                    <tr data-id="${orquideaId}">
                        <td>${orquideaNombre}</td>
                        <td>${correlativo}</td>
                        <td><button type="button" class="btn btn-danger btn-sm eliminarOrquidea">Quitar</button></td>
                    </tr>`;
                    $('#listaOrquideas').append(fila);
                    $('#tablaOrquideas').show();
                    $('#registrarInscripcion').show();
                    $('#selectOrquidea option:selected').remove();
                    $('#correlativo').val('');
                } else {
                    Swal.fire('Error', 'Seleccione una orquídea y asigne un correlativo.', 'error');
                }
            });

            // Eliminar orquídea de la lista
            $(document).on('click', '.eliminarOrquidea', function() {
                const orquideaId = $(this).closest('tr').data('id');
                const orquideaNombre = $(this).closest('tr').find('td:first').text();
                $('#selectOrquidea').append(new Option(orquideaNombre, orquideaId));
                $(this).closest('tr').remove();
                if ($('#listaOrquideas tr').length === 0) {
                    $('#tablaOrquideas').hide();
                    $('#registrarInscripcion').hide();
                }
            });

            $('#registrarInscripcion').click(function() {
                const orquideas = [];
                $('#listaOrquideas tr').each(function() {
                    const id = $(this).data('id');
                    const correlativo = $(this).find('td:eq(1)').text();
                    orquideas.push({
                        id_orquidea: id,
                        correlativo: correlativo
                    });
                });

                $.ajax({
                    url: '../Backend/orquideas_inscripcion.php',
                    type: 'POST',
                    data: {
                        action: 'registrar_inscripcion',
                        id_participante: $('#selectParticipante').val(),
                        orquideas: JSON.stringify(orquideas)
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Éxito', response.message, 'success');
                            $('#listaOrquideas').empty();
                            $('#tablaOrquideas').hide();
                            $('#registrarInscripcion').hide();
                            $('#selectParticipante').val('');
                            $('#orquideasContainer').hide();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error("Error en el servidor:", xhr.responseText);
                        Swal.fire('Error', 'No se pudo registrar la inscripción. Verifica la consola para más detalles.', 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>
