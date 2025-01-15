<?php
// Incluir conexión a la base de datos
include "../Backend/Conexion_bd.php";
session_start();

// Verificar si la sesión está activa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirigir al login si no hay sesión
    exit;
}

// Año actual
$year = date('Y');
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type']; // Capturar tipo de usuario

// Consultar participantes según el tipo de usuario
if ($user_type == 5) {
    $sql_participantes = "SELECT COUNT(*) AS total_participantes 
                          FROM tb_participante 
                          WHERE YEAR(fecha_creacion) = ? AND id_usuario = ?";
    $stmt1 = $conexion->prepare($sql_participantes);
    $stmt1->bind_param("ii", $year, $user_id);
} else {
    $sql_participantes = "SELECT COUNT(*) AS total_participantes 
                          FROM tb_participante 
                          WHERE YEAR(fecha_creacion) = ?";
    $stmt1 = $conexion->prepare($sql_participantes);
    $stmt1->bind_param("i", $year);
}
$stmt1->execute();
$result1 = $stmt1->get_result();
$total_participantes = $result1->fetch_assoc()['total_participantes'];
$stmt1->close();

// Consultar orquídeas según el tipo de usuario
if ($user_type == 5) {
    $sql_orquideas = "SELECT COUNT(*) AS total_orquideas 
                      FROM tb_orquidea 
                      WHERE YEAR(fecha_creacion) = ? AND id_participante IN 
                          (SELECT id FROM tb_participante WHERE id_usuario = ?)";
    $stmt2 = $conexion->prepare($sql_orquideas);
    $stmt2->bind_param("ii", $year, $user_id);
} else {
    $sql_orquideas = "SELECT COUNT(*) AS total_orquideas 
                      FROM tb_orquidea 
                      WHERE YEAR(fecha_creacion) = ?";
    $stmt2 = $conexion->prepare($sql_orquideas);
    $stmt2->bind_param("i", $year);
}
$stmt2->execute();
$result2 = $stmt2->get_result();
$total_orquideas = $result2->fetch_assoc()['total_orquideas'];
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incio</title>

    <!-- Enlaces a Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4.5.2/dist/minty/bootstrap.min.css">

    <!-- Enlace a FontAwesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../../Recursos/css/dashboard.css">
    <link rel="stylesheet" href="../../Recursos/css/icons.css">
    <link rel="icon" href="/Recursos/img/Logo-fotor-bg-remover-2024090519443.png" type="image/x-icon">

</head>

