<?php
    session_start();

    $mensaje="";
    if(isset($_POST['btnAccion'])){
        switch($_POST['btnAccion']){
            case 'Agregar':

                if(is_numeric( openssl_decrypt($_POST['id'],COD,KEY ))){
                    $ID=openssl_decrypt($_POST['id'],COD,KEY );
                    $mensaje.="Ok ID Correcto".$ID."</br>";
                }else{
                    $mensaje.="ID Incorrecto".$ID."</br>";
                }

                if(is_string( openssl_decrypt($_POST['nombre'],COD,KEY ))){
                    $NOMBRE=openssl_decrypt($_POST['nombre'],COD,KEY );
                    $mensaje.="Ok Nombre Correcto".$NOMBRE."</br>";
                }else{
                    $mensaje.="Upps.... algo pasa con el nombre".$ID."</br>";
                }

                if(is_numeric( openssl_decrypt($_POST['cantidad'],COD,KEY ))){
                    $CANTIDAD=openssl_decrypt($_POST['cantidad'],COD,KEY );
                    $mensaje.="Ok Cantidad Correcto".$CANTIDAD."</br>";
                }else{
                    $mensaje.="Upps.... algo pasa con la cantidad".$ID."</br>";
                }

                if(is_numeric( openssl_decrypt($_POST['precio'],COD,KEY ))){
                    $PRECIO=openssl_decrypt($_POST['precio'],COD,KEY );
                    $mensaje.="Ok precio".$PRECIO."</br>";
                }else{
                    $mensaje.="Upps.... algo pasa con el precio".$ID."</br>";
                }
                
                if(!isset($_SESSION['CARRITO'])){
                    $producto=array(
                        'ID'=>$ID,
                        'NOMBRE'=>$NOMBRE,
                        'CANTIDAD'=>$CANTIDAD,
                        'PRECIO'=>$PRECIO
                    );
                    $_SESSION['CARRITO'][0]=$producto;
                    $mensaje="Producto agregado al carrito";
                }else{
                    $NumeroProductos=count($_SESSION['CARRITO']);
                    $producto=array(
                        'ID'=>$ID,
                        'NOMBRE'=>$NOMBRE,
                        'CANTIDAD'=>$CANTIDAD,
                        'PRECIO'=>$PRECIO
                    );
                        $_SESSION['CARRITO'][$NumeroProductos]=$producto;
                        $mensaje="¡Producto añadido al carrito de compras!";
                }
                //$mensaje= print_r($_SESSION,true);
                

            break;
            case "Eliminar":
                if(is_numeric( openssl_decrypt($_POST['id'],COD,KEY ))){
                    $ID=openssl_decrypt($_POST['id'],COD,KEY );

                    foreach($_SESSION['CARRITO'] as $indice=>$producto ){
                        if($producto['ID']==$ID){
                            unset($_SESSION['CARRITO'][$indice]);
                            echo "<script> alert(Elemento eliminado....) </script>";
                        }
                    }
                }else{
                    $mensaje.="ID Incorrecto".$ID."</br>";
                }
            break;
        }
    }
?>