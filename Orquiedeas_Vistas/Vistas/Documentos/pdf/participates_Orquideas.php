<?php
require('../fpdf186/fpdf.php');
include('Conexion_bd.php'); // Asegúrate de que la conexión esté correcta

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Reporte de Participantes y Orquideas Asignadas', 0, 1, 'C');
        $this->Ln(10);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }

    // Función para la tabla de participantes y orquídeas con ajuste dinámico de texto
    function FancyTable($header, $data)
    {
        $this->SetFillColor(30, 144, 255); // Azul para la cabecera
        $this->SetTextColor(255); // Blanco para texto
        $this->SetDrawColor(0, 0, 139); // Azul oscuro para bordes
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', 12);

        // Anchuras de las columnas
        $w = array(40, 40, 45, 40, 35, 30, 30); 

        // Cabecera de la tabla
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 6, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();

        // Restaurar colores y fuente para el contenido
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 10);

        $fill = false;
        foreach ($data as $row) {
            // Ajuste de tamaño si el nombre es muy largo
            $this->SetFontSize(strlen($row['participante_nombre']) > 20 ? 8 : 10);

            $x = $this->GetX();
            $y = $this->GetY();
            $this->MultiCell($w[0], 6, utf8_decode($row['participante_nombre']), 'LR', 'L', $fill);
            $this->SetXY($x + $w[0], $y); 

            // Teléfono y dirección sin ajuste
            $this->Cell($w[1], 6, $row['numero_telefonico'], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, utf8_decode($row['direccion']), 'LR', 0, 'L', $fill);

            // Ajuste dinámico para el nombre de la orquídea
            $this->SetFontSize(strlen($row['nombre_planta']) > 20 ? 8 : 10);

            $x = $this->GetX();
            $y = $this->GetY();
            $this->MultiCell($w[3], 6, utf8_decode($row['nombre_planta']), 'LR', 'L', $fill);
            $this->SetXY($x + $w[3], $y);

            // Otras columnas sin ajuste
            $this->Cell($w[4], 6, utf8_decode($row['origen']), 'LR', 0, 'L', $fill);
            $this->Cell($w[5], 6, 'Grupo: ' . utf8_decode($row['Cod_Grupo']), 'LR', 0, 'L', $fill);
            $this->Cell($w[6], 6, 'Clase: ' . $row['id_clase'], 'LR', 0, 'L', $fill);

            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// Crear objeto PDF en orientación horizontal
$pdf = new PDF('L', 'mm', 'A4');

// Agregar una página
$pdf->AddPage();

// Encabezados de la tabla
$header = array('Participante', 'Telefono', 'Direccion', 'Orquidea', 'Origen', 'Grupo', 'Clase');

// Consulta para obtener los datos
$query = "
    SELECT 
        p.nombre AS participante_nombre, 
        p.numero_telefonico, 
        p.direccion, 
        o.nombre_planta, 
        o.origen, 
        g.Cod_Grupo, 
        c.id_clase
    FROM tb_participante p
    INNER JOIN tb_orquidea o ON p.id = o.id_participante
    INNER JOIN clase c ON o.id_clase = c.id_clase
    INNER JOIN grupo g ON c.id_grupo = g.id_grupo";

$result = mysqli_query($conexion, $query);

// Verificar errores en la consulta
if (!$result) {
    die('Error en la consulta: ' . mysqli_error($conexion));
}

// Obtener los datos
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Generar la tabla
$pdf->FancyTable($header, $data);

// Output del PDF
$pdf->Output('I', 'Reporte_Participantes_Orquideas_Tabla_Grupo_Clase.pdf');
?>
