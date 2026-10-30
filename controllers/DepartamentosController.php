<?php

namespace Controllers;

use Model\DepartamentosModel;
use MVC\Router;
use Intervention\Image\ImageManagerStatic as Image;

class DepartamentosController
{
    public static function index(Router $router)
    {

        $resultado = DepartamentosModel::all();

        $router->render('departamentos/index', [
            'resultado' => $resultado
        ]);
    }

    public static function crear(Router $router)
    {

        $departamento = new DepartamentosModel();
        $errores = DepartamentosModel::getErrores();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $departamento = new DepartamentosModel($_POST['departamento']);


            $errores = $departamento->validar();

            if (empty($errores)) {

                /* Subida de archivos */
                //Generar un nombre unico
                $nombreImagen = md5(uniqid(rand(), true)) . '.jpg';


                if ($_FILES['departamento']['tmp_name']['imagen_departamento']) {
                    // Crear la carpeta para subir imagenes
                    if (!is_dir(CARPETA_IMAGENES_DEPARTAMENTOS)) {
                        mkdir(CARPETA_IMAGENES_DEPARTAMENTOS);
                    }

                    //  Realiza un resize a la imagen con intervention
                    $img = Image::make($_FILES['departamento']['tmp_name']['imagen_departamento'])->fit(500, 500);
                    //  Setear la imagen
                    $departamento->setImagen($nombreImagen);

                    //  Guardar la imagen en el servidor
                    $img->save(CARPETA_IMAGENES_DEPARTAMENTOS . $nombreImagen);
                } else {
                    $departamento->imagen_departamento = '';
                }


                // Guardar en la base de datos
                $resultado = $departamento->guardar();

                if ($resultado) {
                    header('Location: /admin/departamentos?resultado=1');
                    exit;
                }
            }
        }
        $router->render('departamentos/crear', [
            "departamento" => $departamento,
            "errores" => $errores
        ]);
    }

    public static function actualizar(Router $router)
    {
        $id = validarORedireccionar('/admin/departamentos?resultado=5');

        $errores = DepartamentosModel::getErrores();
        $departamento = DepartamentosModel::find($id);


        if (!$departamento) {
            header('Location: /admin/departamentos?resultado=5');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Asignar los atributos
            $args = $_POST['departamento'];

            $departamento->sincronizar($args);

            // Validacion
            $errores = $departamento->validar();


            if (empty($errores)) {

                if ($_FILES['departamento']['tmp_name']['imagen_departamento']) {
                    // Crear la carpeta para subir imagenes
                    if (!is_dir(CARPETA_IMAGENES_DEPARTAMENTOS)) {
                        mkdir(CARPETA_IMAGENES_DEPARTAMENTOS);
                    }
                    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
                    $image = Image::make($_FILES['departamento']['tmp_name']['imagen_departamento'])->fit(500, 500);
                    $departamento->setImagen($nombreImagen, CARPETA_IMAGENES_DEPARTAMENTOS);
                    $image->save(CARPETA_IMAGENES_DEPARTAMENTOS . $nombreImagen);
                }

                $resultado = $departamento->guardar();

                if ($resultado) {
                    header('Location: /admin/departamentos?resultado=2');
                    exit;
                }
            }
        }

        $router->render('departamentos/actualizar', [
            "errores" => $errores,
            "departamento" => $departamento
        ]);
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);

            if ($id) {
                $usuario = DepartamentosModel::find($id);
                $usuario->eliminarDepartamento();
            }
        }
    }
}
