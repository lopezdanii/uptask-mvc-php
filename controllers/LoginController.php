<?php

namespace Controllers;

use Classes\Email;
use MVC\Router;
use Model\Usuario;

class LoginController{
    public static function login(Router $router){
        $alertas=[];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario= new Usuario($_POST);

            $alertas= $usuario->validarLogin();

            if(empty($alertas)){
                
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario){
                    if($usuario->confirmado){
                        //Confirmado y existe

                        //Comprobar password
                        if(password_verify($_POST['password'], $usuario->password)){
                            //Iniciar sesion
                            session_start();

                            $_SESSION['id'] = $usuario->id;
                            $_SESSION['nombre'] = $usuario->nombre;
                            $_SESSION['email'] = $usuario->email;
                            $_SESSION['login'] = true;

                            header('Location: /dashboard');

                        } else{
                            Usuario::setAlerta('error', 'Contraseña incorrecta');

                        }

                    } else
                        Usuario::setAlerta('error', 'El usuario no está confirmado');

                }else{
                    Usuario::setAlerta('error', 'El usuario no esta registrado');
                }
            }

        }

        $alertas = Usuario::getAlertas();


        //Se envian los datos del modelo a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar sesión',
            'alertas' => $alertas
        ]);

        }

    public static function logout(Router $router){
        session_start();

        $_SESSION = [];

        header('Location: /');

    }

    public static function crear(Router $router){

        $usuario = new Usuario;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validar();

            if(empty($alertas)){
                
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario){
                    Usuario::setAlerta('error', 'El usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();

                }else{
                    $usuario->hashPassword();

                    //eliminar password 2
                    unset($usuario->password2);

                    //generar token
                    $usuario->crearToken();

                    //Crear nuevo usuario
                    $resultado= $usuario->guardar();

                    //Enviar Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    if($resultado)
                        header('Location: /mensaje');
                }
            }
        }

        //Pasamos los datos del Modelo hacia la vista
        $router->render('auth/crear', [
            'titulo' => 'Crear cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function passwordOlvidada(Router $router){
        $alertas=[];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario= new Usuario($_POST);
            $alertas= $usuario->validarEmail();
        
        
            if(empty($alertas)){
                //Se busca el usuario
                $usuario=Usuario::where('email', $usuario->email);
                if($usuario){
                    //Existe el usuario
                    if($usuario->confirmado){
                        //Generar nuevo token
                        $usuario->crearToken();
                        unset($usuario->password2);

                        //Actualizar el usuario
                        $usuario->guardar();

                        //Enviar email
                        $email= new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarInstrucciones();


                        //Imprimir alerta
                        Usuario::setAlerta('exito', 'Se han enviado instrucciones a su email');

                    } else
                        Usuario::setAlerta('error', 'El usuario no está confirmado');

                }else{
                    Usuario::setAlerta('error', 'El usuario no está registrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();


        $router->render('auth/passwordOlvidada', [
            'titulo' => '¿Olvidaste la contraseña?',
            'alertas' => $alertas
        ]);
    }

    public static function restablecer(Router $router){
        $token =s($_GET['token']);
        $mostrar=true;
        if(!$token){
            header('Location: /');
        }

        $usuario = Usuario::where('token', $token);

        if(!$usuario){
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar= false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);

            $alertas= $usuario->validarPassword();

            if(empty($alertas)){
                //Hashear nuevo password
                $usuario->hashPassword();

                //eliminar token
                $usuario->token=null;

                //Actualizar usuario en BBDD
                $resultado=$usuario->guardar();

                if($resultado){
                    //Redireccionar
                    header('Location: /');
                }
            } 

        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/restablecer', [
            'titulo' => 'Restablecer contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router){

        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta creada'
        ]);
    }

    public static function confirmar(Router $router){

        $token = s($_GET['token']);

        if(!$token){
            header('Location: /');
        }

        //Encontrar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            //Token no encontrado en ningun usuario
            Usuario::setAlerta('error', 'Token no válido');
        }else{
            //Confirmar cuenta: actualizamos los campos confirmado y token
            $usuario->confirmado= 1;
            $usuario->token= null;
            //unset($usuario->password2);

            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');

        }

        $alertas= Usuario::getAlertas();

        $router->render('auth/confirmar', [
            'titulo' => 'Confirmar cuenta',
            'alertas' => $alertas
        ]);
    }

}
