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

$apiKey = $_ENV['GEMINI_API_KEY'] ?? '';

if (!$apiKey) {
    echo json_encode(['answer' => 'Przepraszam, ale brakuje klucza Google Gemini API w konfiguracji serwera.']);
    exit;
}

$systemInst = "Jesteś wirtualnym asystentem na stronie internetowej firmy. Odpowiadaj maksymalnie zwięźle, prosto, bez zbędnego formatowania tekstu i po polsku na pytania klientów. Na podstawie poniższych danych o firmie udziel precyzyjnej odpowiedzi klientowi na jego zapytanie. Jeśli pytanie nie dotyczy firmy, uprzejmie powiedz, że jesteś jedynie asystentem firmowym i pomagasz tylko wtym zakresie.\n";
$systemInst .= "O firmie: " . strip_tags($cms['about_company'] ?? '') . "\n";
$systemInst .= "Kontakt: " . strip_tags($cms['contact'] ?? '') . "\n";
$systemInst .= "Oferta: " . strip_tags($cms['offer'] ?? '') . "\n";

$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $systemInst . "\n\nPytanie klienta: " . $question]
            ]
        ]
    ]
];

$ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-goog-api-key: ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$resData = json_decode($response, true);
$answer = "Przepraszam, wystąpił techniczny problem ze zrozumieniem Twojego zapytania (Błąd API).";

if (isset($resData['candidates'][0]['content']['parts'][0]['text'])) {
    $answer = trim($resData['candidates'][0]['content']['parts'][0]['text']);
    // Czyszczenie z ewentualnych znaczników markdown np. pogrubień
    $answer = str_replace(['**', '*'], '', $answer);
} else {
    // Dodano do debugowania: błąd zwrócony przez Google API
    $answer .= " SZCZEGÓŁY Z SERWERA GOOGLE: " . strip_tags($response);
}

// Zapis do bazy
$stmt_log = $pdo->prepare("INSERT INTO chatbot (id_cms, question, question_ip, answer) VALUES (?, ?, ?, ?)");
$stmt_log->execute([$id_cms, $question, $_SERVER['REMOTE_ADDR'], $answer]);

echo json_encode(['answer' => $answer]);
?>
