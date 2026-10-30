-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: sql301.infinityfree.com
-- Tiempo de generación: 07-08-2023 a las 20:27:08
-- Versión del servidor: 10.4.17-MariaDB
-- Versión de PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `if0_34747085_abastoscheck`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id` int(11) NOT NULL,
  `nombre_departamento` varchar(45) NOT NULL,
  `imagen_departamento` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id`, `nombre_departamento`, `imagen_departamento`) VALUES
(1, 'Despensa', 'despensa.jpg'),
(2, 'Electronica', ''),
(35, 'Mascotas', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre_producto` varchar(80) NOT NULL,
  `precio_producto` decimal(10,2) NOT NULL,
  `descripcion_producto` varchar(120) NOT NULL,
  `imagen_producto` varchar(200) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `id_departamento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre_producto`, `precio_producto`, `descripcion_producto`, `imagen_producto`, `stock`, `id_departamento`) VALUES
(1, 'Atun de 266gr', '35.50', 'Atun nutritivo listo para preparar', NULL, 5, 1),
(2, 'Arroz 900gr', '50.75', 'Atun nutritivo listo para preparar', 'ef0355733b1aaa2fdabe54f57e61e2f5.jpg', 10, 1),
(3, 'Televison Samsuung', '23444.00', 'Atun nutritivo listo para preparar', NULL, 5, 2),
(4, 'Laptop Asus', '56666.00', 'Atun nutritivo listo para preparar', NULL, 5, 2),
(11, 'Croquetas Chop Cahorro 20kg', '648.50', 'Croquetas Chop Cahorro 20kg', 'fe3f7e794629022ae06dd300ca4bcab1.jpg', 5, 35);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username_usuario` varchar(30) NOT NULL,
  `email_usuario` varchar(60) NOT NULL,
  `password_usuario` varchar(70) NOT NULL,
  `imagen_usuario` varchar(200) DEFAULT NULL,
  `token` varchar(45) DEFAULT NULL,
  `autenticado` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username_usuario`, `email_usuario`, `password_usuario`, `imagen_usuario`, `token`, `autenticado`) VALUES
(0, 'Administrador', 'admin@admin.com', '$2y$10$23/hiJ8/x2.KuIyNPAKoL.HqBMy.efBTuKlMOvtTaBy5vwL.nSLni', '', '', 0),
(8, ' Yael', 'yael@gmail.com', '$2y$10$cSDmv.hT2DQUZykkiXPN3OauUVQ3x6QeippHUfOzRn7hxfJwPBDZu', '27284e968a7039dfaece2442f338e801.jpg', '', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`,`id_departamento`),
  ADD KEY `fk_productos_departamentos_idx` (`id_departamento`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_departamentos` FOREIGN KEY (`id_departamento`) REFERENCES `departamentos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
