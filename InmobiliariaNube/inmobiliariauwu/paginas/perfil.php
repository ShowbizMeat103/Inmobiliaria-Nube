<?php
session_start();

require_once '../config/config_db.php';
include_once 'scripts/login_messages.inc.php';
require_once 'scripts/recomendaciones_process.inc.php'; // Added for recommendation functions

// Si se solicita cerrar sesión
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Verificar que hay sesión activa
if (!isset($_SESSION['user'])) { // Ensure user ID is also in session
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['user']['usuario'];
$userId = $_SESSION['user']['id']; // Get user ID from session

// Obtener datos del usuario desde la BD
try {
    $stmt = $con->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$datos) {
        die("<h2>No se encontraron datos del usuario.</h2>");
    }
} catch (PDOException $e) {
    die("<h2>Error al obtener datos del perfil: " . $e->getMessage() . "</h2>");
}

// Fetch Favorites - display up to 5
$favoritos = getXFavorites($con, $userId, 5, 0);

// Fetch View History - display up to 5 most viewed by user
$historialVisitas = getUserMostViewedProperties($con, $userId, 5, 0);

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil de Usuario</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/perfil.css">
  <link rel="stylesheet" href="css/nav.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'scripts/nav.php'; ?>

<main class="perfil-contenedor">
  <h1><i class="fas fa-user-circle"></i> Perfil de <?php echo htmlspecialchars($datos['nombre']); ?></h1>
  <div class="perfil-datos">
    <p><strong>Usuario:</strong> <?php echo htmlspecialchars($datos['usuario']); ?></p>
    <p><strong>Rol:</strong> <?php echo htmlspecialchars($datos['rol']); ?></p>
    <p><strong>Nombre completo:</strong> <?php echo htmlspecialchars($datos['nombre']); ?></p>
    <p><strong>Correo electrónico:</strong> <?php echo htmlspecialchars($datos['correo']); ?></p>
    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($datos['telefono']); ?></p>
  </div>
  
  <div class="logout-btn">
    <a href="perfil.php?logout" class="log-out">Cerrar Sesión</a>
  </div>

  <!-- Sección Mis Favoritos -->
  <?php if (!empty($favoritos)): ?>
  <section class="profile-section">
    <h2><i class="fas fa-heart"></i> Mis Favoritos</h2>
    <div class="profile-recommendations-cards">
      <?php foreach ($favoritos as $propiedad): ?>
        <?php
          // Assuming images are in 'paginas/img/' or adjust path as needed
          $imagen_url = '/images/inmuebles/sin_imagen.jpg'; 
          if (!empty($propiedad['img_src'])) { // Example: if you have an image path in your data
              // Check if $propiedad['img_src'] is a full URL or relative path
              // If relative to a specific directory, prepend it.
              // For now, let's assume it's relative to 'paginas/' or a direct subfolder like 'img/'
              // If your image paths are stored like 'uploads/image.jpg' and 'uploads' is at the root:
              // $imagen_url = '../' . htmlspecialchars($propiedad['img_src']);
              // If 'img/' is inside 'paginas/':
              // $imagen_url = htmlspecialchars($propiedad['img_src']); // if it already includes 'img/'
              // $imagen_url = 'img/' . htmlspecialchars($propiedad['img_src']); // if it's just the filename
              $imagen_url = htmlspecialchars($propiedad['img_src']);
          }
          $etiqueta = htmlspecialchars(ucfirst($propiedad['estado'] ?? ''));
        ?>
        <div class="card">
          <div class="img">
            <img src="<?php echo $imagen_url; ?>" alt="<?php echo htmlspecialchars($propiedad['titulo']); ?>" />
            <?php if ($etiqueta): ?><span><?php echo $etiqueta; ?></span><?php endif; ?>
          </div>
          <div class="card-content">
            <h3><?php echo htmlspecialchars($propiedad['titulo']); ?></h3>
            <p class="card-description"><?php echo substr(htmlspecialchars($propiedad['descripcion'] ?? 'Descripción no disponible.'), 0, 70) . '...'; ?></p>
            <div class="meta-profile">
              <p><strong>Tipo:</strong> <?php echo htmlspecialchars(ucfirst($propiedad['tipo'])); ?></p>
              <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($propiedad['ubicacion']); ?></p>
            </div>
            <p class="price">$<?php echo number_format($propiedad['precio'], 2); ?></p>
            <a href="detalle_inmueble.php?id=<?php echo $propiedad['id']; ?>" class="btn-card">Ver Detalles</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php elseif ($userId): // Show message only if logged in and no favorites ?>
  <section class="profile-section">
    <h2><i class="fas fa-heart"></i> Mis Favoritos</h2>
    <p>Aún no tienes propiedades guardadas en tus favoritos.</p>
  </section>
  <?php endif; ?>

  <!-- Sección Mi Historial de Visitas -->
  <?php if (!empty($historialVisitas)): ?>
  <section class="profile-section">
    <h2><i class="fas fa-history"></i> Mi Historial de Visitas</h2>
    <div class="profile-recommendations-cards">
      <?php foreach ($historialVisitas as $propiedad): ?>
        <?php
          $imagen_url = htmlspecialchars($propiedad['img_src']);
          // Similar image logic as above
          $etiqueta = htmlspecialchars(ucfirst($propiedad['estado'] ?? ''));
        ?>
        <div class="card">
          <div class="img">
            <img src="<?php echo $imagen_url; ?>" alt="<?php echo htmlspecialchars($propiedad['titulo']); ?>" />
            <?php if ($etiqueta): ?><span><?php echo $etiqueta; ?></span><?php endif; ?>
          </div>
          <div class="card-content">
            <h3><?php echo htmlspecialchars($propiedad['titulo']); ?></h3>
            <p class="card-description"><?php echo substr(htmlspecialchars($propiedad['descripcion'] ?? 'Descripción no disponible.'), 0, 70) . '...'; ?></p>
            <div class="meta-profile">
              <p><strong>Tipo:</strong> <?php echo htmlspecialchars(ucfirst($propiedad['tipo'])); ?></p>
              <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($propiedad['ubicacion']); ?></p>
              <?php if (isset($propiedad['user_total_vistas'])): ?>
                  <p><strong>Vistas por ti:</strong> <?php echo htmlspecialchars($propiedad['user_total_vistas']); ?></p>
              <?php endif; ?>
            </div>
            <p class="price">$<?php echo number_format($propiedad['precio'], 2); ?></p>
            <a href="detalle_inmueble.php?id=<?php echo $propiedad['id']; ?>" class="btn-card">Ver Detalles</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php elseif ($userId): // Show message only if logged in and no history ?>
  <section class="profile-section">
    <h2><i class="fas fa-history"></i> Mi Historial de Visitas</h2>
    <p>No hay historial de visitas para mostrar.</p>
  </section>
  <?php endif; ?>

</main>

<footer class="footer">
  <div class="copy">
    <p>&copy; <?php echo date("Y"); ?> Inmobiliaria Vázquez. Un lugar para lavar dinero.</p>
  </div>
</footer>

</body>
</html>
 