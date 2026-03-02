-- ==========================================================
-- SKRYPT SQL DLA LABORATORIUM 12
-- Zawiera tabele dla Arduino/SCADA oraz Systemu Logowania
-- ==========================================================

-- 1. TABELE DLA ARDUINO I SCADA (Twoje oryginalne)
-- ----------------------------------------------------------

-- Tabela do testowania komunikacji z Arduino (z12b)
CREATE TABLE IF NOT EXISTS `hello_arduino` (
  `num` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `recorded` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Główna tabela na dane z czujników (z12a i z12b)
CREATE TABLE IF NOT EXISTS `vmeter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recorded` datetime NOT NULL DEFAULT current_timestamp(),
  `v0` float NOT NULL DEFAULT 0,
  `v1` float NOT NULL DEFAULT 0,
  `v2` float NOT NULL DEFAULT 0,
  `v3` float NOT NULL DEFAULT 0,
  `v4` float NOT NULL DEFAULT 0,
  `v5` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Tabela analityczna (ogólne logowanie wizyt)
CREATE TABLE IF NOT EXISTS `visitor_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `browser_info` text COLLATE utf8_polish_ci,
  `resolution` varchar(50) COLLATE utf8_polish_ci,
  `recorded` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


-- 2. TABELE DLA SYSTEMU LOGOWANIA (Wymagane przez PHP)
-- ----------------------------------------------------------

-- Tabela użytkowników
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_polish_ci NOT NULL UNIQUE,
  `password` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Tabela do blokowania prób włamań (Brute-force)
CREATE TABLE IF NOT EXISTS `break_ins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `attempt_time` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Tabela szczegółowych logów logowania (sukcesy/porażki)
CREATE TABLE IF NOT EXISTS `goscieportalu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ipaddress` varchar(45) COLLATE utf8_polish_ci NOT NULL,
  `datetime` datetime DEFAULT current_timestamp(),
  `username` varchar(50) COLLATE utf8_polish_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_polish_ci DEFAULT NULL, -- 'SUKCES' / 'PORAZKA'
  `browser` text COLLATE utf8_polish_ci,
  `screen_res` varchar(50) COLLATE utf8_polish_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
