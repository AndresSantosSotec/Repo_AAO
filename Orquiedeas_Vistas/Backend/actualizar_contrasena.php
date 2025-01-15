<?php
// Incluir la conexión a la base de datos
include '../Backend/Conexion_bd.php';

$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['forgot_email'], $_POST['new_password'])) {
        // Recuperación de contraseña
        $forgot_email = mysqli_real_escape_string($conexion, $_POST['forgot_email']);
        $new_password = mysqli_real_escape_string($conexion, $_POST['new_password']);

        if (!empty($forgot_email) && !empty($new_password)) {
            // Verificar si el correo existe
            $check_email_query = "SELECT id_usuario FROM tb_usuarios WHERE correo = '$forgot_email'";
            $check_email_result = mysqli_query($conexion, $check_email_query);

            if (mysqli_num_rows($check_email_result) > 0) {
                // Encriptar la nueva contraseña
                $password_encrypted = password_hash($new_password, PASSWORD_DEFAULT);

                // Actualizar la contraseña
                $update_query = "UPDATE tb_usuarios SET contrasena = '$password_encrypted' WHERE correo = '$forgot_email'";
                if (mysqli_query($conexion, $update_query)) {
                    $message = "Contraseña actualizada correctamente.";
                    $messageType = "success";
                } else {
                    $message = "Error al actualizar la contraseña: " . mysqli_error($conexion);
                    $messageType = "error";
                }
            } else {
                $message = "El correo proporcionado no está registrado.";
                $messageType = "error";
            }
        } else {
            $message = "Por favor, complete todos los campos.";
            $messageType = "error";
        }
    }

    // Cerrar la conexión
    mysqli_close($conexion);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Contraseña</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script>
        <?php if (!empty($messageType) && $messageType === 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '<?php echo $message; ?>',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../Vistas/registrologin.php';
                }
            });
        <?php elseif (!empty($messageType) && $messageType === 'error'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $message; ?>',
                confirmButtonText: 'Intentar de nuevo'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.history.back();
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>
