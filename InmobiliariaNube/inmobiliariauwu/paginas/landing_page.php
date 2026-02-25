<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inicio</title>
  <!-- Estilos combinados -->
  <link rel="stylesheet" href="css/landing_page.css">
  <link rel="stylesheet" href="css/nav.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Iconos Font Awesome -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> <!-- Fuentes de Google -->
</head>
<body>

  <!-- Barra de navegación principal -->
 <?php include 'scripts/nav.php'; ?>

  <!-- Sección principal con imagen y texto destacado -->
  <section class="hero">
    <div class="hero-text">
      <h1>Tu hogar ideal está aquí</h1>
      <p>Explora propiedades en las mejores zonas. Encuentra la que va contigo.</p>
    </div>
  </section>

  <!-- Buscador de propiedades con filtros nuevos -->
  <div class="buscador">
    <form method="GET" action="buscar.php">
      <div class="search-filters">
        <div class="search-filter">
          <label for="property-type">Tipo</label>
          <select id="property-type" name="tipo">
            <option value="">Todos los tipos</option>
            <option value="casa">Casa</option>
            <option value="departamento">Departamento</option>
            <option value="local">Local Comercial</option>
            <option value="oficina">Oficina</option>
          </select>
        </div>
        <div class="search-filter">
          <label for="property-status">Estado</label>
          <select id="property-status" name="estado">
            <option value="">Cualquier Estado</option>
            <option value="venta">Venta</option>
            <option value="renta">Renta</option>
          </select>
        </div>
        <div class="search-filter">
          <label for="price-range">Precio</label>
          <select id="price-range" name="price_range">
            <option value="">Cualquier precio</option>
            <option value="0-500000">Hasta $500,000</option>
            <option value="500000-1000000">$500,000 - $1,000,000</option>
            <option value="1000000-2000000">$1,000,000 - $2,000,000</option>
            <option value="2000000+">Más de $2,000,000</option>
          </select>
        </div>
        <div class="search-input">
          <label for="location">Ubicación</label>
          <input type="text" id="location" name="search" placeholder="Ciudad, colonia, palabra clave..." />
        </div>
      </div>
      <button type="submit" class="search-btn">Buscar propiedades</button>
    </form>
  </div>

  <!-- Información sobre la inmobiliaria -->
<section class="our-info" id="about-us">
  <h2>¿Por qué elegirnos?</h2>
  <p>En Inmobiliaria Vázquez, nos dedicamos a hacer realidad tus sueños inmobiliarios con un servicio personalizado y profesional.</p>
  <div class="info-cards">
    <div class="card">
      <i class="icon home"></i>
      <h3>Amplia Variedad</h3>
      <p>Contamos con el catálogo más extenso de propiedades en las mejores zonas de la ciudad.</p>
    </div>
    <div class="card">
      <i class="icon advisor"></i>
      <h3>Asesoría Personalizada</h3>
      <p>Nuestro equipo de expertos te guiará en cada paso del proceso.</p>
    </div>
    <div class="card">
      <i class="icon investment "></i>
      <h3>Inversión Inteligente</h3>
      <p>Te ayudamos a tomar decisiones financieras acertadas para el mejor retorno.</p>
    </div>
  </div>
</section>

  <!-- Recomendaciones de propiedades -->
<section class="recomendations" id="props">
  <h2>Propiedades Destacadas</h2>
  <div class="recomendations-cards">
    <?php
    // Iniciar sesión si aún no se ha hecho


    // Incluir archivos necesarios
    // Asegúrate que la ruta a config_db.php es correcta desde landing_page.php
    require_once '../config/config_db.php'; 
    include_once 'scripts/recomendaciones_process.inc.php';

    // Obtener ID de usuario si está logueado
    $userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

    // Generar 3 recomendaciones
    $recomendaciones = generateSimpleRecommendations($con, 3, $userId);

    if (!empty($recomendaciones)) {
        foreach($recomendaciones as $propiedad) {
            // Etiqueta basada en el estado (ej. "Venta", "Renta")
            $etiqueta = htmlspecialchars(ucfirst($propiedad['estado'])); 

            // --- IMPORTANTE: Lógica de Imagen (como solicitado, placeholder se mantiene) ---
            // La tabla 'inmuebles' no tiene un campo de URL de imagen.
            // Debes implementar cómo obtener la imagen. 
            $imagen_url = $propiedad['img_src']; // REEMPLAZA ESTO con tu lógica de imagen

    ?>
    <div class="card">
      <div class="img">
        <img src="<?php echo $imagen_url; ?>" alt="<?php echo htmlspecialchars($propiedad['titulo']); ?>" />
        <span><?php echo $etiqueta; ?></span>
      </div>
      <h3><?php echo htmlspecialchars($propiedad['titulo']); ?></h3>
      <p><?php echo substr(htmlspecialchars($propiedad['descripcion'] ?? 'Descripción no disponible.'), 0, 80) . '...'; ?></p>
      
      <!-- Información adicional con campos disponibles -->
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
    } else {
        // Mensaje si no hay recomendaciones
        echo "<p style='text-align:center; width:100%; padding: 2rem;'>No hay propiedades destacadas disponibles en este momento.</p>";
    }
    ?>
  </div>
  <a href="index.php" class="btn">Ver todas las propiedades</a>
</section>

  <!-- Formulario de contacto -->
  <section class="contact-us">
    <h2>Contáctanos</h2>
    <p>¿Tienes alguna pregunta? No dudes en ponerte en contacto con nosotros.</p>
    <form action="contact.php" method="post">
      <input type="text" name="name" placeholder="Tu nombre" required />
      <input type="email" name="email" placeholder="Tu correo electrónico" required />
      <textarea name="message" placeholder="Tu mensaje" required></textarea>
      <button type="submit">Enviar</button>
    </form>
  </section>

  <!-- Pie de página con redes sociales -->
<footer class="footer">
  <div class="main">
    <div class="about">
      <img src="img/logoinmobiliariare.png" class="logo" />
      <p>Más de 15 años de experiencia, lavando dinero xd.</p>
      <div class="social">


        <a href="#"><img src="img\gorjeo.jpg" ></a>
        <a href="#"><img src="img\tik-tok.png" ></a>
        <a href="#"><img src="img\whatsapp_1.png" ></a>
        <a href="#"><img src="img\youtube_1.png" ></a>


      </div>
    </div>
    <div class="menu">
      <h3>Enlaces rápidos</h3>
      <ul>
        <li><a href="landing_page.php">Inicio</a></li>
        <li><a href="index.php">Propiedades</a></li>
        <li><a href="#about-us">Nosotros</a></li>
      </ul>
    </div>
    <div class="contact">
      <h3>Contáctanos</h3>
      <p><i class="i-map"></i> Epigmeo Gonzales 209</p>
      <p><i class="i-phone"></i> +52 442 594 3653</p>
      <p><i class="i-mail"></i> inmobiliariavazquez@inmvaz.com</p>
    </div>
  </div>
  <div class="copy">
    <p>&copy; 2025 Inmobiliaria Vázquez. Un lugar para lavar dinero.</p>
  </div>
</footer>
</body>
</html>