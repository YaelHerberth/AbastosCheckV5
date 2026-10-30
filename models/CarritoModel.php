<?php

namespace Model;

/**
 * Carrito guardado en la sesión como [id_producto => cantidad].
 * El nombre y el precio se leen siempre de la base de datos,
 * nunca de lo que envía el navegador.
 */
class CarritoModel
{
    public static function items(): array
    {
        $carrito = $_SESSION['CARRITO'] ?? [];

        if (!is_array($carrito)) {
            return [];
        }

        // Si quedó un carrito con el formato anterior en la sesión, se descarta
        foreach ($carrito as $id => $cantidad) {
            if (!is_int($id) || !is_int($cantidad) || $cantidad < 1) {
                $_SESSION['CARRITO'] = [];
                return [];
            }
        }

        return $carrito;
    }

    public static function totalUnidades(): int
    {
        return array_sum(self::items());
    }

    /**
     * Agrega unidades de un producto. Devuelve un mensaje de error o null si todo salió bien.
     */
    public static function agregar(int $id, int $cantidad = 1): ?string
    {
        $producto = ProductoModel::find($id);
        if (!$producto || $cantidad < 1) {
            return 'El producto no existe';
        }

        $items = self::items();
        $nuevaCantidad = ($items[$id] ?? 0) + $cantidad;

        if ($nuevaCantidad > (int) $producto->stock) {
            return 'No hay suficientes existencias de ' . $producto->nombre_producto;
        }

        $items[$id] = $nuevaCantidad;
        $_SESSION['CARRITO'] = $items;

        return null;
    }

    /**
     * Agrega hasta $cantidad piezas, limitado por las existencias.
     * Devuelve cuántas se agregaron (0 si el producto ya no existe o está agotado).
     */
    public static function agregarHasta(int $id, int $cantidad): int
    {
        $producto = ProductoModel::find($id);
        if (!$producto || $cantidad < 1) {
            return 0;
        }

        $items = self::items();
        $libres = (int) $producto->stock - ($items[$id] ?? 0);
        $agregar = min($cantidad, max(0, $libres));

        if ($agregar > 0) {
            $items[$id] = ($items[$id] ?? 0) + $agregar;
            $_SESSION['CARRITO'] = $items;
        }

        return $agregar;
    }

    /**
     * Suma o resta unidades (+1 / -1). Si llega a 0 se quita del carrito.
     * Devuelve un mensaje de error o null.
     */
    public static function cambiar(int $id, int $delta): ?string
    {
        $items = self::items();
        if (!isset($items[$id])) {
            return 'Ese producto no está en tu carrito';
        }

        if ($delta > 0) {
            return self::agregar($id, $delta);
        }

        $items[$id] += $delta;
        if ($items[$id] < 1) {
            unset($items[$id]);
        }
        $_SESSION['CARRITO'] = $items;

        return null;
    }

    public static function eliminar(int $id): void
    {
        $items = self::items();
        unset($items[$id]);
        $_SESSION['CARRITO'] = $items;
    }

    public static function vaciar(): void
    {
        $_SESSION['CARRITO'] = [];
    }

    /**
     * Líneas del carrito con los datos actuales de cada producto.
     */
    public static function detalle(): array
    {
        $lineas = [];

        foreach (self::items() as $id => $cantidad) {
            $producto = ProductoModel::find($id);
            if (!$producto) {
                continue;
            }

            $precio = Precios::linea($producto->precio_producto, $producto->precio_oferta, $producto->promo_tipo, $cantidad);

            $lineas[] = [
                'producto' => $producto,
                'cantidad' => $cantidad,
                'unitario' => $precio['unitario'],
                'descuento' => $precio['descuento'],
                'promo' => $precio['promo'],
                'subtotal' => $precio['subtotal'],
                // Las existencias pueden bajar mientras el producto está en el carrito
                'sinExistencias' => $cantidad > (int) $producto->stock,
            ];
        }

        return $lineas;
    }

    public static function total(array $lineas): float
    {
        return round(array_sum(array_column($lineas, 'subtotal')), 2);
    }
}
