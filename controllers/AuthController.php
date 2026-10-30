<?php

namespace Controllers;

use Model\AuthModel;
use MVC\Router;

class AuthController
{

    public static function login(Router $router)
    {

        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new AuthModel($_POST);

            $errores =  $auth->validarLogin();

            if (empty($errores)) {

                $resultado = $auth->existeUsuario();
                
                if (!$resultado) {
                    // Verificar si el usuario existe o no (mensaje de error)
                    $errores = AuthModel::getErrores();
                } else {
                    // Verificar la contraseña
                    $autenticado = $auth->comprobarPassword($resultado);
                    if ($autenticado) {
                        // Autenticar al usuario
                        $auth->autenticar();
                    } else {
                        // Password incorrecto (mensaje de error)
                        $errores = AuthModel::getErrores();
                    }
                }
            }
        }
        $router->render('auth/login', [
            'errores' => $errores
        ]);
    }

    public static function signup(Router $router)
    {
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new AuthModel($_POST);

            $errores = $auth->validarRegistro();

            if (empty($errores)) {

                $resultado = $auth->existeUsuario();
                if ($resultado) {
                    $errores[] = 'El correo ya existe, inicie sesion o intente de nuevo';
                } else {
                    // Registro de usuario
                    $auth->registro();
                }
            }
        }

        $router->render('auth/register', [
            'errores' => $errores
        ]);
    }

    public static function logout()
    {
        session_destroy();
        header('Location: /');
        exit;
    }
}
