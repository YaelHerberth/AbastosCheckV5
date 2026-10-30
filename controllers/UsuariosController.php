<?php

namespace Controllers;

use Model\UsuariosModel;
use MVC\Router;
use Intervention\Image\ImageManagerStatic as Image;

class UsuariosController
{
    public static function index(Router $router)
    {
        $listado = UsuariosModel::listadoAdmin();

        $router->render('usuarios/index', [
            'listado' => $listado,
            'resultado' => $listado->filas
        ]);
    }

    public static function crear(Router $router)
    {

        $usuario = new UsuariosModel();
        $errores = UsuariosModel::getErrores();


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new UsuariosModel($_POST['usuario']);

            
            $errores = $usuario->validar();
            
            if (empty($errores)) {
                
                /* Subida de archivos */
                //Generar un nombre unico
                $nombreImagen = md5(uniqid(rand(), true)) . '.jpg';
    
                
                if ($_FILES['usuario']['tmp_name']['imagen_usuario']) {
                    // Crear la carpeta para subir imagenes
                    if (!is_dir(CARPETA_IMAGENES_USUARIOS)) {
                        mkdir(CARPETA_IMAGENES_USUARIOS);
                    }
                    
                    //  Realiza un resize a la imagen con intervention
                    $img = Image::make($_FILES['usuario']['tmp_name']['imagen_usuario'])->fit(500, 500);
                    //  Setear la imagen
                    $usuario->setImagen($nombreImagen);

                    //  Guardar la imagen en el servidor
                    $img->save(CARPETA_IMAGENES_USUARIOS . $nombreImagen);
                }

                // Guardar en la base de datos
                $usuario->encriptarPassword();
                $resultado = $usuario->guardar();

                if ($resultado) {
                    header('Location: /admin/usuarios?resultado=1');
                    exit;
                }
            }
        }
        $router->render('usuarios/crear', [
            "usuario" => $usuario,
            "errores" => $errores
        ]);
    }

    public static function actualizar(Router $router)
    {
        $id = validarORedireccionar('/admin/usuarios?resultado=5');

        $errores = UsuariosModel::getErrores();
        $usuario = UsuariosModel::find($id);


        if (!$usuario) {
            header('Location: /admin/usuarios?resultado=5');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Cambio de contraseña
            
            // Asignar los atributos
            $args = $_POST['usuario'];
            
            $usuario->comprobarPasswordNueva();
            
            $usuario->sincronizar($args);

            // Validacion
            $errores = $usuario->validar();


            if (empty($errores)) {

                if ($_FILES['usuario']['tmp_name']['imagen_usuario']) {
                    // Crear la carpeta para subir imagenes
                    if (!is_dir(CARPETA_IMAGENES_USUARIOS)) {
                        mkdir(CARPETA_IMAGENES_USUARIOS);
                    }
                    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
                    $image = Image::make($_FILES['usuario']['tmp_name']['imagen_usuario'])->fit(500, 500);
                    $usuario->setImagen($nombreImagen, CARPETA_IMAGENES_USUARIOS);
                    $image->save(CARPETA_IMAGENES_USUARIOS . $nombreImagen);
                }

                $resultado = $usuario->guardar();

                if ($resultado) {
                    header('Location: /admin/usuarios?resultado=2');
                    exit;
                }

            }
        }

        $router->render('usuarios/actualizar', [
            "errores" => $errores,
            "usuario" => $usuario
        ]);
    }

    public static function eliminar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);

            if ($id) {
                $usuario = UsuariosModel::find($id);
                $usuario->eliminarUsuario();
            }
        }
    }

}
