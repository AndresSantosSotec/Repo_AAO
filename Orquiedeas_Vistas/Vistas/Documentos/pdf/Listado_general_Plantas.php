<?php

require('../fpdf186/fpdf.php');
include 'Conexion_bd.php';

// Función para truncar y convertir texto a ISO-8859-1
function truncarTexto($texto, $longitud) {
    $texto = mb_substr($texto, 0, $longitud, 'UTF-8'); // Truncar a la longitud deseada
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto); // Convertir a ISO-8859-1 para FPDF
}

// Obtener las fechas del formulario
$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];

// Validar que las fechas no estén vacías
if ($startDate && $endDate) {
    // Convertir fechas al formato correcto para MySQL
    $startDate = date('Y-m-d', strtotime($startDate));
    $endDate = date('Y-m-d', strtotime($endDate));

    // Modificar la consulta para incluir el filtro de fechas y los nombres de grupo y clase
    $query = "
        SELECT 
            i.correlativo,
            p.nombre AS nombre_participante,
            o.nombre_planta AS nombre_orquidea,
            o.origen,
            LEFT(g.Cod_Grupo, 1) AS grupo,  -- Obtener solo la letra inicial del grupo
            CONCAT('Clase: ', c.id_clase) AS clase,  -- Mostrar Clase: id_clase
            p.fecha_creacion
        FROM tb_inscripcion i
        INNER JOIN tb_participante p ON i.id_participante = p.id
        INNER JOIN tb_orquidea o ON i.id_orquidea = o.id_orquidea
        INNER JOIN grupo g ON o.id_grupo = g.id_grupo
        INNER JOIN clase c ON o.id_clase = c.id_clase
        WHERE p.fecha_creacion BETWEEN '$startDate' AND '$endDate'
        ORDER BY CAST(i.correlativo AS UNSIGNED)"; // Ordenar por correlativo como número
    
    $result = mysqli_query($conexion, $query);

    // Generar el PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Título del reporte
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode('Reporte de Inscripciones'), 0, 1, 'C');
    $pdf->Cell(0, 10, 'Desde: ' . date('d/m/Y', strtotime($startDate)) . ' Hasta: ' . date('d/m/Y', strtotime($endDate)), 0, 1, 'C');
    $pdf->Ln(10);

    // Encabezado de tabla
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Participante', 1);
    $pdf->Cell(60, 10, utf8_decode('Orquídea'), 1);
    $pdf->Cell(20, 10, 'Grupo', 1);
    $pdf->Cell(30, 10, 'Clase', 1);
    $pdf->Cell(20, 10, 'Origen', 1);
    $pdf->Cell(20, 10, 'Correlativo', 1);
    $pdf->Ln();

    // Datos
    $pdf->SetFont('Arial', '', 10);

    while ($row = mysqli_fetch_assoc($result)) {
        $grupo = truncarTexto($row['grupo'], 1);                             // Solo la letra del grupo
        $clase = truncarTexto($row['clase'], 15);                             // Truncar clase a 15 caracteres

        $pdf->Cell(40, 10, truncarTexto($row['nombre_participante'], 20), 1);  // Truncar y convertir a ISO-8859-1
        $pdf->Cell(60, 10, truncarTexto($row['nombre_orquidea'], 30), 1);     // Truncar y convertir a ISO-8859-1
        $pdf->Cell(20, 10, $grupo, 1);                                        // Mostrar solo la letra del grupo
        $pdf->Cell(30, 10, $clase, 1);                                        // Truncar y convertir a ISO-8859-1
        $pdf->Cell(20, 10, truncarTexto($row['origen'], 15), 1);              // Truncar y convertir a ISO-8859-1
        $pdf->Cell(20, 10, $row['correlativo'], 1);
        $pdf->Ln();
    }

    // Pie de página con la fecha y el número de página
    $pdf->SetY(-15);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 10, 'Generado el ' . date('d/m/Y'), 0, 0, 'L');
    $pdf->Cell(0, 10, 'Pagina ' . $pdf->PageNo(), 0, 0, 'R');

    // Salida del PDF
    $pdf->Output('I', 'Reporte_Inscripciones.pdf');
} else {
    echo "Por favor, selecciona un rango de fechas válido.";
}

?>
