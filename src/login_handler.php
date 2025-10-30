<?php
session_start();
require_once 'database.php';

// --- NOWA SEKCJA: Ustawienia Brute Force ---
define('MAX_LOGIN_ATTEMPTS', 4); // Maksymalna liczba prób
define('LOCKOUT_TIME_MINUTES', 2); // Czas blokady w minutach
$ip_address = $_SERVER['REMOTE_ADDR']; // Pobierz IP użytkownika

try {
    // --- NOWA SEKCJA: Sprawdzenie blokady ---
    // 1. Policz, ile było nieudanych prób z tego IP w ciągu ostatnich 15 minut
    $sql_check = "SELECT COUNT(*) FROM login_attempts 
                    WHERE ip_address = :ip AND attempt_time > (NOW() - INTERVAL :minutes MINUTE)";

    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute(['ip' => $ip_address, 'minutes' => LOCKOUT_TIME_MINUTES]);
    $attempts_count = (int)$stmt_check->fetchColumn();

    // 2. Jeśli jest za dużo prób, zablokuj
    if ($attempts_count >= MAX_LOGIN_ATTEMPTS) {
        die("Wykryto zbyt wiele nieudanych prób logowania z Twojego adresu. Spróbuj ponownie za" . LOCKOUT_TIME_MINUTES ."minut.");
    }

    // 3. Kontynuuj logowanie, jeśli formularz został wysłany
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password_form = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password_form, $user['password'])) {
            // --- ZMIANA: Logowanie pomyślne ---
            // 4. Wyczyść historię nieudanych prób dla tego IP
            $sql_clear = "DELETE FROM login_attempts WHERE ip_address = :ip";
            $stmt_clear = $pdo->prepare($sql_clear);
            $stmt_clear->execute(['ip' => $ip_address]);

            // Uruchom sesję (jak wcześniej)
            session_regenerate_id();
            // --- NOWA SEKCJA: Logowanie wizyty (Zad 14, 15, 16) ---
            try {
                // 1. Zbieramy dane z PHP [cite: 310-312]
                $ip = $_SERVER['REMOTE_ADDR'];
                $browser = $_SERVER['HTTP_USER_AGENT']; // Zad. 15

                // 2. Zbieramy dane z JS (z ukrytych pól) [cite: 327]
                $screen_res = $_POST['screen_res'] ?? null;
                $window_res = $_POST['window_res'] ?? null;
                $colors = $_POST['colors'] ?? null;
                $cookies_enabled = $_POST['cookies_enabled'] ?? null;
                $java_enabled = $_POST['java_enabled'] ?? null;
                $language = $_POST['language'] ?? null;

                // 3. Przygotuj zapytanie INSERT do tabeli goscieportalu
                $sql_log = "INSERT INTO goscieportalu (ipaddress, browser, screen_res, window_res, colors, cookies_enabled, java_enabled, language) 
                VALUES (:ip, :browser, :screen_res, :window_res, :colors, :cookies, :java, :lang)";

                $stmt_log = $pdo->prepare($sql_log);

                // 4. Powiąż wszystkie parametry
                $stmt_log->bindParam(':ip', $ip);
                $stmt_log->bindParam(':browser', $browser);
                $stmt_log->bindParam(':screen_res', $screen_res);
                $stmt_log->bindParam(':window_res', $window_res);
                $stmt_log->bindParam(':colors', $colors);
                $stmt_log->bindParam(':cookies', $cookies_enabled);
                $stmt_log->bindParam(':java', $java_enabled);
                $stmt_log->bindParam(':lang', $language);

                // 5. Wykonaj zapytanie
                $stmt_log->execute();

            } catch (PDOException $e) {
                // Cichy błąd - jeśli logowanie wizyty się nie uda, nie przerywaj logowania użytkownika
                // W prawdziwej aplikacji zalogowalibyśmy ten błąd do pliku
                error_log("Błąd logowania wizyty: " . $e->getMessage());
            }
// --- KONIEC NOWEJ SEKCJI ---
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['avatar_path'] = $user['avatar_path']; // ŚCIEŻKA DO AVATARÓW

            header("Location: ../z2/index.php?page=panel");
            exit();

        } else {
            // --- ZMIANA: Logowanie niepomyślne ---
            // 5. Zarejestruj nieudaną próbę
            $sql_log = "INSERT INTO login_attempts (ip_address) VALUES (:ip)";
            $stmt_log = $pdo->prepare($sql_log);
            $stmt_log->execute(['ip' => $ip_address]);

            die("Nieprawidłowa nazwa użytkownika lub hasło.");
        }
    }
    // Obsługa błędu 'try' (jak wcześniej)
} catch (PDOException $e) {
    die("Błąd logowania: ". $e->getMessage());
}
?>