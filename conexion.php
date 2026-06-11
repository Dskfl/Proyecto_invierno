<?php
$hostname = "sql208.infinityfree.com"; // el mysql hostname
$username = "if0_42160087";            // usuario de base de datos de InfinityFree
$password = "6hsRH7eJEz7";             // contraseña de la base de datos
$database = "if0_42160087_reportesinvi"; // Nombre exacto de la BD en el hosting

$conexion = mysqli_connect($hostname, $username, $password, $database);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>