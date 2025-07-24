
-- Tabla para usuarios 
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    monedas_totales INT DEFAULT 0,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla para administradores del sistema
CREATE TABLE administrativos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    permisos JSON, -- permisos específicos del admin
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    hora_inicio_actividad TIME DEFAULT '08:00:00', -- hora desde que está activo en el sistema
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla para colaboradores 
CREATE TABLE colaboradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    especialidad VARCHAR(100), -- área de especialidad (ej: "Tecnología", "Historia")
    preguntas_creadas INT DEFAULT 0,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    hora_inicio_actividad TIME DEFAULT '09:00:00', -- hora desde que está activo en el sistema
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de categorías
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT
);

-- Tabla de niveles de dificultad
CREATE TABLE niveles_dificultad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre ENUM('principiante', 'novato', 'experto') NOT NULL UNIQUE,
    puntos_base INT NOT NULL,
    preguntas_por_partida INT NOT NULL,
    tiempo_por_pregunta INT DEFAULT 10,
    descripcion VARCHAR(200)
);

-- Tabla de preguntas
CREATE TABLE preguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nivel_dificultad_id INT NOT NULL,
    tipo_pregunta ENUM('multiple', 'verdadero_falso') NOT NULL,
    enunciado TEXT NOT NULL,
    explicacion TEXT,
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado_por_colaborador INT, -- referencia a colaboradores
    
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,
    FOREIGN KEY (nivel_dificultad_id) REFERENCES niveles_dificultad(id) ON DELETE CASCADE,
    FOREIGN KEY (creado_por_colaborador) REFERENCES colaboradores(id) ON DELETE SET NULL,
    
    INDEX idx_categoria_nivel (categoria_id, nivel_dificultad_id),
    INDEX idx_activa (activa)
);

-- Tabla de opciones de respuesta
CREATE TABLE opciones_respuesta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta_id INT NOT NULL,
    texto_opcion TEXT NOT NULL,
    es_correcta BOOLEAN DEFAULT FALSE,
    orden_opcion TINYINT NOT NULL,
    
    FOREIGN KEY (pregunta_id) REFERENCES preguntas(id) ON DELETE CASCADE,
    
    INDEX idx_pregunta (pregunta_id)
);

-- Tabla de partidas/sesiones de juego
CREATE TABLE partidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    nivel_dificultad_id INT NOT NULL,
    puntos_obtenidos INT DEFAULT 0,
    monedas_ganadas INT DEFAULT 0,
    preguntas_respondidas INT DEFAULT 0,
    respuestas_correctas INT DEFAULT 0,
    respuestas_incorrectas INT DEFAULT 0,
    tiempo_total_segundos INT DEFAULT 0,
    completada BOOLEAN DEFAULT FALSE,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_fin TIMESTAMP NULL,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,
    FOREIGN KEY (nivel_dificultad_id) REFERENCES niveles_dificultad(id) ON DELETE CASCADE,
    
    INDEX idx_usuario_fecha (usuario_id, fecha_inicio),
    INDEX idx_categoria_nivel (categoria_id, nivel_dificultad_id)
);

-- Tabla de respuestas individuales por partida
CREATE TABLE respuestas_partida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partida_id INT NOT NULL,
    pregunta_id INT NOT NULL,
    opcion_seleccionada_id INT,
    es_correcta BOOLEAN DEFAULT FALSE,
    puntos_obtenidos INT DEFAULT 0,
    tiempo_respuesta_segundos INT,
    orden_pregunta TINYINT NOT NULL,
    
    FOREIGN KEY (partida_id) REFERENCES partidas(id) ON DELETE CASCADE,
    FOREIGN KEY (pregunta_id) REFERENCES preguntas(id) ON DELETE CASCADE,
    FOREIGN KEY (opcion_seleccionada_id) REFERENCES opciones_respuesta(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_partida_pregunta (partida_id, pregunta_id),
    INDEX idx_partida (partida_id)
);

-- Tabla de reportes QR
CREATE TABLE reportes_qr (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_reporte ENUM('estadisticas_personales', 'ranking_global', 'historial_partidas') NOT NULL,
    costo_monedas INT NOT NULL,
    codigo_qr VARCHAR(255) NOT NULL,
    datos_reporte JSON,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_descarga TIMESTAMP NULL,
    descargas_realizadas INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_codigo_qr (codigo_qr),
    INDEX idx_usuario_fecha (usuario_id, fecha_generacion)
);

