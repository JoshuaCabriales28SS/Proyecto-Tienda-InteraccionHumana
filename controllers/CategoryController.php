<?php

namespace Controller;

use Model\Categoria;
use Model\Producto;

class CategoryController extends BaseController {
    public function show(): void {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if(!$id){
            $this->redirect('/index.php');
        }

        $categoria = Categoria::find($id);
        if(!$categoria){
            $this->redirect('/index.php');
        }

        $orden = $_GET['orden'] ?? 'relevancia';
        $ordenesValidos = ['relevancia', 'precio-asc', 'precio-desc', 'nombre'];
        if(!in_array($orden, $ordenesValidos, true)){
            $orden = 'relevancia';
        }

        $minPrecio = filter_var($_GET['min'] ?? null, FILTER_VALIDATE_FLOAT);
        $maxPrecio = filter_var($_GET['max'] ?? null, FILTER_VALIDATE_FLOAT);

        if($minPrecio !== false && $maxPrecio !== false && $minPrecio > $maxPrecio){
            $tmp = $minPrecio;
            $minPrecio = $maxPrecio;
            $maxPrecio = $tmp;
        }

        $productos = Producto::findByCategory($id, [
            'orden' => $orden,
            'min' => $minPrecio,
            'max' => $maxPrecio,
        ]);

        $this->render('category', [
            'categoria' => $categoria,
            'productos' => $productos,
            'orden' => $orden,
            'minPrecio' => $minPrecio,
            'maxPrecio' => $maxPrecio,
        ]);
    }
}
