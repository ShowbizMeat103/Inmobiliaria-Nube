<?php
include_once '../../config/config_db.php'; 
session_start(); // Inicia la sesión para poder usar $_SESSION

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura los datos del formulario
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];

    $errores = [];
    $mensajes = [];

    // Validaciones básicas
    if (empty($usuario) || empty($contrasena) || empty($nombre) || empty($correo) || empty($rol)) {
        $errores['campos'] = "Todos los campos son obligatorios.";
    } 

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores['correo'] = "Correo inválido.";
    }

    if (strlen($contrasena) < 8) {
        $errores['contrasena'] = "La contraseña debe tener al menos 8 caracteres.";
    } elseif (strlen($contrasena) > 20) {
        $errores['contrasena'] = "La contraseña no puede tener más de 20 caracteres.";
    } elseif (!preg_match('/[0-9]/', $contrasena)) {
        $errores['contrasena'] = "La contraseña debe contener al menos un número.";
    }

    if (strlen($usuario) < 4) {
        $errores['usuario'] = "El nombre de usuario debe tener al menos 4 caracteres.";
    } elseif (strlen($usuario) > 20) {
        $errores['usuario'] = "El nombre de usuario no puede tener más de 20 caracteres.";
    }

    if (strlen($nombre) < 4) {
        $errores['nombre'] = "El nombre debe tener al menos 4 caracteres.";
    } elseif (strlen($nombre) > 40) {
        $errores['nombre'] = "El nombre no puede tener más de 40 caracteres.";
    }

    if (!empty($telefono)) {
        if (!preg_match('/^[0-9]+$/', $telefono)) {
            $errores['telefono'] = "El teléfono solo puede contener números.";
        } elseif (strlen($telefono) !== 10) {
            $errores['telefono'] = "El teléfono debe tener exactamente 10 caracteres.";
        }
    } else {
        $telefono = null; // Si está vacío, lo tratamos como null
    }

    // Validación de rol
    $roles_permitidos = ['cliente', 'promotor'];
    if (!in_array($rol, $roles_permitidos)) {
        $errores['rol'] = "Rol inválido.";
    }

    // Validar si usuario o correo ya existen
    try {
        $stmt = $con->prepare("SELECT * FROM usuarios WHERE usuario = :usuario OR correo = :correo");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            if ($result['usuario'] === $usuario) {
                $errores['userUsed'] = "El nombre de usuario ya está en uso.";
            }
            if ($result['correo'] === $correo) {
                $errores['emailUsed'] = "El correo ya está en uso.";
            }
        }

    } catch (PDOException $e) {
        $errores['dbError'] = "Error: " . $e->getMessage();
        $_SESSION['signupErrors'] = $errores;
        header("Location: ../registro.php");
        exit();
    }

    // Si hay errores, los guardamos en la sesión y redirigimos
    if (!empty($errores)) {
        $_SESSION['signupErrors'] = $errores;
        header("Location: ../registro.php");
        exit();
    }

    // Intentar crear el usuario
    try {
        if (createUser($con, $usuario, $correo, $nombre, $contrasena, $telefono, $rol)) {
            $mensajes['mensajeExito'] = "Usuario registrado correctamente.";
            $_SESSION['mensajeExito'] = $mensajes;
            header("Location: ../registro.php");
            exit();
        } else {
            $errores['insertError'] = "Error al registrar el usuario.";
            $_SESSION['signupErrors'] = $errores;
            header("Location: ../registro.php");
            exit();
        }
    } catch (PDOException $e) {
        $errores['dbError'] = "Error: " . $e->getMessage();
        $_SESSION['signupErrors'] = $errores;
        header("Location: ../registro.php");
        exit();
    }

} else {
    header("Location: ../registro.php");
    exit();
}

// Función para guardar datos del usuario en sesión (si quieres usarla después)
function saveUserDataSession($usuario, $correo, $nombre, $contrasena, $telefono) {
    $_SESSION['userData'] = [
        'usuario' => $usuario,
        'correo' => $correo,
        'nombre' => $nombre,
        'contrasena' => $contrasena,
        'telefono' => $telefono
    ];
}

// Función para insertar el usuario en la base de datos
function createUser($pdo, $usuario, $correo, $nombre, $contrasena, $telefono, $rol) {
    $sql = "INSERT INTO usuarios (usuario, rol, contrasena, nombre, correo, telefono) 
            VALUES (:usuario, :rol, :contrasena, :nombre, :correo, :telefono)";
    
    $stmt = $pdo->prepare($sql);

    $options = ['cost' => 12];
    $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT, $options);

    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':rol', $rol);
    $stmt->bindParam(':contrasena', $contrasenaHash);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':telefono', $telefono);

    return $stmt->execute();
}
?>
