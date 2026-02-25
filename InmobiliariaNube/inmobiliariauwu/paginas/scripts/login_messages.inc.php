<?php

function checkLoginMessages(&$error, &$mensaje, &$tipoMensaje) {
    // Verifica si hay mensajes de error o éxito en la sesión
    if (isset($_SESSION['userMessage'])) {
        $error = isset($_SESSION['userMessage']['error']) ? $_SESSION['userMessage']['error'] : '';
        $mensaje = isset($_SESSION['userMessage']['mensaje']) ? $_SESSION['userMessage']['mensaje'] : '';
        $tipoMensaje = isset($_SESSION['userMessage']['tipoMensaje']) ? $_SESSION['userMessage']['tipoMensaje'] : '';
        unset($_SESSION['userMessage']); // Limpiar mensajes después de mostrarlos
    } else {
        $error = '';
        $mensaje = '';
        $tipoMensaje = '';
    }
}

function checkSignUpMessages(){
    if(isset($_SESSION['signupErrors'])) {
     
        $errores = $_SESSION['signupErrors'];

        echo('<div class="alert danger">');
        echo('<ul>');
        foreach ($errores as $error) {
            echo('<li>' . htmlspecialchars($error) . '</li>');
        }
        echo('</ul>');
        echo('<span class="close-alert" onclick="this.parentElement.remove()">×</span>');
        echo('</div>');

        unset($_SESSION['signupErrors']);
        return $errores;
    }else if (isset($_SESSION['mensajeExito'])) {
        $mensajeExito = $_SESSION['mensajeExito'];
        echo('<div class="alert success">');
        foreach ($mensajeExito as $mensaje) {
            echo('<p>' . htmlspecialchars($mensaje) . '</p>');
        }
        echo('<span class="close-alert" onclick="this.parentElement.remove()">×</span>');
        echo('</div>');

        unset($_SESSION['mensajeExito']);
        return $mensajeExito;
    }

}

