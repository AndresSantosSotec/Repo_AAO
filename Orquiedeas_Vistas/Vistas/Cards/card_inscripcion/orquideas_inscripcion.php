<?php
include 'Conexion_bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    error_log("Acción recibida: $action"); // Depuración de la acción

    if ($action === 'registrar_inscripcion') {
        $id_participante = $_POST['id_participante'];
        $orquideas = json_decode($_POST['orquideas'], true);

        error_log("ID Participante: $id_participante");
        error_log("Orquídeas: " . print_r($orquideas, true)); // Depuración de datos

        $response = ['status' => 'error', 'message' => 'No se pudo completar la inscripción.'];
        $success = true;

        foreach ($orquideas as $orquidea) {
            $id_orquidea = $orquidea['id_orquidea'];
            $correlativo = $orquidea['correlativo'];
            $query = "INSERT INTO tb_inscripcion (id_participante, id_orquidea, correlativo) 
                      VALUES ('$id_participante', '$id_orquidea', '$correlativo')";

            if (!mysqli_query($conexion, $query)) {
                $success = false;
                error_log("Error en la inserción: " . mysqli_error($conexion)); // Registra el error de la consulta
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
