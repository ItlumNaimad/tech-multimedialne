-- Baza danych dla Forum z17 (Trzypoziomowa Struktura)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Tabela uzytkownikow
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('admin', 'user') NOT NULL DEFAULT 'user',
  `is_banned` tinyint(1) NOT NULL DEFAULT 0,
  `profanity_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela tematow (kategorii/dyskusji nadrzednych)
CREATE TABLE IF NOT EXISTS `topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela watkow (threads w tematach)
CREATE TABLE IF NOT EXISTS `threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`topic_id`) REFERENCES `topics`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela postow (posty wewnatrz watkow)
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_system_msg` tinyint(1) NOT NULL DEFAULT 0,
  `media_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`thread_id`) REFERENCES `threads`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Przykladowe dane
-- Hasla wygenerowane przez password_hash('admin', PASSWORD_DEFAULT) i password_hash('user', PASSWORD_DEFAULT)
-- np. dla 'admin': $2y$10$w0B7Mfj5H.i0JInMKhfOauZOFWpOfI/e8xG0cUBRj2T2gq8sQpM3. // admin
-- np. dla 'user': $2y$10$tZk52gP/v6eX2z4x.zR6/ObaR6eYjD1y33B11wF79g6V93A9eDk.W // user

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$w0B7Mfj5H.i0JInMKhfOauZOFWpOfI/e8xG0cUBRj2T2gq8sQpM3.', 'admin'),
(2, 'Tomek', '$2y$10$tZk52gP/v6eX2z4x.zR6/ObaR6eYjD1y33B11wF79g6V93A9eDk.W', 'user'),
(3, 'Jacek', '$2y$10$tZk52gP/v6eX2z4x.zR6/ObaR6eYjD1y33B11wF79g6V93A9eDk.W', 'user');

INSERT INTO `topics` (`id`, `title`, `created_by`) VALUES
(1, 'Regulamin i ogloszenia', 1),
(2, 'Dyskusja ogolna', 2);

INSERT INTO `threads` (`id`, `topic_id`, `title`, `created_by`) VALUES
(1, 1, 'Regulamin forum', 1),
(2, 2, 'Co dzis na obiad?', 2);

INSERT INTO `posts` (`id`, `thread_id`, `content`, `created_by`, `is_system_msg`) VALUES
(1, 1, 'Przypominamy o zakazie uzywania wulgaryzmow oraz podawania podejrzanych linkow. Kazde takie naruszenie spotka sie z kara. Nie nalezy rowniez siac mowy nienawisci. Nie uzywajcie wulgarnych slow.', 1, 0),
(2, 2, 'Dzis chyba zjem pizze.', 2, 0),
(3, 2, 'Ja zjem burgera.', 3, 0);

COMMIT;
