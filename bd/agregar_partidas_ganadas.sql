-- Agregar columna partidas_ganadas a la tabla usuarios
ALTER TABLE `usuarios` ADD COLUMN `partidas_ganadas` INT(11) DEFAULT 0 AFTER `monedas_totales`;

-- Actualizar usuarios existentes (opcional, para mantener consistencia)
UPDATE `usuarios` SET `partidas_ganadas` = 0 WHERE `partidas_ganadas` IS NULL;

-- Comentario: Esta columna contará las partidas donde el usuario respondió 100% correctamente
