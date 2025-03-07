SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS `eventia_db` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `eventia_db`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `precio` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `organizador` varchar(100) DEFAULT NULL,
  `imagen` varchar(255) NOT NULL DEFAULT 'img/default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `nombre`, `precio`, `descripcion`, `fecha_inicio`, `fecha_fin`, `ubicacion`, `organizador`, `imagen`) VALUES
(1, 'Concierto de Metallica', '20', NULL, '2025-03-15', NULL, NULL, NULL, 'img/metallica.jpg'),
(2, 'Concierto Anuel AA', '3', NULL, '2025-03-31', NULL, NULL, NULL, 'img/anuel.jpg'),
(3, 'Halloween en Fabrik', '100', 'Halloween en Fabrik!! No te lo pierdas', '2026-10-31', NULL, 'Fabrik', 'Eventia', 'img/halloween.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `foro`
--

CREATE TABLE `foro` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autor` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `evento` int(11) DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `foro`
--

INSERT INTO `foro` (`id`, `titulo`, `autor`, `email`, `mensaje`, `evento`, `fecha_publicacion`) VALUES
(5, 'Muy buen Concierto', 'pa', 'pablerasao@gmail.com', 'Ha sido el mejor concierto que he visto \r\n', 1, '2025-03-07 11:06:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `username` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('cliente','promotor','administrador') NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`username`, `email`, `password`, `rol`) VALUES
('admin', 'admin@eventia.es', '$2y$10$nx7sPLOeZyLFfQ5wHYDSnea7eJOtf5XGhEKDK7YJpe8Bmp8wk5dkG', 'administrador'),
('user', ' user@gmail.com', '$2y$10$0jHBrtOHcO/BQi8mZ1ZZvulNjN4UUQhjRlkx/m55RaH8GbKdd.db.', 'cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `valoraciones`
--

CREATE TABLE `valoraciones` (
  `id_evento` int(11) NOT NULL,
  `username` varchar(10) DEFAULT NULL,
  `nota` int(1) NOT NULL CHECK (`nota` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `valoraciones`
--

INSERT INTO `valoraciones` (`id_evento`, `username`, `nota`, `comentario`, `fecha`) VALUES
(1, 'admin', 5, 'Tercio gratis para aquellos que lleguéis antes de las 19:00!!! No te lo pierdas ;)', '2025-03-13 00:00:00'),
(1, 'user', 5, 'guapísimo', '2025-03-17 00:00:00'),
(1, 'user', 4, 'aunque me decepcionó un poco que no se rompiera la camiseta al terminar el conci :(', '2025-03-17 00:00:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `foro`
--
ALTER TABLE `foro`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `valoraciones`
--
ALTER TABLE `valoraciones`
  ADD KEY `id_evento` (`id_evento`),
  ADD KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `foro`
--
ALTER TABLE `foro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `valoraciones`
--
ALTER TABLE `valoraciones`
  ADD CONSTRAINT `valoraciones_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `valoraciones_ibfk_2` FOREIGN KEY (`username`) REFERENCES `usuarios` (`username`) ON DELETE SET NULL;

-- Eliminamos usuarios si existen para evitar errores
DROP USER IF EXISTS 'usuario_cliente'@'localhost';
DROP USER IF EXISTS 'usuario_promotor'@'localhost';
DROP USER IF EXISTS 'usuario_admin'@'localhost';

-- Clientes - MODIFICADO para incluir UPDATE en la tabla foro
CREATE USER 'usuario_cliente'@'localhost' IDENTIFIED BY 'clientepass';
-- Concedemos acceso a select, insert y UPDATE en foro para que puedan editar sus propios mensajes
GRANT SELECT, INSERT ON eventia_db.usuarios TO 'usuario_cliente'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON eventia_db.foro TO 'usuario_cliente'@'localhost';
GRANT SELECT, INSERT ON eventia_db.eventos TO 'usuario_cliente'@'localhost';
GRANT SELECT, INSERT ON eventia_db.valoraciones TO 'usuario_cliente'@'localhost';

-- Promotores
CREATE USER 'usuario_promotor'@'localhost' IDENTIFIED BY 'promotorpass';
-- Concedemos acceso a select e insert en todas las tablas, ademas de update y delete en eventos
GRANT SELECT, INSERT ON eventia_db.usuarios TO 'usuario_promotor'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON eventia_db.foro TO 'usuario_promotor'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON eventia_db.eventos TO 'usuario_promotor'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON eventia_db.valoraciones TO 'usuario_promotor'@'localhost';

-- Administrador
CREATE USER 'usuario_admin'@'localhost' IDENTIFIED BY 'adminpass';
-- Concedemos todos los permisos
GRANT ALL PRIVILEGES ON eventia_db.* TO 'usuario_admin'@'localhost';

-- Aplicar cambios
FLUSH PRIVILEGES;

COMMIT;