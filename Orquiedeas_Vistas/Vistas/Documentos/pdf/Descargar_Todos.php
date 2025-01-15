<?php
require('../fpdf186/fpdf.php');

// Configuración de la conexión en la nuve de 000webhost
/*
$db_host = 'localhost';
$db_username = 'u245906636_Admin';
$db_password = '2905Andres@';
$db_name = 'u245906636_orquideasAAO';
*/

// Configuración de bd en localHost
$db_host = 'localhost';
$db_username = 'root';
$db_password = ''; // Cambia por una contraseña segura
$db_name = 'bd_orquideas_ver2';

 

// Crear conexión
$conexion = new mysqli($db_host, $db_username, $db_password, $db_name);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener fechas
$startDate = $_POST['start_date'];
$endDate = $_POST['end_date'];

if (empty($startDate) || empty($endDate)) {
    die("Por favor, selecciona un rango de fechas válido.");
}

$startDate = date('Y-m-d', strtotime($startDate));
$endDate = date('Y-m-d', strtotime($endDate));

// Obtener todas las clases con datos en el rango
$queryClases = "
    SELECT DISTINCT c.id_clase, c.nombre_clase 
    FROM tb_orquidea o
    INNER JOIN clase c ON o.id_clase = c.id_clase
    WHERE o.fecha_creacion BETWEEN '$startDate' AND '$endDate'";
$resultClases = $conexion->query($queryClases);

if (!$resultClases || $resultClases->num_rows == 0) {
    die("No se encontraron registros para el rango de fechas seleccionado.");
}

$zip = new ZipArchive();
$zipFile = "Reportes_Clases_" . date('Ymd_His') . ".zip";

if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
    die("Error al crear el archivo ZIP.");
}

while ($clase = $resultClases->fetch_assoc()) {
    $idClase = $clase['id_clase'];
    $nombreClase = utf8_decode($clase['nombre_clase']);

    // Generar PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Reporte: ' . $nombreClase, 0, 1, 'C');
    $pdf->Ln(10);

    // Encabezado
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(30, 10, 'Planta', 1);
    $pdf->Cell(60, 10, 'Participante', 1);
    $pdf->Cell(40, 10, 'Origen', 1);
    $pdf->Ln();

    // Consultar datos de la clase
    $query = "
        SELECT o.nombre_planta, p.nombre AS nombre_participante, o.origen 
        FROM tb_orquidea o
        INNER JOIN tb_participante p ON o.id_participante = p.id
        WHERE o.id_clase = $idClase AND o.fecha_creacion BETWEEN '$startDate' AND '$endDate'
        ORDER BY o.nombre_planta";
    $result = $conexion->query($query);

    if ($result && $result->num_rows > 0) {
        $pdf->SetFont('Arial', '', 10);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(30, 10, utf8_decode($row['nombre_planta']), 1);
            $pdf->Cell(60, 10, utf8_decode($row['nombre_participante']), 1);
            $pdf->Cell(40, 10, utf8_decode($row['origen']), 1);
            $pdf->Ln();
        }

        $fileName = "Reporte_Clase_" . $idClase . ".pdf";
        $pdf->Output('F', $fileName);

        // Agregar al ZIP
        $zip->addFile($fileName, $fileName);
        unlink($fileName); // Eliminar PDF temporal
    }
}

$zip->close();

// Descargar el archivo ZIP
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
header('Content-Length: ' . filesize($zipFile));
readfile($zipFile);

// Eliminar archivo ZIP temporal
unlink($zipFile);
?>
