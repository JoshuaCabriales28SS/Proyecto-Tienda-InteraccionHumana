<?php

namespace Controller;

use Model\Producto;
use Model\Categoria;

class HomeController extends BaseController {
    public function index(): void {
        $busqueda = trim($_GET['q'] ?? '');
        $categoriaBusqueda = filter_var($_GET['categoria'] ?? null, FILTER_VALIDATE_INT);
        $limit = $busqueda !== '' ? 24 : 12;

        $productos = Producto::search([
            'q' => $busqueda,
            'categoria' => $categoriaBusqueda,
            'limit' => $limit,
        ]);

        $categorias = Categoria::all();

        $this->render('home', [
            'productos' => $productos,
            'categorias' => $categorias,
            'busqueda' => $busqueda,
            'categoriaBusqueda' => $categoriaBusqueda,
        ]);
    }
}
