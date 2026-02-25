<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publicar'])) {

    require_once '../../config/config_db.php';

    $error = '';
    $mensaje = '';
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];
    $precio = $_POST['precio'];
    $ubicacion = $_POST['ubicacion'];
    $latitud = $_POST['latitud'] ?: null;
    $longitud = $_POST['longitud'] ?: null;
    $fecha_publicacion = date('Y-m-d');
    $img_src = null; // Initialize img_src

    // Image Upload Handling
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../images/inmuebles/'; // Relative to this script's location
        // Ensure the upload directory exists and is writable
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create it if it doesn't exist
        }

        // Set permissions recursively for the upload directory

        try {
            chmod($upload_dir, 0777);
            // Set permissions for all files and subdirectories inside the upload directory
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($upload_dir, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($iterator as $item) {
                chmod($item, 0777);
            }
        } catch (Exception $ignored) {}

        $file_tmp_path = $_FILES['imagen']['tmp_name'];
        $file_name = $_FILES['imagen']['name'];
        $file_size = $_FILES['imagen']['size'];
        $file_type = $_FILES['imagen']['type'];
        $file_name_parts = explode('.', $file_name);
        $file_ext = strtolower(end($file_name_parts));

        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_ext)) {
            // Max file size (e.g., 5MB)
            if ($file_size < 5000000) {
                $new_file_name = uniqid('', true) . '.' . $file_ext;
                $dest_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp_path, $dest_path)) {
                    // Store the absolute path from the web root for display
                    $img_src = '/images/inmuebles/' . $new_file_name;
                } else {
                    $_SESSION['publicar']['error'] = "Error al mover el archivo subido. Detalles: " . print_r($_FILES['imagen'], true);
                    header("Location: ../publicar_inmueble.php");
                    exit();
                }
            } else {
                $_SESSION['publicar']['error'] = "El archivo es demasiado grande. Máximo 5MB.";
                header("Location: ../publicar_inmueble.php");
                exit();
            }
        } else {
            $_SESSION['publicar']['error'] = "Tipo de archivo no permitido. Solo JPG, JPEG, PNG, GIF.";
            header("Location: ../publicar_inmueble.php");
            exit();
        }
    } elseif (isset($_FILES['imagen']) && $_FILES['imagen']['error'] != UPLOAD_ERR_NO_FILE) {
        // Handle other upload errors
        $_SESSION['publicar']['error'] = "Error al subir la imagen. Código: " . $_FILES['imagen']['error'];
        header("Location: ../publicar_inmueble.php");
        exit();
    }
    // If no image is uploaded, $img_src remains null, which is fine if the column allows NULLs.
    // If an image is required, add a check here.


    // Buscar ID del promotor
    $usuario = $_SESSION['user']['usuario'];
    try {
        $sql = "SELECT * FROM usuarios WHERE usuario = :usuario";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->execute();
        $promotor = $stmt->fetch(PDO::FETCH_ASSOC);
        // Detener la ejecución para ver la salida

        if ($promotor !== false && $promotor['rol'] === 'promotor') {
            

            //Validar datos
            if (empty($titulo) || empty($descripcion) || empty($tipo) || empty($estado) || empty($precio) || empty($ubicacion)) {
                $_SESSION['publicar']['error'] = "Todos los campos son obligatorios.";
                header("Location: ../publicar_inmueble.php");
                exit();
            }

            if((!empty($latitud) || !empty($longitud)) && !isCoordinatesValid($latitud, $longitud)) {
                $_SESSION['publicar']['error'] = "Coordenadas inválidas.";
                header("Location: ../publicar_inmueble.php");
                exit();
            }

            if(!is_numeric($precio) || $precio <= 0) {
                $_SESSION['publicar']['error'] = "El precio debe ser un número positivo.";
                header("Location: ../publicar_inmueble.php");
                exit();
            }

            // Si todo está bien, continuar con la inserción

            $id_promotor = $promotor['id'];

            $sql = "INSERT INTO inmuebles (titulo, descripcion, tipo, estado, precio, ubicacion, latitud, longitud, fecha_publicacion, id_promotor, img_src) 
                    VALUES (:titulo, :descripcion, :tipo, :estado, :precio, :ubicacion, :latitud, :longitud, :fecha_publicacion, :id_promotor, :img_src)";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindParam(':precio', $precio, PDO::PARAM_STR); 
            $stmt->bindParam(':ubicacion', $ubicacion, PDO::PARAM_STR);
            $stmt->bindParam(':latitud', $latitud, PDO::PARAM_STR); 
            $stmt->bindParam(':longitud', $longitud, PDO::PARAM_STR); 
            $stmt->bindParam(':fecha_publicacion', $fecha_publicacion, PDO::PARAM_STR);
            $stmt->bindParam(':id_promotor', $id_promotor, PDO::PARAM_INT);
            $stmt->bindParam(':img_src', $img_src, PDO::PARAM_STR);


        if ($stmt->execute()) {
            $_SESSION['publicar']['mensaje'] = "Propiedad publicada exitosamente.";
            header("Location: ../publicar_inmueble.php");
            exit();
        } else {
             $_SESSION['publicar']['error'] = "Error al publicar: " . $stmt->errorInfo()[2];
            header("Location: ../publicar_inmueble.php");
            exit();
        }
    } else {
        $_SESSION['publicar']['error'] = "No se encontró al promotor.";
        header("Location: ../publicar_inmueble.php");
        exit();
    }
} catch(PDOException $e){
    $_SESSION['publicar']['error'] = "Error en la consulta: " . $e->getMessage();
    header("Location: ../publicar_inmueble.php");
    exit();
}
} else {
    header("Location: ../index.php");
    exit();
}


function isCoordinatesValid($latitud, $longitud) {
    return is_numeric($latitud) && is_numeric($longitud) && 
           $latitud >= -90 && $latitud <= 90 && 
           $longitud >= -180 && $longitud <= 180;
}