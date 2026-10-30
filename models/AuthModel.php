<?php

namespace Model;

class AuthModel extends ActiveRecord
{
    // Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'username_usuario', 'email_usuario', 'password_usuario', 'imagen_usuario', 'token', 'autenticado'];

    public $id;
    public $username_usuario;
    public $email_usuario;
    public $password_usuario;
    public $imagen_usuario;
    public $token;
    public $autenticado;
    public $rol = 'cliente';

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->username_usuario = $args['username_usuario'] ?? '';
        $this->email_usuario = $args['email_usuario'] ?? '';
        $this->password_usuario = $args['password_usuario'] ?? '';
        $this->imagen_usuario = $args['imagen_usuario'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->autenticado = $args['autenticado'] ?? '';
    }

    public function validarLogin()
    {
        if (!$this->email_usuario) {
            self::$errores[] = 'El email es obligatorio';
        } else if (!filter_var($this->email_usuario, FILTER_VALIDATE_EMAIL)) {
            self::$errores[] = 'Formato de Email no valido';
        }
        if (!$this->password_usuario) {
            self::$errores[] = 'El password es obligatorio';
        }

        return self::$errores;
    }

    public function validarRegistro()
    {

        if (!$this->username_usuario) {
            self::$errores[] = 'El nombre de usuario es obligatorio';
        }
        if (!$this->email_usuario) {
            self::$errores[] = 'El email es obligatorio';
        } else if (!filter_var($this->email_usuario, FILTER_VALIDATE_EMAIL)) {
            self::$errores[] = 'Formato de Email no valido';
        }
        if (!$this->password_usuario) {
            self::$errores[] = 'El password es obligatorio';
        }

        return self::$errores;
    }

    public function existeUsuario()
    {
        // Revisar si un usuario existe o no
        $query = "SELECT * FROM " . self::$tabla . " WHERE email_usuario = '" . self::$db->escape_string($this->email_usuario) . "' LIMIT 1";

        $resultado = self::$db->query($query);

        if (!$resultado->num_rows) {
            self::$errores[] = "El usuario no existe";
            return;
        }
        
        return $resultado;
    }


    public function comprobarPassword($resultado)
    {
        $usuario = $resultado->fetch_object();
        $autenticado = password_verify($this->password_usuario, $usuario->password_usuario);

        if (!$autenticado) {
            self::$errores[] = "La contraseña es incorrecta";
        }

        $this->username_usuario = $usuario->username_usuario;
        $this->imagen_usuario = $usuario->imagen_usuario;
        $this->id = $usuario->id;
        $this->rol = $usuario->rol ?? 'cliente';

        return $autenticado;
    }

    public function autenticar()
    {
        // Llenar el arreglo de sesion   
        $_SESSION['id'] = $this->id;   
        $_SESSION['username'] = $this->username_usuario;
        $_SESSION['email'] = $this->email_usuario;
        $_SESSION['imagen'] = $this->imagen_usuario;
        $_SESSION['rol'] = $this->rol;
        $_SESSION['login'] = true;

        // Nuevo ID de sesión al iniciar sesión (evita fijación de sesión)
        session_regenerate_id(true);

        header('Location: /');
        exit;
    }

    public function registro()
    {

        $password = $this->password_usuario;
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $this->password_usuario = $passwordHash;

        $resultado = $this->guardar();
        if ($resultado) {
            header('Location: /signup?resultado=1');
            exit;
        }

    }
}
