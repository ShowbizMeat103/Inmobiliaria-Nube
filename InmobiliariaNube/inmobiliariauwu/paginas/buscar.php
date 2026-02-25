<?php
session_start();
require_once '../config/config_db.php';
require_once 'scripts/buscador.php'; // Reusing the existing search logic script

// Parámetros de paginación y búsqueda
$limite = 9; // Inmuebles por página
$pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$inicio = ($pagina - 1) * $limite;

// Get filter values
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
$estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';

// Initialize precio_min and precio_max
$precio_min_form = isset($_GET['precio_min']) && is_numeric($_GET['precio_min']) ? (float)$_GET['precio_min'] : '';
$precio_max_form = isset($_GET['precio_max']) && is_numeric($_GET['precio_max']) ? (float)$_GET['precio_max'] : '';

// Check for price_range from landing_page.php and parse it
if (isset($_GET['price_range']) && !empty($_GET['price_range'])) {
    $price_range_value = $_GET['price_range'];
    if ($price_range_value === '0-500000') {
        $precio_min_form = 0;
        $precio_max_form = 500000;
    } elseif ($price_range_value === '500000-1000000') {
        $precio_min_form = 500000;
        $precio_max_form = 1000000;
    } elseif ($price_range_value === '1000000-2000000') {
        $precio_min_form = 1000000;
        $precio_max_form = 2000000;
    } elseif ($price_range_value === '2000000+') {
        $precio_min_form = 2000000;
        $precio_max_form = ''; // No upper limit
    }
}

// Obtener los inmuebles y el total
$resultadosBusqueda = buscarInmuebles($con, $searchTerm, $tipo, $estado, $precio_min_form, $precio_max_form, $limite, $inicio);
$filas = $resultadosBusqueda['filas'];
$totalInmuebles = $resultadosBusqueda['totalInmuebles'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Propiedades - Inmobiliaria Vázquez</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/buscar.css"> <!-- Will be updated to match landing_page search style -->
    <link rel="stylesheet" href="css/index.css"> 
    <link rel="stylesheet" href="css/tarjeta_inmueble.css"> 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> <!-- Added Google Fonts link for Poppins -->
</head>
<body>
    <header class="navbar">
        <img src="img/logoinmobiliariare.png" alt="Logo" class="logo" />
        <div class="nav-icons">
          <a href="landing_page.php" class="nav-btn">Inicio</a>
          <a href="index.php" class="nav-btn">Navegación</a>
          <a href="publicar_inmueble.php" class="nav-btn">Publicar Inmueble</a>
          <a href="login.php" class="nav-btn">Iniciar Sesión</a>
          <a href="registro.php" class="nav-btn">Registrarse</a>
          <a href="#" class="nav-btn">Nosotros</a>
        </div>
    </header>

    <!-- Sección de búsqueda -->
    <section class="buscador"> 
      <form method="GET" action="buscar.php">
        <div class="search-filters">
            <div class="search-input full-width-keyword"> 
                <label for="search-term-buscar">Palabra Clave</label>
                <input type="text" id="search-term-buscar" name="search" placeholder="Título, descripción, ubicación..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            </div>
            <div class="search-filter">
                <label for="tipo-buscar">Tipo</label>
                <select id="tipo-buscar" name="tipo">
                    <option value="">Cualquier Tipo</option>
                    <option value="casa" <?php echo ($tipo == 'casa') ? 'selected' : ''; ?>>Casa</option>
                    <option value="departamento" <?php echo ($tipo == 'departamento') ? 'selected' : ''; ?>>Departamento</option>
                    <option value="local" <?php echo ($tipo == 'local') ? 'selected' : ''; ?>>Local</option>
                    <option value="oficina" <?php echo ($tipo == 'oficina') ? 'selected' : ''; ?>>Oficina</option>
                </select>
            </div>
            <div class="search-filter">
                <label for="estado-buscar">Estado</label>
                <select id="estado-buscar" name="estado">
                    <option value="">Cualquier Estado</option>
                    <option value="venta" <?php echo ($estado == 'venta') ? 'selected' : ''; ?>>Venta</option>
                    <option value="renta" <?php echo ($estado == 'renta') ? 'selected' : ''; ?>>Renta</option>
                </select>
            </div>
            <!-- Wrapper for price filters -->
            <div class="price-filters-wrapper">
                <div class="search-filter">
                    <label for="precio-min-buscar">Precio Mín.</label>
                    <input type="number" id="precio-min-buscar" name="precio_min" placeholder="Precio Mín." value="<?php echo htmlspecialchars($precio_min_form); ?>" step="0.01">
                </div>
                <div class="search-filter">
                    <label for="precio-max-buscar">Precio Máx.</label>
                    <input type="number" id="precio-max-buscar" name="precio_max" placeholder="Precio Máx." value="<?php echo htmlspecialchars($precio_max_form); ?>" step="0.01">
                </div>
            </div>
        </div>
        <div class="form-actions"> 
            <button type="submit" class="search-btn">Buscar</button>
            <?php if (!empty($searchTerm) || !empty($tipo) || !empty($estado) || !empty($precio_min_form) || !empty($precio_max_form)): ?>
                <a href="buscar.php" class="limpiar-busqueda search-btn secondary">Limpiar Filtros</a> 
            <?php endif; ?>
        </div>
      </form>
    </section>

    <!-- Contenedor principal donde se mostrarán los inmuebles -->
    <div class="feed-contenedor">
    <?php
        if (count($filas) > 0) {
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
    <?php
            } // Fin del foreach
        } else {
            echo "<p style='text-align:center; grid-column: 1 / -1; font-size: 1.2rem; color: #4f3527;'>No se encontraron propiedades" . (!empty($searchTerm) ? " para la búsqueda '" . htmlspecialchars($searchTerm) . "'" : "") . ".</p>";
        }
    ?>
    </div>

    <!-- Sección de paginación -->
    <?php
        // Calculate totalPaginas before using it in the condition
        $totalPaginas = 0; // Initialize to 0
        if ($totalInmuebles > 0) {
            $totalPaginas = ceil($totalInmuebles / $limite);
        }
    ?>
    <?php if ($totalInmuebles > 0 && $totalPaginas > 1): ?>
    <div class="paginacion"> <!-- Styled by index.css -->
    <?php
        // Build query string for pagination, including all filters
        $queryParams = [];
        if (!empty($searchTerm)) $queryParams['search'] = $searchTerm;
        if (!empty($tipo)) $queryParams['tipo'] = $tipo;
        if (!empty($estado)) $queryParams['estado'] = $estado;
        if (!empty($precio_min_form)) $queryParams['precio_min'] = $precio_min_form; // Use the form values for pagination
        if (!empty($precio_max_form)) $queryParams['precio_max'] = $precio_max_form; // Use the form values for pagination
        
        $queryString = http_build_query($queryParams);
        $baseLink = '?' . $queryString . (empty($queryString) ? '' : '&') . 'page=';

        if($pagina > 1) {
            echo '<a href="' . $baseLink . ($pagina - 1) . '">Anterior</a>';
        }

        for($i = 1; $i <= $totalPaginas; $i++) {
            echo '<a href="' . $baseLink . $i . '" ' . ($i == $pagina ? 'class="active"' : '') . '>' . $i . '</a>';
        }

        if($pagina < $totalPaginas) {
            echo '<a href="' . $baseLink . ($pagina + 1) . '">Siguiente</a>';
        }
    ?>
    </div>
    <?php endif; ?>

</body>
</html>
