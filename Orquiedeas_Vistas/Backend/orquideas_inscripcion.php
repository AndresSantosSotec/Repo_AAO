<?php
include 'Conexion_bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'get_orquideas') {
        $id_participante = $_POST['id_participante'];
        $query = "SELECT `id_orquidea`, `nombre_planta` FROM `tb_orquidea` WHERE `id_participante` = $id_participante";
        $result = mysqli_query($conexion, $query);
        
        $options = '<option value="">Selecciona una orquídea</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            $options .= '<option value="' . $row['id_orquidea'] . '">' . $row['nombre_planta'] . '</option>';
        }
        
        echo json_encode(['options' => $options]);
        exit;
    }

    if ($action === 'registrar_inscripcion') {
        $id_participante = $_POST['id_participante'];
        $orquideas = json_decode($_POST['orquideas'], true);

        $response = ['status' => 'error', 'message' => 'No se pudo completar la inscripción.'];
        $success = true;

        foreach ($orquideas as $orquidea) {
            $id_orquidea = $orquidea['id_orquidea'];
            $correlativo = $orquidea['correlativo'];

            $query = "INSERT INTO tb_inscripcion (id_participante, id_orquidea, correlativo) VALUES ('$id_participante', '$id_orquidea', '$correlativo')";
            if (!mysqli_query($conexion, $query)) {
                $success = false;
                break;
            }
        }

        if ($success) {
            $response = ['status' => 'success', 'message' => 'Inscripción realizada correctamente.'];
        }
        
        echo json_encode($response);
        exit;
    }
}
?>
