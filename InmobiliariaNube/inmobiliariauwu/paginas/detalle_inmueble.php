<?php 
// Para acceder a la database

session_start(); // Inicia la sesión para poder usar $_SESSION


include_once '../config/config_db.php';
include_once 'scripts/recomendaciones_process.inc.php'; 



// Se checa si si se mando mediante GET el id de la propiedad, si no lo mantiene como 0
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Se busca la propiedad
$sql = "SELECT * FROM inmuebles WHERE id = :id LIMIT 1";
$stmt = $con->prepare($sql);
$stmt->execute([":id"=> $id]);

$inmueble = $stmt->fetch(PDO::FETCH_ASSOC);

//Si no encuentra propiedad lo manda a la mierda
if($inmueble === false) {
    header("Location: index.php");
    exit();
}

// Registrar el evento de vista si el usuario está logueado y el inmueble existe
addViewToInmueble($con, $inmueble);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($inmueble['titulo']); ?></title>
        <link rel="stylesheet" href="css/base.css"> <!-- css para todos los archivos -->
        <link rel="stylesheet" href="css/detalle_inueble.css"> <!-- css exclusivo de detalle -->
        <link rel="stylesheet" href="css/nav.css"> <!-- css de la barra de navegación --> 
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
           
</head>
<body>

    <!-- Barra de navegación principal -->
    <?php include 'scripts/nav.php'; ?>
    <header>
        <h1>Detalles del Inmueble</h1>

  <!-- Nuevo contenedor estructurado para el CSS -->
  <div class="detalle-inmueble">
    <div class="tarjeta-detalle">
      <div class="tarjeta-contenido">
        <!-- Galería de imágenes -->
        <!-- 
          GESTIÓN DE IMÁGENES PARA INMUEBLES:
          =================================
          1. UBICACIÓN: Todas las imágenes deben guardarse en la carpeta /img/inmuebles/
          2. NOMBRE DE ARCHIVO: El nombre debe ser exactamente el ID del inmueble + extensión .jpg
             Por ejemplo: para el inmueble con ID=5, el archivo debe ser "5.jpg"
          3. TAMAÑO RECOMENDADO: 1200x800 píxeles (proporción 3:2) para mejor visualización
          4. FALLBACK: Si no existe la imagen, se mostrará automáticamente un placeholder
          
          EJEMPLO COMPLETO:
          - Para el inmueble con ID=10, guardar como: "/img/inmuebles/10.jpg"
        -->
        <div class="galeria-inmueble">
          <img src="<?php echo $inmueble['img_src']; ?>" 
               alt="<?php echo htmlspecialchars($inmueble['titulo']); ?>" 
               style="width:100%; height:auto; object-fit: cover;">
        </div>

        <!-- Título - MISMO PHP QUE ANTES -->
        <h1 class="titulo-inmueble"><?php echo htmlspecialchars($inmueble['titulo']); ?></h1>
        
        <!-- Precio - MISMO PHP QUE ANTES -->
        <p class="precio">$<?php echo number_format($inmueble['precio'], 2); ?></p>
        
        <!-- Botón de Favoritos --> 
        <form action="scripts/favoritos.php" method="POST" class="favoritos-form">
            <input type="hidden" name="inmueble_id" value="<?php echo $inmueble['id']; ?>">
            <button type="submit" class="btn-favorito">
                <i class="fas fa-heart"></i>
                <span>Añadir a Favoritos</span>
            </button>
        </form>
        
        <!-- Características clave -->
        <div class="detalles-clave">
          <div class="detalle-item"><?php echo ucfirst($inmueble['tipo']); ?></div>
          <div class="detalle-item"><?php echo ucfirst($inmueble['estado']); ?></div>
          <div class="detalle-item">Ubicación</div>
        </div>

        <!-- Columna izquierda -->
        <div class="columna-izquierda">
          <!-- Tipo y estado - MISMO PHP QUE ANTES -->
          <p class="tipo-estado"><?php echo ucfirst($inmueble['tipo']) . ' en ' . $inmueble['estado']; ?></p>
          
          <!-- Ubicación - MISMO PHP QUE ANTES -->
          <p class="ubicacion"><?php echo htmlspecialchars($inmueble['ubicacion']); ?></p>
        </div>

        <!-- Columna derecha -->
        <div class="columna-derecha">
          <!-- Descripción - MISMO PHP QUE ANTES -->
          <p class="descripcion"><?php echo htmlspecialchars($inmueble['descripcion']); ?></p>
        </div>

        <!-- Botón de volver - MISMO PHP QUE ANTES pero con clase nueva -->
        <a href="index.php" class="btn-volver"><span>←</span> Volver al listado</a>
      </div>
    </div>
  </div>

</body>
</html>