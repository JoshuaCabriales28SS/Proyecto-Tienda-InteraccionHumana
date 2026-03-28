<?php
    require 'includes/app.php';

    $db = conectarDB();

    $errores = [];

    //autenticar usuario
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        //SANITIZACION
        $email = mysqli_real_escape_string($db ,filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL));
        $password = mysqli_real_escape_string($db, $_POST['password']);

        if(!$email){
            $errores[]="El email es obligatorio o no es valido";
        }
        if(!$password){
            $errores[]="El password es obligatorio";
        }

        if(empty($errores)){
            //revisar si el usuario existe
            $query = "SELECT * FROM usuarios WHERE correo='$email'";
            $resultado = mysqli_query($db, $query);

            //saber si existe
            if($resultado->num_rows){ //el usuario si existe
                $usuario = mysqli_fetch_assoc($resultado);

                //revisar si el password es correcto (funcion para verificar password, si es true la contraseña es correcta)
                $auth = password_verify($password, $usuario['password']);

                if($auth){
                    //usuario autenticado
                    session_start(); //se inicia sesion
                    
                    //llenar arreglo de sesion con informacion que deseas
                    $_SESSION['usuario'] = $usuario['correo'];
                    $_SESSION['login'] = true;
                    
                    //redireccionar si se inicio sesion correctamente al administrador
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
