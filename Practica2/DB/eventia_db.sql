-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-03-2025 a las 13:09:25
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de datos: `eventia_db`
--
CREATE DATABASE IF NOT EXISTS eventia_db DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE eventia_db;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE eventos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE DEFAULT NULL,
  lugar VARCHAR(255) DEFAULT NULL,
  organizador VARCHAR(100) DEFAULT NULL,
  imagen VARCHAR(255) NOT NULL DEFAULT 'img/default.jpg',
  primary key(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `lugar`, `organizador`, `imagen`) VALUES
(1, 'Concierto de Metalica', NULL, '2025-03-15', NULL, NULL, NULL, 'img/metalica.jpg'),
(2, 'Concierto Anuel AA', NULL, '2025-03-31', NULL, NULL, NULL, 'img/anuel.jpg'),
(3, 'Halloween en Fabrik', 'Halloween en Fabrik!! No te lo pierdas', '2026-10-31', NULL, 'Fabrik', 'Eventia', 'img/halloween.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `foro`
--

CREATE TABLE foro (
  id int(11) NOT NULL,
  titulo varchar(255) NOT NULL,
  autor varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  mensaje text NOT NULL,
  evento int(11) DEFAULT NULL,
  fecha_publicacion timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `foro`
--

INSERT INTO foro (id, titulo, autor, email, mensaje, evento, fecha_publicacion) VALUES
(2, 'd', '', '', 'dd', 1, '2025-02-27 09:22:44'),
(3, 'd', '', '', 'xc', 2, '2025-02-27 09:23:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE usuarios (
  username varchar(10) NOT NULL,
  email varchar(100) NOT NULL,
  password varchar(255) NOT NULL,
  rol ENUM('cliente', 'promotor', 'administrador') NOT NULL DEFAULT 'cliente',
  primary key (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO usuarios (username, email, password) VALUES
('user', ' user@gmail.com', 'userpass');


COMMIT;