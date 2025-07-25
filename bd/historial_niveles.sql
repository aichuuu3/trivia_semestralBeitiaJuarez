-- Tabla opcional para el historial de cambios de nivel
-- Ejecutar este script si se desea mantener un registro de las subidas de nivel

CREATE TABLE IF NOT EXISTS historial_niveles (
    id_historial INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    nivel_anterior VARCHAR(20) NOT NULL,
    nivel_nuevo VARCHAR(20) NOT NULL,
    categoria_completada VARCHAR(20) NOT NULL,
    porcentaje_obtenido DECIMAL(5,2) NOT NULL,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (id_usuario, fecha_cambio),
    INDEX idx_fecha (fecha_cambio)
);

-- Comentario sobre el uso:
-- Esta tabla permite rastrear:
-- - Cuándo cada usuario subió de nivel
-- - Qué categoría completó para subir
-- - Con qué porcentaje lo logró (siempre será 100%)
-- - Progresión del usuario a lo largo del tiempo

-- Consultas útiles:
-- Ver historial de un usuario:
-- SELECT * FROM historial_niveles WHERE id_usuario = ? ORDER BY fecha_cambio DESC;

-- Ver quién ha subido de nivel recientemente:
-- SELECT u.nombre, h.nivel_anterior, h.nivel_nuevo, h.fecha_cambio 
-- FROM historial_niveles h 
-- JOIN usuarios u ON h.id_usuario = u.id 
-- ORDER BY h.fecha_cambio DESC LIMIT 10;

-- Estadísticas de progresión:
-- SELECT nivel_nuevo, COUNT(*) as total_usuarios 
-- FROM historial_niveles 
-- GROUP BY nivel_nuevo;
