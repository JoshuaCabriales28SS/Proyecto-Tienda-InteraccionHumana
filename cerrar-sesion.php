<?php
    session_start();

    //funciones para cerrar la sesion
    // session_unset();
    // session_destroy();

    //mejor forma para cerrar sesion, vaciar el arreglo de sesion
    $_SESSION = [];
    header('Location: /index.php');
    exit;
