<?php
require('../fpdf186/fpdf.php');

// Configuración de la conexión a la base de datos
/*
$db_host = 'localhost';
$db_username = 'u245906636_Admin';
$db_password = '2905Andres@'; // Cambia por una contraseña segura
$db_name = 'u245906636_orquideasAAO';
*/

// Configuración de bd en localHost
$db_host = 'localhost';
$db_username = 'root';
$db_password = ''; // Cambia por una contraseña segura
$db_name = 'bd_orquideas_ver2';
// Crear conexión
$conexion = new mysqli($db_host, $db_username, $db_password, $db_name);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener filtros y fechas del formulario
$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];
$classFilter = $_GET['class_filter'];

// Validar que las fechas no estén vacías
if ($startDate && $endDate) {
    $startDate = date('Y-m-d', strtotime($startDate));
    $endDate = date('Y-m-d', strtotime($endDate));

    // Construir la consulta SQL para orquídeas inscritas con el filtro de clase
    $query = "
        SELECT 
            o.id_orquidea, 
            o.nombre_planta, 
            CONCAT(g.Cod_Grupo, '/', c.id_clase) AS grupo_clase, -- Concatenar Cod_Grupo e id_clase
            c.nombre_clase,
            p.nombre AS nombre_participante,
            o.origen,
            i.correlativo
        FROM tb_orquidea o
        INNER JOIN tb_inscripcion i ON o.id_orquidea = i.id_orquidea
        INNER JOIN grupo g ON o.id_grupo = g.id_grupo
        INNER JOIN clase c ON o.id_clase = c.id_clase
        INNER JOIN tb_participante p ON o.id_participante = p.id
        WHERE o.fecha_creacion BETWEEN '$startDate' AND '$endDate'";
    
    // Agregar filtro de clase si está seleccionado
    if (!empty($classFilter)) {
        $query .= " AND o.id_clase = " . intval($classFilter);
    }

    $query .= " ORDER BY g.Cod_Grupo, c.nombre_clase, o.nombre_planta";

    $result = $conexion->query($query);

    if (!$result || $result->num_rows == 0) {
        echo "No se encontraron resultados para el rango de fechas y filtros seleccionados.";
        exit;
    }

    // Generar el PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Título del reporte
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode('Asociación Altaverapacense de Orquideología'), 0, 1, 'C');
    $pdf->Cell(0, 10, 'Listado General de Plantas por Clases', 0, 1, 'C');
    $pdf->Cell(0, 10, 'Desde: ' . date('d/m/Y', strtotime($startDate)) . ' Hasta: ' . date('d/m/Y', strtotime($endDate)), 0, 1, 'C');
    $pdf->Ln(10);

    // Encabezado de la tabla
    $pdf->SetFillColor(230, 230, 230);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(10, 10, 'No', 0);
    $pdf->Cell(60, 10, 'Planta', 0);
    $pdf->Cell(70, 10, 'Grupo/Clase', 0);
    $pdf->Cell(10, 10, 'Es', 0);
    $pdf->Cell(10, 10, 'Hi', 0);
    $pdf->Ln();

    // Datos de la tabla
    $pdf->SetFont('Arial', '', 10);

    while ($row = $result->fetch_assoc()) {
        $grupo_clase = utf8_decode($row['grupo_clase']); // Cod_Grupo/id_clase
        $especie = ($row['origen'] === 'Especie') ? 'X' : '';
        $hibrida = ($row['origen'] === 'Hibrida') ? 'X' : '';

        $pdf->Cell(10, 10, $row['correlativo'], 0, 0, 'C'); // Usar el correlativo en lugar del contador
        $pdf->Cell(60, 10, utf8_decode($row['nombre_planta']), 0);
        $pdf->Cell(70, 10, $grupo_clase, 0);
        $pdf->Cell(10, 10, $especie, 0, 0, 'C');
        $pdf->Cell(10, 10, $hibrida, 0, 0, 'C');
        $pdf->Ln();
    }

    // Pie de página
    $pdf->SetY(-15);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 10, 'Generado el ' . date('d/m/Y'), 0, 0, 'L');
    $pdf->Cell(0, 10, 'Pagina ' . $pdf->PageNo(), 0, 0, 'R');

    $pdf->Output('I', 'Listado_Orquideas_Por_Clases.pdf');
} else {
    echo "Por favor, selecciona un rango de fechas válido.";
}
?>
