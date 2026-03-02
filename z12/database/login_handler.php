<?php
session_start();
require_once 'database.php'; // Łączymy się z bazą
require_once 'utils.php';       // Funkcje pomocnicze (geo)

// --- KONFIGURACJA BRUTE FORCE ---
define('MAX_LOGIN_ATTEMPTS', 3);
define('LOCKOUT_TIME_MINUTES', 1);
$ip_address = $_SERVER['REMOTE_ADDR'];

try {
    // --- 1. GEOBLOKADA I POBIERANIE DANYCH ---
    $details = ip_details($ip_address);
    $country = $details->country ?? null;
    $lat = $details->loc ? explode(',', $details->loc)[0] : null;
    $lon = $details->loc ? explode(',', $details->loc)[1] : null;

    if ($country !== 'PL' && $country !== null) {
        die("Logowanie dozwolone tylko z Polski. (Twój kraj: $country)");
    }

    // --- 2. SPRAWDZENIE BLOKADY (Tabela break_ins) ---
    $sql_check = "SELECT COUNT(*) FROM break_ins 
                  WHERE ip_address = :ip AND attempt_time > (NOW() - INTERVAL :minutes MINUTE)";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute(['ip' => $ip_address, 'minutes' => LOCKOUT_TIME_MINUTES]);
    $attempts_count = (int)$stmt_check->fetchColumn();

    if ($attempts_count >= MAX_LOGIN_ATTEMPTS) {
        die("Zbyt wiele nieudanych prób. Konto zablokowane na " . LOCKOUT_TIME_MINUTES . " minutę.");
    }

    // --- 3. LOGOWANIE ---
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password_form = $_POST['password'];

        $browser = $_SERVER['HTTP_USER_AGENT'];
        $screen_res = $_POST['screen_res'] ?? 'Nieznana';

        // Szukamy użytkownika
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        // Weryfikacja hasła
        if ($user && password_verify($password_form, $user['password'])) {

            // --- SUKCES ---
            if ($attempts_count > 0) {
                $_SESSION['login_warning'] = "Uwaga! Odnotowano $attempts_count nieudane próby logowania z Twojego IP.";
            }

            // Wyczyść licznik włamań
            $sql_clear = "DELETE FROM break_ins WHERE ip_address = :ip";
            $stmt_clear = $pdo->prepare($sql_clear);
            $stmt_clear->execute(['ip' => $ip_address]);

            session_regenerate_id();

            // A. Logujemy do 'goscieportalu' (systemowa tabela logowania)
            $sql_log1 = "INSERT INTO goscieportalu (ipaddress, datetime, username, status, browser, screen_res) 
                         VALUES (:ip, NOW(), :user, 'SUKCES', :browser, :screen)";
            $stmt_log1 = $pdo->prepare($sql_log1);
            $stmt_log1->execute([
                'ip' => $ip_address,
                'user' => $username,
                'browser' => $browser,
                'screen' => $screen_res
            ]);

            // B. Logujemy do 'visitor_logs' (Twoja tabela analityczna)
            $sql_log2 = "INSERT INTO visitor_logs (latitude, longitude, browser_info, resolution, recorded) 
                         VALUES (:lat, :lon, :browser, :res, NOW())";
            $stmt_log2 = $pdo->prepare($sql_log2);
            $stmt_log2->execute([
                'lat' => $lat,
                'lon' => $lon,
                'browser' => $browser,
                'res' => $screen_res
            ]);

            // Ustawiamy zmienne sesyjne
            $_SESSION['loggedin'] = true;
            $_SESSION['app_id'] = 'lab12';
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];

            // Przekierowanie
            if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
                header("Location: " . $_POST['redirect_url']);
            } else {
                header("Location: ../index.php?page=home");
            }
            exit();

        } else {
            // --- PORAŻKA ---
            
            // Logujemy PORAŻKĘ do obu tabel
            $sql_log1 = "INSERT INTO goscieportalu (ipaddress, datetime, username, status, browser, screen_res) 
                         VALUES (:ip, NOW(), :user, 'PORAZKA', :browser, :screen)";
            $stmt_log1 = $pdo->prepare($sql_log1);
            $stmt_log1->execute([
                'ip' => $ip_address,
                'user' => $username,
                'browser' => $browser,
                'screen' => $screen_res
            ]);

            $sql_log2 = "INSERT INTO visitor_logs (latitude, longitude, browser_info, resolution, recorded) 
                         VALUES (:lat, :lon, :browser_info, :res, NOW())";
            $stmt_log2 = $pdo->prepare($sql_log2);
            $stmt_log2->execute([
                'lat' => $lat,
                'lon' => $lon,
                'browser_info' => "[PRÓBA LOGOWANIA: $username] " . $browser,
                'res' => $screen_res
            ]);

            // Dodajemy wpis do break_ins (do blokady)
            $sql_break = "INSERT INTO break_ins (ip_address, attempt_time) VALUES (:ip, NOW())";
            $stmt_break = $pdo->prepare($sql_break);
            $stmt_break->execute(['ip' => $ip_address]);

            die("Błędny login lub hasło.");
        }
    }
} catch (PDOException $e) {
    die("Błąd systemu: ". $e->getMessage());
}
?>