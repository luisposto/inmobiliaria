-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-11-2025 a las 04:10:51
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inmobiliaria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_propiedad`
--

CREATE TABLE `estados_propiedad` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estados_propiedad`
--

INSERT INTO `estados_propiedad` (`id`, `nombre`) VALUES
(1, 'Disponible'),
(4, 'En Revisión'),
(2, 'Reservada'),
(3, 'Vendida');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_propiedades`
--

CREATE TABLE `imagenes_propiedades` (
  `id` int(11) NOT NULL,
  `propiedad_id` int(11) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `orden` int(11) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `imagenes_propiedades`
--

INSERT INTO `imagenes_propiedades` (`id`, `propiedad_id`, `ruta`, `orden`, `creado_en`) VALUES
(1, 14, 'prop_691f5546d3a1a.jpg', 0, '2025-11-20 17:52:06'),
(2, 14, 'prop_691f554e27fe0.jpg', 0, '2025-11-20 17:52:14'),
(3, 14, 'prop_691f5555f150d.jpg', 0, '2025-11-20 17:52:21'),
(4, 1, 'prop_1.jpg', 1, '2025-11-20 18:29:56'),
(5, 2, 'prop_2.jpg', 1, '2025-11-20 18:29:56'),
(6, 3, 'prop_3.jpg', 1, '2025-11-20 18:29:56'),
(7, 4, 'prop_4.jpg', 1, '2025-11-20 18:29:56'),
(8, 5, 'prop_5.jpg', 1, '2025-11-20 18:29:56'),
(9, 6, 'prop_6.jpg', 1, '2025-11-20 18:29:56'),
(10, 7, 'prop_7.jpg', 1, '2025-11-20 18:29:56'),
(11, 8, 'prop_8.jpg', 1, '2025-11-20 18:29:56'),
(12, 9, 'prop_9.jpg', 1, '2025-11-20 18:29:56'),
(13, 11, 'prop_10.jpg', 1, '2025-11-20 18:29:56'),
(14, 12, 'prop_11.jpg', 1, '2025-11-20 18:29:56'),
(15, 13, 'prop_12.jpg', 1, '2025-11-20 18:29:56'),
(16, 14, 'prop_13.jpg', 1, '2025-11-20 18:29:56'),
(17, 15, 'prop_14.jpg', 1, '2025-11-20 18:29:56'),
(18, 16, 'prop_15.jpg', 1, '2025-11-20 18:29:56'),
(19, 17, 'prop_16.jpg', 1, '2025-11-20 18:29:56'),
(20, 18, 'prop_17.jpg', 1, '2025-11-20 18:29:56'),
(21, 19, 'prop_18.jpg', 1, '2025-11-20 18:29:56'),
(22, 20, 'prop_19.jpg', 1, '2025-11-20 18:29:56'),
(23, 21, 'prop_20.jpg', 1, '2025-11-20 18:29:56'),
(24, 22, 'prop_21.jpg', 1, '2025-11-20 18:29:56'),
(25, 23, 'prop_22.jpg', 1, '2025-11-20 18:29:56'),
(26, 24, 'prop_23.jpg', 1, '2025-11-20 18:29:56'),
(27, 25, 'prop_24.jpg', 1, '2025-11-20 18:29:56'),
(28, 26, 'prop_25.jpg', 1, '2025-11-20 18:29:56'),
(29, 27, 'prop_26.jpg', 1, '2025-11-20 18:29:56'),
(30, 28, 'prop_27.jpg', 1, '2025-11-20 18:29:56'),
(31, 29, 'prop_28.jpg', 1, '2025-11-20 18:29:56'),
(32, 30, 'prop_29.jpg', 1, '2025-11-20 18:29:56'),
(33, 31, 'prop_30.jpg', 1, '2025-11-20 18:29:57'),
(34, 32, 'prop_31.jpg', 1, '2025-11-20 18:29:57'),
(35, 33, 'prop_32.jpg', 1, '2025-11-20 18:29:57'),
(36, 34, 'prop_33.jpg', 1, '2025-11-20 18:29:57'),
(37, 35, 'prop_34.jpg', 1, '2025-11-20 18:29:57'),
(38, 36, 'prop_35.jpg', 1, '2025-11-20 18:29:57'),
(39, 37, 'prop_36.jpg', 1, '2025-11-20 18:29:57'),
(40, 38, 'prop_37.jpg', 1, '2025-11-20 18:29:57'),
(41, 39, 'prop_38.jpg', 1, '2025-11-20 18:29:57'),
(42, 40, 'prop_39.jpg', 1, '2025-11-20 18:29:57'),
(43, 41, 'prop_40.jpg', 1, '2025-11-20 18:29:57'),
(44, 42, 'prop_41.jpg', 1, '2025-11-20 18:29:57'),
(45, 43, 'prop_42.jpg', 1, '2025-11-20 18:29:57'),
(46, 44, 'prop_43.jpg', 1, '2025-11-20 18:29:57'),
(48, 46, 'prop_45.jpg', 1, '2025-11-20 18:29:57'),
(49, 47, 'prop_46.jpg', 1, '2025-11-20 18:29:57'),
(50, 48, 'prop_47.jpg', 1, '2025-11-20 18:29:57'),
(51, 49, 'prop_48.jpg', 1, '2025-11-20 18:29:57'),
(52, 50, 'prop_49.jpg', 1, '2025-11-20 18:29:57'),
(53, 51, 'prop_50.jpg', 1, '2025-11-20 18:29:57'),
(54, 52, 'prop_51.jpg', 1, '2025-11-20 18:29:57'),
(55, 53, 'prop_52.jpg', 1, '2025-11-20 18:29:57'),
(56, 54, 'prop_53.jpg', 1, '2025-11-20 18:29:57'),
(57, 55, 'prop_54.jpg', 1, '2025-11-20 18:29:57'),
(58, 56, 'prop_55.jpg', 1, '2025-11-20 18:29:57'),
(59, 57, 'prop_56.jpg', 1, '2025-11-20 18:29:57'),
(60, 58, 'prop_57.jpg', 1, '2025-11-20 18:29:57'),
(61, 59, 'prop_58.jpg', 1, '2025-11-20 18:29:57'),
(62, 60, 'prop_59.jpg', 1, '2025-11-20 18:29:57'),
(63, 61, 'prop_60.jpg', 1, '2025-11-20 18:29:57'),
(64, 62, 'prop_61.jpg', 1, '2025-11-20 18:29:57'),
(65, 13, 'prop_691f68c801680.jpg', 0, '2025-11-20 19:15:20'),
(66, 13, 'prop_691f68df6fb75.jpg', 3, '2025-11-20 19:15:43'),
(67, 13, 'prop_691f68ffac587.jpg', 2, '2025-11-20 19:16:15'),
(68, 45, 'prop_691f6af719e66.jpg', 0, '2025-11-20 19:24:39'),
(69, 45, 'prop_691f6b027a8cb.jpg', 0, '2025-11-20 19:24:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operaciones`
--

