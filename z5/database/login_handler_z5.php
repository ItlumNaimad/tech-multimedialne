<?php
session_start();
require_once 'database_z5.php';
require_once 'utils.php'; // <-- DOŁĄCZAMY NASZ NOWY PLIK

// --- NOWA SEKCJA: Ustawienia Brute Force (bez zmian) ---
define('MAX_LOGIN_ATTEMPTS', 3);
define('LOCKOUT_TIME_MINUTES', 1);
$ip_address = $_SERVER['REMOTE_ADDR'];

try {
    // --- NOWA SEKCJA: ZADANIE 17a - GEOBLOKADA ---
    $details = ip_details($ip_address);
    $country = $details->country ?? null;

    // Sprawdzamy kraj. Jeśli nie jest 'PL' i nie jest to błąd (null), blokujemy.
    if ($country !== 'PL' && $country !== null) {
        // Ta blokada jest łatwa do obejścia przez VPN.
        die("Logowanie jest dozwolone tylko dla użytkowników z terytorium Polski. (Twój kraj: $country)");
    }
    // --- KONIEC SEKCJI GEOBLOKADY ---


    // --- SEKCJA BRUTE FORCE (bez zmian) ---
    $sql_check = "SELECT COUNT(*) FROM login_attempts 
                        WHERE ip_address = :ip AND attempt_time > (NOW() - INTERVAL :minutes MINUTE)";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute(['ip' => $ip_address, 'minutes' => LOCKOUT_TIME_MINUTES]);
    $attempts_count = (int)$stmt_check->fetchColumn();

    if ($attempts_count >= MAX_LOGIN_ATTEMPTS) {
        die("Wykryto zbyt wiele nieudanych prób logowania. Spróbuj ponownie za " . LOCKOUT_TIME_MINUTES . " minut.");
    }

    // --- RESZTA SKRYPTU  ---
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password_form = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password_form, $user['password'])) {

            $sql_clear = "DELETE FROM login_attempts WHERE ip_address = :ip";
            $stmt_clear = $pdo->prepare($sql_clear);
            $stmt_clear->execute(['ip' => $ip_address]);

            session_regenerate_id();

            // --- SEKCJA LOGOWANIA WIZYTY  ---
            try {
                $ip = $_SERVER['REMOTE_ADDR'];
                $browser = $_SERVER['HTTP_USER_AGENT'];
                $screen_res = $_POST['screen_res'] ?? null;
                $window_res = $_POST['window_res'] ?? null;
                $colors = $_POST['colors'] ?? null;
                $cookies_enabled = $_POST['cookies_enabled'] ?? null;
                $java_enabled = $_POST['java_enabled'] ?? null;
                $language = $_POST['language'] ?? null;

                $sql_log = "INSERT INTO goscieportalu (ipaddress, browser, screen_res, window_res, colors, cookies_enabled, java_enabled, language) 
                    VALUES (:ip, :browser, :screen_res, :window_res, :colors, :cookies, :java, :lang)";

                $stmt_log = $pdo->prepare($sql_log);
                $stmt_log->bindParam(':ip', $ip);
                $stmt_log->bindParam(':browser', $browser);
                $stmt_log->bindParam(':screen_res', $screen_res);
                $stmt_log->bindParam(':window_res', $window_res);
                $stmt_log->bindParam(':colors', $colors);
                $stmt_log->bindParam(':cookies', $cookies_enabled);
                $stmt_log->bindParam(':java', $java_enabled);
                $stmt_log->bindParam(':lang', $language);
                $stmt_log->execute();
            } catch (PDOException $e) {
                error_log("Błąd logowania wizyty: " . $e->getMessage());
            }

            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['avatar_path'] = $user['avatar_path'];

            // Inteligentne przekierowanie
            if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
                // Jeśli formularz podał nam, dokąd wrócić, użyj tego
                header("Location: " . $_POST['redirect_url']);
            } else {
                // Jeśli nie (dla bezpieczeństwa), użyj domyślnej lokalizacji z z2
                header("Location: ../z2/index.php?page=panel");
            }
            exit();

        } else {
            // Logowanie niepomyślne - zarejestruj próbę
            $sql_log_fail = "INSERT INTO login_attempts (ip_address) VALUES (:ip)";
            $stmt_log_fail = $pdo->prepare($sql_log_fail);
            $stmt_log_fail->execute(['ip' => $ip_address]);

            die("Nieprawidłowa nazwa użytkownika lub hasło.");
        }
    }
} catch (PDOException $e) {
    die("Błąd logowania: ". $e->getMessage());
}
?>