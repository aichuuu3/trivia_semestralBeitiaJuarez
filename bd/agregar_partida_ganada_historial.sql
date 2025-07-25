-- Agregar columna partida_ganada a la tabla historial_monedas
ALTER TABLE `historial_monedas` ADD COLUMN `partida_ganada` TINYINT(1) DEFAULT 0 AFTER `tiempo_total`;

-- Comentario: Esta columna indicará si la partida fue ganada (100% de aciertos)
