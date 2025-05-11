--
-- Base de datos: `eventia_db`
--
CREATE DATABASE IF NOT EXISTS `eventia_db` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `eventia_db`;

-- Insercion de datos en la tabla Eventos

INSERT INTO eventos (id, nombre, precio, descripcion, fecha_inicio, ubicacion, organizador, imagen, entradas) VALUES
(1, 'Concierto de Metallica', 20, NULL, '2025-03-15 10:00:00', 'Palacio Vistalegre', 'Eventia', 'img/metallica.jpg', 100),
(2, 'Concierto Anuel AA', 3, NULL, '2025-03-15 20:00:00', 'Wizink', 'UA', 'img/anuel.jpg', 50),
(3, 'Halloween en Fabrik', 100, 'Halloween en Fabrik!! No te lo pierdas', '2025-03-15 20:00:00', 'Fabrik', 'Eventia', 'img/halloween.jpg', 200),
(4, 'Festival Indie 2025', 35.50, 'Festival de música indie con las mejores bandas emergentes', '2025-06-20 20:00:00', 'Parque de la Ciudad', 'promotor1', 'img/indie-fest.jpg', 300),
(5, 'Concierto de Dua Lipa', 45, '¡Ven a disfrutar de la increíble música de Dua Lipa en vivo!', '2025-05-25 21:00:00', 'Palacio de los Deportes', 'Eventia', 'img/dualipa.jpg', 150),
(6, 'Concierto de Coldplay', 60, 'Una experiencia única con Coldplay en un escenario increíble.', '2025-06-10 19:00:00', 'Estadio Metropolitano', 'promotor1', 'img/coldplay.jpg', 200),
(7, 'Musical El Rey León', 50, 'Disfruta de la magia y los espectáculos del aclamado musical El Rey León. Vive la historia de Simba con música, danza y efectos visuales impresionantes.', '2025-07-01 18:00:00', 'Teatro Lope de Vega', 'Eventia', 'img/rey-leon.jpg', 300),
(8, 'Festival de musica', 30, 'Festival para aquellos que les guste todo tipo de música o quienes deseen experimentar nuevos estilos.', '2025-06-10 19:00:00', 'Ifema de Madrid', 'admin', 'img/festival-musica .jpg', 200);

-- Insercion de datos en la tabla Foro

INSERT INTO foro (titulo, autor, mensaje, evento, fecha_publicacion, parent_id) VALUES
('Dudas sobre el Festival Indie 2025', 'user', '¿Cuánto tiempo antes debo llegar para conseguir un buen lugar?', 4, '2025-05-10 18:00:00', NULL),
('Dudas sobre el musical El Rey León', 'user', '¿Alguien sabe si hay descuentos para grupos?', 6, '2025-06-15 15:00:00', NULL),
('Dudas sobre el musical El Rey León', 'admin', 'Sí, hay descuentos para grupos de más de 10 personas. ¡Te recomiendo que llames al teatro para más información!', 6, '2025-06-15 16:00:00', 1),
('Dudas sobre el musical El Rey León', 'admin', 'Además de los descuentos, también ofrecen un paquete familiar con acceso a actividades especiales.', 6, '2025-06-15 16:30:00', 1),
('Recomendaciones para el concierto de Coldplay', 'admin', 'Les recomiendo llegar temprano para la mejor experiencia. ¡No se lo pierdan!', 5, '2025-05-09 19:30:00', NULL);


-- Insercion de datos en la tabla Usuarios

INSERT INTO usuarios (username, email, password, rol, puntos, sal) VALUES
('admin', 'admin@eventia.es', '28ab8d632daf6f8db094855bf1c34fb2980b4a9e06e81a5b094533a8c4e62d5c', 'administrador', 0, '60017997c3cd912d2552ab4d2bfa31fe'),
('user', ' user@gmail.com', 'a82914ac1cf67bbb6bd963a9b7ceb260a0435c131dbd65d21290920b320f4737', 'cliente', 0, '939d012ef22c3d110c9cc1f4c42c569f');

-- Insercion de datos en la tabla Valoraciones

INSERT INTO valoraciones (id, id_evento, username, nota, comentario, fecha) VALUES
(1, 1, 'admin', 5, 'Tercio gratis para aquellos que lleguéis antes de las 19:00!!! No te lo pierdas ;)', '2025-03-13'),
(2, 2, 'user', 5, 'guapísimo', '2025-03-17'),
(3, 3, 'user', 4, 'aunque me decepcionó un poco que no se rompiera la camiseta al terminar el conci :(', '2025-03-17'),
(4, 4, 'admin', 4, 'Muy buen concierto, pero me gustaría que el sonido fuera mejor.', '2025-05-24'),
(5, 5, 'user', 5, '¡Increíble experiencia! La puesta en escena fue espectacular.', '2025-06-12');


-- Insertar el usuario promotor
INSERT INTO usuarios (username, email, password, rol, puntos, sal) VALUES
('promotor1', 'promotor1@eventia.es', 'e4fc3b6a405e7e69f850c909f6475ab940f4e0542566f0e59a5f8cbbcdce546d', 'promotor', 0, '66ab0db1e1acb5c4e909884b04d5f306');

-- Actualizar un evento existente para asignarlo al promotor (opcional)
UPDATE eventos SET organizador = 'promotor1' WHERE id = 2;
