<?php
/**
 * z15/functions.php
 * Funkcje pomocnicze: detekcja IP, przeglądarki, systemu, statystyki.
 */

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    return $_SERVER['REMOTE_ADDR'];
}

function getBrowserAndOS() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $browser = "Nieznana";
    $os = "Nieznany";

    // Prosta detekcja przeglądarki
    if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) $browser = 'Internet Explorer';
    elseif (preg_match('/Firefox/i', $userAgent)) $browser = 'Firefox';
    elseif (preg_match('/Chrome/i', $userAgent)) $browser = 'Chrome';
    elseif (preg_match('/Safari/i', $userAgent)) $browser = 'Safari';
    elseif (preg_match('/Opera/i', $userAgent)) $browser = 'Opera';
    elseif (preg_match('/Netscape/i', $userAgent)) $browser = 'Netscape';

    // Prosta detekcja OS
    if (preg_match('/windows|win32/i', $userAgent)) $os = 'Windows';
    elseif (preg_match('/macintosh|mac os x/i', $userAgent)) $os = 'Mac OS';
    elseif (preg_match('/linux/i', $userAgent)) $os = 'Linux';
    elseif (preg_match('/android/i', $userAgent)) $os = 'Android';
    elseif (preg_match('/iphone/i', $userAgent)) $os = 'iOS';

    return ['browser' => $browser, 'os' => $os];
}

function logLogin($pdo, $userId) {
    $ip = getClientIP();
    $info = getBrowserAndOS();
    $stmt = $pdo->prepare("INSERT INTO logi (id_uzytkownika, ip_address, przegladarka, system_operacyjny) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $ip, $info['browser'], $info['os']]);
}

function getTempoIcon($count, $average) {
    if ($average == 0) return '❓';
    $ratio = $count / $average;
    if ($ratio < 0.5) return '🐌 (Ślimak)';
    if ($ratio < 0.9) return '🐢 (Żółw)';
    if ($ratio < 1.2) return '👤 (Człowiek)';
    return '🐆 (Puma)';
}
?>
