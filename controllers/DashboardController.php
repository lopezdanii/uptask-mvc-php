<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Proyecto;

class DashboardController {

    public static function index(Router $router){

        session_start();
        isAuth();

        $id= $_SESSION['id'];
        $proyectos = Proyecto::whereAll('propietarioId', $id);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);

    }


    public static function crearProyecto(Router $router){

        session_start();

        isAuth();

        $alertas =[];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $proyecto= new Proyecto($_POST);

            //Validacion formulario
            $alertas= $proyecto->validarProyecto();


            if(empty($alertas)){
                //Generar URL unica
                $proyecto->url = md5(uniqid());

                //Almacenar el id del usuario
                $proyecto->propietarioId= $_SESSION['id'];
                $proyecto->guardar();


                header('Location: /proyecto?id=' . $proyecto->url);
            }
            
        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);

    }

    public static function proyecto(Router $router){
        session_start();
        isAuth();

        $token= $_GET['id'];
        if(!$token){
            header('Location: /dashboard');
        }
        
        //Protección privacidad: Revisar que la persona que visita el proyecto es quien lo creo
        $proyecto = Proyecto::where('url', $token);
        if($proyecto->propietarioId !== $_SESSION['id']){
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);

    }


    public static function perfil(Router $router){
        session_start();

        isAuth();
        $alertas= [];
        $usuario= Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas =$usuario->validarPerfil();

            if(empty($alertas)){
                //verificar email
                $existeUsuario = Usuario::where('email', $usuario->email);
                if($existeUsuario && $existeUsuario->id !== $usuario->id){
                    Usuario::setAlerta('error','El email ya existe');
                }else{
                    //Guardar registro
                    $resultado=$usuario->guardar();
                    //Se actualiza el nombre a la variable de sesión, se actualiza en la barra
                    $_SESSION['nombre']= $usuario->nombre;
                    if($resultado){
                        Usuario::setAlerta('exito','Datos actualizados correctamente');
                    }
                }
            }
        }
    
        $alertas= Usuario::getAlertas();
        
        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' =>$alertas
        ]);

    }

    public static function cambiarPassword(Router $router){
        $alertas=[];
        session_start();
        isAuth();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = Usuario::find($_SESSION['id']);

            //Sincronizar con los datos del formulario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();

            if(empty($alertas)){
                
                $resultado = $usuario->comprobarPassword();
                if($resultado){
                    $usuario->password = $usuario->password_nuevo;
                    //Eliminar propiedades no necesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);
                    
                    $usuario->hashPassword();

                    $resultado = $usuario->guardar();

                    if($resultado){
                        Usuario::setAlerta('exito', "Contraseña actualizada correctamente");
                    }else{
                        Usuario::setAlerta('error', "Ha habido un problema al actualizar la contraseña");
                    }

                } else{
                    Usuario::setAlerta('error', 'Contraseña incorrecta');
                }
            }
        
        }

        $alertas = Usuario::getAlertas();

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Contraseña',
            'alertas'=>$alertas
        ]);

        
    }

}