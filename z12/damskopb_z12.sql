-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db1.zetohosting.pl:3306
-- Generation Time: Mar 09, 2026 at 10:16 AM
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
-- Baza danych: `damskopb_z12`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `break_ins`
--

CREATE TABLE `break_ins` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `goscieportalu`
--

CREATE TABLE `goscieportalu` (
  `id` int(11) NOT NULL,
  `ipaddress` varchar(45) NOT NULL,
  `datetime` datetime DEFAULT current_timestamp(),
  `username` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `browser` text DEFAULT NULL,
  `screen_res` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_polish_ci;

--
-- Zrzut danych tabeli `goscieportalu`
--

INSERT INTO `goscieportalu` (`id`, `ipaddress`, `datetime`, `username`, `status`, `browser`, `screen_res`) VALUES
(1, '46.239.134.222', '2026-03-03 08:22:19', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864'),
(2, '46.239.134.222', '2026-03-03 08:22:54', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864'),
(3, '46.239.134.222', '2026-03-03 12:52:44', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864'),
(4, '82.146.252.13', '2026-03-03 16:17:32', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864'),
(5, '82.146.252.13', '2026-03-03 16:19:42', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864'),
(6, '185.174.154.77', '2026-03-05 16:01:04', 'user1', 'PORAZKA', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864'),
(7, '185.174.154.77', '2026-03-05 16:01:15', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864'),
(8, '185.174.154.77', '2026-03-05 16:01:41', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864'),
(9, '185.174.154.77', '2026-03-05 16:02:24', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864'),
(10, '185.174.154.77', '2026-03-05 17:34:14', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864'),
(11, '185.174.154.77', '2026-03-05 17:34:26', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864'),
(12, '185.174.154.77', '2026-03-05 19:08:36', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864'),
(13, '185.174.154.77', '2026-03-05 19:09:13', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864'),
(14, '82.146.252.9', '2026-03-06 00:11:03', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080'),
(15, '82.146.252.9', '2026-03-06 00:15:50', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864'),
(16, '82.146.252.9', '2026-03-06 14:15:00', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864'),
(17, '46.239.137.125', '2026-03-08 15:14:21', 'grad', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '1920x1080'),
(18, '79.186.36.81', '2026-03-08 16:22:02', 'user1', 'PORAZKA', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440'),
(19, '79.186.36.81', '2026-03-08 16:22:10', 'user1', 'SUKCES', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `hello_arduino`
--

CREATE TABLE `hello_arduino` (
  `num` int(11) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `recorded` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pomiary`
--

CREATE TABLE `pomiary` (
  `id` int(11) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `x1` float NOT NULL DEFAULT 0,
  `x2` float NOT NULL DEFAULT 0,
  `x3` float NOT NULL DEFAULT 0,
  `x4` float NOT NULL DEFAULT 0,
  `x5` float NOT NULL DEFAULT 0,
  `ventilation` int(11) NOT NULL DEFAULT 0,
  `fire_alarm` int(11) NOT NULL DEFAULT 0,
  `flood` int(11) NOT NULL DEFAULT 0,
  `gas` int(11) NOT NULL DEFAULT 0,
  `co2` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_polish_ci;

--
-- Zrzut danych tabeli `pomiary`
--

INSERT INTO `pomiary` (`id`, `datetime`, `x1`, `x2`, `x3`, `x4`, `x5`, `ventilation`, `fire_alarm`, `flood`, `gas`, `co2`) VALUES
(1, '2026-03-05 16:02:54', 20.3, 34.1, 18.8, 18, 40, 1, 0, 1, 0, 1),
(2, '2026-03-05 16:05:10', 15, 30, 24, 23, 39.5, 1, 0, 1, 0, 1),
(3, '2026-03-05 16:55:54', 23.6, 19.4, 21.7, 20.1, 18.6, 2, 0, 0, 1, 0),
(4, '2026-03-05 16:57:03', -5.2, 25.4, 6, 10, 38.1, 2, 0, 0, 1, 0),
(5, '2026-03-05 17:35:04', 40, 25, 50, 35, 10, 2, 1, 1, 0, 1),
(6, '2026-03-06 00:12:06', 100, 100, 100, 100, 100, 2, 1, 1, 1, 1),
(7, '2026-03-06 00:13:16', 18.3, 20.3, 23.2, 21.5, 19.2, 1, 0, 0, 0, 0),
(8, '2026-03-06 14:16:20', 20.5, 19, 23.7, 23.9, 50, 1, 1, 0, 0, 1),
(9, '2026-03-08 15:15:36', 11, 22, 33, 44, 55, 1, 1, 1, 1, 1),
(10, '2026-03-08 15:15:57', 22, 33, 44, 55, 66, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_polish_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'user1', '$2y$10$pSMiCUnPZkC8NJ5RrdG9/uOPUsA3sabZDzPjN.2u963ndKuDw6iJm', '2026-03-03 08:22:13'),
(2, 'grad', '$2y$10$VUJTWqvQjmB1Je8aObsxre2.Y2klxN315nWOYO07peT03.koBVVb2', '2026-03-08 15:14:17');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `visitor_logs`
--

CREATE TABLE `visitor_logs` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `browser_info` text DEFAULT NULL,
  `resolution` varchar(50) DEFAULT NULL,
  `cookies_enabled` tinyint(1) DEFAULT 0,
  `recorded` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_polish_ci;

--
-- Zrzut danych tabeli `visitor_logs`
--

INSERT INTO `visitor_logs` (`id`, `ip_address`, `latitude`, `longitude`, `browser_info`, `resolution`, `cookies_enabled`, `recorded`) VALUES
(1, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-03 08:22:19'),
(2, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-03 08:22:54'),
(3, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-03 12:52:44'),
(4, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-03 16:17:32'),
(5, NULL, 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-03 16:17:32'),
(6, NULL, 53.143, 18.1331, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-03 16:17:40'),
(7, NULL, 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-03 16:19:03'),
(8, NULL, 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-03 16:19:35'),
(9, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-03 16:19:42'),
(10, NULL, 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-03 16:19:43'),
(11, NULL, 53.1235, 18.0076, '[PRÓBA LOGOWANIA: user1] Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-05 16:01:04'),
(12, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-05 16:01:15'),
(13, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-05 16:01:41'),
(14, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-05 16:02:24'),
(15, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-05 17:34:14'),
(16, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-05 17:34:26'),
(17, '185.174.154.77', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-05 19:08:02'),
(18, '185.174.154.77', 53.1333, 18.006, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-05 19:08:10'),
(19, '185.174.154.77', 53.1249, 18.0027, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-05 19:08:12'),
(20, '185.174.154.77', 53.1249, 18.0027, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-05 19:08:13'),
(21, '185.174.154.77', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-05 19:08:15'),
(22, '185.174.154.77', 53.1333, 18.006, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-05 19:08:22'),
(23, '185.174.154.77', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-05 19:08:33'),
(24, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-05 19:08:36'),
(25, '185.174.154.77', 53.1249, 18.0027, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-05 19:08:39'),
(26, '185.174.154.77', 53.1249, 18.0027, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-05 19:08:44'),
(27, '185.174.154.77', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-05 19:09:08'),
(28, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-05 19:09:13'),
(29, '185.174.154.77', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-05 19:09:13'),
(30, '185.174.154.77', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-05 19:09:18'),
(31, '185.174.154.77', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-05 19:09:29'),
(32, '185.174.154.77', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-05 19:09:32'),
(33, '185.174.154.77', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-05 19:09:56'),
(34, '185.174.154.77', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-05 19:09:58'),
(35, '82.146.252.9', 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 1, '2026-03-06 00:10:56'),
(36, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 0, '2026-03-06 00:11:03'),
(37, '82.146.252.9', 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 1, '2026-03-06 00:11:03'),
(38, '82.146.252.9', 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 1, '2026-03-06 00:11:11'),
(39, '82.146.252.9', 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 1, '2026-03-06 00:11:36'),
(40, '82.146.252.9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 1, '2026-03-06 00:11:46'),
(41, '82.146.252.9', 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 1, '2026-03-06 00:12:16'),
(42, '82.146.252.9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 1, '2026-03-06 00:12:21'),
(43, '82.146.252.9', 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 1, '2026-03-06 00:13:07'),
(44, '82.146.252.9', 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 1, '2026-03-06 00:13:21'),
(45, '82.146.252.9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 1, '2026-03-06 00:13:25'),
(46, '82.146.252.9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-06 00:15:40'),
(47, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-06 00:15:50'),
(48, '82.146.252.9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-06 00:15:50'),
(49, '82.146.252.9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-06 00:15:57'),
(50, '82.146.252.9', 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 1, '2026-03-06 00:16:02'),
(51, '82.146.252.9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1920x1080', 1, '2026-03-06 00:16:05'),
(52, '82.146.252.9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '1536x864', 0, '2026-03-06 00:34:20'),
(53, '82.146.252.9', 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-06 14:14:55'),
(54, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 0, '2026-03-06 14:15:00'),
(55, '82.146.252.9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-06 14:15:04'),
(56, '82.146.252.9', 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-06 14:16:04'),
(57, '82.146.252.9', 53.1412, 18.1418, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-06 14:16:33'),
(58, '82.146.252.9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '1536x864', 1, '2026-03-06 14:16:38'),
(59, '46.239.137.125', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '1920x1080', 0, '2026-03-08 15:14:11'),
(60, '46.239.137.125', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '1920x1080', 0, '2026-03-08 15:14:12'),
(61, '46.239.137.125', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '1920x1080', 0, '2026-03-08 15:14:17'),
(62, NULL, 53.1235, 18.0076, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '1920x1080', 0, '2026-03-08 15:14:21'),
(63, '46.239.137.125', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '1920x1080', 0, '2026-03-08 15:14:21'),
(64, '46.239.137.125', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '1920x1080', 0, '2026-03-08 15:14:34'),
(65, '46.239.137.125', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '1920x1080', 0, '2026-03-08 15:14:41'),
(66, '46.239.137.125', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '1920x1080', 0, '2026-03-08 15:14:45'),
(67, '46.239.137.125', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '1920x1080', 0, '2026-03-08 15:16:16'),
(68, '79.186.36.81', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440', 0, '2026-03-08 16:21:39'),
(69, '79.186.36.81', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440', 0, '2026-03-08 16:21:45'),
(70, '79.186.36.81', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440', 0, '2026-03-08 16:21:59'),
(71, NULL, 52.4069, 16.9299, '[PRÓBA LOGOWANIA: user1] Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440', 0, '2026-03-08 16:22:02'),
(72, NULL, 52.4069, 16.9299, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440', 0, '2026-03-08 16:22:10'),
(73, '79.186.36.81', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440', 0, '2026-03-08 16:22:10'),
(74, '79.186.36.81', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440', 0, '2026-03-08 16:22:14'),
(75, '79.186.36.81', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440', 0, '2026-03-08 16:22:19'),
(76, '79.186.36.81', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440', 0, '2026-03-08 16:22:20'),
(77, '79.186.36.81', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440', 0, '2026-03-08 16:22:24'),
(78, '79.186.36.81', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440', 0, '2026-03-08 16:22:25'),
(79, '79.186.36.81', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '3440x1440', 0, '2026-03-08 16:22:27');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `vmeter`
--

CREATE TABLE `vmeter` (
  `id` int(11) NOT NULL,
  `recorded` datetime NOT NULL DEFAULT current_timestamp(),
  `v0` float NOT NULL DEFAULT 0,
  `v1` float NOT NULL DEFAULT 0,
  `v2` float NOT NULL DEFAULT 0,
  `v3` float NOT NULL DEFAULT 0,
  `v4` float NOT NULL DEFAULT 0,
  `v5` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_polish_ci;

--
-- Zrzut danych tabeli `vmeter`
--

INSERT INTO `vmeter` (`id`, `recorded`, `v0`, `v1`, `v2`, `v3`, `v4`, `v5`) VALUES
(1, '2026-03-03 16:17:37', 25, 17, 15, 20, 30, 22),
(2, '2026-03-03 16:17:39', 25, 17, 15, 20, 30, 22),
(3, '2026-03-03 16:17:48', 11, 22, 33, 44, 55, 66),
(4, '2026-03-03 16:18:08', 50, 15, 10, 36, 24, 30),
(5, '2026-03-03 16:18:22', 28, 20, 10, 0, 24, 25),
(6, '2026-03-03 16:18:37', 28, 20, 10, -14.2, 24, 25);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `break_ins`
--
ALTER TABLE `break_ins`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `goscieportalu`
--
ALTER TABLE `goscieportalu`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `hello_arduino`
--
ALTER TABLE `hello_arduino`
  ADD PRIMARY KEY (`num`);

--
-- Indeksy dla tabeli `pomiary`
--
ALTER TABLE `pomiary`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeksy dla tabeli `visitor_logs`
--
ALTER TABLE `visitor_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `vmeter`
--
ALTER TABLE `vmeter`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `break_ins`
--
ALTER TABLE `break_ins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT dla tabeli `goscieportalu`
--
ALTER TABLE `goscieportalu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT dla tabeli `hello_arduino`
--
ALTER TABLE `hello_arduino`
  MODIFY `num` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `pomiary`
--
ALTER TABLE `pomiary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `visitor_logs`
--
ALTER TABLE `visitor_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT dla tabeli `vmeter`
--
ALTER TABLE `vmeter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