-- Tabla de historial de monedas (solo para usuaritos)
CREATE TABLE historial_monedas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_transaccion ENUM('ganadas_partida', 'gastadas_reporte', 'ajuste_admin') NOT NULL,
    cantidad INT NOT NULL,
    partida_id INT NULL,
    reporte_qr_id INT NULL,
    descripcion VARCHAR(255),
    admin_que_ajusto INT NULL,
    fecha_transaccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (partida_id) REFERENCES partidas(id) ON DELETE SET NULL,
    FOREIGN KEY (reporte_qr_id) REFERENCES reportes_qr(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_que_ajusto) REFERENCES administrativos(id) ON DELETE SET NULL,
    
    INDEX idx_usuario_fecha (usuario_id, fecha_transaccion),
    INDEX idx_tipo (tipo_transaccion)
);

-- Insertar niveles de dificultad
INSERT INTO niveles_dificultad (nombre, puntos_base, preguntas_por_partida, descripcion) VALUES
('principiante', 10, 10, 'Nivel básico - 10 puntos por respuesta correcta'),
('novato', 15, 15, 'Nivel intermedio - 15 puntos por respuesta correcta'),
('experto', 20, 20, 'Nivel avanzado - 20 puntos por respuesta correcta');

-- Insertar categorías iniciales
INSERT INTO categorias (nombre, descripcion, color, icono) VALUES
('Tecnología', 'Preguntas sobre programación, hardware, software y tecnología en general', '#28a745', 'fas fa-laptop-code'),
('Historia', 'Eventos históricos, personajes importantes y fechas relevantes', '#dc3545', 'fas fa-scroll'),
('Ciencia', 'Física, química, biología, matemáticas y ciencias naturales', '#007bff', 'fas fa-atom'),
('Salud', 'Medicina, anatomía, nutrición y bienestar general', '#17a2b8', 'fas fa-heartbeat');


-- Administrativos
INSERT INTO administrativos (nombre, email, password, permisos, hora_inicio_actividad) VALUES
('Admin Principal', 'admin@trivia.com', '$2y$10$E4ljn8N8D8qxCt/a.2uFmuKqvgpgKUbwdANdyP0JVhvOhJKLPB2gK', '{"gestion_usuarios": true, "gestion_preguntas": true, "reportes": true, "configuracion": true}', '07:00:00'), -- password: admin123
('Admin Secundario', 'admin2@trivia.com', '$2y$10$E4ljn8N8D8qxCt/a.2uFmuKqvgpgKUbwdANdyP0JVhvOhJKLPB2gK', '{"gestion_usuarios": true, "gestion_preguntas": false, "reportes": true, "configuracion": false}', '08:30:00'); -- password: admin123

-- Colaboradores
INSERT INTO colaboradores (nombre, email, password, especialidad, hora_inicio_actividad) VALUES
('Colaborador Tech', 'colab.tech@trivia.com', '$2y$10$H3xU9q8M1L6vKt/e.7uGmuNqxgpjKUcydCOezP1MVjwPjMLOQC3hL', 'Tecnología', '09:00:00'), -- password: colab123
('Colaborador Historia', 'colab.historia@trivia.com', '$2y$10$H3xU9q8M1L6vKt/e.7uGmuNqxgpjKUcydCOezP1MVjwPjMLOQC3hL', 'Historia', '10:00:00'), -- password: colab123
('Colaborador Ciencia', 'colab.ciencia@trivia.com', '$2y$10$H3xU9q8M1L6vKt/e.7uGmuNqxgpjKUcydCOezP1MVjwPjMLOQC3hL', 'Ciencia', '14:00:00'); -- password: colab123

-- Usuarios 
INSERT INTO usuarios (nombre, email, password, monedas_totales) VALUES
('Usuario Test 1', 'usuario1@trivia.com', '$2y$10$F2kU7q9N0K5vLt/d.6uHmuMqvgpkLUdxdBPfzQ2NWkxQkNMPRD4iM', 50), -- password: user123
('Usuario Test 2', 'usuario2@trivia.com', '$2y$10$F2kU7q9N0K5vLt/d.6uHmuMqvgpkLUdxdBPfzQ2NWkxQkNMPRD4iM', 100), -- password: user123
('Jugador Premium', 'premium@trivia.com', '$2y$10$F2kU7q9N0K5vLt/d.6uHmuMqvgpkLUdxdBPfzQ2NWkxQkNMPRD4iM', 500); -- password: user123


INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(1, 1, 'multiple', '¿Qué significa HTML?', 'HTML significa HyperText Markup Language, es el lenguaje estándar para crear páginas web.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'HyperText Markup Language', TRUE, 1),
(@pregunta_id, 'High Tech Modern Language', FALSE, 2),
(@pregunta_id, 'Home Tool Markup Language', FALSE, 3),
(@pregunta_id, 'Hyperlink and Text Markup Language', FALSE, 4);

INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(1, 1, 'verdadero_falso', '¿JavaScript y Java son el mismo lenguaje de programación?', 'JavaScript y Java son lenguajes completamente diferentes, aunque comparten parte del nombre.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Verdadero', FALSE, 1),
(@pregunta_id, 'Falso', TRUE, 2);

INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(1, 1, 'multiple', '¿Cuál es la función principal de CSS?', 'CSS (Cascading Style Sheets) se utiliza para dar estilo y diseño a las páginas web.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Crear bases de datos', FALSE, 1),
(@pregunta_id, 'Dar estilo a páginas web', TRUE, 2),
(@pregunta_id, 'Programar funcionalidades', FALSE, 3),
(@pregunta_id, 'Manejar servidores', FALSE, 4);

-- TECNOLOGÍA - NOVATO
INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(1, 2, 'multiple', '¿Qué es una API?', 'API significa Application Programming Interface, es un conjunto de reglas que permite la comunicación entre diferentes software.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Un tipo de base de datos', FALSE, 1),
(@pregunta_id, 'Una interfaz de programación de aplicaciones', TRUE, 2),
(@pregunta_id, 'Un lenguaje de programación', FALSE, 3),
(@pregunta_id, 'Un sistema operativo', FALSE, 4);

INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(1, 2, 'verdadero_falso', '¿Git es un sistema de control de versiones distribuido?', 'Git es efectivamente un sistema de control de versiones distribuido, creado por Linus Torvalds.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Verdadero', TRUE, 1),
(@pregunta_id, 'Falso', FALSE, 2);

-- TECNOLOGÍA - EXPERTO
INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(1, 3, 'multiple', '¿Cuál de estos patrones de diseño pertenece al grupo de patrones creacionales?', 'El patrón Singleton es un patrón creacional que asegura que una clase tenga solo una instancia.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Observer', FALSE, 1),
(@pregunta_id, 'Adapter', FALSE, 2),
(@pregunta_id, 'Singleton', TRUE, 3),
(@pregunta_id, 'Strategy', FALSE, 4);

-- HISTORIA - PRINCIPIANTE
INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(2, 1, 'multiple', '¿En qué año comenzó la Segunda Guerra Mundial?', 'La Segunda Guerra Mundial comenzó en 1939 con la invasión de Polonia por parte de Alemania.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, '1938', FALSE, 1),
(@pregunta_id, '1939', TRUE, 2),
(@pregunta_id, '1940', FALSE, 3),
(@pregunta_id, '1941', FALSE, 4);

INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(2, 1, 'verdadero_falso', '¿Cristóbal Colón llegó a América en 1492?', 'Cristóbal Colón llegó a América el 12 de octubre de 1492.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Verdadero', TRUE, 1),
(@pregunta_id, 'Falso', FALSE, 2);

-- CIENCIA - PRINCIPIANTE
INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(3, 1, 'multiple', '¿Cuál es el símbolo químico del agua?', 'H2O es la fórmula química del agua, compuesta por dos átomos de hidrógeno y uno de oxígeno.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'H2O', TRUE, 1),
(@pregunta_id, 'CO2', FALSE, 2),
(@pregunta_id, 'O2', FALSE, 3),
(@pregunta_id, 'NaCl', FALSE, 4);

INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(3, 1, 'verdadero_falso', '¿La Tierra es el tercer planeta más cercano al Sol?', 'La Tierra es efectivamente el tercer planeta más cercano al Sol en nuestro sistema solar.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Verdadero', TRUE, 1),
(@pregunta_id, 'Falso', FALSE, 2);

-- SALUD - PRINCIPIANTE
INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(4, 1, 'multiple', '¿Cuántos litros de agua se recomienda beber al día?', 'Se recomienda beber aproximadamente 2 litros de agua al día para mantener una buena hidratación.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, '1 litro', FALSE, 1),
(@pregunta_id, '2 litros', TRUE, 2),
(@pregunta_id, '3 litros', FALSE, 3),
(@pregunta_id, '4 litros', FALSE, 4);

INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(4, 1, 'verdadero_falso', '¿El ejercicio regular ayuda a fortalecer el sistema inmunológico?', 'El ejercicio regular moderado fortalece el sistema inmunológico y mejora la salud general.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Verdadero', TRUE, 1),
(@pregunta_id, 'Falso', FALSE, 2);


INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(1, 2, 'multiple', '¿Qué es React?', 'React es una biblioteca de JavaScript para construir interfaces de usuario, desarrollada por Facebook.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Un servidor web', FALSE, 1),
(@pregunta_id, 'Una biblioteca de JavaScript', TRUE, 2),
(@pregunta_id, 'Un sistema operativo', FALSE, 3),
(@pregunta_id, 'Una base de datos', FALSE, 4);

INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(2, 2, 'multiple', '¿Quién fue el primer emperador romano?', 'Augusto (Octavio) fue el primer emperador romano, gobernando desde el 27 a.C.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Julio César', FALSE, 1),
(@pregunta_id, 'Augusto', TRUE, 2),
(@pregunta_id, 'Nerón', FALSE, 3),
(@pregunta_id, 'Marco Aurelio', FALSE, 4);

INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(3, 2, 'multiple', '¿Cuál es la velocidad de la luz en el vacío?', 'La velocidad de la luz en el vacío es aproximadamente 299,792,458 metros por segundo.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, '300,000 km/s', TRUE, 1),
(@pregunta_id, '150,000 km/s', FALSE, 2),
(@pregunta_id, '450,000 km/s', FALSE, 3),
(@pregunta_id, '200,000 km/s', FALSE, 4);

INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(4, 2, 'multiple', '¿Cuál es la vitamina que se produce cuando la piel se expone al sol?', 'La vitamina D se sintetiza en la piel cuando se expone a la radiación ultravioleta B del sol.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Vitamina A', FALSE, 1),
(@pregunta_id, 'Vitamina C', FALSE, 2),
(@pregunta_id, 'Vitamina D', TRUE, 3),
(@pregunta_id, 'Vitamina E', FALSE, 4);

-- Preguntas nivel experto
INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(2, 3, 'multiple', '¿En qué año cayó el Imperio Romano de Occidente?', 'El Imperio Romano de Occidente cayó en el año 476 d.C. cuando Odoacro depuso al último emperador Rómulo Augústulo.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, '410 d.C.', FALSE, 1),
(@pregunta_id, '455 d.C.', FALSE, 2),
(@pregunta_id, '476 d.C.', TRUE, 3),
(@pregunta_id, '500 d.C.', FALSE, 4);

INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(3, 3, 'multiple', '¿Cuál es el principio de incertidumbre en mecánica cuántica?', 'El principio de incertidumbre de Heisenberg establece que no se puede conocer simultáneamente la posición y el momento de una partícula con precisión absoluta.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Principio de Pauli', FALSE, 1),
(@pregunta_id, 'Principio de Heisenberg', TRUE, 2),
(@pregunta_id, 'Principio de Bohr', FALSE, 3),
(@pregunta_id, 'Principio de Schrödinger', FALSE, 4);

INSERT INTO preguntas (categoria_id, nivel_dificultad_id, tipo_pregunta, enunciado, explicacion, activa) VALUES
(4, 3, 'multiple', '¿Cuál es la función principal de las mitocondrias?', 'Las mitocondrias son los orgánulos responsables de la producción de ATP (energía) en las células eucariotas.', TRUE);
SET @pregunta_id = LAST_INSERT_ID();
INSERT INTO opciones_respuesta (pregunta_id, texto_opcion, es_correcta, orden_opcion) VALUES
(@pregunta_id, 'Síntesis de proteínas', FALSE, 1),
(@pregunta_id, 'Producción de energía (ATP)', TRUE, 2),
(@pregunta_id, 'Almacenamiento de información genética', FALSE, 3),
(@pregunta_id, 'Digestión celular', FALSE, 4);