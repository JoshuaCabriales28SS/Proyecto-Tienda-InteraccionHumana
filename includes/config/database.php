<?php
    function conectarDB(){
        $db = new mysqli('localhost', 'root', 'root', 'db_tiendaonline_crud');

        if($db->connect_error){
            echo "Error, no se conecto a la base de datos";
            exit;
        }

        return $db;
    }
