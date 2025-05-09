--
-- Base de datos: `eventia_db`
--
CREATE DATABASE IF NOT EXISTS `eventia_db` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `eventia_db`;

-- Insercion de datos en la tabla Eventos

INSERT INTO eventos (id, nombre, precio, descripcion, fecha_inicio, ubicacion, organizador, imagen, entradas) VALUES
(1, 'Concierto de Metallica', 20.00, NULL, '2025-03-15 10:00:00', 'Palacio Vistalegre', 'Eventia', 'img/metallica.jpg', 100),
(2, 'Concierto Anuel AA', 3.00, NULL, '2025-03-15 20:00:00', 'Wizink', 'UA', 'img/anuel.jpg', 50),
(3, 'Halloween en Fabrik', 100.00, 'Halloween en Fabrik!! No te lo pierdas', '2025-03-15 20:00:00', 'Fabrik', 'Eventia', 'img/halloween.jpg', 200);


-- Insercion de datos en la tabla Foro

INSERT INTO foro (id, titulo, autor, mensaje, evento, fecha_publicacion) VALUES
(4, 'Prueba ', 'admin', 'Probando \r\n', NULL, '2025-03-07 19:37:51');

-- Insercion de datos en la tabla Usuarios

INSERT INTO usuarios (username, email, password, rol, puntos, sal) VALUES
('admin', 'admin@eventia.es', '28ab8d632daf6f8db094855bf1c34fb2980b4a9e06e81a5b094533a8c4e62d5c', 'administrador', 0, '60017997c3cd912d2552ab4d2bfa31fe'),
('user', ' user@gmail.com', 'a82914ac1cf67bbb6bd963a9b7ceb260a0435c131dbd65d21290920b320f4737', 'cliente', 0, '939d012ef22c3d110c9cc1f4c42c569f');

-- Insercion de datos en la tabla Valoraciones

INSERT INTO valoraciones (id, id_evento, username, nota, comentario, fecha) VALUES
(1, 1, 'admin', 5, 'Tercio gratis para aquellos que lleguéis antes de las 19:00!!! No te lo pierdas ;)', '2025-03-13'),
(2, 2, 'user', 5, 'guapísimo', '2025-03-17'),
(3, 3, 'user', 4, 'aunque me decepcionó un poco que no se rompiera la camiseta al terminar el conci :(', '2025-03-17');


-- Insertar el usuario promotor
INSERT INTO usuarios (username, email, password, rol, puntos, sal) VALUES
('promotor1', 'promotor1@eventia.es', 'e4fc3b6a405e7e69f850c909f6475ab940f4e0542566f0e59a5f8cbbcdce546d', 'promotor', 0, '66ab0db1e1acb5c4e909884b04d5f306');

-- Insertar un nuevo evento asignado a este promotor
INSERT INTO eventos (nombre, precio, descripcion, fecha_inicio, ubicacion, organizador, imagen) VALUES
('Festival Indie 2025', 35.50, 'Festival de música indie con las mejores bandas emergentes', '2025-06-20 20:00:00', 'Parque de la Ciudad', 'promotor1', 'img/indie-fest.jpg');

-- Actualizar un evento existente para asignarlo al promotor (opcional)
UPDATE eventos SET organizador = 'promotor1' WHERE id = 2;