CREATE TABLE `operaciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `operaciones`
--

INSERT INTO `operaciones` (`id`, `nombre`) VALUES
(2, 'Alquiler'),
(3, 'Alquiler Temporario'),
(11, 'Permuta'),
(9, 'Reserva'),
(10, 'Señada'),
(1, 'Venta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propiedades`
--

CREATE TABLE `propiedades` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(15,2) NOT NULL DEFAULT 0.00,
  `precio_usd` decimal(12,2) DEFAULT 0.00,
  `direccion` varchar(255) DEFAULT NULL,
  `ciudad` varchar(120) DEFAULT NULL,
  `provincia` varchar(120) DEFAULT NULL,
  `pais` varchar(120) DEFAULT 'Argentina',
  `ambientes` int(11) DEFAULT 0,
  `banios` int(11) DEFAULT 0,
  `cochera` tinyint(1) DEFAULT 0,
  `superficie` int(11) DEFAULT 0,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `destacado` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_id` int(11) DEFAULT NULL,
  `operacion_id` int(11) DEFAULT NULL,
  `estado_id` int(11) DEFAULT NULL,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `propiedades`
--

INSERT INTO `propiedades` (`id`, `titulo`, `descripcion`, `precio`, `precio_usd`, `direccion`, `ciudad`, `provincia`, `pais`, `ambientes`, `banios`, `cochera`, `superficie`, `lat`, `lng`, `imagen`, `destacado`, `created_at`, `tipo_id`, `operacion_id`, `estado_id`, `latitud`, `longitud`, `usuario_id`) VALUES
(1, 'Hermosa Casa con Jardín en Palermo', 'Casa familiar espaciosa en zona tranquila. Cuenta con amplio jardín, parrilla y quincho. Cerca de parques y avenidas principales.', 350000.00, 500500000.00, 'Av. Libertador 4500', 'Palermo', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 5, 3, 1, 280, -34.5694200, -58.4239800, 'prop_1.jpg', 0, '2025-11-19 23:30:42', 1, 1, NULL, -34.56942000, -58.42398000, NULL),
(2, 'Departamento Moderno con Balcón en Microcentro', 'Funcional monoambiente, ideal para estudiantes o inversión. Excelente ubicación con acceso a subtes y colectivos.', 85000.00, 121550000.00, 'Florida 200', 'Microcentro', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 1, 1, 0, 35, -34.6041000, -58.3734000, 'prop_2.jpg', 0, '2025-11-19 23:30:42', 2, 1, NULL, -34.60410000, -58.37340000, NULL),
(3, 'PH con Terraza y Parrilla en San Telmo', 'Encantador PH de estilo antiguo, muy luminoso. Sin expensas y con una gran terraza privada.', 120000.00, 171600000.00, 'Defensa 800', 'San Telmo', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 0, 1, 0, 95, -34.6148000, -58.3742000, 'prop_3.jpg', 0, '2025-11-19 23:30:42', 1, 3, 3, -34.61480000, -58.37420000, NULL),
(4, 'Alquiler: Departamento de 2 Ambientes en Belgrano', 'Departamento a estrenar con cocina integrada y balcón. Edificio con amenities (pileta y gimnasio).', 80000.00, 114400000.00, 'Zabala 1800', 'Belgrano', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 2, 1, 0, 55, -34.5629000, -58.4485000, 'prop_4.jpg', 0, '2025-11-19 23:30:42', 2, 2, NULL, -34.56290000, -58.44850000, NULL),
(5, 'Terreno en Venta en Luján', 'Lote listo para construir, con servicios de luz y agua en la zona. Ideal para casa de fin de semana.', 45000.00, 64350000.00, 'Calle 50 y Ruta 7', 'Luján', 'Buenos Aires', 'Argentina', 0, 0, 0, 500, -34.5956000, -59.0984000, 'prop_5.jpg', 0, '2025-11-19 23:30:42', 4, 1, NULL, -34.59560000, -59.09840000, NULL),
(6, 'Casa en Venta en Barrio Cerrado de Nordelta', 'Exclusiva casa moderna con detalles de diseño. Acceso a lago, club house y seguridad 24 hs.', 850000.00, 1215500000.00, 'Barrio Los Lagos, Nordelta', 'Tigre', 'Buenos Aires', 'Argentina', 6, 4, 1, 450, -34.4095000, -58.6475000, 'prop_6.jpg', 0, '2025-11-19 23:30:42', 1, 1, NULL, -34.40950000, -58.64750000, NULL),
(7, 'Departamento 3 Ambientes en La Plata', 'Amplio departamento con vista panorámica. A una cuadra de Plaza Moreno y cerca de facultades.', 150000.00, 214500000.00, 'Avenida 13 N° 900', 'La Plata', 'Buenos Aires', 'Argentina', 3, 2, 0, 110, -34.9205000, -57.9544000, 'prop_7.jpg', 0, '2025-11-19 23:30:42', 2, 1, NULL, -34.92050000, -57.95440000, NULL),
(8, 'Alquiler Temporario: Casa de Verano en Pinamar', 'Chalet cerca de la playa, totalmente equipado. Disponible para alquiler por quincena o mes.', 250000.00, 357500000.00, 'Intermédanos 400', 'Pinamar', 'Buenos Aires', 'Argentina', 4, 2, 1, 180, -37.1593000, -56.8624000, 'prop_8.jpg', 0, '2025-11-19 23:30:42', 1, 2, NULL, -37.15930000, -56.86240000, NULL),
(9, 'Oficina Céntrica en Córdoba Capital', 'Espacio ideal para consultorio o estudio. Recepción, dos privados y kitchenette. Edificio corporativo.', 95000.00, 135850000.00, 'Bv. San Juan 150', 'Córdoba', 'Córdoba', 'Argentina', 3, 1, 0, 70, -31.4239000, -64.1856000, 'prop_9.jpg', 0, '2025-11-19 23:30:42', NULL, 1, NULL, -31.42390000, -64.18560000, NULL),
(11, 'Local Comercial con Vidriera en Almagro', 'Local a la calle, amplio y muy visible. Ideal para rubro gastronómico o tienda.', 120000.00, 171600000.00, 'Av. Corrientes 4000', 'Almagro', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 0, 1, 0, 150, -34.6069000, -58.4208000, 'prop_10.jpg', 0, '2025-11-20 00:46:51', 5, 2, 1, NULL, NULL, NULL),
(12, 'PH Reciclado en Venta en Villa Crespo', 'PH con entrada independiente, dos plantas, patio y terraza. Excelentes terminaciones.', 195000.00, 278850000.00, 'Loyola 700', 'Villa Crespo', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 4, 2, 0, 140, -34.5997000, -58.4411000, 'prop_11.jpg', 0, '2025-11-20 00:46:51', 3, 1, 2, NULL, NULL, NULL),
(13, 'Departamento 2 Ambientes Luminoso', 'Recien pintado, cerca del subte B.', 95000.00, 135850000.00, 'Tucumán 1500', 'Balvanera', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 2, 1, 1, 45, 0.0000000, 0.0000000, 'prop_12.jpg', 1, '2025-11-20 03:06:42', 2, 2, 1, NULL, NULL, NULL),
(14, 'Chalet de Lujo con Pileta', 'Propiedad exclusiva en barrio residencial.', 750000.00, 3333333.00, 'Los Ceibos 123', 'San Isidro', 'Buenos Aires', 'Argentina', 7, 3, 1, 350, 0.0000000, 0.0000000, 'prop_13.jpg', 1, '2025-11-20 03:06:42', 1, 1, 1, NULL, NULL, NULL),
(15, 'Monoambiente para Inversión', 'Ideal renta temporaria. Bajas expensas.', 70000.00, 100100000.00, 'Córdoba 1900', 'Recoleta', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 1, 1, 1, 30, -34.5940000, -58.4010000, 'prop_691f711c489a3.jpg', 1, '2025-11-20 03:06:42', 2, 1, 2, NULL, NULL, NULL),
(16, 'Terreno con Servicios en Canning', 'Lote de 800m2 listo para construir.', 65000.00, 92950000.00, 'Ruta 52 km 5', 'Ezeiza', 'Buenos Aires', 'Argentina', 0, 0, 0, 800, -34.8870000, -58.5010000, 'prop_15.jpg', 1, '2025-11-20 03:06:42', 4, 1, 1, NULL, NULL, NULL),
(17, 'PH 3 Ambientes con Patio', 'Excelente estado, sin expensas.', 135000.00, 193050000.00, 'Donado 2500', 'Villa Urquiza', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 3, 1, 0, 85, -34.5770000, -58.4870000, 'prop_16.jpg', 1, '2025-11-20 03:06:42', 3, 1, 1, NULL, NULL, NULL),
(18, 'Oficina/Local en Esquina', 'Gran visibilidad. Apto múltiples usos.', 150000.00, 214500000.00, 'Av. Colón 500', 'Córdoba', 'Córdoba', 'Argentina', 0, 1, 0, 120, -31.4150000, -64.1950000, 'prop_17.jpg', 0, '2025-11-20 03:06:42', 5, 1, 1, NULL, NULL, NULL),
(19, 'Departamento de Alquiler en Barrio Norte', 'Dormitorio amplio, muy tranquilo.', 65000.00, 92950000.00, 'Arenales 3000', 'Barrio Norte', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 2, 1, 0, 55, -34.5870000, -58.4050000, 'prop_18.jpg', 0, '2025-11-20 03:06:42', 2, 2, 1, NULL, NULL, NULL),
(20, 'Casa de Fin de Semana en Tandil', 'Vistas panorámicas a las sierras.', 180000.00, 257400000.00, 'Camino a La Cascada', 'Tandil', 'Buenos Aires', 'Argentina', 4, 2, 0, 150, -37.3320000, -59.1330000, 'prop_19.jpg', 0, '2025-11-20 03:06:42', 1, 1, 1, NULL, NULL, NULL),
(21, 'Local Comercial en Centro', 'Cerca de bancos y oficinas.', 85000.00, 121550000.00, 'San Martín 400', 'Rosario', 'Santa Fe', 'Argentina', 0, 1, 0, 70, -32.9460000, -60.6380000, 'prop_20.jpg', 0, '2025-11-20 03:06:42', 5, 2, 1, NULL, NULL, NULL),
(22, 'Departamento 3 Ambientes con Cochera', 'Edificio moderno con seguridad 24hs.', 220000.00, 314600000.00, 'Cabildo 1000', 'Colegiales', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 3, 2, 0, 90, -34.5800000, -58.4500000, 'prop_21.jpg', 0, '2025-11-20 03:06:42', 2, 1, 3, NULL, NULL, NULL),
(23, 'Terreno en Mar del Plata', 'Ideal complejo de cabañas.', 55000.00, 78650000.00, 'Ruta 11 km 560', 'Mar del Plata', 'Buenos Aires', 'Argentina', 0, 0, 0, 1000, -38.0000000, -57.5500000, 'prop_22.jpg', 0, '2025-11-20 03:06:42', 4, 1, 1, NULL, NULL, NULL),
(24, 'Casa Antigua a Refaccionar', 'Gran potencial de inversión en zona céntrica.', 90000.00, 128700000.00, 'Calle 10 N° 550', 'La Plata', 'Buenos Aires', 'Argentina', 4, 1, 0, 120, -34.9200000, -57.9540000, 'prop_23.jpg', 0, '2025-11-20 03:06:42', 1, 1, 2, NULL, NULL, NULL),
(25, 'Departamento 1 Ambiente Temporario', 'Totalmente equipado, cerca de la playa.', 50000.00, 71500000.00, 'Av. Costanera 100', 'Villa Gesell', 'Buenos Aires', 'Argentina', 1, 1, 0, 32, -37.2600000, -56.9700000, 'prop_24.jpg', 0, '2025-11-20 03:06:42', 2, 3, 1, NULL, NULL, NULL),
(26, 'PH 4 Ambientes con Entrada Independiente', 'Terraza propia y parrilla.', 185000.00, 264550000.00, 'Acoyte 300', 'Caballito', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 4, 2, 0, 110, -34.6130000, -58.4350000, 'prop_25.jpg', 0, '2025-11-20 03:06:42', 3, 1, 1, NULL, NULL, NULL),
(27, 'Local en Galería Comercial', 'Alto tránsito peatonal.', 40000.00, 57200000.00, 'Florida 900', 'Microcentro', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 0, 0, 0, 20, -34.6040000, -58.3730000, 'prop_26.jpg', 0, '2025-11-20 03:06:42', 5, 2, 1, NULL, NULL, NULL),
(28, 'Casa Moderna en Barrio Cerrado', 'Amenities de primer nivel.', 450000.00, 643500000.00, 'Barrio San Sebastián', 'Pilar', 'Buenos Aires', 'Argentina', 5, 4, 0, 280, -34.3310000, -58.8980000, 'prop_27.jpg', 0, '2025-11-20 03:06:42', 1, 1, 1, NULL, NULL, NULL),
(29, 'Departamento 2 Ambientes en Alquiler', 'Balcón al frente, luminoso.', 70000.00, 100100000.00, 'Paraguay 4500', 'Palermo', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 2, 1, 0, 50, -34.5820000, -58.4210000, 'prop_28.jpg', 0, '2025-11-20 03:06:42', 2, 2, 1, NULL, NULL, NULL),
(30, 'Terreno 400m2 en Zona Industrial', 'Acceso rápido a autopista.', 40000.00, 57200000.00, 'Ruta 3 km 32', 'Cañuelas', 'Buenos Aires', 'Argentina', 0, 0, 0, 400, -35.0350000, -58.7490000, 'prop_29.jpg', 0, '2025-11-20 03:06:42', 4, 1, 3, NULL, NULL, NULL),
(31, 'PH Alquiler con Terraza', 'Ideal pareja joven. Muy bajas expensas.', 55000.00, 78650000.00, 'Billinghurst 1800', 'Palermo', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 2, 1, 0, 75, -34.5860000, -58.4110000, 'prop_30.jpg', 0, '2025-11-20 03:06:42', 3, 2, 1, NULL, NULL, NULL),
(32, 'Casa en Barrio Céntrico de Mendoza', 'Jardín con churrasquera.', 160000.00, 228800000.00, 'San Lorenzo 2000', 'Mendoza', 'Mendoza', 'Argentina', 4, 2, 0, 140, -32.8890000, -68.8450000, 'prop_31.jpg', 0, '2025-11-20 03:06:42', 1, 1, 1, NULL, NULL, NULL),
(33, 'Local apto Gastronomía', 'Con salida de humos y habilitación.', 110000.00, 0.00, 'José Cubas 3000', 'Villa Devoto', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 0, 2, 0, 90, NULL, NULL, 'prop_32.jpg', 0, '2025-11-20 03:06:42', 5, 1, 1, NULL, NULL, NULL),
(34, 'Departamento en Venta con Vista al Río', 'Piso alto, balcón corrido.', 250000.00, 357500000.00, 'Azcuénaga 10', 'Olivos', 'Buenos Aires', 'Argentina', 3, 2, 0, 105, -34.5090000, -58.4710000, 'prop_33.jpg', 0, '2025-11-20 03:06:42', 2, 1, 1, NULL, NULL, NULL),
(35, 'Terreno Frente al Golf', 'Ubicación privilegiada en Sierra de los Padres.', 75000.00, 107250000.00, 'Calle del Golf s/n', 'Sierra de los Padres', 'Buenos Aires', 'Argentina', 0, 0, 0, 600, -37.9550000, -57.8000000, 'prop_34.jpg', 0, '2025-11-20 03:06:42', 4, 1, 2, NULL, NULL, NULL),
(36, 'PH 2 Ambientes en Alquiler', 'Reciclado a nuevo, muy moderno.', 58000.00, 82940000.00, 'Sarmiento 400', 'San Telmo', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 2, 1, 0, 60, -34.6150000, -58.3750000, 'prop_35.jpg', 0, '2025-11-20 03:06:42', 3, 2, 1, NULL, NULL, NULL),
(37, 'Casa Céntrica en Salta', 'Estilo colonial, patio interior.', 145000.00, 207350000.00, 'Balcarce 800', 'Salta', 'Salta', 'Argentina', 5, 2, 0, 180, -24.7860000, -65.4110000, 'prop_36.jpg', 0, '2025-11-20 03:06:42', 1, 1, 1, NULL, NULL, NULL),
(38, 'Departamento Temporario en Puerto Madero', 'Amoblado de lujo, full amenities.', 150000.00, 214500000.00, 'Aimé Painé 1500', 'Puerto Madero', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 2, 1, 0, 70, -34.6080000, -58.3610000, 'prop_37.jpg', 0, '2025-11-20 03:06:42', 2, 3, 1, NULL, NULL, NULL),
(39, 'Local en Peatonal', 'Máximo flujo de gente.', 95000.00, 135850000.00, 'Peatonal 9 de Julio 120', 'Resistencia', 'Chaco', 'Argentina', 0, 1, 0, 55, NULL, NULL, 'prop_691f6e2ca8570.jpg', 0, '2025-11-20 03:06:42', 5, 1, 1, NULL, NULL, NULL),
(40, 'Terreno en Barrio Cerrado', 'Seguridad 24hs. Club house.', 90000.00, 128700000.00, 'Barrio La Lomada', 'Pilar', 'Buenos Aires', 'Argentina', 0, 0, 0, 700, -34.4250000, -58.8910000, 'prop_39.jpg', 0, '2025-11-20 03:06:42', 4, 1, 1, NULL, NULL, NULL),
(41, 'Casa con 3 Dormitorios y Garaje', 'Excelente para familia grande.', 210000.00, 300300000.00, 'Matienzo 450', 'Lomas de Zamora', 'Buenos Aires', 'Argentina', 4, 2, 0, 160, -34.7600000, -58.4060000, 'prop_40.jpg', 0, '2025-11-20 03:06:42', 1, 1, 2, NULL, NULL, NULL),
(42, 'Departamento 2 Ambientes Venta', 'Antigüedad 10 años. Bajas expensas.', 115000.00, 164450000.00, 'Av. Del Libertador 1200', 'Vicente López', 'Buenos Aires', 'Argentina', 2, 1, 0, 58, -34.5300000, -58.4600000, 'prop_41.jpg', 0, '2025-11-20 03:06:42', 2, 1, 1, NULL, NULL, NULL),
(43, 'Local en Recoleta', 'Ideal peluquería o boutique.', 70000.00, 100100000.00, 'Ayacucho 1500', 'Recoleta', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 0, 1, 0, 45, -34.5880000, -58.3970000, 'prop_42.jpg', 0, '2025-11-20 03:06:42', 5, 2, 1, NULL, NULL, NULL),
(44, 'PH 3 Ambientes con Cochera', 'Muy buen estado, cerca de estación.', 175000.00, 250250000.00, 'Rawson 1200', 'Quilmes', 'Buenos Aires', 'Argentina', 3, 1, 0, 95, -34.7210000, -58.2530000, 'prop_43.jpg', 0, '2025-11-20 03:06:42', 3, 1, 1, NULL, NULL, NULL),
(45, 'Casa en Barrio La Serena', 'A 5 cuadras del mar. Lista para habitar.', 220000.00, 314600000.00, 'Calle 40 N° 150', 'Miramar', 'Buenos Aires', 'Argentina', 4, 2, 1, 130, NULL, NULL, 'prop_691f6ad73540e.jpg', 1, '2025-11-20 03:06:42', 1, 1, 1, NULL, NULL, NULL),
(46, 'Departamento Alquiler Estudiantes', 'Zona universitaria, muy seguro.', 45000.00, 64350000.00, 'Rivadavia 1100', 'Tucumán', 'Tucumán', 'Argentina', 2, 1, 0, 40, -26.8330000, -65.2010000, 'prop_45.jpg', 0, '2025-11-20 03:06:42', 2, 2, 1, NULL, NULL, NULL),
(47, 'Terreno 300m2 en La Pampa', 'Acceso a agua y luz.', 25000.00, 35750000.00, 'Ruta 5 km 600', 'Santa Rosa', 'La Pampa', 'Argentina', 0, 0, 0, 300, -36.6170000, -64.2830000, 'prop_46.jpg', 0, '2025-11-20 03:06:42', 4, 1, 1, NULL, NULL, NULL),
(48, 'Local Comercial en Galpón', 'Amplias dimensiones, techo alto.', 190000.00, 271700000.00, 'Av. Crovara 500', 'La Matanza', 'Buenos Aires', 'Argentina', 0, 2, 0, 400, -34.6650000, -58.5550000, 'prop_47.jpg', 0, '2025-11-20 03:06:42', 5, 1, 3, NULL, NULL, NULL),
(49, 'PH con Terraza Temporario', 'Para turismo o trabajo por meses.', 60000.00, 85800000.00, 'Honduras 5000', 'Palermo', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 3, 1, 0, 80, -34.5880000, -58.4280000, 'prop_48.jpg', 0, '2025-11-20 03:06:42', 3, 3, 1, NULL, NULL, NULL),
(50, 'Casa de Campo con 2 Hectáreas', 'Ideal para emprendimiento rural.', 350000.00, 500500000.00, 'Camino Real s/n', 'San Antonio de Areco', 'Buenos Aires', 'Argentina', 5, 3, 0, 200, -34.2300000, -59.4500000, 'prop_49.jpg', 0, '2025-11-20 03:06:42', 1, 1, 1, NULL, NULL, NULL),
(51, 'Departamento Monoambiente en Venta', 'Balcón francés. Edificio con ascensor.', 88000.00, 125840000.00, 'Juncal 1200', 'Retiro', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 1, 1, 0, 38, -34.5910000, -58.3840000, 'prop_50.jpg', 0, '2025-11-20 03:06:42', 2, 1, 1, NULL, NULL, NULL),
(52, 'Local en Zona de Colegios', 'Perfecto para librería o kiosco.', 55000.00, 78650000.00, 'Belgrano 1800', 'Ramos Mejía', 'Buenos Aires', 'Argentina', 0, 1, 0, 35, -34.6530000, -58.5700000, 'prop_51.jpg', 0, '2025-11-20 03:06:42', 5, 2, 2, NULL, NULL, NULL),
(53, 'Terreno con Arroyo', 'Paisaje natural único en el Delta.', 35000.00, 50050000.00, 'Río Carapachay 100', 'Tigre', 'Buenos Aires', 'Argentina', 0, 0, 0, 1500, -34.3300000, -58.5900000, 'prop_52.jpg', 0, '2025-11-20 03:06:42', 4, 1, 1, NULL, NULL, NULL),
(54, 'Casa con 4 Dormitorios y Jardín', 'Muy espaciosa, ideal para familia grande.', 290000.00, 414700000.00, 'Av. Maipú 1500', 'Florida', 'Buenos Aires', 'Argentina', 5, 3, 0, 250, -34.5360000, -58.4870000, 'prop_53.jpg', 0, '2025-11-20 03:06:42', 1, 1, 1, NULL, NULL, NULL),
(55, 'Departamento 3 Ambientes en Alquiler', 'Cochera opcional. Cerca de comercios.', 85000.00, 121550000.00, 'Defensa 100', 'San Telmo', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 3, 1, 0, 75, -34.6150000, -58.3690000, 'prop_54.jpg', 0, '2025-11-20 03:06:42', 2, 2, 1, NULL, NULL, NULL),
(56, 'PH 2 Ambientes a Estrenar', 'Diseño moderno, mucha luz.', 110000.00, 157300000.00, 'Gascón 100', 'Almagro', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 2, 1, 0, 50, -34.6090000, -58.4180000, 'prop_55.jpg', 0, '2025-11-20 03:06:42', 3, 1, 3, NULL, NULL, NULL),
(57, 'Local en Zona Residencial', 'Bajos costos de mantenimiento.', 48000.00, 68640000.00, 'Viamonte 800', 'San Miguel de Tucumán', 'Tucumán', 'Argentina', 0, 1, 0, 30, -26.8170000, -65.2070000, 'prop_56.jpg', 0, '2025-11-20 03:06:42', 5, 2, 1, NULL, NULL, NULL),
(58, 'Casa Temporario en Bariloche', 'Cerca del centro de esquí Catedral.', 95000.00, 135850000.00, 'Ruta 82 km 20', 'Bariloche', 'Río Negro', 'Argentina', 3, 2, 0, 100, -41.1350000, -71.3190000, 'prop_57.jpg', 0, '2025-11-20 03:06:42', 1, 3, 1, NULL, NULL, NULL),
(59, 'Departamento 4 Ambientes en Venta', 'Balcón aterrazado. Edificio con sum.', 310000.00, 443300000.00, 'Av. Libertador 7000', 'Núñez', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 4, 2, 0, 120, -34.5390000, -58.4650000, 'prop_58.jpg', 0, '2025-11-20 03:06:42', 2, 1, 1, NULL, NULL, NULL),
(60, 'Terreno con Vista al Mar', 'Primer lote en acantilado.', 80000.00, 114400000.00, 'Camino a Chapadmalal', 'Mar del Plata', 'Buenos Aires', 'Argentina', 0, 0, 0, 550, -38.1000000, -57.6500000, 'prop_59.jpg', 0, '2025-11-20 03:06:42', 4, 1, 1, NULL, NULL, NULL),
(61, 'PH 3 Ambientes en Alquiler', 'Con lavadero independiente.', 72000.00, 102960000.00, 'Castro Barros 1000', 'Boedo', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 3, 1, 0, 65, -34.6200000, -58.4170000, 'prop_60.jpg', 0, '2025-11-20 03:06:42', 3, 2, 1, NULL, NULL, NULL),
(62, 'Local de Grandes Dimensiones', 'Con depósito y oficina.', 250000.00, 357500000.00, 'Av. Rivadavia 11000', 'Floresta', 'Ciudad Autónoma de Buenos Aires', 'Argentina', 0, 2, 0, 300, -34.6330000, -58.5020000, 'prop_60.jpg', 0, '2025-11-20 03:06:42', 5, 1, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_propiedad`
--

