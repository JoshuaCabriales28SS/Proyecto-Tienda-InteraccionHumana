<?php
require 'includes/app.php';
$db = conectarDB();

$email = "correo@correo.com";
$password = "123456";

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$query = "INSERT INTO usuarios (correo, password) VALUES ('$email','$passwordHash');";

mysqli_query($db, $query);