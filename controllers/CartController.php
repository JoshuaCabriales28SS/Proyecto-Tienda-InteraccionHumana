<?php

namespace Controller;

use Model\Carrito;

class CartController extends BaseController {
    public function index(): void {
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        $db = conectarDB();
        $accion = $_POST['accion'] ?? null;
        $productoId = filter_var($_POST['productos_id'] ?? null, FILTER_VALIDATE_INT);
        $returnTo = sanitizeReturnTo($_POST['return_to'] ?? '/carrito.php');

        if($this->requestMethod() === 'POST' && $accion){
            if($accion === 'eliminar' && $productoId){
                Carrito::deleteByProductoId($productoId);
                if(isset($_SESSION['carrito_seleccion']) && is_array($_SESSION['carrito_seleccion'])){
                    $_SESSION['carrito_seleccion'] = array_values(array_diff($_SESSION['carrito_seleccion'], [$productoId]));
                }
            }

            if(($accion === 'incrementar' || $accion === 'decrementar') && $productoId){
                $item = Carrito::findItem($productoId);
                if($item){
                    $cantidadActual = (int) $item['cantidad'];
                    $query = "SELECT stock FROM productos WHERE id = {$productoId} LIMIT 1";
                    $resultado = mysqli_query($db, $query);
                    $producto = $resultado ? mysqli_fetch_assoc($resultado) : null;
                    $stock = $producto ? (int) $producto['stock'] : 0;

                    if($accion === 'incrementar' && $cantidadActual < $stock){
                        Carrito::updateQuantityByProductoId($productoId, $cantidadActual + 1);
                    }
                    if($accion === 'decrementar'){
                        if($cantidadActual > 1){
                            Carrito::updateQuantityByProductoId($productoId, $cantidadActual - 1);
                        } else {
                            Carrito::deleteByProductoId($productoId);
                            if(isset($_SESSION['carrito_seleccion']) && is_array($_SESSION['carrito_seleccion'])){
                                $_SESSION['carrito_seleccion'] = array_values(array_diff($_SESSION['carrito_seleccion'], [$productoId]));
                            }
                        }
                    }
                }
            }

            if($accion === 'seleccionar' && $productoId){
                $seleccion = $_SESSION['carrito_seleccion'] ?? null;
                $seleccionado = isset($_POST['seleccionado']);

                if(!is_array($seleccion)){
                    $seleccion = Carrito::getIds();
                }
                $seleccion = array_map('intval', $seleccion);
                if($seleccionado && !in_array($productoId, $seleccion, true)){
                    $seleccion[] = $productoId;
                }
                if(!$seleccionado){
                    $seleccion = array_values(array_diff($seleccion, [$productoId]));
                }
                $_SESSION['carrito_seleccion'] = $seleccion;
            }

            if($accion === 'seleccionar_todo'){
                $_SESSION['carrito_seleccion'] = Carrito::getIds();
            }

            if($accion === 'limpiar_seleccion'){
                $_SESSION['carrito_seleccion'] = [];
            }

            $idsActuales = Carrito::getIds();
            if(empty($idsActuales)){
                unset($_SESSION['carrito_seleccion']);
            }

            $this->redirect($returnTo);
        }

        $carrito = Carrito::getItems();
        $seleccionActiva = array_key_exists('carrito_seleccion', $_SESSION) && is_array($_SESSION['carrito_seleccion']);
        $seleccion = $seleccionActiva ? array_map('intval', $_SESSION['carrito_seleccion']) : [];
        $carritoDetalle = [];
        $totalCarrito = 0;
        $totalCantidad = 0;
        $totalSeleccion = 0;
        $cantidadSeleccion = 0;

        foreach($carrito as $producto){
            $totalCarrito += (float) $producto['precio'] * (int) $producto['cantidad'];
            $totalCantidad += (int) $producto['cantidad'];
            $producto['seleccionado'] = !$seleccionActiva || in_array((int) $producto['producto_id'], $seleccion, true);
            if($producto['seleccionado']){
                $totalSeleccion += (float) $producto['precio'] * (int) $producto['cantidad'];
                $cantidadSeleccion += (int) $producto['cantidad'];
            }
            $carritoDetalle[] = $producto;
        }

        if(empty($carrito) && $seleccionActiva){
            unset($_SESSION['carrito_seleccion']);
            $seleccionActiva = false;
            $seleccion = [];
        }

        $puedePagar = $seleccionActiva ? $cantidadSeleccion > 0 : !empty($carrito);

        $this->render('cart', [
            'carritoDetalle' => $carritoDetalle,
            'seleccionActiva' => $seleccionActiva,
            'seleccion' => $seleccion,
            'totalCarrito' => $totalCarrito,
            'totalSeleccion' => $totalSeleccion,
            'cantidadSeleccion' => $cantidadSeleccion,
            'puedePagar' => $puedePagar,
        ]);
    }

    public function checkout(): void {
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        $seleccionActiva = array_key_exists('carrito_seleccion', $_SESSION) && is_array($_SESSION['carrito_seleccion']);
        $seleccion = $seleccionActiva ? array_map('intval', $_SESSION['carrito_seleccion']) : [];

        if($this->requestMethod() === 'POST'){
            if($seleccionActiva && !empty($seleccion)){
                Carrito::deleteByProductoIds($seleccion);
                $_SESSION['carrito_seleccion'] = array_values(array_diff($_SESSION['carrito_seleccion'], $seleccion));
            } else {
                Carrito::clearAll();
            }

            $this->redirect('/index.php');
        }

        $productosCarrito = $seleccionActiva ? Carrito::getItemsByProductoIds($seleccion) : Carrito::getItems();
        $cantidadSeleccion = 0;
        $total = 0;

        foreach($productosCarrito as $producto){
            $cantidadSeleccion += (int) $producto['cantidad'];
            $total += (float) $producto['precio'] * (int) $producto['cantidad'];
        }

        $this->render('checkout', [
            'productosCarrito' => $productosCarrito,
            'cantidadSeleccion' => $cantidadSeleccion,
            'total' => $total,
        ]);
    }
}
