<?php

namespace Model;

/**
 * Búsqueda, orden y paginación hechos en el servidor para las tablas del admin.
 *
 * Lee de la URL: ?q=texto&orden=columna&dir=asc|desc&pagina=N
 * Solo acepta en "orden" las claves de $ordenes, así ningún texto de la URL llega al SQL.
 *
 * Uso en un modelo:
 *   $listado = new Listado(['id' => 'p.id', 'nombre' => 'p.nombre_producto'], 'id');
 *   $listado->cargar(self::$db, 'SELECT p.*', 'FROM productos AS p', ['p.nombre_producto'], 'p.id');
 *   $listado->filas  → registros de la página actual
 */
class Listado
{
    public const POR_PAGINA = 20;

    public $q;
    public $pagina;
    public $orden;
    public $dir;
    public $porPagina = self::POR_PAGINA;

    public $filas = [];
    public $total = 0;
    public $paginas = 1;

    private $ordenes;
    private $ordenDefecto;
    private $dirDefecto;
    // Filtros propios de cada pantalla que deben conservarse en los enlaces (ej. estado del pedido)
    private $extra;

    /**
     * @param array $ordenes clave de la URL => expresión SQL para ORDER BY
     * @param array $extra parámetros que se conservan en los enlaces, ej. ['estado' => 'listo']
     */
    public function __construct(array $ordenes, string $ordenDefecto, string $dirDefecto = 'desc', array $extra = [], ?array $get = null)
    {
        $get = $get ?? $_GET;

        $this->ordenes = $ordenes;
        $this->ordenDefecto = $ordenDefecto;
        $this->dirDefecto = $dirDefecto;
        $this->extra = array_filter($extra, function ($valor) {
            return $valor !== null && $valor !== '';
        });

        $this->q = mb_substr(trim((string) ($get['q'] ?? '')), 0, 100, 'UTF-8');

        $pagina = filter_var($get['pagina'] ?? 1, FILTER_VALIDATE_INT);
        $this->pagina = $pagina && $pagina > 0 ? $pagina : 1;

        $orden = (string) ($get['orden'] ?? '');
        $this->orden = isset($ordenes[$orden]) ? $orden : $ordenDefecto;

        $dir = strtolower((string) ($get['dir'] ?? ''));
        $this->dir = in_array($dir, ['asc', 'desc'], true) ? $dir : $dirDefecto;
    }

    /**
     * Cuenta los resultados y carga solo la página actual.
     *
     * @param string   $select      "SELECT ..." sin FROM
     * @param string   $desde       "FROM ... JOIN ..." sin WHERE
     * @param array    $buscarEn    columnas donde se busca el texto con LIKE
     * @param string   $columnaId   si el texto es un número (o "#12"), también busca ese id exacto
     * @param array    $condiciones filtros extra con "?" (ej. "p.estado = ?")
     * @param string   $tipos       tipos de bind_param para $valores
     * @param string   $agrupar     "GROUP BY ..." opcional
     * @param callable $crear       convierte cada fila (ej. en un objeto del modelo)
     */
    public function cargar(
        \mysqli $db,
        string $select,
        string $desde,
        array $buscarEn,
        string $columnaId = '',
        array $condiciones = [],
        string $tipos = '',
        array $valores = [],
        string $agrupar = '',
        ?callable $crear = null
    ): self {
        if ($this->q !== '' && $buscarEn) {
            // % y _ del texto se buscan tal cual, no como comodines
            $like = '%' . addcslashes($this->q, '%_\\') . '%';
            $partes = [];
            foreach ($buscarEn as $columna) {
                $partes[] = "{$columna} LIKE ?";
                $tipos .= 's';
                $valores[] = $like;
            }

            $numero = ltrim($this->q, '#');
            if ($columnaId !== '' && ctype_digit($numero)) {
                $partes[] = "{$columnaId} = ?";
                $tipos .= 'i';
                $valores[] = (int) $numero;
            }

            $condiciones[] = '(' . implode(' OR ', $partes) . ')';
        }

        $where = $condiciones ? ' WHERE ' . implode(' AND ', $condiciones) : '';

        // Total de resultados (la subconsulta también sirve cuando hay GROUP BY)
        $conteo = $db->prepare("SELECT COUNT(*) AS total FROM (SELECT 1 {$desde}{$where} {$agrupar}) AS resultados");
        if ($valores) {
            $conteo->bind_param($tipos, ...$valores);
        }
        $conteo->execute();
        $this->total = (int) $conteo->get_result()->fetch_assoc()['total'];

        $this->paginas = max(1, (int) ceil($this->total / $this->porPagina));
        $this->pagina = min($this->pagina, $this->paginas);

        $orden = $this->ordenes[$this->orden] . ($this->dir === 'asc' ? ' ASC' : ' DESC');
        $limite = (int) $this->porPagina;
        $desplazamiento = ($this->pagina - 1) * $limite;

        $consulta = $db->prepare("{$select} {$desde}{$where} {$agrupar} ORDER BY {$orden} LIMIT {$limite} OFFSET {$desplazamiento}");
        if ($valores) {
            $consulta->bind_param($tipos, ...$valores);
        }
        $consulta->execute();
        $resultado = $consulta->get_result();

        $this->filas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $this->filas[] = $crear ? $crear($fila) : $fila;
        }

