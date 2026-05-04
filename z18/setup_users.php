<?php
require_once 'db_connect.php';

echo "<h2>Inicjalizacja użytkowników dla z18 (Photo Gallery)</h2>";

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DELETE FROM users");
    $pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");
    
    $users = [
        ['admin', 'admin', 'admin'],
        ['moderator', 'admin', 'moderator'],
        ['kowalsky', 'admin', 'user']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    
    foreach ($users as $u) {
        $username = $u[0];
        $password = $u[1];
        $role = $u[2];
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt->execute([$username, $hash, $role]);
        echo "Dodano użytkownika: <strong>$username</strong> (hasło: $password, rola: $role)<br>";
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "<br><p style='color: green;'>Sukces! Użytkownicy zostali poprawnie zainicjalizowani.</p>";
    echo "<a href='login.php'>Przejdź do logowania</a>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Błąd: " . $e->getMessage() . "</p>";
}
