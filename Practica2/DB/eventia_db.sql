SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


-- Base de datos: eventia_db

CREATE DATABASE IF NOT EXISTS eventia_db DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE eventia_db;



-- Estructura de  la tabla Eventos

CREATE TABLE eventos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  precio VARCHAR(255) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE DEFAULT NULL,
  lugar VARCHAR(255) DEFAULT NULL,
  organizador VARCHAR(100) DEFAULT NULL,
  imagen VARCHAR(255) NOT NULL DEFAULT 'img/default.jpg',
  primary key(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Insercion de datos en la tabla Eventos

INSERT INTO `eventos` (`id`, `nombre`, `precio`, `descripcion`, `fecha_inicio`, `fecha_fin`, `lugar`, `organizador`, `imagen`) VALUES
(1, 'Concierto de Metallica', 20, NULL, '2025-03-15', NULL, NULL, NULL, 'img/metallica.jpg'),
(2, 'Concierto Anuel AA', 3, NULL, '2025-03-31', NULL, NULL, NULL, 'img/anuel.jpg'),
(3, 'Halloween en Fabrik', 100, 'Halloween en Fabrik!! No te lo pierdas', '2026-10-31', NULL, 'Fabrik', 'Eventia', 'img/halloween.jpg');





-- Estructura de la tabla Foro

CREATE TABLE foro (
  id int(11) NOT NULL,
  titulo varchar(255) NOT NULL,
  autor varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  mensaje text NOT NULL,
  evento int(11) DEFAULT NULL,
  fecha_publicacion timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Insercion de datos en la tabla Foro

INSERT INTO foro (id, titulo, autor, email, mensaje, evento, fecha_publicacion) VALUES
(2, 'd', '', '', 'dd', 1, '2025-02-27 09:22:44'),
(3, 'd', '', '', 'xc', 2, '2025-02-27 09:23:46');





-- Estructura de la tabla Usuarios

CREATE TABLE usuarios (
  username varchar(10) NOT NULL,
  email varchar(100) NOT NULL,
  password varchar(255) NOT NULL,
  rol ENUM('cliente', 'promotor', 'administrador') NOT NULL DEFAULT 'cliente',
  primary key (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insercion de datos en la tabla Usuarios

INSERT INTO usuarios (username, email, password, rol) VALUES
('admin', 'admin@eventia.es', '$2y$10$nx7sPLOeZyLFfQ5wHYDSnea7eJOtf5XGhEKDK7YJpe8Bmp8wk5dkG', 'administrador'),
('user', ' user@gmail.com', '$2y$10$0jHBrtOHcO/BQi8mZ1ZZvulNjN4UUQhjRlkx/m55RaH8GbKdd.db.', 'cliente');



--
-- CREACION DE LOS USUARIOS DE ACCESO A LA BD
--

-- Clientes
CREATE USER 'usuario_cliente' @'localhost' IDENTIFIED BY 'clientepass';
-- Concedemos acceso a select e insert en todas las tablas menos eventos
GRANT SELECT, INSERT ON eventia_db.usuarios TO 'usuario_cliente'@'localhost';
GRANT SELECT, INSERT ON eventia_db.foro TO 'usuario_cliente'@'localhost';
GRANT SELECT, INSERT ON eventia_db.eventos TO 'usuario_cliente'@'localhost';


-- Promotores
CREATE USER 'usuario_promotor' @'localhost' IDENTIFIED BY 'promotorpass';
-- Concedemos acceso a select e insert en todas las tablas, ademas de update y delete en eventos
GRANT SELECT, INSERT ON eventia_db.usuarios TO 'usuario_promotor'@'localhost';
GRANT SELECT, INSERT ON eventia_db.foro TO 'usuario_promotor'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON eventia_db.eventos TO 'usuario_promotor'@'localhost';


-- Administrador
CREATE USER 'usuario_admin' @'localhost' IDENTIFIED BY 'adminpass';
-- Concedemos todos los permisos
GRANT ALL PRIVILEGES ON eventia_db.* TO 'usuario_admin'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;


COMMIT;