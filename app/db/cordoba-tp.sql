-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-06-2022 a las 00:08:50
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cordoba-tp`
--

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `id` int(8) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `idUsuarioCreador` int(8) DEFAULT NULL,
  `entidad` varchar(25) NOT NULL,
  `idEntidad` int(11) NOT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(8) NOT NULL,
  `codigo` varchar(5) NOT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `idMesa` int(8) DEFAULT NULL,
  `rutaImagen` varchar(300) DEFAULT NULL,
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_productos`
--

CREATE TABLE `pedido_productos` (
  `id` int(8) NOT NULL,
  `idPedido` int(8) DEFAULT NULL,
  `idProducto` int(8) DEFAULT NULL,
  `cantidad` int(8) DEFAULT NULL,
  `fechaInsercion` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(8) NOT NULL,
  `nombre` varchar(35) DEFAULT NULL,
  `tiempoEstimado` time DEFAULT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `precio` float NOT NULL,
  `idRolEncargado` int(8) NOT NULL,
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `clave` varchar(30) DEFAULT NULL,
  `idRol` int(8) DEFAULT NULL,
  `fechaInsercion` datetime DEFAULT current_timestamp(),
  `fechaBaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `clave`, `idRol`, `fechaInsercion`, `fechaBaja`) VALUES
(5, 'Pablo', '$2y$10$HItw2Cvgm7G6l8aLwFz2M.7', 1, '2022-06-04 16:18:20', NULL),
(6, 'Manuel', '$2y$10$37IlohhvG6omkZNVBYXvxO8', 1, '2022-06-04 16:26:42', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
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
-- Indices de la tabla `pedido_productos`
--
ALTER TABLE `pedido_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
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
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_productos`
--
ALTER TABLE `pedido_productos`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
