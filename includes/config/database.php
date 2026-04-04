<?php
    function conectarDB(){
        $db = new mysqli('mysql.railway.internal', 'root', 'rCeAZNBXqWuEvXnACdoDTtLBGqiBAqsD', 'railway', '3306');

        if($db->connect_error){
            echo "Error, no se conecto a la base de datos";
            exit;
        }

        return $db;
    }
