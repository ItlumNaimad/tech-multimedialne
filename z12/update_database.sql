-- Plik: z12/update_database.sql
-- Cel: Dostosowanie bazy danych do współpracy z Arduino (zgodnie z PDF z12b)

-- 1. Upewnienie się, że tabela hello_arduino ma auto_increment (częsty błąd w kodzie Arduino)
ALTER TABLE `hello_arduino` MODIFY `num` INT(11) NOT NULL AUTO_INCREMENT;

-- 2. Rozszerzenie tabeli vmeter o kolumny alarmowe i wentylację
-- Dzięki temu Arduino będzie mogło wysyłać nie tylko napięcia (v0-v5), 
-- ale też stany logiczne (pożar, zalanie, wentylacja).
ALTER TABLE `vmeter` 
ADD COLUMN `ventilation` INT(11) NOT NULL DEFAULT 0,
ADD COLUMN `fire_alarm` INT(11) NOT NULL DEFAULT 0,
ADD COLUMN `flood` INT(11) NOT NULL DEFAULT 0,
ADD COLUMN `gas` INT(11) NOT NULL DEFAULT 0,
ADD COLUMN `co2` INT(11) NOT NULL DEFAULT 0;

-- 3. (Opcjonalnie) Jeśli chcesz zachować stare dane z 'pomiary', możesz je przenieść:
-- INSERT INTO vmeter (recorded, v0, v1, v2, v3, v4, ventilation, fire_alarm, flood, gas, co2)
-- SELECT datetime, x1, x2, x3, x4, x5, ventilation, fire_alarm, flood, gas, co2 FROM pomiary;
