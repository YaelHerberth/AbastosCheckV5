-- Roles de usuario y registro de pedidos.
-- Ejecutar una sola vez sobre la base de datos (abastoscheck_v5).

-- 1. Rol de usuario: solo los 'admin' pueden entrar al panel de administración.
ALTER TABLE `usuarios`
  ADD COLUMN `rol` ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente';

-- El usuario Administrador (id 0) es quien ya tenía el panel en la vista de perfil.
UPDATE `usuarios` SET `rol` = 'admin' WHERE `id` = 0;

-- 2. Pedidos
CREATE TABLE `pedidos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_usuario` INT NOT NULL,
  `total` DECIMAL(10,2) NOT NULL,
  `estado` ENUM('pendiente', 'preparando', 'listo', 'entregado', 'cancelado') NOT NULL DEFAULT 'pendiente',
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pedidos_usuarios_idx` (`id_usuario`),
  CONSTRAINT `fk_pedidos_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Se guarda nombre y precio al momento de la compra, para que el historial
-- no cambie si después se edita o elimina el producto.
CREATE TABLE `pedido_detalle` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_pedido` INT NOT NULL,
  `id_producto` INT NOT NULL,
  `nombre_producto` VARCHAR(80) NOT NULL,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `cantidad` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_detalle_pedidos_idx` (`id_pedido`),
  CONSTRAINT `fk_detalle_pedidos` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
