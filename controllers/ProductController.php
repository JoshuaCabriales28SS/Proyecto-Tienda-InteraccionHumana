<?php

namespace Controller;

use Model\Producto;
use Model\Categoria;
use Model\Carrito;

class ProductController extends BaseController {
    public function show(): void {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if(!$id){
            $this->redirect('/index.php');
        }

        $producto = Producto::find($id);
        if(!$producto){
            $this->redirect('/index.php');
        }

        $categoriaProducto = Categoria::find((int) $producto['categorias_id']);
        $sinStock = (int) $producto['stock'] < 1;
        $errores = [];
        $agregado = filter_var($_GET['agregado'] ?? null, FILTER_VALIDATE_BOOLEAN);

        if($this->requestMethod() === 'POST'){
            if(session_status() === PHP_SESSION_NONE){
                session_start();
            }

            $cantidad = filter_var($_POST['cantidad'] ?? null, FILTER_VALIDATE_INT);
            if(!$cantidad || $cantidad < 1){
                $errores[] = 'Debes elegir una cantidad (minimo 1)';
            } elseif($cantidad > (int) $producto['stock']){
                $errores[] = 'La cantidad supera el stock disponible';
            }

            if(empty($errores)){
                $item = Carrito::findItem($id);
                if($item){
                    $nuevaCantidad = (int) $item['cantidad'] + $cantidad;
                    if($nuevaCantidad > (int) $producto['stock']){
                        $errores[] = 'No hay suficiente stock para esa cantidad';
                    } else {
                        Carrito::updateQuantityByProductoId($id, $nuevaCantidad);
                    }
                } else {
                    Carrito::create($id, $cantidad);
                }
            }

            if(empty($errores)){
                $seleccion = $_SESSION['carrito_seleccion'] ?? [];
                if(!is_array($seleccion)){
                    $seleccion = [];
                }
                if(!in_array($id, $seleccion, true)){
                    $seleccion[] = $id;
                }
                $_SESSION['carrito_seleccion'] = $seleccion;

                $this->redirect('/producto.php?id=' . $id . '&agregado=1');
            }
        }

        $this->render('product', [
            'producto' => $producto,
            'categoriaProducto' => $categoriaProducto,
            'sinStock' => $sinStock,
            'errores' => $errores,
            'agregado' => $agregado,
        ]);
    }
}
