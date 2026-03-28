<?php
    require '../../includes/app.php';
    estaAutenticado();

    $db = conectarDB();

    // SOLICITAR CATEGORIAS DISPONIBLES
    $queryCat = "SELECT * FROM categorias";
    $resultadoCategorias = mysqli_query($db, $queryCat);

    // ERRORES
    $errores = [];

    // VARIABLES PARA GUARDAR DATOS
    $stock = '';
    $nombre = '';
    $precio = '';
    $descripcion = '';
    $categoria_id = '';

    // SE EJECUTA AL MOMENTO DE ENVIAR LOS DATOS
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //Asignar archivos a una variable, la variable es igual a la imagen subida
        $imagen = $_FILES['imagen'];
        $medida = 1000*1000;

        // TOMA VALORES DE LOS CAMPOS
        $stock = mysqli_real_escape_string($db, $_POST['stock']);
        $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
        $precio = mysqli_real_escape_string($db, $_POST['precio']);
        $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
        $categoria_id = mysqli_real_escape_string($db, $_POST['categoria_id']);

        // VALIDACIÓN DE IMAGEN
        if(!$imagen['name'] || $imagen['error']){
            $errores[] = "Debes añadir una imagen";
        }
        if($imagen['size']>$medida){
            $errores[] = "La imagen es muy pesada";
        }

        // VALIDACIÓN DE DATOS
        if(!$stock){
            $errores[] = "Debes añadir una cantidad";
        }
        if(!$nombre){
            $errores[] = "Debes añadir un nombre";
        }
        if(!$precio){
            $errores[] = "Debes añadir un precio";
        }
        if(strlen($descripcion) < 50){
            $errores[] = "Debes añadir una descripcion y debe tener al menos 50 caracteres";
        }
        if(!$categoria_id){
            $errores[] = "Debes elegir una categoria";
        }

        // REVISAR ARREGLO DE ERRORES
        if(empty($errores)){
            $carpetaImagenes = '../../images/';

            //subir foto a carpeta interna
            if(!is_dir($carpetaImagenes)){
                mkdir($carpetaImagenes, 0755, true); // SI NO EXISTE LA CARPETA, SE CREA
            }

            // GENERAR NOMBRE PARA IMAGEN
            $nombreImagen = md5( uniqid( rand(), true) ). ".jpg";

            // MOVER LA IMAGEN A LA CARPETA
            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
            
            //GENERAR CODIGO DE BARRAS DEL PRODUCTO
            $codigoBarras = "";
            for ($i=0; $i < 10; $i++){
                $num = random_int(0,9);
                $codigoBarras .= strval($num);
            }
            
            $query = "INSERT INTO productos (stock, nombre, precio, imagen, descripcion, codigo, categorias_id) VALUES ('$stock','$nombre', '$precio', '$nombreImagen', '$descripcion', '$codigoBarras', '$categoria_id')";

            $resultado = mysqli_query($db, $query);
            
            if($resultado){
                // REDIRECCIONAR
                header("Location: /admin/index.php?resultado=1");
                exit;
            }
        }
    }

    incluirTemplate('header');
?>

    <main class="inventario" id="main-content">
        <div class="contenedor inventario-contenido">
            <h1>Agregar producto</h1>
            <a class="btn btn-naranja btn-volver" href="../index.php">
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
                    <path d="M9 14l-4 -4l4 -4" />
                    <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
                </svg>
            </a>
            
            <?php foreach($errores as $error): ?>
                <div class="alerta error" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endforeach; ?>

            <form action="/admin/productos/crear.php" method="POST" enctype="multipart/form-data">
                <fieldset class="inventario-form">

                    <div class="campo">
                        <label for="imagen">Imagen:</label>
                        <input 
                            type="file" 
                            name="imagen" 
                            id="imagen"
                            accept="image/jpeg, image/png"
                            required
                        >
                    </div>

                    <div class="campo">
                        <label for="nombre">Nombre:</label>
                        <input 
                            type="text" 
                            name="nombre" 
                            id="nombre" 
                            value="<?php echo $nombre;?>"
                            required
                        > <!-- VALUE GUARDA DATOS PARA NO INSERTARLOS DE NUEVO -->
                    </div>

                    <div class="cantidades">
                        <div class="campo">
                            <label for="precio">Precio:</label>
                            <input 
                                type="number" 
                                name="precio" 
                                id="precio" 
                                min="0"
                                value="<?php echo $precio;?>"
                                required
                            >
                        </div>
                        <div class="campo">
                            <label for="stock">Stock:</label>
                            <input 
                                type="number" 
                                name="stock" 
                                id="stock" 
                                min="0"
                                value="<?php echo $stock;?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="campo">
                        <label for="descripcion">Descripción:</label>
                        <textarea 
                            name="descripcion" 
                            id="descripcion"
                            required><?php echo $descripcion; ?></textarea>
                    </div>

                    <div class="campo">
                        <label for="categoria">Categoria:</label>
                        <select name="categoria_id" required>
                            <option value="" disabled selected>-- Seleccionar --</option>
    
                            <?php while($categoria = mysqli_fetch_assoc($resultadoCategorias)): ?>
                                <option <?php echo $categoria_id === $categoria['id'] ? 'selected' : ''; ?> value="<?php echo $categoria['id']; ?>">
                                    <?php echo $categoria['nombre']; ?>
                                </option>
                            <?php endwhile; ?>
    
                        </select>
                    </div>
                </fieldset>
                <button class="btn btn-verde btn-agregar" type="submit">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        >
                        <path d="M4.929 4.929a10 10 0 1 1 14.141 14.141a10 10 0 0 1 -14.14 -14.14zm8.071 4.071a1 1 0 1 0 -2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 1 0 2 0v-2h2a1 1 0 1 0 0 -2h-2v-2z" />
                    </svg>
                    <p>Agregar</p>
                </button>
            </form>
        </div>
    </main>

<?php
    incluirTemplate('footer');
?>
