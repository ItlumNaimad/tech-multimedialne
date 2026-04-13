<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

$id_cms = $_POST['id_cms'] ?? 1;
$question = trim($_POST['question'] ?? '');

if (!$question) {
    echo json_encode(['answer' => 'Proszę zadaj pytanie.']);
    exit;
}

// Pobranie danych CMS do odpowiedzi
$stmt_cms = $pdo->prepare("SELECT * FROM cms WHERE id_cms = ?");
$stmt_cms->execute([$id_cms]);
$cms = $stmt_cms->fetch();

// Normalizacja pytania (małe litery, brak znaków przystankowych, spacji)
$norm_q = strtolower($question);
$norm_q = preg_replace('/[^\p{L}\p{N}\s]/u', '', $norm_q); // Usuń znaki przystankowe
$norm_q = preg_replace('/\s+/', ' ', $norm_q); // Nadmiarowe spacje na pojedyncze

$answer = "";

if (preg_match('/\b(cześć|czesc|dzień dobry|hejka|siema|witaj|witam)\b/', $norm_q)) {
    $answer = "Witaj Szanowny Kliencie!";
} elseif (preg_match('/\b(kontakt|adres|telefon)\b/', $norm_q)) {
    $answer = "Kontakt: " . ($cms['contact'] ?: "Niestety nie mamy danych kontaktowych w bazie.");
} elseif (preg_match('/\b(nawigacja|mapa|dojazd|jak do nas dotrzeć)\b/', $norm_q)) {
    $answer = "Jak do nas dotrzeć: Sprawdź zakładkę Mapa lub skorzystaj z linku: " . ($cms['google_map_link'] ? "Mamy mapę w systemie." : "Brak mapy.");
} elseif (preg_match('/\b(oferta|co oferujecie)\b/', $norm_q)) {
    $answer = "Nasza oferta: " . ($cms['offer'] ?: "Brak oferty w bazie.");
} elseif ($norm_q === "?" || $norm_q === "h" || preg_match('/\b(pomoc|lista pytań)\b/', $norm_q)) {
    $answer = "Mogę odpowiedzieć na pytania o: cześć, kontakt (adres, telefon), nawigacja (mapa), oferta. Wpisz jedno z tych słów.";
} else {
    $answer = "Jestem tylko początkującym botem i nie znam odpowiedzi na to pytanie. Ale chętnie się uczę!";
}

// Zapis do bazy
$stmt_log = $pdo->prepare("INSERT INTO chatbot (id_cms, question, question_ip, answer) VALUES (?, ?, ?, ?)");
$stmt_log->execute([$id_cms, $question, $_SERVER['REMOTE_ADDR'], $answer]);

echo json_encode(['answer' => $answer]);
?>
