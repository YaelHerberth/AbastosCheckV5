-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 18, 2022 at 07:38 PM
-- Server version: 8.0.27
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tienda`
--

-- --------------------------------------------------------

--
-- Table structure for table `bebesyninos`
--

DROP TABLE IF EXISTS `bebesyninos`;
CREATE TABLE IF NOT EXISTS `bebesyninos` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Precio` decimal(20,2) NOT NULL,
  `Descripcion` text NOT NULL,
  `Imagen` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bebesyninos`
--

INSERT INTO `bebesyninos` (`ID`, `Nombre`, `Precio`, `Descripcion`, `Imagen`) VALUES
(1, 'Andadera Tren Musical Para Niña', '1499.00', '', '../images/BebesyNiños/b1.webp'),
(2, 'Andadera 3 Niveles Altura Disney Mickey Mouse', '1799.00', '', '../images/BebesyNiños/b2.webp'),
(3, 'Pañal para Bebé Huggies All Around Unisex, Etapa 6 con 40 Piezas', '259.00', '', '../images/BebesyNiños/b3.webp'),
(4, 'Pañal para Bebé Huggies UltraConfort Niña, Etapa 6 con 60 Piezas', '449.00', '', '../images/BebesyNiños/b4.webp'),
(5, 'Pañal para Bebé Huggies UltraConfort Niño, Etapa 5 con 60 Piezas', '449.00', '', '../images/BebesyNiños/b5.webp'),
(6, 'Pañales Huggies Supreme Etapa 5 Niño con 36 piezas', '465.00', '', '../images/BebesyNiños/b6.webp'),
(7, 'Pañales Huggies Supreme Etapa 5 Niño con 36 piezas', '253.30', '', '../images/BebesyNiños/b7.webp'),
(8, 'Jugo Gerber Etapa 2 Manzana y Ciruela 175 ml', '16.60', '', '../images/BebesyNiños/b8.webp');

-- --------------------------------------------------------

--
-- Table structure for table `carnesypescados`
--

DROP TABLE IF EXISTS `carnesypescados`;
CREATE TABLE IF NOT EXISTS `carnesypescados` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Precio` decimal(20,2) NOT NULL,
  `Descripcion` text NOT NULL,
  `Imagen` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `carnesypescados`
--

INSERT INTO `carnesypescados` (`ID`, `Nombre`, `Precio`, `Descripcion`, `Imagen`) VALUES
(1, 'Milanesa de res Pulpa Negra Familiar Rancho Don Francisco Selecta Kg', '221.00', '', '../images/CarnesyPescados/c1.webp'),
(2, 'Pollo con viceras Bachoco por kg', '51.50', '', '../images/CarnesyPescados/c2.webp'),
(3, 'Pollo Entero C/V Pilgrims Kg', '54.90', '', '../images/CarnesyPescados/c3.webp'),
(4, 'Conejo Natural para Asar Kg', '155.00', '', '../images/CarnesyPescados/c4.webp'),
(5, 'Rib Steak de res Rancho Don Francisco Sonora Kg', '459.00', '', '../images/CarnesyPescados/c5.webp'),
(6, 'Salmón Entero Fresco Kg', '439.00', '', '../images/CarnesyPescados/c6.webp'),
(7, 'Huachinango del Golfo Fresco Kg', '319.00', '', '../images/CarnesyPescados/c7.webp'),
(8, 'Pargo Huachinango Kg', '489.00', '', '../images/CarnesyPescados/c8.webp');

-- --------------------------------------------------------

--
-- Table structure for table `despensa`
--

