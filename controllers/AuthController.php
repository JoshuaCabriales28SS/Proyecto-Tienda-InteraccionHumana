<?php

namespace Controller;

class AuthController extends BaseController {
    public function login(): void {
        $errores = [];

        if($this->requestMethod() === 'POST'){
            $db = conectarDB();
            $email = mysqli_real_escape_string($db, filter_var($_POST['correo'] ?? '', FILTER_VALIDATE_EMAIL));
            $password = mysqli_real_escape_string($db, $_POST['password'] ?? '');

            if(!$email){
                $errores[] = 'El email es obligatorio o no es valido';
            }
            if(!$password){
                $errores[] = 'El password es obligatorio';
            }

            if(empty($errores)){
                $query = "SELECT * FROM usuarios WHERE correo = '{$email}'";
                $resultado = mysqli_query($db, $query);

                if($resultado && $resultado->num_rows){
                    $usuario = mysqli_fetch_assoc($resultado);
                    $auth = password_verify($password, $usuario['password']);
                    if($auth){
                        session_start();
                        $_SESSION['usuario'] = $usuario['correo'];
                        $_SESSION['login'] = true;
                        $this->redirect('/admin/index.php');
                    } else {
                        $errores[] = 'El password es incorrecto';
                    }
                } else {
                    $errores[] = 'El usuario no existe';
                }
            }
        }

        $this->render('login', ['errores' => $errores]);
    }

    public function logout(): void {
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
        $_SESSION = [];
        $this->redirect('/index.php');
    }
}
