<?php
include '../Backend/Conexion_bd.php'; // Ajusta la ruta según tu estructura


// Capturar ID y tipo de usuario desde la sesión
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Crear la consulta para participantes según el tipo de usuario
if ($user_type == 5) {
    // Si el usuario es tipo 5, mostrar solo los participantes que él registró
    $query = "SELECT `id`, `nombre` FROM `tb_participante` WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $participantes = $stmt->get_result();
} else {
    // Si es administrador, mostrar todos los participantes
    $query = "SELECT `id`, `nombre` FROM `tb_participante`";
    $participantes = mysqli_query($conexion, $query);
}

// Consultar los grupos de orquídeas
$grupos = mysqli_query($conexion, "SELECT `id_grupo`, `nombre_grupo` FROM `grupo`");
?>
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card mx-auto" style="max-width: 100%;">
                <div class="card-header bg-primary text-white text-center">
                    <h3><i class="fas fa-leaf"></i> Registrar Orquídea</h3>
                </div>
                <div class="card-body">
                    <form id="form-orquidea" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="nombre_planta" class="form-label">Nombre de la Planta</label>
                                <input type="text" class="form-control" id="nombre_planta" name="nombre_planta" required>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <label for="origen" class="form-label">Origen</label>
                                <select class="form-select" id="origen" name="origen" required>
                                    <option value="">Selecciona el Origen</option>
                                    <option value="Especie">Especie</option>
                                    <option value="Híbrida">Híbrida</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <label for="id_grupo" class="form-label">Grupo</label>
                                <select class="form-select" id="id_grupo" name="id_grupo" required>
                                    <option value="">Selecciona un Grupo</option>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($grupos)) {
                                        echo '<option value="' . $row['id_grupo'] . '">' . $row['nombre_grupo'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <label for="id_clase" class="form-label">Clase</label>
                                <select class="form-select" id="id_clase" name="id_clase" required>
                                    <option value="">Selecciona una Clase</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <label for="contador" class="form-label">Cantidad de Orquídeas</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary" id="decrementar">-</button>
                                    <input type="number" class="form-control text-center" id="contador" name="contador" value="1" min="1" max="99" readonly>
                                    <button type="button" class="btn btn-outline-secondary" id="incrementar">+</button>
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="id_participante" class="form-label">Participante</label>
                                <select class="form-select" id="id_participante" name="id_participante" required>
                                    <option value="">Selecciona un Participante</option>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($participantes)) {
                                        echo '<option value="' . $row['id'] . '">' . $row['nombre'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="foto" class="form-label">Foto de la Orquídea</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            </div>

                            <div class="col-12 mb-3 text-center">
                                <button type="button" class="btn btn-primary" id="abrir-camara">Abrir Cámara</button>
                                <button type="button" class="btn btn-secondary" id="apagar-camara" style="display:none;">Apagar Cámara</button>
                                <video id="video" class="mt-2 w-100" style="display:none;" autoplay></video>
                                <canvas id="canvas" style="display:none;"></canvas>
                                <button type="button" id="capturar" class="btn btn-success mt-2" style="display:none;">Capturar</button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Registrar Orquídea</button>

                        <a href="./Dashboard.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Inicio
                        </a>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Mejora de estilos para responsividad */
    @media (max-width: 575.98px) {
        #video {
            width: 100%;
            height: auto;
        }

        .card {
            margin: 10px 0;
        }

        .form-control,
        .form-select {
            font-size: 1rem;
        }

        .btn {
            font-size: 1rem;
            padding: 10px;
        }
    }
</style>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const incrementButton = document.getElementById('incrementar');
        const decrementButton = document.getElementById('decrementar');
        const contadorInput = document.getElementById('contador');

        // Incrementar el valor del contador hasta un máximo de 99
        incrementButton.addEventListener('click', () => {
            let count = parseInt(contadorInput.value);
            if (count < 99) {
                contadorInput.value = count + 1;
            }
        });

        // Decrementar el valor del contador hasta un mínimo de 1
        decrementButton.addEventListener('click', () => {
            let count = parseInt(contadorInput.value);
            if (count > 1) {
                contadorInput.value = count - 1;
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
        const userType = <?php echo json_encode($_SESSION['user_type']); ?>;

        console.log("ID del Usuario:", userId);
        console.log("Tipo de Usuario:", userType);

        if (userType === 5) {
            console.log("Mostrando solo los participantes registrados por el usuario.");
        } else {
            console.log("Mostrando todos los participantes.");
        }
    });

    let isFormDirty = false;
    let isFormSubmitted = false;

    // Detectar cambios en el formulario
    $('#form-orquidea :input').on('change', function() {
        isFormDirty = true;
    });

    $('#form-orquidea').on('submit', function() {
        isFormSubmitted = true;
    });

    // Advertencia al intentar salir con cambios sin guardar
    window.addEventListener('beforeunload', function(e) {
        if (isFormDirty && !isFormSubmitted) {
            e.preventDefault();
            e.returnValue = 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
        }
    });
</script>