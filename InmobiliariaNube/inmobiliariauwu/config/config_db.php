<?php

$host = "144.126.138.152";
$dbname = "inmobiliaria";
$dbuser = "adminInmobiliaria";
$dbpass = "12345";

// Cambiado a PDO en vez de mysqli porque mysqli es una puta mierda, aparte que PDO es basicamente identico pero mejor. - jd

//
try{
    $con = new PDO("mysql:host=$host;dbname=$dbname",$dbuser,$dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
    $con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    die("<h1>No hay conexión a la database: " . $e->getMessage() . "</h1>");
}
?>
