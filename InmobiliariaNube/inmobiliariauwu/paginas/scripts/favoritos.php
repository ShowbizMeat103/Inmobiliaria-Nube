<?php
session_start(); // Iniciar la sesión para acceder a $_SESSION['user']['id']

// Incluir el archivo de configuración de la base de datos
include_once '../../config/config_db.php';


// Verificar si el usuario está logueado y si su ID está en la sesión
if (!isset($_SESSION['user']['id'])) {
    // Redirigir al login si no está logueado o no se encuentra el ID
    header("Location: ../login.php");
    exit();
}




// Verificar si se recibió el id del inmueble mediante POST
if (isset($_POST['inmueble_id'])) {
    $id_inmueble = (int)$_POST['inmueble_id']; // Dato recibido por POST
    $id_usuario = (int)$_SESSION['user']['id']; // Obtener el ID del usuario de la sesión
    $fecha_guardado = date('Y-m-d H:i:s'); // Fecha y hora actual

    // Verificar si el inmueble ya está en favoritos para este usuario
    $sql_check = "SELECT id FROM favoritos WHERE id_usuario = :id_usuario AND id_inmueble = :id_inmueble";
    $stmt_check = $con->prepare($sql_check);
    $stmt_check->execute([
        ':id_usuario' => $id_usuario,
        ':id_inmueble' => $id_inmueble
    ]);

    if ($stmt_check->fetch()) {
        // El inmueble ya está en favoritos
        header("Location: ../detalle_inmueble.php?id=" . $id_inmueble);
        exit();
    }

    // Preparar la consulta SQL para insertar en la tabla favoritos
    $sql = "INSERT INTO favoritos (id_usuario, id_inmueble, fecha_guardado) VALUES (:id_usuario, :id_inmueble, :fecha_guardado)";
    $stmt = $con->prepare($sql);

    try {
        // Ejecutar la consulta
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':id_inmueble' => $id_inmueble,
            ':fecha_guardado' => $fecha_guardado
        ]);

        // Redirigir de vuelta a la página del inmueble con un mensaje de éxito
        header("Location: ../detalle_inmueble.php?id=" . $id_inmueble);
        exit();
    } catch (PDOException $e) {
        // Manejar el error
        // Para depuración: error_log("Error al agregar a favoritos: " . $e->getMessage());
        header("Location: ../detalle_inmueble.php?id=" . $id_inmueble);
        exit();
    }

} else {
    // Si no se recibió el id del inmueble, redirigir a la página de inicio o a donde desees
}
?>