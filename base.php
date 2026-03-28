<?php
require 'includes/app.php';
incluirTemplate('header');

// obtener id
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if(!$id){
    echo "Categoria no valida";
    exit;
}

// consulta
$query = "SELECT * FROM productos WHERE categoria_id = $id";
$resultado = mysqli_query($db, $query);
?>

<main class="contenedor" id="main-content">
    <h2>Productos de la categoría</h2>

    <div class="productos">
        <?php while($producto = mysqli_fetch_assoc($resultado)): ?>
            <div class="producto">
                <h3><?php echo $producto['nombre']; ?></h3>
                <p>$<?php echo $producto['precio']; ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<?php
incluirTemplate('footer');
?>
