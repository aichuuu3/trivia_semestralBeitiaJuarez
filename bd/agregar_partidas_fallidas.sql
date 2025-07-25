-- Agregar columna partidas_fallidas a la tabla usuarios
ALTER TABLE `usuarios` ADD COLUMN `partidas_fallidas` INT(11) DEFAULT 0 AFTER `partidas_ganadas`;

-- Actualizar usuarios existentes (opcional, para mantener consistencia)
UPDATE `usuarios` SET `partidas_fallidas` = 0 WHERE `partidas_fallidas` IS NULL;

-- Comentario: Esta columna contará las partidas donde el usuario no respondió 100% correctamente
