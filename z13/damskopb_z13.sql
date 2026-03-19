-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db1.zetohosting.pl:3306
-- Generation Time: Mar 19, 2026 at 11:14 AM
-- Wersja serwera: 10.11.7-MariaDB-1:10.11.7+maria~ubu2204
-- Wersja PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `damskopb_z13`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `logowanie`
--

CREATE TABLE `logowanie` (
  `idl` int(11) NOT NULL,
  `idp` int(11) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `state` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `podzadanie`
--

CREATE TABLE `podzadanie` (
  `idpz` int(11) NOT NULL,
  `idz` int(11) NOT NULL,
  `idp` int(11) NOT NULL,
  `nazwa_podzadania` varchar(255) NOT NULL,
  `stan` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownik`
--

CREATE TABLE `pracownik` (
  `idp` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_polish_ci;

--
-- Zrzut danych tabeli `pracownik`
--

INSERT INTO `pracownik` (`idp`, `login`, `password`) VALUES
(1, 'admin', 'admin'),
(2, 'pracownik1', 'pass1'),
(3, 'pracownik2', 'pass2'),
(4, 'pracownik3', 'pass3'),
(5, 'pracownik4', 'pass4');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zadanie`
--

CREATE TABLE `zadanie` (
  `idz` int(11) NOT NULL,
  `idp` int(11) NOT NULL,
  `nazwa_zadania` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_polish_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `logowanie`
--
ALTER TABLE `logowanie`
  ADD PRIMARY KEY (`idl`);

--
-- Indeksy dla tabeli `podzadanie`
--
ALTER TABLE `podzadanie`
  ADD PRIMARY KEY (`idpz`),
  ADD KEY `idz` (`idz`),
  ADD KEY `idp` (`idp`);

--
-- Indeksy dla tabeli `pracownik`
--
ALTER TABLE `pracownik`
  ADD PRIMARY KEY (`idp`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indeksy dla tabeli `zadanie`
--
ALTER TABLE `zadanie`
  ADD PRIMARY KEY (`idz`),
  ADD KEY `idp` (`idp`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `logowanie`
--
ALTER TABLE `logowanie`
  MODIFY `idl` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `podzadanie`
--
ALTER TABLE `podzadanie`
  MODIFY `idpz` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `pracownik`
--
ALTER TABLE `pracownik`
  MODIFY `idp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `zadanie`
--
ALTER TABLE `zadanie`
  MODIFY `idz` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `podzadanie`
--
ALTER TABLE `podzadanie`
  ADD CONSTRAINT `podzadanie_ibfk_1` FOREIGN KEY (`idz`) REFERENCES `zadanie` (`idz`) ON DELETE CASCADE,
  ADD CONSTRAINT `podzadanie_ibfk_2` FOREIGN KEY (`idp`) REFERENCES `pracownik` (`idp`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `zadanie`
--
ALTER TABLE `zadanie`
  ADD CONSTRAINT `zadanie_ibfk_1` FOREIGN KEY (`idp`) REFERENCES `pracownik` (`idp`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
