<?php

namespace Controllers;

use Model\Proyecto;
use MVC\Router;

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


        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil'
        ]);

    }

}