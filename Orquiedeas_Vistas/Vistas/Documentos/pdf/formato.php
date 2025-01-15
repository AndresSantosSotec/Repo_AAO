<?php
require('../fpdf186/fpdf.php');  // Cargar la librería FPDF
include '../../../Backend/Conexion_bd.php'; // Conexión a la base de datos
session_start();

// Configurar la conexión para que utilice UTF-8
mysqli_set_charset($conexion, "utf8");

// Obtener el ID del usuario y tipo de usuario de la sesión
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Consultar datos del participante para usuarios tipo 5
$participante = [];
if ($user_type == 5) {
    $query_participante = "
        SELECT nombre, numero_telefonico, direccion
        FROM tb_participante
        WHERE id_usuario = ?";
    
    $stmt_participante = $conexion->prepare($query_participante);
    $stmt_participante->bind_param("i", $user_id);
    $stmt_participante->execute();
    $result_participante = $stmt_participante->get_result();
    
    if ($result_participante && $result_participante->num_rows > 0) {
        $participante = $result_participante->fetch_assoc();
    }
}

// Obtener el total de orquídeas dependiendo del tipo de usuario
$total_orquideas = 0;
if ($user_type == 1) {
    // Total de todas las orquídeas (para administradores)
    $query_total = "SELECT COUNT(*) AS total FROM tb_orquidea";
    $result_total = mysqli_query($conexion, $query_total);
    if ($result_total) {
        $row = mysqli_fetch_assoc($result_total);
        $total_orquideas = $row['total'];
    }
} elseif ($user_type == 5) {
    // Total de orquídeas del participante
    $query_total = "
        SELECT COUNT(*) AS total 
        FROM tb_orquidea o
        INNER JOIN tb_participante p ON o.id_participante = p.id
        WHERE p.id_usuario = ?";
    $stmt_total = $conexion->prepare($query_total);
    $stmt_total->bind_param("i", $user_id);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    if ($result_total) {
        $row = $result_total->fetch_assoc();
        $total_orquideas = $row['total'];
    }
}

class PDF extends FPDF {
    // Método para convertir a texto compatible con FPDF
    function texto($str) {
        return iconv('UTF-8', 'ISO-8859-1', $str);
    }

