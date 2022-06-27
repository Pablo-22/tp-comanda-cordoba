-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-06-2022 a las 05:40:19
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de datos: `cordoba-tp`
--
CREATE DATABASE IF NOT EXISTS `cordoba-tp` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cordoba-tp`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id` int(8) NOT NULL,
  `idMesa` int(8) DEFAULT NULL,
  `idPedido` int(8) NOT NULL,
  `puntuacionMesa` int(2) DEFAULT NULL,
  `puntuacionMozo` int(2) DEFAULT NULL,
  `puntuacionCocinero` int(2) DEFAULT NULL,
  `puntuacionRestaurante` int(2) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fechaInsercion` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `encuestas`
--

INSERT INTO `encuestas` (`id`, `idMesa`, `idPedido`, `puntuacionMesa`, `puntuacionMozo`, `puntuacionCocinero`, `puntuacionRestaurante`, `descripcion`, `fechaInsercion`, `fechaBaja`) VALUES
(1, 25, 9, 8, 8, 9, 8, 'Muy bueno, la calidad de los productos los sabores la atención, la ambientación todo perfecto. Solo faltan más opciones vegetarianas', '2022-06-26 23:49:28', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_mesas`
--

CREATE TABLE `estados_mesas` (
  `id` int(8) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `idUsuarioCreador` int(8) DEFAULT NULL,
  `idEntidad` int(11) NOT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estados_mesas`
--

INSERT INTO `estados_mesas` (`id`, `descripcion`, `idUsuarioCreador`, `idEntidad`, `fechaInsercion`, `fechaBaja`) VALUES
(10, 'Libre', 11, 21, '2022-06-16 00:02:23', NULL),
(11, 'Libre', 11, 22, '2022-06-16 00:02:27', NULL),
(12, 'Libre', 11, 23, '2022-06-16 00:02:29', NULL),
(13, 'Libre', 11, 24, '2022-06-16 00:02:32', NULL),
(14, 'Libre', 11, 25, '2022-06-16 00:02:36', NULL),
(15, 'Libre', 11, 26, '2022-06-16 00:02:40', NULL),
(16, 'Libre', 11, 27, '2022-06-16 00:02:43', NULL),
(17, 'Libre', 11, 28, '2022-06-16 00:02:45', NULL),
(18, 'Con cliente esperando pedido', 11, 24, '2022-06-20 18:04:02', NULL),
(19, 'Con cliente comiendo pedido', 11, 24, '2022-06-21 19:40:36', NULL),
(22, 'Con cliente esperando pedido', 11, 23, '2022-06-21 19:56:50', NULL),
(23, 'Con cliente comiendo pedido', 11, 23, '2022-06-21 20:03:33', NULL),
(24, 'Con cliente pagando pedido', 11, 23, '2022-06-21 20:14:24', NULL),
(25, 'Con cliente pagando pedido', 11, 24, '2022-06-21 20:14:51', NULL),
(26, 'Con cliente esperando pedido', 11, 22, '2022-06-21 22:16:29', NULL),
(27, 'Con cliente comiendo pedido', 11, 22, '2022-06-21 22:21:21', NULL),
(28, 'Con cliente pagando pedido', 11, 22, '2022-06-21 22:21:40', NULL),
(29, 'Con cliente esperando pedido', NULL, 25, '2022-06-26 15:00:01', NULL),
(30, 'Con cliente esperando pedido', 11, 25, '2022-06-26 16:02:23', NULL),
(31, 'Con cliente esperando pedido', 11, 25, '2022-06-26 16:07:31', NULL),
(32, 'Con cliente esperando pedido', 11, 25, '2022-06-26 16:08:10', NULL),
(33, 'Con cliente comiendo pedido', 11, 25, '2022-06-26 22:51:32', NULL),
(34, 'Con cliente pagando pedido', 11, 25, '2022-06-26 22:53:45', NULL),
(35, 'Cerrada', 11, 25, '2022-06-26 22:58:34', NULL),
(36, 'Cerrada', 11, 24, '2022-06-26 22:58:49', NULL),
(37, 'Cerrada', 11, 23, '2022-06-26 22:58:52', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_pedidos`
--

CREATE TABLE `estados_pedidos` (
  `id` int(8) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `idUsuarioCreador` int(8) DEFAULT NULL,
  `idEntidad` int(11) NOT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estados_pedidos`
--

INSERT INTO `estados_pedidos` (`id`, `descripcion`, `idUsuarioCreador`, `idEntidad`, `fechaInsercion`, `fechaBaja`) VALUES
(1, 'Pendiente', 11, 1, '2022-06-20 18:04:02', NULL),
(2, 'En preparación', 11, 1, '2022-06-20 20:08:38', NULL),
(37, 'En preparación', 11, 1, '2022-06-20 23:49:02', NULL),
(38, 'Listo para servir', 11, 1, '2022-06-20 23:49:34', NULL),
(39, 'Pedido entregado', 11, 1, '2022-06-21 19:40:36', NULL),
(42, 'Pendiente', 11, 4, '2022-06-21 19:56:50', NULL),
(43, 'En preparación', 11, 4, '2022-06-21 19:58:18', NULL),
(44, 'En preparación', 11, 4, '2022-06-21 20:02:45', NULL),
(45, 'Listo para servir', 11, 4, '2022-06-21 20:03:08', NULL),
(46, 'Pedido entregado', 11, 4, '2022-06-21 20:03:33', NULL),
(47, 'Pedido pagado', 11, 4, '2022-06-21 20:14:24', NULL),
(48, 'Pedido pagado', 11, 1, '2022-06-21 20:14:51', NULL),
(49, 'Pendiente', 11, 5, '2022-06-21 22:16:29', NULL),
(50, 'En preparación', 11, 5, '2022-06-21 22:19:05', NULL),
(51, 'En preparación', 11, 5, '2022-06-21 22:20:45', NULL),
(52, 'Listo para servir', 11, 5, '2022-06-21 22:20:58', NULL),
(53, 'Pedido entregado', 11, 5, '2022-06-21 22:21:21', NULL),
(54, 'Pedido pagado', 11, 5, '2022-06-21 22:21:40', NULL),
(55, 'Pendiente', 11, 6, '2022-06-26 15:00:01', NULL),
(56, 'Pendiente', 11, 7, '2022-06-26 16:02:23', NULL),
(57, 'Pendiente', 11, 8, '2022-06-26 16:07:31', NULL),
(58, 'Pendiente', 11, 9, '2022-06-26 16:08:10', NULL),
(59, 'En preparación', 12, 9, '2022-06-26 19:43:50', NULL),
(60, 'En preparación', 11, 9, '2022-06-26 22:43:35', NULL),
(61, 'Listo para servir', 11, 9, '2022-06-26 22:50:27', NULL),
(62, 'Pedido entregado', 11, 9, '2022-06-26 22:51:32', NULL),
(63, 'Pedido pagado', 11, 9, '2022-06-26 22:53:45', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_productos_pedidos`
--

CREATE TABLE `estados_productos_pedidos` (
  `id` int(8) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `idUsuarioCreador` int(8) DEFAULT NULL,
  `idEntidad` int(11) NOT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estados_productos_pedidos`
--

INSERT INTO `estados_productos_pedidos` (`id`, `descripcion`, `idUsuarioCreador`, `idEntidad`, `fechaInsercion`, `fechaBaja`) VALUES
(1, 'Pendiente', 11, 1, '2022-06-20 18:04:02', NULL),
(2, 'Pendiente', 11, 2, '2022-06-20 18:04:02', NULL),
(3, 'En preparación', 11, 2, '2022-06-20 20:08:38', NULL),
(39, 'Listo', 11, 2, '2022-06-20 23:48:32', NULL),
(40, 'En preparación', 11, 1, '2022-06-20 23:49:02', NULL),
(41, 'Listo', 11, 1, '2022-06-20 23:49:33', NULL),
(46, 'Pendiente', 11, 7, '2022-06-21 19:56:50', NULL),
(47, 'Pendiente', 11, 8, '2022-06-21 19:56:50', NULL),
(48, 'En preparación', 11, 7, '2022-06-21 19:58:18', NULL),
(49, 'Listo', 11, 7, '2022-06-21 20:01:29', NULL),
(50, 'En preparación', 11, 8, '2022-06-21 20:02:45', NULL),
(51, 'Listo', 11, 8, '2022-06-21 20:03:08', NULL),
(52, 'Pendiente', 11, 9, '2022-06-21 22:16:29', NULL),
(53, 'Pendiente', 11, 10, '2022-06-21 22:16:29', NULL),
(54, 'En preparación', 11, 9, '2022-06-21 22:19:05', NULL),
(55, 'Listo', 11, 9, '2022-06-21 22:19:51', NULL),
(56, 'En preparación', 11, 10, '2022-06-21 22:20:45', NULL),
(57, 'Listo', 11, 10, '2022-06-21 22:20:58', NULL),
(58, 'Pendiente', NULL, 11, '2022-06-26 15:00:01', NULL),
(59, 'Pendiente', NULL, 12, '2022-06-26 15:00:01', NULL),
(60, 'Pendiente', 11, 13, '2022-06-26 16:02:23', NULL),
(61, 'Pendiente', 11, 14, '2022-06-26 16:02:23', NULL),
(62, 'Pendiente', 11, 15, '2022-06-26 16:07:31', NULL),
(63, 'Pendiente', 11, 16, '2022-06-26 16:07:31', NULL),
(64, 'Pendiente', 11, 17, '2022-06-26 16:08:10', NULL),
(65, 'Pendiente', 11, 18, '2022-06-26 16:08:10', NULL),
(66, 'En preparación', 12, 18, '2022-06-26 19:43:50', NULL),
(67, 'En preparación', 11, 17, '2022-06-26 22:43:35', NULL),
(68, 'Listo', 11, 17, '2022-06-26 22:50:06', NULL),
(69, 'Listo', 11, 18, '2022-06-26 22:50:27', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_usuarios`
--

CREATE TABLE `estados_usuarios` (
  `id` int(8) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `idUsuarioCreador` int(8) DEFAULT NULL,
  `idEntidad` int(11) NOT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estados_usuarios`
--

INSERT INTO `estados_usuarios` (`id`, `descripcion`, `idUsuarioCreador`, `idEntidad`, `fechaInsercion`, `fechaBaja`) VALUES
(1, 'Ocupado', 11, 11, '2022-06-20 20:54:41', NULL),
(2, 'Ocupado', 11, 11, '2022-06-20 20:55:49', NULL),
(3, 'Ocupado', 11, 11, '2022-06-20 20:56:51', NULL),
(4, 'Ocupado', 11, 11, '2022-06-20 20:58:07', NULL),
(5, 'Ocupado', 11, 11, '2022-06-20 20:58:47', NULL),
(6, 'Ocupado', 11, 11, '2022-06-20 20:59:31', NULL),
(7, 'Ocupado', 11, 11, '2022-06-20 21:01:55', NULL),
(8, 'Ocupado', 11, 11, '2022-06-20 21:03:55', NULL),
(9, 'Ocupado', 11, 11, '2022-06-20 21:06:57', NULL),
(10, 'Ocupado', 11, 11, '2022-06-20 21:07:55', NULL),
(11, 'Ocupado', 11, 11, '2022-06-20 21:08:05', NULL),
(12, 'Ocupado', 11, 11, '2022-06-20 21:08:38', NULL),
(13, 'Ocupado', 11, 11, '2022-06-20 21:09:11', NULL),
(14, 'Ocupado', 11, 11, '2022-06-20 21:09:59', NULL),
(15, 'Ocupado', 11, 11, '2022-06-20 21:11:15', NULL),
(16, 'Ocupado', 11, 11, '2022-06-20 21:12:06', NULL),
(17, 'Libre', 11, 11, '2022-06-20 23:31:04', NULL),
(18, 'Libre', 11, 11, '2022-06-20 23:32:38', NULL),
(19, 'Libre', 11, 11, '2022-06-20 23:36:48', NULL),
(20, 'Libre', 11, 11, '2022-06-20 23:39:03', NULL),
(21, 'Libre', 11, 11, '2022-06-20 23:47:50', NULL),
(22, 'Libre', 11, 11, '2022-06-20 23:48:32', NULL),
(23, 'Ocupado', 11, 11, '2022-06-20 23:49:02', NULL),
(24, 'Libre', 11, 11, '2022-06-20 23:49:34', NULL),
(25, 'Ocupado', 11, 11, '2022-06-21 19:58:18', NULL),
(26, 'Libre', 11, 11, '2022-06-21 20:01:29', NULL),
(27, 'Ocupado', 11, 11, '2022-06-21 20:02:45', NULL),
(28, 'Libre', 11, 11, '2022-06-21 20:03:08', NULL),
(29, 'Ocupado', 11, 11, '2022-06-21 22:19:05', NULL),
(30, 'Libre', 11, 11, '2022-06-21 22:19:51', NULL),
(31, 'Ocupado', 11, 11, '2022-06-21 22:20:45', NULL),
(32, 'Libre', 11, 11, '2022-06-21 22:20:58', NULL),
(33, 'Ocupado', 12, 12, '2022-06-26 19:43:50', NULL),
(34, 'Ocupado', 11, 11, '2022-06-26 22:43:36', NULL),
(35, 'Libre', 11, 11, '2022-06-26 22:50:06', NULL),
(36, 'Libre', 11, 11, '2022-06-26 22:50:27', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `id` int(8) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `idUsuarioCreador` int(8) DEFAULT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `logs`
--

INSERT INTO `logs` (`id`, `descripcion`, `idUsuarioCreador`, `fechaInsercion`, `fechaBaja`) VALUES
(9, 'Ha iniciado sesión', 11, '2022-06-20 18:16:35', NULL),
(10, 'Ha iniciado sesión', 11, '2022-06-20 18:17:32', NULL),
(11, 'Ha iniciado sesión', 11, '2022-06-21 19:04:18', NULL),
(12, 'Ha iniciado sesión', 11, '2022-06-21 19:39:44', NULL),
(13, 'Ha iniciado sesión', 11, '2022-06-22 20:14:31', NULL),
(14, 'Ha iniciado sesión', 11, '2022-06-26 14:59:13', NULL),
(15, 'Ha creado un pedido', 11, '2022-06-26 15:00:01', NULL),
(16, 'Ha creado un pedido', 11, '2022-06-26 16:02:23', NULL),
(17, 'Ha creado un pedido', 11, '2022-06-26 16:07:31', NULL),
(18, 'Ha creado un pedido', 11, '2022-06-26 16:08:10', NULL),
(19, 'Ha iniciado sesión', 11, '2022-06-26 17:11:47', NULL),
(20, 'Ha iniciado sesión', 11, '2022-06-26 18:25:40', NULL),
(21, 'Ha creado un pedido', 11, '2022-06-26 18:29:32', NULL),
(22, 'Ha creado un pedido', 11, '2022-06-26 18:30:08', NULL),
(23, 'Ha iniciado sesión', 12, '2022-06-26 18:37:41', NULL),
(24, 'Ha iniciado sesión', 12, '2022-06-26 19:26:06', NULL),
(25, 'Ha tomado el pedido A0007', 12, '2022-06-26 19:43:50', NULL),
(26, 'Ha iniciado sesión', 11, '2022-06-26 22:22:17', NULL),
(27, 'Ha tomado el pedido A0007', 11, '2022-06-26 22:43:36', NULL),
(28, 'Ha iniciado sesión', 11, '2022-06-26 22:49:57', NULL),
(29, 'Se ha cargado la encuesta relacionada al pedido ', 11, '2022-06-26 23:49:28', NULL),
(30, 'Se ha cargado la encuesta relacionada al pedido A0', 11, '2022-06-26 23:49:50', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(8) NOT NULL,
  `codigo` varchar(5) NOT NULL,
  `capacidad` int(2) DEFAULT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `codigo`, `capacidad`, `fechaInsercion`, `fechaBaja`) VALUES
(21, 'A0001', 4, '2022-06-16 00:02:23', NULL),
(22, 'A0002', 4, '2022-06-16 00:02:27', NULL),
(23, 'A0003', 4, '2022-06-16 00:02:29', NULL),
(24, 'A0004', 4, '2022-06-16 00:02:32', NULL),
(25, 'A0005', 4, '2022-06-16 00:02:36', NULL),
(26, 'A0006', 2, '2022-06-16 00:02:40', NULL),
(27, 'A0007', 2, '2022-06-16 00:02:43', NULL),
(28, 'A0008', 2, '2022-06-16 00:02:45', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(8) NOT NULL,
  `codigo` varchar(5) NOT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `idMesa` int(8) DEFAULT NULL,
  `nombreCliente` varchar(50) DEFAULT NULL,
  `rutaImagen` varchar(300) DEFAULT NULL,
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `codigo`, `fechaInsercion`, `idMesa`, `nombreCliente`, `rutaImagen`, `fechaBaja`) VALUES
(1, 'A0003', '2022-06-20 18:04:02', 24, NULL, '..\\ImagenesPedidos\\A0003.png', NULL),
(4, 'A0004', '2022-06-21 19:56:50', 23, 'Juan', '..\\ImagenesPedidos\\A0004.png', NULL),
(5, 'A0005', '2022-06-21 22:16:29', 22, 'Juan', '..\\ImagenesPedidos\\A0005.png', NULL),
(6, 'A0006', '2022-06-26 15:00:01', 25, 'Juan', '..\\ImagenesPedidos\\A0006.png', '2022-06-27 00:00:00'),
(7, 'A0006', '2022-06-26 16:02:23', 25, 'Pepe', '..\\ImagenesPedidos\\A0006.png', '2022-06-27 00:00:00'),
(8, 'A0006', '2022-06-26 16:07:31', 25, 'Pepe', '..\\ImagenesPedidos\\A0006.png', '2022-06-27 00:00:00'),
(9, 'A0007', '2022-06-26 16:08:10', 25, 'Pepe', '..\\ImagenesPedidos\\A0007.png', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(8) NOT NULL,
  `nombre` varchar(35) DEFAULT NULL,
  `tiempoEstimado` decimal(10,0) DEFAULT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `precio` float NOT NULL,
  `idRolEncargado` int(8) NOT NULL,
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `tiempoEstimado`, `fechaInsercion`, `precio`, `idRolEncargado`, `fechaBaja`) VALUES
(3, 'Doble CheeseBurguer', '15', '2022-06-18 17:42:08', 1100, 6, NULL),
(4, 'Nuggets x5', '10', '2022-06-18 17:44:05', 600, 6, NULL),
(5, 'Cerveza Corona', '1', '2022-06-18 17:44:42', 230, 3, NULL),
(6, 'Agua mineral sin gas', '1', '2022-06-18 17:44:56', 150, 3, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_pedidos`
--

CREATE TABLE `productos_pedidos` (
  `id` int(8) NOT NULL,
  `idPedido` int(8) DEFAULT NULL,
  `idProducto` int(8) DEFAULT NULL,
  `cantidad` int(8) DEFAULT NULL,
  `tiempoEstimado` int(11) DEFAULT NULL,
  `fechaInsercion` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `productos_pedidos`
--

INSERT INTO `productos_pedidos` (`id`, `idPedido`, `idProducto`, `cantidad`, `tiempoEstimado`, `fechaInsercion`, `fechaBaja`) VALUES
(1, 1, 3, 2, 20, '2022-06-20 18:04:02', NULL),
(2, 1, 5, 2, 20, '2022-06-20 18:04:02', NULL),
(7, 4, 3, 2, 25, '2022-06-21 19:56:50', NULL),
(8, 4, 6, 2, 3, '2022-06-21 19:56:50', NULL),
(9, 5, 3, 2, 3, '2022-06-21 22:16:29', NULL),
(10, 5, 6, 2, 7, '2022-06-21 22:16:29', NULL),
(11, 6, 3, 2, NULL, '2022-06-26 15:00:01', '2022-06-27 00:00:00'),
(12, 6, 6, 2, NULL, '2022-06-26 15:00:01', '2022-06-27 00:00:00'),
(13, 6, 3, 2, NULL, '2022-06-26 16:02:23', '2022-06-27 00:00:00'),
(14, 6, 6, 2, NULL, '2022-06-26 16:02:23', '2022-06-27 00:00:00'),
(15, 6, 3, 2, NULL, '2022-06-26 16:07:31', '2022-06-27 00:00:00'),
(16, 6, 6, 2, NULL, '2022-06-26 16:07:31', '2022-06-27 00:00:00'),
(17, 9, 3, 2, 8, '2022-06-26 16:08:10', NULL),
(18, 9, 6, 2, 5, '2022-06-26 16:08:10', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(8) NOT NULL,
  `descripcion` varchar(30) DEFAULT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `descripcion`, `fechaInsercion`, `fechaBaja`) VALUES
(1, 'socio', '2022-06-04 14:57:50', NULL),
(3, 'bartender', '2022-06-04 14:58:54', NULL),
(4, 'mozo', '2022-06-04 14:59:05', NULL),
(5, 'cervecero', '2022-06-04 14:59:33', NULL),
(6, 'cocinero', '2022-06-04 14:59:39', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(8) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `clave` varchar(400) DEFAULT NULL,
  `idRol` int(8) DEFAULT NULL,
  `sector` varchar(600) DEFAULT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `clave`, `idRol`, `sector`, `fechaInsercion`, `fechaBaja`) VALUES
(11, 'Pablo', '$2y$10$H2qOs36wSWXwEblmNjkDh.4xFksj04tMxSagW0riG1f4QA98T7/L2', 1, NULL, '2022-06-12 18:43:26', NULL),
(12, 'Juan', '$2y$10$UWOOzqCI240AHTISXwSMKOLw2iuvhblllH9inGCJe83LAxqBqlfUG', 3, NULL, '2022-06-14 19:51:44', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estados_mesas`
--
ALTER TABLE `estados_mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estados_pedidos`
--
ALTER TABLE `estados_pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estados_productos_pedidos`
--
ALTER TABLE `estados_productos_pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estados_usuarios`
--
ALTER TABLE `estados_usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos_pedidos`
--
ALTER TABLE `productos_pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `estados_mesas`
--
ALTER TABLE `estados_mesas`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `estados_pedidos`
--
ALTER TABLE `estados_pedidos`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `estados_productos_pedidos`
--
ALTER TABLE `estados_productos_pedidos`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT de la tabla `estados_usuarios`
--
ALTER TABLE `estados_usuarios`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `productos_pedidos`
--
ALTER TABLE `productos_pedidos`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;
