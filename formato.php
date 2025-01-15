<?php
require('fpdf/fpdf.php');

class PDF extends FPDF {
    // Encabezado del documento
    function Header() {
        // Logo
        $this->Image('/Recusros/img/logopdf.png', 10, 10, 30); // Reemplaza 'logo.png' con el nombre y ubicación de tu logo
        // Título
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'ASOCIACION ALTAVERAPACENSE DE ORQUIDEOLOGIA', 0, 1, 'C');
        $this->Cell(0, 10, 'COBAN, ALTA VERAPAZ, GUATEMALA CENTROAMERICA', 0, 1, 'C');
        $this->Ln(10);
        $this->Cell(0, 10, 'PLANILLA DE INSCRIPCION DE PLANTAS', 0, 1, 'C');
        $this->Ln(10);
    }

    // Pie de página
    function Footer() {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }

    // Contenido del formulario
    function Formulario() {
        $this->SetFont('Arial', '', 10);

        // Campos para nombre y dirección
        $this->Cell(50, 10, 'Nombre del Expositor:', 0, 0);
        $this->Cell(0, 10, '______________________________________________', 0, 1);
        
        $this->Cell(50, 10, 'Direccion:', 0, 0);
        $this->Cell(100, 10, '________________________________', 0, 0);
        $this->Cell(30, 10, 'Telefono:', 0, 0);
        $this->Cell(0, 10, '____________', 0, 1);

        $this->Ln(10);

        // Tabla
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(230, 230, 230); // Color de fondo de la cabecera
        $this->Cell(20, 10, 'Cantidad', 1, 0, 'C', true);
        $this->Cell(60, 10, 'Nombre de las Plantas', 1, 0, 'C', true);
        $this->Cell(20, 10, 'Especie', 1, 0, 'C', true);
        $this->Cell(20, 10, 'Hibrido', 1, 0, 'C', true);
        $this->Cell(20, 10, 'Clase', 1, 0, 'C', true);
        $this->Cell(20, 10, 'No. Correlativo', 1, 1, 'C', true);

        // Filas vacías para rellenar
        $this->SetFont('Arial', '', 10);
        for ($i = 0; $i < 15; $i++) {
            $this->Cell(20, 10, '', 1);
            $this->Cell(60, 10, '', 1);
            $this->Cell(20, 10, '', 1);
            $this->Cell(20, 10, '', 1);
            $this->Cell(20, 10, '', 1);
            $this->Cell(20, 10, '', 1);
            $this->Ln();
        }

        $this->Ln(10);

        // Nota al final
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Favor de llenar esta planilla en duplicado escribiendo claramente', 0, 1);
        $this->Cell(0, 10, 'La ultima columna es para uso exclusivo del comite de recepcion de plantas', 0, 1);
    }
}

// Crear documento
$pdf = new PDF();
$pdf->AddPage();
$pdf->Formulario();
$pdf->Output();
?>
