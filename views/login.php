<main class="login" id="main-content">
    <div class="contenedor">
        <?php foreach($errores as $error): ?>
            <div class="alerta error" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>

        <div class="login-contenido">
            <h2>Inicio de Sesión</h2>
            <p class="login-subtexto">Ingresa para administrar productos y pedidos.</p>
            <form method="POST">
                <fieldset>
                    <div class="campos">
                        <label for="correo">Usuario:</label>
                        <input type="email" id="correo" name="correo" autocomplete="email" required>
                    </div>
                    <div class="campos">
                        <label for="password">Contrasena:</label>
                        <input type="password" name="password" id="password" autocomplete="current-password" required>
                    </div>
                </fieldset>
                <input class="btn btn-verde btn-acceder" type="submit" value="Acceder">
            </form>
        </div>
    </div>
</main>
