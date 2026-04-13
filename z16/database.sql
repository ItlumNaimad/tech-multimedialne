-- Struktura bazy danych dla zadania z16 (CMS)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Tabela `cms` - zawartość portali
--

CREATE TABLE `cms` (
  `id_cms` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `logo_file` varchar(255) DEFAULT 'logo.svg',
  `about_company` text DEFAULT NULL,
  `contact` text DEFAULT NULL,
  `google_map_link` text DEFAULT NULL,
  `offer` text DEFAULT NULL,
  PRIMARY KEY (`id_cms`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabela `admins` - loginy administratorów dla poszczególnych CMSów
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cms` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cms` (`id_cms`),
  CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`id_cms`) REFERENCES `cms` (`id_cms`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabela `chatbot` - historia rozmów
--

CREATE TABLE `chatbot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cms` int(11) NOT NULL,
  `datetime` datetime DEFAULT CURRENT_TIMESTAMP,
  `question` text NOT NULL,
  `question_ip` varchar(45) NOT NULL,
  `answer` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cms` (`id_cms`),
  CONSTRAINT `chatbot_ibfk_1` FOREIGN KEY (`id_cms`) REFERENCES `cms` (`id_cms`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabela `login_history` - historia logowań adminów
--

CREATE TABLE `login_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cms` int(11) NOT NULL,
  `datetime` datetime DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cms` (`id_cms`),
  CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`id_cms`) REFERENCES `cms` (`id_cms`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Przykładowe dane dla testowego portalu (admin/admin)
--

INSERT INTO `cms` (`id_cms`, `url`, `logo_file`, `about_company`, `contact`, `google_map_link`, `offer`) VALUES
(1, 'localhost/z16', 'logo.svg', 'Nasza firma od stu lat zajmuje się zbijaniem bąków i robieniem CMSów.', 'Adres: al Prof Sylwestra Kaliskiego 11, 85-796 Bydgoszcz, Poland<br>Tel: 123 456 789<br>Email: kontakt@megafirma.pl', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2393.633230467575!2d18.0084423!3d53.1259174!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47031393693e5065%3A0xc3f6068285514f7b!2sPolitechnika%20Bydgoska%20im.%20Jana%20i%20J%C4%99drzeja%20%C5%9Anadeckich!5e0!3m2!1spl!2spl!4v1712312345678!5m2!1spl!2spl', 'Oferujemy torty na korty (10zł), konie na stadionie (20zł) oraz bluzy na luzy (30zł).');


INSERT INTO `admins` (`id_cms`, `username`, `password`) VALUES
(1, 'admin', 'admin');

COMMIT;