DROP TABLE IF EXISTS `despensa`;
CREATE TABLE IF NOT EXISTS `despensa` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Precio` decimal(20,2) NOT NULL,
  `Descripcion` text NOT NULL,
  `Imagen` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `despensa`
--

INSERT INTO `despensa` (`ID`, `Nombre`, `Precio`, `Descripcion`, `Imagen`) VALUES
(1, 'Frijol Pinto Precíssimo 908 gr', '24.90', '', '../images/Despensa/d1.webp'),
(2, 'Atún Herdez en agua 130 g', '21.40', '', '../images/Despensa/d2.webp'),
(3, 'Arroz Precíssimo Extra 900 gr', '17.50', '', '../images/Despensa/d3.webp'),
(4, 'Huevo Blanco 18 piezas', '41.90', '', '../images/Despensa/d4.webp'),
(5, 'Leche Lala Entera 6 piezas de 1 lt c/u', '125.00', '', '../images/Despensa/d5.webp'),
(6, 'Papel Higiénico Regio Luxury Almond Touch 4 rollos', '43.20', '', '../images/Despensa/d6.webp'),
(7, 'Aceite Comestible de Soya Valley Foods 946 ml', '52.50', '', '../images/Despensa/d7.webp'),
(8, 'Mayonesa Precíssimo 390 gr', '30.00', '', '../images/Despensa/d8.webp');

-- --------------------------------------------------------

--
-- Table structure for table `frutasyverduras`
--

DROP TABLE IF EXISTS `frutasyverduras`;
CREATE TABLE IF NOT EXISTS `frutasyverduras` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Precio` decimal(20,2) NOT NULL,
  `Descripcion` text NOT NULL,
  `Imagen` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `frutasyverduras`
--

INSERT INTO `frutasyverduras` (`ID`, `Nombre`, `Precio`, `Descripcion`, `Imagen`) VALUES
(1, 'Chile Pimiento Morrón Naranja Kg', '77.90', '', '../images/FrutasyVerduras/fv1.webp'),
(2, 'Romero Macho Manojo', '10.50', '', '../images/FrutasyVerduras/fv2.webp'),
(3, 'Sábila', '15.00', '', '../images/FrutasyVerduras/fv3.webp'),
(4, 'Cebolla Perlas Kg', '99.90', '', '../images/FrutasyVerduras/fv4.webp'),
(5, 'Manzana Royal Gala Kg', '71.00', '', '../images/FrutasyVerduras/fv5.webp'),
(6, 'Sandía Baby Kg', '23.50', '', '../images/FrutasyVerduras/fv6.webp'),
(7, 'Plátano Dominico Kg', '40.90', '', '../images/FrutasyVerduras/fv7.webp'),
(8, 'Pitahaya Kg', '177.90', '', '../images/FrutasyVerduras/fv8.webp');

-- --------------------------------------------------------

--
-- Table structure for table `jugosybebidas`
--

DROP TABLE IF EXISTS `jugosybebidas`;
CREATE TABLE IF NOT EXISTS `jugosybebidas` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Precio` decimal(20,2) NOT NULL,
  `Descripcion` text NOT NULL,
  `Imagen` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jugosybebidas`
--

INSERT INTO `jugosybebidas` (`ID`, `Nombre`, `Precio`, `Descripcion`, `Imagen`) VALUES
(1, 'Refresco Coca-Cola 2 Lt Pack Con 2 Piezas Pet', '62.50', '', '../images/JugosyBebidas/jb1.webp'),
(2, 'Refresco Pepsi 237 Ml Pack Con 6 Piezas Lata', '48.00', '', '../images/JugosyBebidas/jb2.webp'),
(3, 'Agua Natural Ciel 5 Lt', '27.60', '', '../images/JugosyBebidas/jb3.webp'),
(4, 'Agua Natural Bonafont 1 Paquete Con 8 Pzas De 330 Ml', '34.90', '', '../images/JugosyBebidas/jb4.webp'),
(5, 'Agua Mineral Peñafiel Sifón 1.75 Lt', '28.50', '', '../images/JugosyBebidas/jb5.webp'),
(6, 'Refresco Jarritos Mandarina 600 Ml Bot', '9.00', '', '../images/JugosyBebidas/jb6.webp'),
(7, 'Jugo Boing Manzana 125 ml', '3.80', '', '../images/JugosyBebidas/jb7.webp'),
(8, 'Jugo Boing 125 ml', '3.80', '', '../images/JugosyBebidas/jb8.webp');

-- --------------------------------------------------------

--
-- Table structure for table `limpieza`
--

