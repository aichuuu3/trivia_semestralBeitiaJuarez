-- Tabla para rastrear el historial de monedas ganadas por los usuarios
CREATE TABLE IF NOT EXISTS historial_monedas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    monedas_ganadas INT NOT NULL,
    categoria VARCHAR(50),
    porcentaje DECIMAL(5,2),
    tiempo_total INT,
    fecha_ganadas TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario_fecha (usuario_id, fecha_ganadas),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