<body>
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
        <img src="/Recursos/img/Logo-fotor-bg-remover-2024090519443.png" alt="Logo 2" class="responsive-img" style="width: 105px; margin-right: 10px;">
        <img src="/Recursos/img/hdumg.png" alt="Logo Universidad" class="responsive-img" style="width: 322px;">
        <!-- -->
    </div>
    <!-- Sidebar -->
    <?php include '../Vistas/modales/side.php'; ?>
    <!---->
    <div class="main-content" id="main-content">
        <h1>Bienvenido</h1>
        <b>Haz click en el icono que quieres acceder</b>
        <!-- Sub-sección: Datos del año actual -->
        <div class="row mt-4">
            <!-- Card de Participantes -->
            <div class="col-lg-6 section-5">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Participantes Registrados (<?php echo $year; ?>)</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_participantes; ?> Participantes</h5>
                    </div>
                </div>
            </div>
            <!-- Card de Orquídeas -->
            <div class="col-lg-6">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Orquídeas Registradas (<?php echo $year; ?>)</div>
                    <div class="card-body crdbody">
                        <h5 class="card-title"><?php echo $total_orquideas; ?> Orquídeas</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Perfiles de Usuario -->
            <div class="col-lg-4 col-md-6 mb-4 section-5" style="display: none;">
                <div class="card crdbody" onclick="location.href='Registro_usuario.php'">
                    <div class="card-body">
                        <i class="fas fa-user card-icon perfiles"></i>
                        <h5 class="card-title">Participantes</h5>
                        <p class="card-text">Gestiona los perfiles de los usuarios.</p>
                    </div>
                </div>
            </div>

            <!-- Registro de Orquídeas -->
            <div class="col-lg-4 col-md-6 mb-4 section-5" style="display: none;">
                <div class="card crdbody" onclick="location.href='Neva_orquidea.php'">
                    <div class="card-body">
                        <i class="fas fa-plus-circle card-icon orquidea"></i>
                        <h5 class="card-title">Gestionar Orquídeas</h5>
                        <p class="card-text">Gestiona y registra Orquídeas.</p>
                    </div>
                </div>
            </div>

            <!-- Identificación de Orquídeas -->
            <div class="col-lg-4 col-md-6 mb-4 " style="display: none;">
                <div class="card crdbody" onclick="location.href='Identificar.php'">
                    <div class="card-body">
                        <i class="fas fa-leaf card-icon identificacion"></i>
                        <h5 class="card-title">Identificación de Orquídeas</h5>
                        <p class="card-text">Sistema para identificar orquídeas.</p>
                    </div>
                </div>
            </div>

            <!-- Juzgamiento -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card" onclick="location.href='juzgamiento.php'">
                    <div class="card-body">
                        <i class="fas fa-gavel card-icon juzgamiento"></i>
                        <h5 class="card-title">Designar Ganadores</h5>
                        <p class="card-text">Sistema de juzgamiento de orquídeas.</p>
                    </div>
                </div>
            </div>

            <!-- Reporte de Orquídeas -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card" onclick="location.href='Reportes.php'">
                    <div class="card-body">
                        <i class="fas fa-chart-bar card-icon reporte"></i>
                        <h5 class="card-title">Reporte de Orquídeas</h5>
                        <p class="card-text">Consulta reportes completos de orquídeas, su clasificación, ganadores, y accede a los formatos de inscripción y juzgamiento.</p>
                    </div>
                </div>
            </div>

            <!-- Revisión de Estado de Orquídeas -->
            <div id="cardEstado" class="col-lg-4 col-md-6 mb-4 section-5" style="display: none;">
                <div class="card" onclick="location.href='estado.php'">
                    <div class="card-body">
                        <i class="fas fa-search card-icon revision"></i>
                        <h5 class="card-title" id="cardTitle">Estado de Orquídeas</h5>
                        <p class="card-text" id="cardText">Revisa el estado actual de las orquídeas en competición.</p>
                    </div>
                </div>
            </div>

            <!-- Premios -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card" onclick="location.href='Trofeos.php'">
                    <div class="card-body">
                        <i class="fas fa-trophy card-icon premios"></i>
                        <h5 class="card-title">Asignar Trofeos</h5>
                        <p class="card-text">Gestiona y otorga los premios de las orquídeas.</p>
                    </div>
                </div>
            </div>

            <!-- Formatos de Inscripción -->
            <div class="col-lg-4 col-md-6 mb-4 section-5" style="display: none;">
                <div class="card" onclick="descargarReportes()">
                    <div class="card-body">
                        <i class="fas fa-file card-icon premios"></i>
                        <h5 class="card-title">Formato Inscripcion</h5>
                        <p class="card-text">Descargar los formatos de inscripción para registrarse de forma manuscrita.</p>
                    </div>
                </div>
            </div>
            <!-- Revisión de Estado de Orquídeas -->

            <!-- Revisión de Estado de Orquídeas -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card" onclick="location.href='Inscricion_orquidea.php'">
                    <div class="card-body text-center">
                        <!-- Iconos de FontAwesome -->
                        <i class="fas fa-plus-circle card-icon orquidea"></i>
                        <h5 class="card-title" id="cardTitle">Inscripción</h5>
                        <p class="card-text" id="cardText">Inscribe las orquídeas al concurso.</p>
                    </div>
                </div>
            </div>
        </div> <!-- Cierre del div.row -->
    </div>
    <script>
        // Obtenemos el tipo de usuario desde una variable PHP
        const userType = <?php echo json_encode($user_type); ?>;

        // Seleccionamos los elementos de la tarjeta
        const cardTitle = document.getElementById("cardTitle");
        const cardText = document.getElementById("cardText");
        const cardEstado = document.getElementById("cardEstado");

        // Validación del tipo de usuario
        if (userType == 5) {
            // Cambiamos el texto de la tarjeta
            cardText.textContent = "Revisa el estado de tus orquídeas (Un administrador pronto le asignara un estado).";
        }

        // Mostrar la tarjeta si corresponde
        cardEstado.style.display = "block";
    </script>
    <script>
        function descargarReportes() {
            const reportes = [
                '../Vistas/Documentos/pdf/FormatoInscripcion.pdf'
                
            ];

            reportes.forEach((reporte) => {
                const link = document.createElement('a');
                link.href = reporte;
                link.download = reporte.split('/').pop() + '.pdf'; // Nombre del archivo descargado
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        }
    </script>
    <script>
        // Ejecutar cuando el DOM esté completamente cargado
        document.addEventListener('DOMContentLoaded', function() {
            // Capturar el tipo de usuario desde PHP
            const userType = <?php echo json_encode($user_type); ?>;

            // Enviar el tipo de usuario a la consola del navegador
            console.log("Tipo de Usuario Conectado:", userType);

            // Ocultar todas las secciones al inicio
            document.querySelectorAll('.col-lg-4').forEach(section => {
                section.style.display = 'none';
            });

            if (userType === 1) {
                // Mostrar todas las secciones para el usuario tipo 1
                document.querySelectorAll('.col-lg-4').forEach(section => {
                    section.style.display = 'block';
                });
                console.log("Usuario tipo 1: Acceso completo al dashboard.");
            } else if (userType === 5) {
                // Mostrar solo las secciones específicas para tipo 5
                document.querySelectorAll('.section-5').forEach(section => {
                    section.style.display = 'block';
                });
                console.log("Usuario tipo 5: Acceso limitado.");
            } else {
                console.log("Usuario sin acceso a secciones específicas.");
            }
        });
    </script>

    <script>
        //alerta de smartphone 
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar mensaje de alerta la primera vez que se carga el dashboard en esta sesión
            if (!sessionStorage.getItem('firstVisitDashboard')) {
                Swal.fire({
                    title: 'Atención',
                    text: 'Para una mejor experiencia en dispositivos móviles, te recomendamos activar el modo de escritorio en tu navegador o utilizar una tableta o computadora para evitar posibles dificultades de visualización.',
                    icon: 'info',
                    confirmButtonText: 'Entendido'
                });

                // Marcar que el usuario ya ha visto el mensaje en esta sesión
                sessionStorage.setItem('firstVisitDashboard', 'true');
            }
        });
    </script>

    <!--Footer-->
    <footer class="bg-light text-dark py-4">
        <div class="container">
            <div class="row">
                <!-- Información de los creadores -->
                <div class="col-md-4 col-12 mb-3 mb-md-0">
                    <h5><b>Creado y Diseñado por:</b></h5>
                    <ul class="list-unstyled">
                        <li>Pablo Andrés Santos González</li>
                        <li>Isaac Andony Guadamuz Ruiz</li>
                        <li>Selvyn Alberto Contreras Alvarado</li>
                        <li>Stephany Lissethe Ramírez González</li>
                    </ul>
                </div>

                <!-- Redes sociales y derechos -->
                <div class="col-md-4 col-12 text-center mb-3 mb-md-0">
                    <p class="mt-3">© 2024 Ingeniería en Sistemas - Plan Diario <b>UMG Campus Cobán</b></p>
                </div>

                <!-- Información de ubicación y logo -->
                <div class="col-md-4 col-12 d-flex justify-content-end align-items-center">
                    <div class="text-end me-3">
                        <h5><b>A.A.O</b></h5>
                        <p class="mb-0">Cobán</p>
                        <p class="mb-0">Alta Verapaz</p>
                        <p>Guatemala</p>
                    </div>
                    <img src="/Recursos/img/SISTEMAS_UMG-removebg-preview.png" alt="Logo Universidad" class="responsive-img" style="width: 200px;">
                </div>
            </div>
        </div>
    </footer>




    <!-- Enlaces a Bootstrap JS, jQuery y tus scripts personalizados -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <script src="../../Recursos/js/side.js"></script>
</body>

</html>