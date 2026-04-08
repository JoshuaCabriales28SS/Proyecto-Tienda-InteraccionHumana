<?php

namespace Controller;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager as Image;
use Model\Categoria;
use Model\Producto;

class AdminController extends BaseController {
    public function index(): void {
        estaAutenticado();

        if($this->requestMethod() === 'POST'){
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if($id){
                $producto = Producto::find($id);
                if($producto && !empty($producto['imagen'])){
                    $rutaImagen = __DIR__ . '/../images/' . $producto['imagen'];
                    if(file_exists($rutaImagen)){
                        unlink($rutaImagen);
                    }
                }
                Producto::delete($id);
                $this->redirect('/admin/index.php?resultado=3');
            }
        }

        $productos = Producto::search(['limit' => 1000]);
        $resultado = filter_input(INPUT_GET, 'resultado', FILTER_VALIDATE_INT);

        $this->render('admin/index', [
            'productos' => $productos,
            'resultado' => $resultado,
        ]);
    }

    public function createProduct(): void {
        estaAutenticado();

        $categorias = Categoria::all();
        $errores = [];
        $values = [
            'nombre' => '',
            'precio' => '',
            'stock' => '',
            'descripcion' => '',
            'categorias_id' => '',
        ];

        if($this->requestMethod() === 'POST'){
            $values = [
                'nombre' => $_POST['nombre'] ?? '',
                'precio' => $_POST['precio'] ?? '',
                'stock' => $_POST['stock'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'categorias_id' => $_POST['categorias_id'] ?? '',
            ];

            $nombreImagen = md5(uniqid(rand(), true)) . '.jpg';
            $producto = new Producto($values);
            $producto->imagen = $nombreImagen;

            $errores = $producto->validar();
            if($_FILES['imagen']['tmp_name'] ?? false){
                $manager = new Image(Driver::class);
                $imagen = $manager->decodePath($_FILES['imagen']['tmp_name'])->resize(800, 600, function($constraint){
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            if(empty($errores)){
                $carpetaImagenes = __DIR__ . '/../../images/';
                if(!is_dir($carpetaImagenes)){
                    mkdir($carpetaImagenes, 0755, true);
                }

                if(isset($imagen)){
                    $imagen->save($carpetaImagenes . $nombreImagen);
                }

                $producto->guardar();
                $this->redirect('/admin/index.php?resultado=1');
            }
        }

        $this->render('admin/create', [
            'errores' => $errores,
            'categorias' => $categorias,
            'values' => $values,
        ]);
    }

    public function editProduct(): void {
        estaAutenticado();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if(!$id){
            $this->redirect('/admin/index.php');
        }

        $productoData = Producto::find($id);
        if(!$productoData){
            $this->redirect('/admin/index.php');
        }

        $categorias = Categoria::all();
        $errores = [];
        $values = [
            'nombre' => $productoData['nombre'],
            'precio' => $productoData['precio'],
            'stock' => $productoData['stock'],
            'descripcion' => $productoData['descripcion'],
            'categorias_id' => $productoData['categorias_id'],
        ];

        if($this->requestMethod() === 'POST'){
            $values = [
                'nombre' => $_POST['nombre'] ?? '',
                'precio' => $_POST['precio'] ?? '',
                'stock' => $_POST['stock'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'categorias_id' => $_POST['categorias_id'] ?? '',
                'codigo' => $productoData['codigo'],
                'imagen' => $productoData['imagen'],
            ];

            $producto = new Producto($values);
            $producto->id = $id;
            $producto->codigo = $productoData['codigo'];
            $producto->imagen = $productoData['imagen'];

            $nuevaImagen = $_FILES['imagen']['name'] ?? '';
            if($nuevaImagen){
                $nombreImagen = md5(uniqid(rand(), true)) . '.jpg';
                $producto->imagen = $nombreImagen;
                $manager = new Image(Driver::class);
                $imagen = $manager->decodePath($_FILES['imagen']['tmp_name'])->resize(800, 600, function($constraint){
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $errores = $producto->validar();
            if(empty($errores)){
                $carpetaImagenes = __DIR__ . '/../../images/';
                if(!is_dir($carpetaImagenes)){
                    mkdir($carpetaImagenes, 0755, true);
                }

                if(isset($imagen)){
                    $imagen->save($carpetaImagenes . $producto->imagen);
                    $rutaAnterior = $carpetaImagenes . $productoData['imagen'];
                    if($productoData['imagen'] && file_exists($rutaAnterior)){
                        unlink($rutaAnterior);
                    }
                }

                $producto->actualizar();
                $this->redirect('/admin/index.php?resultado=2');
            }
        }

        $this->render('admin/edit', [
            'errores' => $errores,
            'categorias' => $categorias,
            'values' => $values,
            'producto' => $productoData,
        ]);
    }
}
