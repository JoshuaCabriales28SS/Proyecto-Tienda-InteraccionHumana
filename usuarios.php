<?php
//USUARIO DE PRUEBA

//importar conexion
require 'includes/app.php';
$db = conectarDB();

//crear email
$email = "correo@correo.com";
$password = "123456";

//PASSWORD_DEFAULT o PASSWORD_BCRYPT para hashear
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

//query para crear usuarios
$query = "INSERT INTO usuarios (correo, password) VALUES ('$email','$passwordHash');";

//agregar usuario a base de datos
mysqli_query($db, $query);