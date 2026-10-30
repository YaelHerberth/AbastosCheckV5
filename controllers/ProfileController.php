<?php

namespace Controllers;

use Model\UsuariosModel;
use MVC\Router;

class ProfileController
{
    public static function profile(Router $router){

        $id_usuario = $_SESSION['id'];
        if($id_usuario == null){
            header('Location: /login?resultado=2');
            exit;
        }
        
        $resultado = UsuariosModel::find($id_usuario);
        
        $router->render('profile/index', [
            'resultado' => $resultado
        ]);
    }
}