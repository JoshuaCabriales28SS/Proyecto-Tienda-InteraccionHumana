<?php
    require 'includes/app.php';
    $db = conectarDB();
    $errores = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $email = mysqli_real_escape_string($db ,filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL));
        $password = mysqli_real_escape_string($db, $_POST['password']);

        if(!$email){
            $errores[]="El email es obligatorio o no es valido";
        }
        if(!$password){
            $errores[]="El password es obligatorio";
        }
        if(empty($errores)){
            $query = "SELECT * FROM usuarios WHERE correo='$email'";
            $resultado = mysqli_query($db, $query);

            if($resultado->num_rows){
                $usuario = mysqli_fetch_assoc($resultado);
                $auth = password_verify($password, $usuario['password']);
                if($auth){
                    session_start();                    
                    $_SESSION['usuario'] = $usuario['correo'];
                    $_SESSION['login'] = true;
                    header('Location: /admin/index.php');
                    exit;
                }else{
                    $errores[]="El password es incorrecto";
                }
            }else{
                $errores[]="El usuario no existe";
            }
        }
    }

    incluirTemplate('header');
?>

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

<?php
    mysqli_close($db);
    incluirTemplate('footer');
?>