    // Encabezado del documento
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $this->texto('ASOCIACION ALTAVERAPACENSE DE ORQUIDEOLOGIA'), 0, 1, 'C');
        $this->Cell(0, 10, $this->texto('COBAN, ALTA VERAPAZ, GUATEMALA CENTROAMERICA'), 0, 1, 'C');
        $this->Ln(10);
        $this->Cell(0, 10, $this->texto('PLANILLA DE INSCRIPCION DE PLANTAS'), 0, 1, 'C');
        $this->Ln(10);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $this->texto('Pagina ') . $this->PageNo(), 0, 0, 'C');
    }

    // Contenido del formulario
    function Formulario($orquideas, $participante, $isAdmin = false, $total_orquideas = 0) {
        $this->SetFont('Arial', '', 10);

        // Mostrar información del participante solo si no es administrador
        if (!$isAdmin) {
            $this->Cell(50, 10, $this->texto('Nombre del Expositor:'), 0, 0);
            $this->Cell(0, 10, $this->texto($participante['nombre'] ?? '________________'), 0, 1);
            
            $this->Cell(50, 10, $this->texto('Direccion:'), 0, 0);
            $this->Cell(100, 10, $this->texto($participante['direccion'] ?? '____________'), 0, 0);
            $this->Cell(20, 10, $this->texto('Telefono:'), 0, 0);
            $this->Cell(0, 10, $this->texto($participante['numero_telefonico'] ?? '____'), 0, 1);

            $this->Ln(10);
        }

        // Cabecera de la tabla
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(230, 230, 230); // Color de fondo de la cabecera
        $this->Cell(20, 10, $this->texto('Cantidad'), 1, 0, 'C', true);
        $this->Cell(60, 10, $this->texto('Nombre de las Plantas'), 1, 0, 'C', true);
        $this->Cell(20, 10, $this->texto('Especie'), 1, 0, 'C', true);
        $this->Cell(20, 10, $this->texto('Híbrida'), 1, 0, 'C', true);
        $this->Cell(20, 10, $this->texto('Clase'), 1, 0, 'C', true);
        $this->Cell(20, 10, $this->texto('Grupo'), 1, 0, 'C', true);

        // Si es administrador, agregar columna de Participante
        if ($isAdmin) {
            $this->Cell(40, 10, $this->texto('Participante'), 1, 1, 'C', true);
        } else {
            $this->Cell(0, 10, '', 0, 1); // Salto de línea
        }

        // Filas con los datos de las orquídeas agrupadas
        $this->SetFont('Arial', '', 8);
        foreach ($orquideas as $orquidea) {
            $this->Cell(20, 10, $this->texto($orquidea['cantidad']), 1, 0, 'C');
            $this->Cell(60, 10, $this->texto($orquidea['nombre_planta']), 1, 0, 'C');
            
            // Marcar la columna correspondiente con una "X" para Especie o Híbrida
            if ($orquidea['origen'] === 'Especie') {
                $this->Cell(20, 10, 'X', 1, 0, 'C'); // Especie
                $this->Cell(20, 10, '', 1, 0, 'C');  // Híbrida
            } else {
                $this->Cell(20, 10, '', 1, 0, 'C');  // Especie
                $this->Cell(20, 10, 'X', 1, 0, 'C'); // Híbrida
            }

            $this->Cell(20, 10, $this->texto('Clase: ' . $orquidea['id_clase']), 1, 0, 'C');
            $this->Cell(20, 10, $this->texto('Grupo ' . $orquidea['Cod_Grupo']), 1, 0, 'C');

            // Si es administrador, mostrar el nombre del participante
            if ($isAdmin) {
                $this->Cell(40, 10, $this->texto($orquidea['nombre_participante']), 1, 1, 'C');
            } else {
                $this->Cell(0, 10, '', 0, 1); // Salto de línea
            }
        }

        $this->Ln(10);

        // Mostrar el total de orquídeas inscritas
        $this->SetFont('Arial', 'B', 10);
        $texto_total = $isAdmin ? 'Total de orquídeas pre-inscritas: ' : 'Tus orquídeas inscritas: ';
        $this->Cell(0, 10, $this->texto($texto_total . $total_orquideas), 0, 1, 'L');

        // Nota al final
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $this->texto('Favor de llenar esta planilla en duplicado escribiendo claramente'), 0, 1);
        $this->Cell(0, 10, $this->texto('La ultima columna es para uso exclusivo del comite de recepcion de plantas'), 0, 1);
    }
}

// Construir la consulta SQL según el tipo de usuario
$orquideas = [];
if ($user_type == 1) {
    // Consulta para el administrador que obtiene todas las orquídeas con el nombre del participante
    $query = "
        SELECT 
            o.nombre_planta, 
            o.origen, 
            o.id_clase, 
            g.Cod_Grupo, 
            COUNT(*) as cantidad, 
            p.nombre AS nombre_participante 
        FROM tb_orquidea o
        INNER JOIN grupo g ON o.id_grupo = g.id_grupo 
        INNER JOIN tb_participante p ON o.id_participante = p.id 
        GROUP BY o.nombre_planta, o.origen, o.id_clase, g.Cod_Grupo, p.nombre";
    
    $result = mysqli_query($conexion, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $orquideas[] = $row;
        }
    }
} elseif ($user_type == 5) {
    // Consulta para el usuario tipo 5 que solo obtiene sus orquídeas agrupadas
    $query = "
        SELECT 
            o.nombre_planta, 
            o.origen, 
            o.id_clase, 
            g.Cod_Grupo, 
            COUNT(*) as cantidad 
        FROM tb_orquidea o
        INNER JOIN grupo g ON o.id_grupo = g.id_grupo 
        INNER JOIN tb_participante p ON o.id_participante = p.id 
        WHERE p.id_usuario = ? 
        GROUP BY o.nombre_planta, o.origen, o.id_clase, g.Cod_Grupo";

    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orquideas[] = $row;
        }
    }
}

// Crear y generar el PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->Formulario($orquideas, $participante, $user_type == 1, $total_orquideas);
$pdf->Output();
?>
