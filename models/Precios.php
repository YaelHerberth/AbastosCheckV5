<?php

namespace Model;

/**
 * Reglas de precio en un solo lugar. Las usan el carrito (para mostrar)
 * y PedidoModel (para cobrar), así nunca muestran cantidades distintas.
 */
class Precios
{
    // promo => [llevas, pagas]
    public const PROMOS = [
        '2x1' => [2, 1],
        '3x2' => [3, 2],
    ];

    /** Precio por pieza vigente: el de oferta si existe y es menor */
    public static function unitario($precio, $oferta): float
    {
        $precio = (float) $precio;
        if ($oferta !== null && $oferta !== '' && (float) $oferta > 0 && (float) $oferta < $precio) {
            return (float) $oferta;
        }
        return $precio;
    }

    /**
     * Calcula una línea de compra.
     * Ej. 2x1 con 5 piezas: 2 grupos completos → 2 piezas gratis, se pagan 3.
     *
     * @return array{unitario: float, descuento: float, subtotal: float, promo: ?string}
     */
    public static function linea($precio, $oferta, $promo, int $cantidad): array
    {
        $unitario = self::unitario($precio, $oferta);
        $descuento = 0.0;
        $promoAplicada = null;

        if (isset(self::PROMOS[$promo])) {
            [$llevas, $pagas] = self::PROMOS[$promo];
            $gratis = intdiv($cantidad, $llevas) * ($llevas - $pagas);
            $descuento = round($gratis * $unitario, 2);
            $promoAplicada = $promo;
        }

        return [
            'unitario' => $unitario,
            'descuento' => $descuento,
            'subtotal' => round($cantidad * $unitario - $descuento, 2),
            'promo' => $promoAplicada,
        ];
    }

    /** Costo de envío según el tipo de entrega y el subtotal de productos */
    public static function envio(string $tipoEntrega, float $subtotal): float
    {
        if ($tipoEntrega !== 'domicilio' || $subtotal >= ENVIO_GRATIS_DESDE) {
            return 0.0;
        }
        return COSTO_ENVIO;
    }
}
