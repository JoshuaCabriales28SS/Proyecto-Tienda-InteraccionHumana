<?php
require 'funciones.php';
require 'config/database.php';
require __DIR__ . '/../vendor/autoload.php';

use Model\Producto;
use Model\Categoria;
use Model\Carrito;

$db = conectarDB();

Producto::setDB($db);
Categoria::setDB($db);
Carrito::setDB($db);
