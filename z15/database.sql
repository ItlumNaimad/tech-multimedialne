CREATE TABLE IF NOT EXISTS uzytkownicy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    haslo VARCHAR(255) NOT NULL,
    nazwisko VARCHAR(100) NOT NULL,
    typ ENUM('klient', 'pracownik', 'admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS zagadnienia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nazwa VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS logi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_uzytkownika INT,
    data_godzina DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    przegladarka VARCHAR(255),
    system_operacyjny VARCHAR(100),
    FOREIGN KEY (id_uzytkownika) REFERENCES uzytkownicy(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS zgloszenia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_klienta INT NOT NULL,
    id_pracownika INT,
    id_zagadnienia INT NOT NULL,
    temat VARCHAR(255) NOT NULL,
    status ENUM('otwarte', 'w_trakcie', 'zamkniete') DEFAULT 'otwarte',
    ocena INT NULL DEFAULT NULL,
    data_utworzenia DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_klienta) REFERENCES uzytkownicy(id),
    FOREIGN KEY (id_pracownika) REFERENCES uzytkownicy(id),
    FOREIGN KEY (id_zagadnienia) REFERENCES zagadnienia(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS wiadomosci (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_zgloszenia INT NOT NULL,
    id_autora INT NOT NULL,
    tresc TEXT NOT NULL,
    data_godzina DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_zgloszenia) REFERENCES zgloszenia(id) ON DELETE CASCADE,
    FOREIGN KEY (id_autora) REFERENCES uzytkownicy(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO zagadnienia (nazwa) VALUES 
('Sprzedaż nowych usług (Hosting/Domeny)'),
('Pomoc techniczna (SSH/PHP/MySQL)'),
('Rezygnacja z usługi'),
('Inne');

INSERT INTO uzytkownicy (login, haslo, nazwisko, typ) VALUES 
('pracownik1', 'pass1', 'Kowalski', 'pracownik'),
('pracownik2', 'pass2', 'Nowak', 'pracownik'),
('admin', 'admin', 'Administrator', 'admin'),
('klient1', 'pass1', 'Zieliński', 'klient'),
('klient2', 'pass2', 'Wiśniewski', 'klient');
