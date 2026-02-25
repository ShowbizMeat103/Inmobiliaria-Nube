<?php

session_start(); // Inicia la sesión para poder usar $_SESSION
// Incluye el archivo de configuración, donde debería estar la conexión a la base de datos ($con)
include_once '../../config/config_db.php';

// Si el formulario fue enviado mediante POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoge los valores enviados desde el formulario
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    try {
        // Intenta validar al usuario como promotor (tabla 'usuarios')
        // Primero, busca al usuario por su nombre de usuario
        $stmt = $con->prepare("SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si se encontró un usuario y la contraseña coincide con el hash almacenado
        if ($resultado && password_verify($contrasena, $resultado['contrasena'])) {
            $_SESSION['user']['usuario'] = $resultado['usuario']; // Guarda el usuario en sesión
            $_SESSION['user']['rol'] = $resultado['rol']; // Define el rol
            $_SESSION['user']['id'] = $resultado['id']; // Guarda el ID del usuario en sesión
            // Prepara mensaje para mostrar (aunque el header redirige antes)
            $_SESSION['userMessage']['mensaje'] = "Inicio de sesión exitoso como {$resultado['rol']}.";
            $_SESSION['userMessage']['tipoMensaje'] = 'success';
            header('Location: ../index.php');
            exit();
        } else {
            // Si no hay resultados o la contraseña no coincide, el usuario o contraseña son incorrectos
            $_SESSION['userMessage']['error'] = "Usuario o contraseña incorrectos.";
            $_SESSION['userMessage']['mensaje'] = $_SESSION['userMessage']['error'];
            $_SESSION['userMessage']['tipoMensaje'] = 'danger';
            header("Location: ../login.php"); // Redirige al formulario de login
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['userMessage']['error'] = "Error en la consulta: " . $e->getMessage();
        $_SESSION['userMessage']['mensaje'] = $_SESSION['userMessage']['error'];
        $_SESSION['userMessage']['tipoMensaje'] = 'danger';
        header("Location: ../login.php"); // Redirige al formulario de login
        exit();
    }
}