DROP TABLE IF EXISTS `limpieza`;
CREATE TABLE IF NOT EXISTS `limpieza` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Precio` decimal(20,2) NOT NULL,
  `Descripcion` text NOT NULL,
  `Imagen` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `limpieza`
--

INSERT INTO `limpieza` (`ID`, `Nombre`, `Precio`, `Descripcion`, `Imagen`) VALUES
(1, 'Papel higiénico Petalo Rendimax 12 rollos de 320 hojas c/u', '91.70', '', '../images/Limpieza/l1.webp'),
(2, 'Papel Higiénico Pétalo Ultra Jumbo 16 Rollos, 247 Hojas Dobles', '60.90', '', '../images/Limpieza/l2.webp'),
(3, 'Limpiador Multiusos Pinol El Original 1 L', '21.50', '', '../images/Limpieza/l3.webp'),
(4, 'Limpiador Líquido Fabuloso Frescura Activa Antibacterial Lavanda 1 l', '33.50', '', '../images/Limpieza/l4.webp'),
(5, 'Blanqueador Cloralex El Rendidor 3750ml', '47.90', '', '../images/Limpieza/l5.webp'),
(6, 'Limpiador Cloralex Mascotas Exteriores 3750ml', '85.90', '', '../images/Limpieza/l6.webp'),
(7, 'Limpiador de Vidrios Windex Crystal Rain 500 ml', '25.50', '', '../images/Limpieza/l7.webp'),
(8, 'Limpiador 3 en 1 Aceite para Muebles 500 ml', '129.00', '', '../images/Limpieza/l8.webp');

-- --------------------------------------------------------

--
-- Table structure for table `mascotas`
--

DROP TABLE IF EXISTS `mascotas`;
CREATE TABLE IF NOT EXISTS `mascotas` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Precio` decimal(20,2) NOT NULL,
  `Descripcion` text NOT NULL,
  `Imagen` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mascotas`
--

INSERT INTO `mascotas` (`ID`, `Nombre`, `Precio`, `Descripcion`, `Imagen`) VALUES
(1, 'Alimento Seco para perro Nucan 25 Kg', '739.00', '', '../images/Mascotas/m1.webp'),
(2, 'Alimento para perro Dog Chow Adulto Minis y Pequeños 20 Kg', '959.00', '', '../images/Mascotas/m2.webp'),
(3, 'Cama Tochito para Mascota Trainer\'s Choice Multicolor', '425.00', '', '../images/Mascotas/m3.webp'),
(4, 'Cama Escocesa Chica Trainer\'s Choice', '335.00', '', '../images/Mascotas/m4.webp'),
(5, 'Collar y Correa Trainer\'s Choice Extra Chico 10mm paquete con 2 piezas', '85.00', '', '../images/Mascotas/m5.webp'),
(6, 'Comedero para Mascota Trainer\'s Choice 2 lt', '53.00', '', '../images/Mascotas/m6.webp'),
(7, 'Shampoo Sin Sulfatos Ph Blanceado Amarillo Respet Republic Of Pets 475 ml', '75.00', '', '../images/Mascotas/m7.webp'),
(8, 'Shampoo Thankful Dog con Avena 400 Ml', '114.00', '', '../images/Mascotas/m8.webp');

-- --------------------------------------------------------

--
-- Table structure for table `panaderiaytortilleria`
--

DROP TABLE IF EXISTS `panaderiaytortilleria`;
CREATE TABLE IF NOT EXISTS `panaderiaytortilleria` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Precio` decimal(20,2) NOT NULL,
  `Descripcion` text NOT NULL,
  `Imagen` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `panaderiaytortilleria`
--

INSERT INTO `panaderiaytortilleria` (`ID`, `Nombre`, `Precio`, `Descripcion`, `Imagen`) VALUES
(1, 'Tortilla Del Barrio 315 Gramo Pieza', '18.00', '', '../images/PanaderiayTortilleria/pt1.webp'),
(2, 'Tortillas de Harina Paquete con 10 Piezas', '18.90', '', '../images/PanaderiayTortilleria/pt2.webp'),
(3, 'Tortilla de Maíz Blanca Taquera 1 Kg', '20.00', '', '../images/PanaderiayTortilleria/pt3.webp'),
(4, 'Tortilla de Maíz Amarilla 1 Kg', '14.40', '', '../images/PanaderiayTortilleria/pt4.webp'),
(5, 'Pan de Barra Integral Valley Foods 680 gr', '41.90', '', '../images/PanaderiayTortilleria/pt5.webp'),
(6, 'Pan de Barra Blanco Valley Foods 680 gr', '39.50', '', '../images/PanaderiayTortilleria/pt6.webp'),
(7, 'Dona de Chocolate 1 Pieza', '8.90', '', '../images/PanaderiayTortilleria/pt7.webp'),
(8, 'Dona de Azúcar 1 Pieza', '8.90', '', '../images/PanaderiayTortilleria/pt8.webp');

