<?php

namespace Controllers;

use Intervention\Image\ImageManagerStatic as Image;
use Model\DepartamentosModel;
use Model\ProductoModel;
use MVC\Router;

class ProductosController
{
    public static function index(Router $router)
    {
        $listado = ProductoModel::listadoAdmin();
        $router->render('productos/index', [
            'listado' => $listado,
            'resultado' => $listado->filas
        ]);
    }

    public static function crear(Router $router)
    {

        $errores = ProductoModel::getErrores();
        $producto = new ProductoModel();
        $departamentos = DepartamentosModel::all();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto = new ProductoModel($_POST['producto']);

            $errores = $producto->validar();

            if (empty($errores)) {
                /* Subida de archivos */
                //Generar un nombre unico
                $nombreImagen = md5(uniqid(rand(), true)) . '.jpg';

                if ($_FILES['producto']['tmp_name']['imagen_producto']) {
                    // Crear la carpeta para subir imagenes
                    if (!is_dir(CARPETA_IMAGENES_PRODUCTOS)) {
                        mkdir(CARPETA_IMAGENES_PRODUCTOS);
                    }

                    //  Realiza un resize a la imagen con intervention
                    $img = Image::make($_FILES['producto']['tmp_name']['imagen_producto'])->fit(500, 500);
                    //  Setear la imagen
                    $producto->setImagen($nombreImagen);



                    //  Guardar la imagen en el servidor
                    $img->save(CARPETA_IMAGENES_PRODUCTOS . $nombreImagen);
                }

                $resultado = $producto->guardar();

                if ($resultado) {
                    header('Location: /admin/productos?resultado=1');
                    exit;
                }
            }
        }

        $router->render('productos/crear', [
            "errores" => $errores,
            "producto" => $producto,
            "departamentos" => $departamentos
        ]);
    }

    public static function actualizar(Router $router){

        $id = validarORedireccionar('/admin/productos?resultado=5');
        $errores = ProductoModel::getErrores();
        $producto = new ProductoModel();
        $producto = ProductoModel::find($_GET['id']);
        $departamentos = DepartamentosModel::all();
        
        if (!$producto) {
            header('Location: /admin/productos?resultado=5');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Asignar los atributos
            $args = $_POST['producto'];

            $producto->sincronizar($args);

            // Validacion
            $errores = $producto->validar();


            if (empty($errores)) {

                if ($_FILES['producto']['tmp_name']['imagen_producto']) {
                    // Crear la carpeta para subir imagenes
                    if (!is_dir(CARPETA_IMAGENES_PRODUCTOS)) {
                        mkdir(CARPETA_IMAGENES_PRODUCTOS);
                    }
                    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
                    $image = Image::make($_FILES['producto']['tmp_name']['imagen_producto'])->fit(500, 500);
                    $producto->setImagen($nombreImagen, CARPETA_IMAGENES_PRODUCTOS);
                    $image->save(CARPETA_IMAGENES_PRODUCTOS . $nombreImagen);
                }

                $resultado = $producto->guardar();

                if ($resultado) {
                    header('Location: /admin/productos?resultado=2');
                    exit;
                }
            }
        }

        $router->render('productos/actualizar', [
            "errores" => $errores,
            "producto" => $producto,
            "departamentos" => $departamentos
        ]);
    }

    public static function eliminar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);

            if ($id) {
                $usuario = ProductoModel::find($id);
                $usuario->eliminarProducto();
            }
        }
    }
}
