<?php
// Reglas de negocio de la tienda. Cambia aquí los valores sin tocar el código.

// Entrega a domicilio
define('COSTO_ENVIO', 30.00);           // pesos
define('ENVIO_GRATIS_DESDE', 300.00);   // compras desde este monto no pagan envío

// Tiempo estimado para tener listo el pedido (minutos)
define('MINUTOS_RECOGER', 15);
define('MINUTOS_DOMICILIO', 45);

// Productos con estas existencias o menos se marcan como "inventario bajo"
define('STOCK_BAJO', 5);