-- --------------------------------------------------------

--
-- Table structure for table `salchichoneria`
--

DROP TABLE IF EXISTS `salchichoneria`;
CREATE TABLE IF NOT EXISTS `salchichoneria` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Precio` decimal(20,2) NOT NULL,
  `Descripcion` text NOT NULL,
  `Imagen` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `salchichoneria`
--

INSERT INTO `salchichoneria` (`ID`, `Nombre`, `Precio`, `Descripcion`, `Imagen`) VALUES
(1, 'Salchicha Viena de Pavo Alpino 830 Gr', '52.90', '', '../images/Salchichoneria/s1.webp'),
(2, 'Salchicha Tipo Frankfurt Peñaranda 400 Gr', '168.90', '', '../images/Salchichoneria/s2.webp'),
(3, 'Salchicha para Asar con Tocino Kir 800 Gr', '64.90', '', '../images/Salchichoneria/s3.webp'),
(4, 'Salchicha para Asar con Queso Bafar 800 Gr', '66.90', '', '../images/Salchichoneria/s4.webp'),
(5, 'Salchicha Viena de Pavo San Rafael 1 Kg', '83.90', '', '../images/Salchichoneria/s5.webp'),
(6, 'Salchicha de Pavo Bernina Ahumada 340 gr', '81.50', '', '../images/Salchichoneria/s6.webp'),
(7, 'Salchicha Wiener Peñaranda 200 gr', '132.90', '', '../images/Salchichoneria/s7.webp'),
(8, 'Salchicha Pechuga de Pavo San Rafael 500 Gr', '84.90', '', '../images/Salchichoneria/s8.webp');

-- --------------------------------------------------------

--
-- Table structure for table `tblproductos`
--

DROP TABLE IF EXISTS `tblproductos`;
CREATE TABLE IF NOT EXISTS `tblproductos` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Precio` decimal(20,2) NOT NULL,
  `Descripcion` text NOT NULL,
  `Imagen` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblproductos`
--

INSERT INTO `tblproductos` (`ID`, `Nombre`, `Precio`, `Descripcion`, `Imagen`) VALUES
(1, 'Jitomate', '13.46', 'Jitomates frecos', '../images/BebesyNiños/b1.webp'),
(2, 'Mango', '14.55', 'Mangos frescos', '../images/frutas2.jpg'),
(3, 'Zanahoria', '5.65', 'Zanahorias frescas', '../images/frutas3.jpg\r\n'),
(4, 'Naranja', '9.00', 'Naranjas frescas', '../images/frutas4.jpg'),
(5, 'Brocoli', '8.00', 'Brocoli fresco', '../images/frutas5.jpg'),
(6, 'Elote', '5.90', 'Elotes frescos', '../images/frutas6.jpg'),
(7, 'Manzana', '23.80', 'Manzanas Frescas', '../images/frutas7.jpg'),
(8, 'Cebolla', '5.25', 'Cebollas frescas', '../images/frutas8.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`) VALUES
(11, 'yael12@gmail.com', '$2y$10$F/C8e8GoHGDWrAGwOlJ8VOVHQoKy7tKioKbWo9qHQ71C5G9MQlvqy'),
(1, 'admin@admin.com', '$2y$10$zJ1BFBUzT9tSQ.DtfJMvLuwXm5uV5x1mzQXxiFcV6Bjm.nrlkSA6e'),
(12, 'yael@gmail.com', '$2y$10$EdT64oGJTypfMfhZJKoFMOhkUyp1hRD.IQ5ysKtYrLs.X2t6Uwnf6');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
