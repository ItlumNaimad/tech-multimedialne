<?php
/**
 * Plik: database/migration_monit.php
 * Cel: Utworzenie tabeli 'monit' do obsługi powiadomień.
 */
require_once 'database.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS monit (
        idm INT AUTO_INCREMENT PRIMARY KEY,
        idp_to INT NOT NULL,
        idpz INT NOT NULL,
        message VARCHAR(255) NOT NULL,
        is_read TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (idp_to) REFERENCES pracownik(idp) ON DELETE CASCADE,
        FOREIGN KEY (idpz) REFERENCES podzadanie(idpz) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_polish_ci;";

    $pdo->exec($sql);
    echo "Tabela 'monit' została przygotowana.";
} catch (PDOException $e) {
    die("Błąd migracji: " . $e->getMessage());
}
?>
