<?php
namespace Model;

class ProductoModel extends ActiveRecord
{
    protected static $tabla = 'productos';
    protected static $tabla2 = 'departamentos';
    protected static $columnasDB = ['id', 'nombre_producto', 'precio_producto', 'precio_oferta', 'promo_tipo', 'descripcion_producto', 'imagen_producto', 'stock', 'id_departamento'];

    public $id;
    public $nombre_producto;
    public $precio_producto;
    public $precio_oferta;
    public $promo_tipo;
    public $descripcion_producto;
    public $imagen_producto;
    public $stock;
    public $id_departamento;
    public $nombre_departamento;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre_producto = $args['nombre_producto'] ?? '';
        $this->precio_producto = $args['precio_producto'] ?? '';
        $this->precio_oferta = $args['precio_oferta'] ?? null;
        $this->promo_tipo = $args['promo_tipo'] ?? null;
        $this->descripcion_producto = $args['descripcion_producto'] ?? '';
        $this->imagen_producto = $args['imagen_producto'] ?? '';
        $this->stock = $args['stock'] ?? '';
        $this->id_departamento = $args['id_departamento'] ?? '';
        $this->nombre_departamento = $args['nombre_departamento'] ?? '';
    }

    /** Precio por pieza que se cobra hoy (el de oferta si existe) */
    public function precioVigente(): float
    {
        return Precios::unitario($this->precio_producto, $this->precio_oferta);
    }

    public function tieneOferta(): bool
    {
        return $this->precioVigente() < (float) $this->precio_producto;
    }

    /** Productos más recientes con existencias (para "Novedades") */
    public static function novedades(int $limite = 4): array
    {
        $limite = max(1, $limite);
        return self::consultarSQL(
            "SELECT p.*, d.nombre_departamento FROM productos AS p
             LEFT JOIN departamentos AS d ON p.id_departamento = d.id
             WHERE p.stock > 0
             ORDER BY p.id DESC LIMIT {$limite}"
        );
    }

    /** Productos por id, en el mismo orden de $ids (omite los que ya no existen) */
    public static function porIds(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) {
            return [];
        }

        $lista = implode(',', $ids); // ya son enteros
        $productos = self::consultarSQL(
            "SELECT p.*, d.nombre_departamento FROM productos AS p
             LEFT JOIN departamentos AS d ON p.id_departamento = d.id
             WHERE p.id IN ({$lista})"
        );

        $porId = [];
        foreach ($productos as $producto) {
            $porId[(int) $producto->id] = $producto;
        }

        $ordenados = [];
        foreach ($ids as $id) {
            if (isset($porId[$id])) {
                $ordenados[] = $porId[$id];
            }
        }
        return $ordenados;
    }

    /** Tabla del admin: búsqueda, orden y paginación en el servidor (ver Listado) */
    public static function listadoAdmin(): Listado
    {
        $listado = new Listado([
            'id' => 'p.id',
            'nombre' => 'p.nombre_producto',
            'precio' => self::PRECIO_VIGENTE,
            'stock' => 'p.stock',
            'departamento' => 'd.nombre_departamento',
        ], 'id');

        return $listado->cargar(
            self::$db,
            'SELECT p.*, d.nombre_departamento',
            'FROM productos AS p LEFT JOIN departamentos AS d ON p.id_departamento = d.id',
            ['p.nombre_producto', 'p.descripcion_producto', 'd.nombre_departamento'],
            'p.id',
            [], '', [], '',
            function ($fila) {
                return static::crearObjeto($fila);
            }
        );
    }

    function findProducto($id){
        $query = "SELECT * FROM " . static::$tabla . " WHERE id_departamento = {$id}";
        $resultado = self::consultarSQL($query);

        return $resultado;
    }

    // Precio que se cobra hoy, en SQL (misma regla que Precios::unitario)
    private const PRECIO_VIGENTE = '(CASE WHEN p.precio_oferta > 0 AND p.precio_oferta < p.precio_producto THEN p.precio_oferta ELSE p.precio_producto END)';

    /**
     * Búsqueda del catálogo público.
     * $filtros: q (texto), departamento (id), min, max (precio), orden.
     */
    public static function buscar(array $filtros): array
    {
        $condiciones = [];
        $tipos = '';
        $valores = [];

        if ($filtros['q'] !== '') {
            $condiciones[] = '(p.nombre_producto LIKE ? OR p.descripcion_producto LIKE ?)';
            $like = '%' . $filtros['q'] . '%';
            $tipos .= 'ss';
            array_push($valores, $like, $like);
        }
        if ($filtros['departamento']) {
            $condiciones[] = 'p.id_departamento = ?';
            $tipos .= 'i';
            $valores[] = $filtros['departamento'];
        }
        if ($filtros['min'] !== null) {
            $condiciones[] = self::PRECIO_VIGENTE . ' >= ?';
            $tipos .= 'd';
            $valores[] = $filtros['min'];
        }
        if ($filtros['max'] !== null) {
            $condiciones[] = self::PRECIO_VIGENTE . ' <= ?';
            $tipos .= 'd';
            $valores[] = $filtros['max'];
        }

        if (!empty($filtros['ofertas'])) {
            $condiciones[] = '(p.promo_tipo IS NOT NULL OR (p.precio_oferta > 0 AND p.precio_oferta < p.precio_producto))';
        }

        // Solo valores de esta lista llegan al ORDER BY
        $ordenes = [
            'nombre' => 'p.nombre_producto ASC',
            'precio_asc' => self::PRECIO_VIGENTE . ' ASC',
            'precio_desc' => self::PRECIO_VIGENTE . ' DESC',
        ];
        $orden = $ordenes[$filtros['orden']] ?? $ordenes['nombre'];

        $query = 'SELECT p.*, d.nombre_departamento FROM productos AS p
            LEFT JOIN departamentos AS d ON p.id_departamento = d.id';
        if ($condiciones) {
            $query .= ' WHERE ' . implode(' AND ', $condiciones);
        }
        $query .= " ORDER BY {$orden}";

        $consulta = self::$db->prepare($query);
        if ($valores) {
            $consulta->bind_param($tipos, ...$valores);
        }
        $consulta->execute();
        $resultado = $consulta->get_result();

        $productos = [];
        while ($registro = $resultado->fetch_assoc()) {
            $productos[] = static::crearObjeto($registro);
        }

        return $productos;
    }

    function findDepartamento($id){
        $query = "SELECT nombre_departamento FROM " . static::$tabla2 . " WHERE id = {$id}";
        $resultado = self::consultarSQL($query);

        // Array_shift manda el primer elemento de un arreglo
        return array_shift($resultado);
    }

    public function validar()
    {
        if (!$this->nombre_producto) {
            self::$errores[] = 'El Nombre es obligatorio';
        }
        if (!$this->precio_producto) {
            self::$errores[] = 'El Precio es obligatorio';
        } 
        if (!$this->stock) {
            self::$errores[] = 'El Numero de Stock es obligatorio';
        }
        if (!$this->id_departamento) {
            self::$errores[] = 'El Departamento es obligatorio';
        }
        if (!$this->descripcion_producto) {
            self::$errores[] = 'La Descripcion es obligatoria';
        }

        // Promoción: los campos vacíos se guardan como NULL (sin promoción)
        if ($this->precio_oferta === '' ) {
            $this->precio_oferta = null;
        }
        if ($this->promo_tipo === '' || $this->promo_tipo === false) {
            $this->promo_tipo = null;
        }
        if ($this->precio_oferta !== null) {
            if (!is_numeric($this->precio_oferta) || (float) $this->precio_oferta <= 0) {
                self::$errores[] = 'El Precio de oferta debe ser mayor a 0';
            } elseif ((float) $this->precio_oferta >= (float) $this->precio_producto) {
                self::$errores[] = 'El Precio de oferta debe ser menor al precio normal';
            }
        }
        if ($this->promo_tipo !== null && !isset(Precios::PROMOS[$this->promo_tipo])) {
            self::$errores[] = 'La Promoción no es válida';
        }

        return self::$errores;
    }

    public function setImagen($imagen)
    {
        // Elimina la imagen previa
        if (!is_null($this->id)) {
            $this->eliminarImagenProducto();
        }

        // Asignar al atributo de imagen el nombre de la imagen
        if ($imagen) {
            $this->imagen_producto = $imagen;
        }
    }

    //Eliminar imagen
    public function eliminarImagenProducto()
    {
        // Comprobar que $this->imagen_producto no este vacio
        if(!$this->imagen_producto == ''){

            // Comprobar que existe el archivo
            $existeArchivo = file_exists(CARPETA_IMAGENES_PRODUCTOS . $this->imagen_producto);

            if ($existeArchivo) {
                unlink(CARPETA_IMAGENES_PRODUCTOS . $this->imagen_producto);
            }
            
        }else{
            return;
        }
    }

    public function eliminarProducto()
    {

        $query = "DELETE FROM " . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        
        $resultado = self::$db->query($query);

        if ($resultado) {
            $this->eliminarImagenProducto();
            header('Location: /admin/productos?resultado=4');
            exit;
        }
    }
}