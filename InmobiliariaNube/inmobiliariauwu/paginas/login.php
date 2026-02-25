<?php
// Incluye el archivo de configuración, donde debería estar la conexión a la base de datos ($con)

session_start(); // Inicia la sesión para poder usar $_SESSION

// Si el usuario ya está logueado, redirige directamente al index
if (isset($_SESSION['user']['usuario'])) {
    header("Location: index.php");
    exit(); // Termina la ejecución del script
}



include_once 'scripts/login_messages.inc.php';
?>


<!DOCTYPE html>
<html>
<head>
  <title>Página de login</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="css/base.css"> <!-- css para todos los archivos -->
  <link rel="stylesheet" href="css/login.css"> <!-- css exclusivo de index -->
  <link rel="stylesheet" href="css/nav.css"> <!-- css de la barra de navegación -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome para iconos -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> <!-- Fuentes de Google -->
</head>
<body class="bg-light">

<a class="back-button" href="index.php">Atrás</a>

<div class="login-container">
  <div class="login-card">
    <!-- Mostrar mensaje (éxito o error) si existe -->
    <?php if (!empty($mensaje)): ?>
    <div class="alert <?= $tipoMensaje ?>">
      <?= $mensaje ?>
      <span class="close-alert" onclick="this.parentElement.remove()">×</span>
    </div>
    <?php endif; ?>

    <!-- Encabezado con ícono -->
    <div class="login-header">
      <img src="img/logoinmobiliariare.png" alt="Logo" width="60">
      <h2>Iniciar sesión</h2>
    </div>

      <form method="POST" action="scripts/login_process.inc.php">
        <input type="text" name="usuario" placeholder="Nombre de usuario" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <button type="submit">Iniciar sesión</button>
      </form>

      <p class="registro">¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
  </div>

<!-- Script para ocultar alerta después de 2 segundos -->
<script>
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
      alert.style.transition = 'opacity 0.5s ease';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    }
  }, 2000);
</script>
</body>
</html>

