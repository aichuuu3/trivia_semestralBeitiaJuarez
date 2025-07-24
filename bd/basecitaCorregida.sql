-- Script SQL para la tabla colaboradores en la base de datos trivita
-- Ejecutar este script si la tabla no existe o necesita modificaciones

-- Usar la base de datos trivita
-- Esta base de datos es la versión corregida de lo que se requiere para el trivia
USE trivita;

-- Crear tabla colaboradores si no existe
CREATE TABLE IF NOT EXISTS `colaboradores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL UNIQUE,
  `nombre` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` date NOT NULL,
  `hora_inicio_actividad` time NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar columna activo si no existe (para soft delete)
ALTER TABLE `colaboradores` 
ADD COLUMN IF NOT EXISTS `activo` tinyint(1) NOT NULL DEFAULT 1;

-- Agregar índices para mejorar rendimiento
ALTER TABLE `colaboradores` 
ADD INDEX IF NOT EXISTS `idx_email` (`email`),
ADD INDEX IF NOT EXISTS `idx_activo` (`activo`);

-- Datos de ejemplo (opcional)
-- INSERT INTO `colaboradores` (`email`, `nombre`, `password`, `fecha_registro`, `hora_inicio_actividad`, `activo`) VALUES
-- ('admin@trivia.com', 'Administrador', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', CURDATE(), CURTIME(), 1),
-- ('colaborador1@trivia.com', 'Juan Pérez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', CURDATE(), CURTIME(), 1);
