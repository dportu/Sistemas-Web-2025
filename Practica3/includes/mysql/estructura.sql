SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de datos: eventia_db
--


-- Borrado de usuarios y base de datos previos
DROP DATABASE IF EXISTS eventia_db;
DROP USER IF EXISTS 'usuario_cliente'@'localhost';
DROP USER IF EXISTS 'usuario_admin'@'localhost';
DROP USER IF EXISTS 'usuario_promotor'@'localhost';


-- Creacion de la base de datos
CREATE DATABASE IF NOT EXISTS eventia_db DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE eventia_db;




-- Estructura de la tabla Eventos

CREATE TABLE eventos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  precio VARCHAR(255) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  fecha_inicio DATE NOT NULL,
  ubicacion VARCHAR(255) DEFAULT NULL,
  organizador VARCHAR(100) DEFAULT NULL,
  imagen VARCHAR(255) NOT NULL DEFAULT 'img/default.jpg',
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura de la tabla Foro

CREATE TABLE foro (
  id int(11) NOT NULL AUTO_INCREMENT,
  titulo varchar(255) NOT NULL,
  autor varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  mensaje text NOT NULL,
  evento int(11) DEFAULT NULL,
  fecha_publicacion timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura de la tabla Usuarios

CREATE TABLE usuarios (
  username varchar(10) NOT NULL,
  email varchar(100) NOT NULL,
  password varchar(255) NOT NULL,
  rol ENUM('cliente', 'promotor', 'administrador') NOT NULL DEFAULT 'cliente',
  puntos VARCHAR(255) NOT NULL DEFAULT 0,
  PRIMARY KEY(username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura de la tabla Valoraciones
CREATE TABLE valoraciones (
  id_evento INT(11) NOT NULL,
  username varchar(10) DEFAULT NULL,
  nota INT(1) NOT NULL CHECK (nota BETWEEN 1 AND 5),
  comentario TEXT DEFAULT NULL,
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha en que se realizó la valoración
  FOREIGN KEY (id_evento) REFERENCES eventos(id) ON DELETE CASCADE,
  FOREIGN KEY (username) REFERENCES usuarios(username) ON DELETE SET NULL,
  PRIMARY KEY(id_evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- CREACION DE LOS USUARIOS DE ACCESO A LA BD
--


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