        return $this;
    }

    /** URL con los parámetros actuales y los cambios indicados; omite los valores por defecto */
    public function url(array $cambios = []): string
    {
        $parametros = array_merge($this->extra, [
            'q' => $this->q,
            'orden' => $this->orden,
            'dir' => $this->dir,
            'pagina' => $this->pagina,
        ], $cambios);

        if ($parametros['orden'] === $this->ordenDefecto && $parametros['dir'] === $this->dirDefecto) {
            unset($parametros['orden'], $parametros['dir']);
        }
        if ((int) ($parametros['pagina'] ?? 1) <= 1) {
            unset($parametros['pagina']);
        }
        $parametros = array_filter($parametros, function ($valor) {
            return $valor !== null && $valor !== '';
        });

        return $parametros ? '?' . http_build_query($parametros) : '?';
    }

    /** URL para ordenar por $columna: la primera vez ascendente, si ya está así, descendente */
    public function urlOrden(string $columna): string
    {
        $dir = ($this->orden === $columna && $this->dir === 'asc') ? 'desc' : 'asc';
        return $this->url(['orden' => $columna, 'dir' => $dir, 'pagina' => 1]);
    }

    /** Parámetros que el buscador debe reenviar como campos ocultos */
    public function camposOcultos(): array
    {
        $campos = $this->extra;
        if ($this->orden !== $this->ordenDefecto || $this->dir !== $this->dirDefecto) {
            $campos['orden'] = $this->orden;
            $campos['dir'] = $this->dir;
        }
        return $campos;
    }

    /** Número del primer y último registro mostrados, ej. [21, 40] */
    public function rango(): array
    {
        if ($this->total === 0) {
            return [0, 0];
        }
        $inicio = ($this->pagina - 1) * $this->porPagina + 1;
        return [$inicio, min($this->total, $inicio + count($this->filas) - 1)];
    }

    /** Páginas a mostrar en el paginador: 1 … 4 5 [6] 7 8 … 20 (null = puntos suspensivos) */
    public function paginasVisibles(int $alrededor = 2): array
    {
        $visibles = [];
        $anterior = 0;
        for ($n = 1; $n <= $this->paginas; $n++) {
            $cerca = abs($n - $this->pagina) <= $alrededor;
            if ($n === 1 || $n === $this->paginas || $cerca) {
                if ($anterior && $n - $anterior > 1) {
                    $visibles[] = null;
                }
                $visibles[] = $n;
                $anterior = $n;
            }
        }
        return $visibles;
    }
}
