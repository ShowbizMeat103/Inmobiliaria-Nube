<?php
session_start();
include_once 'scripts/login_messages.inc.php'; // Incluye el archivo de mensajes de inicio de sesión


?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="css/base.css"> <!-- css para todos los archivos -->
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/registro.css">
    <link rel="stylesheet" href="css/nav.css"> <!-- css de la barra de navegación -->
</head>
<body>
  <a class="back-button" href="index.php">Atrás</a>
    
    <div class="login-container"> <?php // Use a container similar to login.php ?>
        <div class="login-card">
            <div class="login-header"> <?php // Add a header section ?>
                <img src="img/logoinmobiliariare.png" alt="Logo" width="120">
                <h2>Registro</h2> <?php // Changed H1 to H2 to match login.php style ?>
            </div>

            <!-- Esto muestra los mensajes de error. Preguntarle al juan como cambiarlos para modificar su estilo. -->
            <?php checkSignUpMessages(); ?>
            
            <form action="scripts/sign_up_process.inc.php" method="POST">
                <?php // Removed .form-group divs as login.css styles inputs directly ?>
                <input type="text" name="usuario" placeholder="Nombre de usuario" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <input type="text" name="nombre" placeholder="Nombre completo" required>
                <select name="rol" required>
                    <option value="" disabled selected>Selecciona un rol</option>
                    <option value="cliente">Cliente (Si quieres ver inmuebles)</option>
                    <option value="promotor">Promotor (Si quieres aportar con tu propiedad)</option>
                </select>
                <input type="email" name="correo" placeholder="Correo" required>
                <input type="text" name="telefono" placeholder="Teléfono (opcional)">
                <button type="submit">Registrarme</button>
            </form>

            <p class="registro">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </div>
    </div>

    <script>
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
      alert.style.transition = 'opacity 1s ease';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 1000);
    }
  }, 2000);
</script>
</body>


</html>
