-- Migración 002: entrega, pago y promociones
-- Ejecutar una sola vez sobre abastoscheck_v5 (después de 001_roles_y_pedidos.sql)

-- Promociones por producto
--   precio_oferta: si tiene valor, se cobra ese precio y el normal se muestra tachado
--   promo_tipo:    2x1 o 3x2 (se aplica sobre el precio vigente)
ALTER TABLE productos
    ADD COLUMN precio_oferta DECIMAL(10,2) NULL DEFAULT NULL AFTER precio_producto,
    ADD COLUMN promo_tipo ENUM('2x1','3x2') NULL DEFAULT NULL AFTER precio_oferta;

-- Datos de entrega y pago de cada pedido
ALTER TABLE pedidos
    ADD COLUMN tipo_entrega ENUM('recoger','domicilio') NOT NULL DEFAULT 'recoger' AFTER total,
    ADD COLUMN direccion VARCHAR(255) NULL DEFAULT NULL AFTER tipo_entrega,
    ADD COLUMN telefono VARCHAR(20) NULL DEFAULT NULL AFTER direccion,
    ADD COLUMN notas VARCHAR(255) NULL DEFAULT NULL AFTER telefono,
    ADD COLUMN metodo_pago ENUM('efectivo','tarjeta') NOT NULL DEFAULT 'efectivo' AFTER notas,
    ADD COLUMN costo_envio DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER metodo_pago,
    ADD COLUMN listo_estimado DATETIME NULL DEFAULT NULL AFTER costo_envio;

-- Descuento por promoción en cada línea (subtotal = cantidad * precio_unitario - descuento)
ALTER TABLE pedido_detalle
    ADD COLUMN promo VARCHAR(10) NULL DEFAULT NULL AFTER cantidad,
    ADD COLUMN descuento DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER promo;

-- Acelera el reporte de ventas del día
ALTER TABLE pedidos ADD INDEX idx_pedidos_creado (creado_en);
