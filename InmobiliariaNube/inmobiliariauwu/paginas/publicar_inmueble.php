<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'promotor') {
    header("Location: login.php");
    exit();
}

$mensaje = '';
$error = '';
if (isset($_SESSION['publicar']['mensaje'])) {
    $mensaje = $_SESSION['publicar']['mensaje'];
    unset($_SESSION['publicar']['mensaje']);
} elseif (isset($_SESSION['publicar']['error'])) {
    $error = $_SESSION['publicar']['error'];
    unset($_SESSION['publicar']['error']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicar Inmueble</title>

    
        <!-- 1) Crga Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> <!-- Fuentes de Google -->

       <!-- 2) Luego tus estilos globales y de página -->
        <link rel="stylesheet" href="css/base.css"> <!-- css para todos los archivos -->
        <link rel="stylesheet" href="css/publicar_inmueble.css"> <!-- css exclusivo de index -->
        <link rel="stylesheet" href="css/nav.css"> <!-- css de la barra de navegación -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Iconos Font Awesome -->


</head>
<body>
  <!-- Barra de navegación principal -->
 <?php include 'scripts/nav.php'; ?>

</header>




<?php if ($mensaje): ?>
    <p style="color: green;"><?php echo $mensaje; ?></p>
<?php elseif ($error): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<div class="formulario-inmueble">
  <h2>Publicar nuevo inmueble</h2>
  <form method="POST" action="scripts/publicar_inmueble.inc.php" enctype="multipart/form-data">
    <label for="titulo">Título:</label>
    <input type="text" name="titulo" id="titulo" required>

    <label for="descripcion">Descripción:</label>
    <textarea name="descripcion" id="descripcion" rows="3"></textarea>

    <label for="tipo">Tipo:</label>
    <select name="tipo" id="tipo" required>
      <option value="casa">Casa</option>
      <option value="departamento">Departamento</option>
      <option value="local">Local</option>
      <option value="oficina">Oficina</option>
    </select>

    <label for="estado">Estado:</label>
    <select name="estado" id="estado" required>
      <option value="venta">Venta</option>
      <option value="renta">Renta</option>
    </select>

    <label for="precio">Precio:</label>
    <input type="number" name="precio" id="precio" step="0.01" required>

    <label for="ubicacion">Ubicación:</label>
    <input type="text" name="ubicacion" id="ubicacion" required>

    <label for="latitud">Latitud (opcional):</label>
    <input type="number" name="latitud" id="latitud" step="0.000001">

    <label for="longitud">Longitud (opcional):</label>
    <input type="number" name="longitud" id="longitud" step="0.000001">

    <label for="imagen">Imagen del inmueble:</label>
    <div class="inputfile-wrapper">
        <input type="file" name="imagen" id="imagen" accept="image/*" class="inputfile">
        <label for="imagen" class="boton-archivo">Seleccionar archivo</label>
        <span id="file-name-display" class="file-name-display"></span>
    </div>

    <button type="submit" name="publicar" class="boton-detalle">Publicar</button>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('imagen');
    const fileNameDisplay = document.getElementById('file-name-display');
    const fileLabelButton = document.querySelector('label.boton-archivo[for="imagen"]');

    if (fileInput && fileNameDisplay && fileLabelButton) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                fileNameDisplay.textContent = 'Archivo: ' + fileName;
                fileLabelButton.textContent = 'Cambiar Archivo';
            } else {
                fileNameDisplay.textContent = '';
                fileLabelButton.textContent = 'Seleccionar archivo';
            }
        });
    }
});
</script>
</body>
</html>
