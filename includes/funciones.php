<?php
    //constantes
    define('TEMPLATES_URL', __DIR__ . '/templates');
    define('FUNCIONES_URL', __DIR__ . '/funciones.php');

    //funciones
    function incluirTemplate(string $nombre){
        include TEMPLATES_URL."/$nombre.php";
    }
    function debuguear($variable){
        echo "<pre>";
        var_dump($variable);
        echo "</pre>";
        exit;
    }
    function obtenerImagenProducto(?string $imagen): string{
        $imagen = trim((string) $imagen);
        if($imagen !== ''){
            $ruta = __DIR__ . '/../images/' . $imagen;
            if(file_exists($ruta)){
                return '/images/' . $imagen;
            }
        }
        return '/build/img/producto_prueba.png';
    }
    function obtenerMetricasProducto(int $productoId): array{
        $rating = 3.6 + (($productoId % 14) / 10);
        if($rating > 4.9){
            $rating = 4.9;
        }
        $rating = round($rating, 1);
        $reviews = 25 + (($productoId * 17) % 400);

        return [
            'rating' => $rating,
            'reviews' => $reviews
        ];
    }
    function renderStars(float $rating): string{
        $estrellas = '';
        $llenas = (int) round($rating);
        if($llenas < 1){
            $llenas = 1;
        }
        if($llenas > 5){
            $llenas = 5;
        }

        $icono = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l2.9 6.2 6.8.6-5.1 4.4 1.6 6.6L12 16l-6.2 3.8 1.6-6.6L2 8.8l6.8-.6L12 2z" fill="currentColor"/></svg>';
        for($i = 0; $i < 5; $i++){
            $clase = $i < $llenas ? 'star is-filled' : 'star';
            $estrellas .= '<span class="' . $clase . '">' . $icono . '</span>';
        }

        return $estrellas;
    }
    function estaAutenticado(): void{
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        if(empty($_SESSION['login'])){
            header('Location: /index.php');
            exit;
        }
    }
