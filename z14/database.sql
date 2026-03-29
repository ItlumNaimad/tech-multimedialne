-- Tabela szkoleniowców (Coach)
CREATE TABLE IF NOT EXISTS `coach` (
  `idc` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  PRIMARY KEY (`idc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Tabela kursantów (Pracownik)
CREATE TABLE IF NOT EXISTS `pracownik` (
  `idp` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  PRIMARY KEY (`idp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Tabela lekcji
CREATE TABLE IF NOT EXISTS `lekcje` (
  `idl` int(11) NOT NULL AUTO_INCREMENT,
  `idc` int(11) NOT NULL,
  `nazwa` varchar(255) NOT NULL,
  `tresc` text NOT NULL,
  `plik_multimedialny` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idl`),
  FOREIGN KEY (`idc`) REFERENCES `coach`(`idc`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Tabela testów
CREATE TABLE IF NOT EXISTS `test` (
  `idt` int(11) NOT NULL AUTO_INCREMENT,
  `idc` int(11) NOT NULL,
  `nazwa` varchar(255) NOT NULL,
  `max_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idt`),
  FOREIGN KEY (`idc`) REFERENCES `coach`(`idc`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Tabela pytań testowych
CREATE TABLE IF NOT EXISTS `pytania` (
  `idpyt` int(11) NOT NULL AUTO_INCREMENT,
  `idt` int(11) NOT NULL,
  `tresc_pytania` text NOT NULL,
  `odpowiedz_a` varchar(255) NOT NULL,
  `odpowiedz_b` varchar(255) NOT NULL,
  `odpowiedz_c` varchar(255) NOT NULL,
  `odpowiedz_d` varchar(255) NOT NULL,
  `a` tinyint(1) NOT NULL DEFAULT 0,
  `b` tinyint(1) NOT NULL DEFAULT 0,
  `c` tinyint(1) NOT NULL DEFAULT 0,
  `d` tinyint(1) NOT NULL DEFAULT 0,
  `plik_multimedialny` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idpyt`),
  FOREIGN KEY (`idt`) REFERENCES `test`(`idt`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Tabela wyników testów
CREATE TABLE IF NOT EXISTS `wyniki` (
  `idw` int(11) NOT NULL AUTO_INCREMENT,
  `idp` int(11) NOT NULL,
  `idt` int(11) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `punkty` int(11) NOT NULL,
  `plik_pdf` varchar(255) NOT NULL,
  PRIMARY KEY (`idw`),
  FOREIGN KEY (`idp`) REFERENCES `pracownik`(`idp`) ON DELETE CASCADE,
  FOREIGN KEY (`idt`) REFERENCES `test`(`idt`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Tabela logów aktywności
CREATE TABLE IF NOT EXISTS `logi_aktywnosci` (
  `id_logu` int(11) NOT NULL AUTO_INCREMENT,
  `rola` varchar(20) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `akcja` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_logu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Zasilenie bazy danymi testowymi
INSERT IGNORE INTO `coach` (`login`, `haslo`) VALUES ('coach1', 'pass1'), ('coach2', 'pass2'), ('coach3', 'pass3'), ('admin', 'admin');
INSERT IGNORE INTO `pracownik` (`login`, `haslo`) VALUES ('prac1', 'pass1'), ('prac2', 'pass2'), ('prac3', 'pass3');

 -- Znajdź definicję tabeli lekcje i dodaj kolumnę:
CREATE TABLE IF NOT EXISTS `lekcje` (
`idl` int(11) NOT NULL AUTO_INCREMENT,
`idc` int(11) NOT NULL,
`nazwa` varchar(255) NOT NULL,
`tresc` text NOT NULL,
`plik_multimedialny` varchar(255) DEFAULT NULL,
`plik_pdf` varchar(255) DEFAULT NULL, -- Dodaj tę linię
PRIMARY KEY (`idl`),
FOREIGN KEY (`idc`) REFERENCES `coach`(`idc`) ON DELETE CASCADE)

ALTER TABLE `lekcje`
ADD COLUMN `plik_pdf` VARCHAR(255) DEFAULT NULL
AFTER `plik_multimedialny`;