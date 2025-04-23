<?php

namespace Controllers;

use Model\Proyecto;
use Model\Tarea;
use MVC\Router;

class TareaController {

    public static function index(){

        $proyectoId= $_GET['id'];
        
        if(!$proyectoId){
            header('Location: /dashboard');
        }
        
        $proyecto= Proyecto::where('url', $proyectoId);
        session_start();

        
        if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id'] ){
            header('Location: /404');
        }
        
        $tareas=Tarea::whereAll('proyectoId', $proyecto->id);
        echo json_encode(['tareas' => $tareas]);
    }

    public static function crear(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            session_start();
            
            //$proyecto= Proyecto::where('url', $_SESSION['id']);
            $proyecto= Proyecto::where('url', $_POST['proyectoId']);

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al agregar una tarea'
                ];
                echo json_encode($respuesta);
                return;
            }


            $tarea= new Tarea($_POST);

            $tarea-> proyectoId = $proyecto->id;
            $resultado= $tarea->guardar();

            $respuesta= [
                'tipo' => 'exito',
                'id' => $resultado['id'],
                'mensaje' => 'Tarea creada correctamente',
                'proyectoId' => $proyecto->id
            ];
            echo json_encode($respuesta);
        }
    }

    public static function actualizar(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            //validar que el proyecto existe
            $proyecto = Proyecto::where('url', $_POST['proyectoId']);
            session_start();

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar una tarea'
                ];
                echo json_encode($respuesta);
                return;
            }

            $tarea= new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado= $tarea->guardar();
            if($resultado){
                $respuesta= [
                    'tipo' => 'exito',
                    'id' => $tarea->id,
                    'proyectoId' => $proyecto->id,
                    'mensaje' => 'Actualizado correctamente'
                ];
                
                echo json_encode(['respuesta' => $respuesta]);
            }

        }
    }

    public static function eliminar(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            //validar que el proyecto existe
            $proyecto = Proyecto::where('url', $_POST['proyectoId']);
            session_start();

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar una tarea'
                ];
                echo json_encode($respuesta);
                return;
            }

            $tarea= new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado= $tarea->eliminar();
            if($resultado){
                $respuesta= [
                    'tipo' => 'exito',
                    'resultado' => $resultado,
                    'mensaje' => 'Eliminado correctamente'
                ];
                
            echo json_encode(['respuesta' => $respuesta]);
            }

        }

    }

}