<?php
    require '../includes/app.php';
    estaAutenticado();

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
        $id = $_POST['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if($id){
            $query = "SELECT imagen FROM productos WHERE id = $id";
            $resultado = mysqli_query($db, $query);
            $producto = mysqli_fetch_assoc($resultado);
            $rutaImagen = '../images/' . $producto['imagen'];
            if($producto['imagen'] && file_exists($rutaImagen)){
                unlink($rutaImagen);
            }
            
            $query = "DELETE FROM productos WHERE id=$id";
            $resultado = mysqli_query($db, $query);
            if($resultado){
                header('Location: /admin/index.php?resultado=3');
                exit;
            }
        }
    }

    $query = "SELECT * FROM productos";
    $resultadoProductos = mysqli_query($db, $query);

    $resultado = $_GET['resultado'] ?? null;

    incluirTemplate('header');
?>

<main class="productos_admin" id="main-content">
    <div class="contenedor">
        <h1>Administrador de Inventario</h1>
        
        <?php if(intval($resultado) === 1):?>
            <p class="alerta exito" role="status">Producto añadido correctamente</p>
        <?php elseif(intval($resultado) === 2):?>
            <p class="alerta exito" role="status">Producto editado correctamente</p>
        <?php elseif(intval($resultado) === 3):?>
            <p class="alerta exito" role="status">Producto eliminado correctamente</p>
        <?php endif; ?>

        <div class="interacciones-admin">
            <a class="btn btn-verdeOsc btn-agregar" href="/admin/productos/crear.php">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    >
                    <path d="M4.929 4.929a10 10 0 1 1 14.141 14.141a10 10 0 0 1 -14.14 -14.14zm8.071 4.071a1 1 0 1 0 -2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 1 0 2 0v-2h2a1 1 0 1 0 0 -2h-2v-2z" />
                </svg>
                <p>Añadir</p>
            </a>
        </div>

        <div class="tabla-responsive">
            <table class="productos-tabla">
                <thead>
                    <tr>
                        <th>Stock</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Descripción</th>
                        <th>Codigo de Barras</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($producto = mysqli_fetch_assoc($resultadoProductos)): ?>
                        <tr class="tr">
                            <td> <?php echo $producto['stock']; ?> </td>
                            <td> <?php echo $producto['nombre']; ?> </td>
                            <td> <?php echo '$'.number_format($producto['precio'], 2, '.', ','); ?> </td>
                            <td class="tr-desc"> <?php echo $producto['descripcion']; ?> </td>
                            <td> <?php echo $producto['codigo']; ?> </td>
                            <td>
                                <img src="<?php echo obtenerImagenProducto($producto['imagen']); ?>" alt="<?php echo $producto['nombre']; ?>">
                            </td>
                            <td>
                                <div class="btns-int">
                                    <a class="btn btn-naranja btn-editar btn-margin-bottom" href="productos/editar.php?id=<?php echo $producto['id']; ?>">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="#000000"
                                            stroke-width="1"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            >
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                    </a>

                                    <div class="eliminacion">
                                        <button class="btn btn-rojo btn-eliminar btn_confirmar" id="mostrarEliminar">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="24"
                                                height="24"
                                                viewBox="0 0 24 24"
                                                fill="currentColor"
                                                >
                                                <path d="M20 6a1 1 0 0 1 .117 1.993l-.117 .007h-.081l-.919 11a3 3 0 0 1 -2.824 2.995l-.176 .005h-8c-1.598 0 -2.904 -1.249 -2.992 -2.75l-.005 -.167l-.923 -11.083h-.08a1 1 0 0 1 -.117 -1.993l.117 -.007h16z" />
                                                <path d="M14 2a2 2 0 0 1 2 2a1 1 0 0 1 -1.993 .117l-.007 -.117h-4l-.007 .117a1 1 0 0 1 -1.993 -.117a2 2 0 0 1 1.85 -1.995l.15 -.005h4z" />
                                            </svg>
                                        </button>
                                        <div class="ventana_eliminar" id="ventanaEliminar">
                                            <div class="eliminar">
                                                <h2 for="">¿Estas seguro que deseas eliminar <?php echo $producto['nombre']; ?>?</h2>
                                                
                                                <div class="btns_confirmar">
                                                    <button class="btn btn-cancelar" id="cerrarEliminar">Cancelar</button>
                                                    
                                                    <form method="POST">
                                                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                                        <button class="btn btn-rojo btn-eliminar" type="submit">Eliminar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php
    mysqli_close($db);

    incluirTemplate('footer');
?>
