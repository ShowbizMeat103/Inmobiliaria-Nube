<?php
// Incluye el archivo de configuración que establece la conexión a la base de datos
// include_once asegura que solo se incluya una vez para evitar conflictos
session_start(); // Inicia la sesión para poder usar $_SESSION


include_once 'scripts/login_messages.inc.php'; // Incluye el archivo de mensajes -- Todavia no hace nada, pero es para el futuro.
include_once 'scripts/recomendaciones_process.inc.php'; // Incluye el archivo de recomendaciones -- Todavia no hace nada, pero es para el futuro.

//  Checar si se confunden culeros

require_once '../config/config_db.php';

    //TODO: Mover los scripts de esta pagina a un include separado, por metodos.(Mas seguro y limpio)
?>


<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8"> <!-- Define el juego de caracteres como UTF-8 para soportar acentos y símbolos -->
        <title>Inmobiliaria Vázquez</title> <!-- Título de la pestaña del navegador -->
        
        <!-- 1) Crga Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> <!-- Fuentes de Google -->

        <!-- 2) Luego tus estilos globales y de página -->
        <link rel="stylesheet" href="css/base.css"> <!-- css para todos los archivos -->
        <link rel="stylesheet" href="css/index.css"> <!-- css exclusivo de index -->
        <link rel="stylesheet" href="css/tarjeta_inmueble.css"> <!-- New CSS for property cards -->
        <link rel="stylesheet" href="css/nav.css"> <!-- css de la barra de navegación -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Iconos Font Awesome -->

    </head>
    <body>

  <!-- Barra de navegación principal -->
 <?php include 'scripts/nav.php'; ?>

<!-- Título principal de la página -->
<h2 style="text-align:center; margin-top: 2rem; margin-bottom: 1.5rem; font-size:2rem; color:#2b1b12;">Nuestras Propiedades</h2>

<!-- Contenedor principal donde se mostrarán los inmuebles -->
<div class="feed-contenedor">

<?php
    // Obtener ID de usuario si está logueado
    $userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

    // Generar 4 recomendaciones
    $recomendaciones = generateSimpleRecommendations($con, 4, $userId);

    if (!empty($recomendaciones)) {
        foreach($recomendaciones as $propiedad) {
            $etiqueta = 'Recomendado';
            $imagen_url = $propiedad['img_src']; // Ajusta según tu lógica real de imágenes
?>
    <div class="card">
      <div class="img">
        <img src="<?php echo $imagen_url; ?>" alt="<?php echo htmlspecialchars($propiedad['titulo']); ?>" />
        <span><?php echo $etiqueta; ?></span>
      </div>
      <h3><?php echo htmlspecialchars($propiedad['titulo']); ?></h3>
      <p><?php echo substr(htmlspecialchars($propiedad['descripcion'] ?? 'Descripción no disponible.'), 0, 80) . '...'; ?></p>
      <div style="padding: 0 0rem 0.5rem; font-size: 0.9em; color: #4f3527;">
          <p style="margin-bottom: 0.25rem;"><strong>Tipo:</strong> <?php echo htmlspecialchars(ucfirst($propiedad['tipo'])); ?></p>
          <p style="margin-bottom: 0.25rem;"><strong>Ubicación:</strong> <?php echo htmlspecialchars($propiedad['ubicacion']); ?></p>
          <p><strong>Publicado:</strong> <?php echo htmlspecialchars(date("d/m/Y", strtotime($propiedad['fecha_publicacion']))); ?></p>
      </div>
      <p class="price" style="margin-top: 0.5rem;">$<?php echo number_format($propiedad['precio'], 2); ?></p>
      <a href="detalle_inmueble.php?id=<?php echo $propiedad['id']; ?>" class="btn" style="margin: 0.5rem auto 1rem; display: block; width: fit-content; text-align: center; padding: 0.6rem 1.5rem;">Ver Detalles</a>
    </div>
<?php
        }
    }

    // Variable que define cuántos inmuebles se mostrarán por página
    $limite = 5;

    // Determina el número de página actual desde el parámetro GET 'page'
    // Si no se recibe, se asume que es la página 1
    $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Calcula el inicio del registro para la consulta SQL (paginación)
    $inicio = ($pagina - 1) * $limite;

    // Consulta SQL para obtener los inmuebles con paginación
    $sql = "SELECT * FROM inmuebles LIMIT :limite OFFSET :inicio";

    $stmt = $con->prepare($sql);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $stmt->execute();

    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($filas as $fila) {
        $etiqueta = htmlspecialchars(ucfirst($fila['estado']));
        $imagen_url = $fila['img_src']; // Ajusta según tu lógica real de imágenes
?>
    <div class="card">
      <div class="img">
        <img src="<?php echo $imagen_url; ?>" alt="<?php echo htmlspecialchars($fila['titulo']); ?>" />
        <span><?php echo $etiqueta; ?></span>
      </div>
      <h3><?php echo htmlspecialchars($fila['titulo']); ?></h3>
      <p><?php echo substr(htmlspecialchars($fila['descripcion'] ?? 'Descripción no disponible.'), 0, 80) . '...'; ?></p>
      <div style="padding: 0 0rem 0.5rem; font-size: 0.9em; color: #4f3527;">
          <p style="margin-bottom: 0.25rem;"><strong>Tipo:</strong> <?php echo htmlspecialchars(ucfirst($fila['tipo'])); ?></p>
          <p style="margin-bottom: 0.25rem;"><strong>Ubicación:</strong> <?php echo htmlspecialchars($fila['ubicacion']); ?></p>
          <p><strong>Publicado:</strong> <?php echo htmlspecialchars(date("d/m/Y", strtotime($fila['fecha_publicacion']))); ?></p>
      </div>
      <p class="price" style="margin-top: 0.5rem;">$<?php echo number_format($fila['precio'], 2); ?></p>
      <a href="detalle_inmueble.php?id=<?php echo $fila['id']; ?>" class="btn" style="margin: 0.5rem auto 1rem; display: block; width: fit-content; text-align: center; padding: 0.6rem 1.5rem;">Ver Detalles</a>
    </div>
<?php } // Fin del while ?>

</div>

<!-- Sección de paginación -->
<div class="paginacion">
<?php 
    $totalSQL = "SELECT COUNT(*) as total FROM inmuebles";
    $totalRes = $con->query($totalSQL);
    $totalInmuebles = $totalRes->fetchColumn(); // Use fetchColumn for single value

    $totalPaginas = ceil($totalInmuebles / $limite);

    if($pagina > 1) {
        echo '<a href="?page=' . ($pagina - 1) . '">Anterior</a>';
    }

    for($i = 1; $i <= $totalPaginas; $i++) {
        echo '<a href="?page=' . $i . '" ' . ($i == $pagina ? 'style="font-weight:bold;"' : '') . '>' . $i . '</a>';
    }

    if($pagina < $totalPaginas) {
        echo '<a href="?page=' . ($pagina + 1) . '">Siguiente</a>';
    }
?>
    </div>

    </body>
</html>

