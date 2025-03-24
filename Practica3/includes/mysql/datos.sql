-- Insercion de datos en la tabla Eventos

INSERT INTO eventos (id, nombre, precio, descripcion, fecha_inicio, ubicacion, organizador, imagen) VALUES
(1, 'Concierto de Metallica', 20, NULL, '2025-03-15', NULL, NULL, 'img/metallica.jpg'),
(2, 'Concierto Anuel AA', 3, NULL, '2025-03-31', NULL, NULL, 'img/anuel.jpg'),
(3, 'Halloween en Fabrik', 100, 'Halloween en Fabrik!! No te lo pierdas', '2026-10-31', 'Fabrik', 'Eventia', 'img/halloween.jpg');

-- Insercion de datos en la tabla Foro

INSERT INTO foro (id, titulo, autor, email, mensaje, evento, fecha_publicacion) VALUES
(4, 'Prueba ', 'admin', 'admin@eventia.es', 'Probando \r\n', NULL, '2025-03-07 19:37:51');

-- Insercion de datos en la tabla Usuarios

INSERT INTO usuarios (username, email, password, rol, puntos) VALUES
('admin', 'admin@eventia.es', '$2y$10$nx7sPLOeZyLFfQ5wHYDSnea7eJOtf5XGhEKDK7YJpe8Bmp8wk5dkG', 'administrador', 0),
('user', ' user@gmail.com', '$2y$10$0jHBrtOHcO/BQi8mZ1ZZvulNjN4UUQhjRlkx/m55RaH8GbKdd.db.', 'cliente', 0);

-- Insercion de datos en la tabla Valoraciones

INSERT INTO valoraciones (id_evento, username, nota, comentario, fecha) VALUES
(1, 'admin', 5, 'Tercio gratis para aquellos que lleguéis antes de las 19:00!!! No te lo pierdas ;)', '2025-03-13'),
(2, 'user', 5, 'guapísimo', '2025-03-17'),
(3, 'user', 4, 'aunque me decepcionó un poco que no se rompiera la camiseta al terminar el conci :(', '2025-03-17');