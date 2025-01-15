<?php
session_start();

// Verificar si la sesión está activa
if (!isset($_SESSION['user_id'])) {
    // Redirigir al login si no hay sesión
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>

    <!-- Enlaces a Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4.5.2/dist/minty/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Enlace a FontAwesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../../Recursos/css/dashboard.css">
    <link rel="stylesheet" href="../../Recursos/css/icons.css">
    <!-- Incluir SweetAlert -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS Bundle (incluye Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>



    <!-- Estilos personalizados para el main-content y las tarjetas pequeñas -->
    <style>
        #contenido-principal {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            /* Alinear las tarjetas en el centro */
            padding: 20px;
            /* Espacio alrededor del contenido principal */
        }

        .my-custom-card {
            width: 550px;
            /* Ancho reducido */
            height: auto;
            /* Altura ajustable al contenido */
            margin: 10px;
            /* Separación entre tarjetas */
            padding: 15px;
            /* Espaciado interno */
            border-radius: 10px;
            /* Bordes redondeados */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            /* Sombra ligera */
            background-color: #f9f9f9;
            /* Color de fondo */
        }

        .my-custom-card .card-body {
            padding: 10px;
            /* Espaciado interno en el cuerpo de la tarjeta */
        }

        .my-custom-card .card-title {
            font-size: 16px;
            /* Tamaño de fuente más pequeño para el título */
            margin-bottom: 10px;
        }

        .my-custom-card .card-text {
            font-size: 14px;
            /* Tamaño de fuente más pequeño para el texto */
            margin-bottom: 10px;
        }

        .my-custom-card .btn {
            font-size: 12px;
            /* Botón pequeño */
            padding: 5px 10px;
            /* Espaciado pequeño en el botón */
        }
    </style>
</head>
<style>
    .responsive-img {
        max-width: 100%;
        /* La imagen no excederá el ancho de su contenedor */
        height: auto;
        /* Mantiene la proporción de la imagen */
    }

    .logo-container {
        position: absolute;
        /* Mantiene la posición en la esquina superior derecha */
        top: 10px;
        right: 10px;
        z-index: 1000;
        /* Asegura que esté por encima de otros elementos */
        display: flex;
        /* Utiliza Flexbox para alinear los logos */
        flex-direction: row;
        /* Alineación horizontal */
        align-items: center;
        /* Centra verticalmente los logos */
    }

    .main-content {
        margin-top: 80px;
        /* Añade margen superior para evitar superposición con los logos */
        padding: 20px;
        /* Espaciado interno para el contenido */
    }
</style>
<!-- Logo de la universidad en la esquina superior derecha -->
<div class="logo-container">
    <!-- Segundo logo -->
    <img src="/Recursos/img/Logo-fotor-bg-remover-2024090519443.png" alt="Logo 2" class="responsive-img" style="width: 150px; margin-right: 10px;">
    <img src="/Recursos/img/hdumg.png" alt="Logo Universidad" class="responsive-img" style="width: 220px;">
    <!-- -->
</div>

<body>
    <!-- Sidebar -->
    <?php include '../Vistas/modales/side_estado.php'; ?>
    <!-- Contenido principal donde se aplicarán las tarjetas pequeñas -->
    <div id="contenido-principal">
        <?php include '../Vistas/Cards/card_estado/std.php' ?>
    </div>


    <!-- Enlaces a Bootstrap JS, jQuery y tus scripts personalizados -->
    <!-- Incluye jQuery antes de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <script src="../../Recursos/js/side.js"></script>
    <script>
        $(document).on('click', '.btn-editar', function() {
            var idOrquidea = $(this).data('id'); // Obtener el ID de la orquídea

            // Cargar la vista de edición directamente en el contenedor principal
            $.ajax({
                url: '../Vistas/Cards/card_estado/edit_std.php', // Ruta del archivo PHP que se va a cargar
                type: 'GET',
                data: {
                    id_orquidea: idOrquidea
                }, // Enviar el ID de la orquídea a la solicitud
                success: function(response) {
                    // Reemplazar el contenido de #contenido-principal con la respuesta (edit_std.php)
                    $('#contenido-principal').html(response);
                },
                error: function(err) {
                    console.error('Error al cargar la página de edición de estado:', err);
                }
            });
        });
    </script>

    <!-- Script para manejar la carga dinámica -->
    <script>
        $(document).ready(function() {
            // Interceptar el clic en los enlaces del menúF
            $('ul li a').click(function(e) {
                if ($(this).hasClass('no-ajax')) {
                    return; // Si es un enlace sin AJAX, no hacer nada
                }
                e.preventDefault(); // Prevenir la acción predeterminada del enlace

                var target = $(this).data('target'); // Obtener el archivo objetivo

                // Usar AJAX para cargar el archivo PHP dentro del contenedor principal
                $.ajax({
                    url: target,
                    type: 'GET',
                    success: function(response) {
                        $('#contenido-principal').html(response); // Reemplazar el contenido
                    },
                    error: function() {
                        alert('Error al cargar el contenido.');
                    }
                });
            });

            // Función para descargar el reporte de juzgamiento
            window.downloadReport = function() {
                window.open('../../Vistas/Documentos/pdf/listado_pdf.php', '_blank');
            }
        });
    </script>

</body>



</html>