CREATE TABLE `tipos_propiedad` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_propiedad`
--

INSERT INTO `tipos_propiedad` (`id`, `nombre`) VALUES
(8, 'Bodega'),
(1, 'Casa'),
(2, 'Departamento'),
(6, 'Galpón'),
(5, 'Local'),
(3, 'PH'),
(7, 'Quinta'),
(4, 'Terreno');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Admin', 'admin@inmobiliaria.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2025-11-19 23:26:48'),
(2, 'Juan Pérez', 'juan.perez@ejemplo.com', '9e04f1ea70fbb5163b9f312026fd6df68bfa936a3d810076f969fc4eb85f4e93', '2025-11-19 23:30:14'),
(3, 'María Gómez', 'maria.gomez@ejemplo.com', 'a8e7a3f884d2239ec9bebc0b4e4f9c51be87fb9b060eddfaeeead94a76ae3f68', '2025-11-19 23:30:14'),
(4, 'Pedro Rodríguez', 'pedro.rodriguez@ejemplo.com', '2eefc197ea789640d5fc6763a88bbcdcd4762354fc7a2a3c4315eedc318b1e67', '2025-11-19 23:30:14'),
(5, 'Agente Torres', 'agente.torres@inmobiliaria.com', 'a3e8f7d9c6b5e4a3f2d1c0b9a8e7d6c5b4a3e2d1c0b9a8e7d6c5b4a3e2d1c0b9', '2025-11-20 00:46:51');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `estados_propiedad`
--
ALTER TABLE `estados_propiedad`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `imagenes_propiedades`
--
ALTER TABLE `imagenes_propiedades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propiedad_id` (`propiedad_id`);

--
-- Indices de la tabla `operaciones`
--
ALTER TABLE `operaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tipo_propiedad` (`tipo_id`),
  ADD KEY `fk_operacion_propiedad` (`operacion_id`),
  ADD KEY `fk_prop_usuario` (`usuario_id`),
  ADD KEY `fk_prop_estado` (`estado_id`);

--
-- Indices de la tabla `tipos_propiedad`
--
ALTER TABLE `tipos_propiedad`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `estados_propiedad`
--
ALTER TABLE `estados_propiedad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `imagenes_propiedades`
--
ALTER TABLE `imagenes_propiedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT de la tabla `operaciones`
--
ALTER TABLE `operaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `propiedades`
--
ALTER TABLE `propiedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT de la tabla `tipos_propiedad`
--
ALTER TABLE `tipos_propiedad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `imagenes_propiedades`
--
ALTER TABLE `imagenes_propiedades`
  ADD CONSTRAINT `imagenes_propiedades_ibfk_1` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD CONSTRAINT `fk_operacion_propiedad` FOREIGN KEY (`operacion_id`) REFERENCES `operaciones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prop_estado` FOREIGN KEY (`estado_id`) REFERENCES `estados_propiedad` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prop_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tipo_propiedad` FOREIGN KEY (`tipo_id`) REFERENCES `tipos_propiedad` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
