<?php

namespace Model;

class ActiveRecord
{

    // Base de datos
    protected static $db;
    protected static $columnasDB = [];
    protected static $tabla = '';

    // Errores - Validacion
    protected static $errores = [];

    //Definir la conexion a la base de datos
    public static function setDB($database)
    {
        self::$db = $database;
    }

    public function guardar()
    {
        if (!is_null($this->id)) {
            // Actualizar
            return $this->actualizar();
        } else {
            // Creando un nuevo registro
            return $this->crear();
            
        }
    }

    public function crear()
    {
        // Sanitizar los datos
        $atributos = $this->sanitizarDatos();

        // Join sirve para aplanar arreglos osea que los datos se vean en una misma linea, toma 2 parametros, el primero es el separador, es decir como es que se va a separar y el segundo parametro son los datos,
        // array_keys te muestra las llaves del arreglo, es decir el titulo que guarda la informacion

        //Insertar en la base de datos
        $query = "INSERT INTO " . static::$tabla . " ( ";
        $query .=  join(', ', array_keys($atributos));
        $query .= " ) VALUES (";
        $query .=  join(", ", array_map([self::class, 'valorSQL'], $atributos));
        $query .= ")";

        $resultado = self::$db->query($query);

        return $resultado;


        // // Mensaje de exito o error
        // if ($resultado) {
        //     //Redireccionar al usuario
        //     header('Location: /login?resultado=1');
        // }
    }

    public function actualizar()
    {
        // Sanitizar los datos
        $atributos = $this->sanitizarDatos();

        $valores = [];
        foreach ($atributos as $key => $value) {
            $valores[] = "{$key} = " . self::valorSQL($value);
        }


        $query = "UPDATE " . static::$tabla . " SET ";
        $query .= join(', ', $valores);
        $query .= "WHERE id = '" . self::$db->escape_string($this->id) . "'";
        $query .= "LIMIT 1  ";

        $resultado = self::$db->query($query);

        return $resultado;
    }

    // Eliminar un registro

    public function eliminar()
    {

        $query = "DELETE FROM " . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);

        if ($resultado) {
            if ($_POST['tipo'] === 'propiedad') {
                $this->eliminarImagen();
            }
            header('Location: /admin?resultado=3');
            exit;
        }
    }

    public function atributos()
    {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {

            if ($columna === 'id') continue; // Continue en un if: Si se cumple la condicion deja de ejecutar el if
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarDatos()
    {
        $atributos = $this->atributos();
        $sanitizando = [];

        foreach ($atributos as $key => $value) {
            // null se conserva para guardarlo como NULL en la base de datos
            $sanitizando[$key] = is_null($value) ? null : self::$db->escape_string((string) $value);
        }
        return $sanitizando;
    }

    // Valor ya escapado → literal SQL ('texto' o NULL)
    protected static function valorSQL($valor): string
    {
        return is_null($valor) ? 'NULL' : "'{$valor}'";
    }

    //Subida de archivos
    public function setImagen($imagen)
    {
        // Elimina la imagen previa
        if (!is_null($this->id)) {
            $this->eliminarImagen();
        }

        // Asignar al atributo de imagen el nombre de la imagen
        if ($imagen) {
            $this->imagen = $imagen;
        }
    }

    //Eliminar imagen
    public function eliminarImagen()
    {
        // Comprobar que existe el archivo
        $existeArchivo = file_exists(CARPETA_IMAGENES . $this->imagen);
        if ($existeArchivo) {
            unlink(CARPETA_IMAGENES . $this->imagen);
        }
    }

    // Validacion
    public static function getErrores()
    {
        return static::$errores;
    }

    public function validar()
    {
        static::$errores = [];
        return static::$errores;
    }

    // Lista todas los registros
    public static function all()
    {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);

        return $resultado;
    }

    // Busca los registros segun el id del registro
    public static function find($id)
    {
        $id = (int) $id;
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = {$id}";
        $resultado = self::consultarSQL($query);

        // Array_shift manda el primer elemento de un arreglo

        return array_shift($resultado);
    }

    public static function findAll($id)
    {
        $id = (int) $id;
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = {$id}";
        $resultado = self::consultarSQL($query);

        // Array_shift manda el primer elemento de un arreglo

        return $resultado;
    }

    // Obtiene determinado numero de registros
    public static function get($cantidad)
    {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT " . $cantidad;
        $resultado = self::consultarSQL($query);

        return $resultado;
    }

    public static function consultarSQL($query)
    {
        // Consultar en la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }
        // Liberar la memoria
        $resultado->free();

        // Retornar los resultados
        return $array;
    }

    protected static function crearObjeto($registro)
    {
        $objeto = new static;

        foreach ($registro as $key => $value) {
            if (property_exists($objeto, $key)) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Sincroniza el objeto en memoria con los cambios realizados por el usuario
    public function sincronizar($args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }
}
