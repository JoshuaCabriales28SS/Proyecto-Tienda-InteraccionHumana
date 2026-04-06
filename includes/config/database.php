<?php
    function conectarDB(){
        $host     = getenv('MYSQLHOST')     ?: 'localhost';
        $user     = getenv('MYSQLUSER')     ?: 'root';
        $password = getenv('MYSQLPASSWORD') ?: 'root';
        $database = getenv('MYSQLDATABASE') ?: 'db_tiendaonline_crud';
        $port     = getenv('MYSQLPORT')     ?: 3306;

        $db = new mysqli($host, $user, $password, $database, $port);

        if($db->connect_error){
            echo "Error, no se conecto a la base de datos";
            exit;
        }

        return $db;
    }