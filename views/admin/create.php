<main class="inventario" id="main-content">
    <div class="contenedor inventario-contenido">
        <h1>Agregar producto</h1>
        <a class="btn btn-naranja btn-volver" href="/admin/index.php">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 14l-4 -4l4 -4" />
                <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
            </svg>
        </a>

        <?php foreach($errores as $error): ?>
            <div class="alerta error" role="alert"><?php echo $error; ?></div>
        <?php endforeach; ?>

        <form action="/admin/productos/crear.php" method="POST" enctype="multipart/form-data">
            <fieldset class="inventario-form">
                <div class="campo">
                    <label for="imagen">Imagen:</label>
                    <input type="file" name="imagen" id="imagen" accept="image/jpeg, image/png" required>
                </div>
                <div class="campo">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($values['nombre'], ENT_QUOTES); ?>" required>
                </div>
                <div class="cantidades">
                    <div class="campo">
                        <label for="precio">Precio:</label>
                        <input type="number" name="precio" id="precio" min="0" value="<?php echo htmlspecialchars($values['precio'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="campo">
                        <label for="stock">Stock:</label>
                        <input type="number" name="stock" id="stock" min="0" value="<?php echo htmlspecialchars($values['stock'], ENT_QUOTES); ?>" required>
                    </div>
                </div>
                <div class="campo">
                    <label for="descripcion">Descripción:</label>
                    <textarea name="descripcion" id="descripcion" required><?php echo htmlspecialchars($values['descripcion'], ENT_QUOTES); ?></textarea>
                </div>
                <div class="campo">
                    <label for="categoria">Categoria:</label>
                    <select name="categorias_id" required>
                        <option value="" disabled selected>-- Seleccionar --</option>
                        <?php foreach($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>" <?php echo $values['categorias_id'] == $categoria['id'] ? 'selected' : ''; ?>><?php echo $categoria['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </fieldset>
            <button class="btn btn-verde btn-agregar" type="submit">Agregar</button>
        </form>
    </div>
</main>
