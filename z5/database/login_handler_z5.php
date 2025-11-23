<?php
session_start();
require_once 'database_z5.php'; // Łączymy się z bazą Z5
require_once 'utils.php';       // Funkcje pomocnicze (geo)

// --- KONFIGURACJA BRUTE FORCE (Zgodnie z instrukcją z5) ---
// "po 3 błędnych logowaniach dane konto jest blokowane na 1 minutę" [cite: 1385-1386]
define('MAX_LOGIN_ATTEMPTS', 3);
define('LOCKOUT_TIME_MINUTES', 1);
$ip_address = $_SERVER['REMOTE_ADDR'];

try {
    // --- 1. GEOBLOKADA (Twoja istniejąca logika) ---
    $details = ip_details($ip_address);
    $country = $details->country ?? null;
    if ($country !== 'PL' && $country !== null) {
        die("Logowanie do myCloud dozwolone tylko z Polski. (Twój kraj: $country)");
    }

    // --- 2. SPRAWDZENIE BLOKADY (Tabela break_ins) ---
    // Sprawdzamy tabelę 'break_ins' zamiast 'login_attempts'
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

        // Pobieramy dane dodatkowe do logów (Zad 15/16 przeniesione do z5)
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $screen_res = $_POST['screen_res'] ?? 'Nieznana'; // Dane z JS

        // Szukamy użytkownika
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        // Weryfikacja hasła
        if ($user && password_verify($password_form, $user['password'])) {

            // --- SUKCES ---

            // A. Sprawdź, czy było włamanie przed chwilą (do wyświetlenia ostrzeżenia)
            if ($attempts_count > 0) {
                $_SESSION['login_warning'] = "Uwaga! Odnotowano $attempts_count nieudane próby logowania z Twojego IP.";
            }

            // B. Wyczyść licznik włamań (lub zostaw do historii, ale zresetuj blokadę)
            // Tutaj usuwamy wpisy, żeby odblokować użytkownika
            $sql_clear = "DELETE FROM break_ins WHERE ip_address = :ip";
            $stmt_clear = $pdo->prepare($sql_clear);
            $stmt_clear->execute(['ip' => $ip_address]);

            session_regenerate_id();

            // C. Logujemy SUKCES do goscieportalu
            // Zapisujemy status 'SUKCES' i nazwę użytkownika
            $sql_log = "INSERT INTO goscieportalu (ipaddress, datetime, username, status, browser, screen_res) 
                        VALUES (:ip, NOW(), :user, 'SUKCES', :browser, :screen)";
            $stmt_log = $pdo->prepare($sql_log);
            $stmt_log->execute([
                'ip' => $ip_address,
                'user' => $username,
                'browser' => $browser,
                'screen' => $screen_res
            ]);

            // D. Ustawiamy zmienne sesyjne
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];

            // E. Katalog macierzysty (wymagane przez instrukcję z5)
            // Ścieżka względna od folderu z5 (gdzie jest index.php) do plików
            $_SESSION['user_dir'] = '../mycloud_files/' . $user['username'];

            // F. Przekierowanie
            if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
                header("Location: " . $_POST['redirect_url']);
            } else {
                // Domyślnie idź do dysku
                header("Location: ../z5/index.php?page=home");
            }
            exit();

        } else {
            // --- PORAŻKA ---

            // A. Logujemy PORAŻKĘ do goscieportalu [cite: 1381]
            $sql_log = "INSERT INTO goscieportalu (ipaddress, datetime, username, status, browser, screen_res) 
                        VALUES (:ip, NOW(), :user, 'PORAZKA', :browser, :screen)";
            $stmt_log = $pdo->prepare($sql_log);
            $stmt_log->execute([
                'ip' => $ip_address,
                'user' => $username, // Logujemy, na kogo próbowano się włamać
                'browser' => $browser,
                'screen' => $screen_res
            ]);

            // B. Dodajemy wpis do break_ins (do blokady)
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