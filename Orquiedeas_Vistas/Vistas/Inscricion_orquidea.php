<?php
include '../Backend/Conexion_bd.php';
session_start();

// Capturar el ID del usuario y el tipo de usuario desde la sesión
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Consultar los participantes y los grupos desde la base de datos
$participantes = mysqli_query($conexion, "SELECT `id`, `nombre` FROM `tb_participante`");
$grupos = mysqli_query($conexion, "SELECT `id_grupo`, `nombre_grupo` FROM `grupo`");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Orquídea</title>
    <!-- Enlaces a Bootstrap JS (necesario para el funcionamiento de los modales) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4.5.2/dist/minty/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../../Recursos/css/dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
</head>

<body>
    <style>
        .responsive-img {
            max-width: 100%; /* La imagen no excederá el ancho de su contenedor */
            height: auto;    /* Mantiene la proporción de la imagen */
        }
        .logo-container {
            position: absolute; /* Mantiene la posición en la esquina superior derecha */
            top: 10px;
            right: 10px;
            z-index: 1000; /* Asegura que esté por encima de otros elementos */
            display: flex; /* Utiliza Flexbox para alinear los logos */
            flex-direction: row; /* Alineación horizontal */
            align-items: center; /* Centra verticalmente los logos */
        }

        .main-content {
            margin-top: 80px; /* Añade margen superior para evitar superposición con los logos */
            padding: 20px; /* Espaciado interno para el contenido */
        }
    </style>
    <!-- Logo de la universidad en la esquina superior derecha -->
    
    <!-- Sidebar -->
        <div class="sidebar">
        <h2>Admin Panel</h2>
            <ul>
                <li><a href="Dashboard.php"><i class="fas fa-home"></i> <span>Volver al Inicio</span></a></li>
            </ul>
        </div>


    <!-- Contenido principal -->
    <div id="contenido-principal">
        <?php include '../Vistas/Cards/card_inscripcion/card_inscri.php' ?>
    </div>

    <script>
        // Script para cargar clases según el grupo seleccionado
        $('#id_grupo').on('change', function() {
            var id_grupo = $(this).val();

            // Si se selecciona un grupo, cargar las clases por AJAX
            if (id_grupo) {
                $.ajax({
                    type: 'POST',
                    url: '../Backend/get_clases.php', // Cambia la ruta si es necesario
                    data: {
                        id_grupo: id_grupo
                    },
                    success: function(response) {
                        $('#id_clase').html(response); // Actualizar el select de clases con los resultados
                    },
                    error: function() {
                        alert("Error al cargar las clases");
                    }
                });
            } else {
                $('#id_clase').html('<option value="">Selecciona una Clase</option>');
            }
        });

        // Manejador del formulario
        $('#form-orquidea').on('submit', function(e) {
            e.preventDefault(); // Prevenir el envío tradicional del formulario

            var formData = new FormData(this);

            $.ajax({
                url: '../Backend/agregar_orquidea.php', // Cambia esta URL si es necesario
                type: 'POST',
                data: formData,
                processData: false, // No procesar los datos
                contentType: false, // No establecer un content-type específico
                dataType: 'json', // Esperamos una respuesta JSON
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Orquídea registrada',
                            text: response.message,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = 'Neva_orquidea.php'; // Redirigir
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error en la solicitud: ', jqXHR.status, errorThrown);
                    console.log(jqXHR.responseText);
                    try {
                        let parsedResponse = JSON.parse(jqXHR.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: parsedResponse.message,
                            confirmButtonText: 'Aceptar'
                        });
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo procesar la solicitud. Revisa la consola para más detalles.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                }
            });
        });
    </script>
    

    <script src="../../Recursos/js/side.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>