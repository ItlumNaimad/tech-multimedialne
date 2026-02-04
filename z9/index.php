<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$currentUser = $_SESSION['username'];

// Get users for recipient list
$users_result = mysqli_query($conn, "SELECT username FROM users WHERE username != '$currentUser'");
$users = [];
while ($row = mysqli_fetch_assoc($users_result)) {
    $users[] = $row['username'];
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Komunikator - Czat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Komunikator</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="#">Czat</a></li>
            </ul>
            <div class="navbar-nav">
                <span class="navbar-text me-3">Zalogowany jako: <strong><?php echo $currentUser; ?></strong></span>
                <a class="btn btn-outline-light btn-sm" href="logout.php">Wyloguj</a>
            </div>
        </div>
    </div>
</nav>

<div class="container my-4 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Wiadomości</h5>
                </div>
                <div class="card-body">
                    <div id="chat-box">
                        <!-- Wiadomości będą ładowane przez JS -->
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <form id="send-form" enctype="multipart/form-data">
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <select name="recipient" class="form-select" required>
                                    <option value="" selected disabled>Wybierz odbiorcę...</option>
                                    <?php if ($currentUser === 'admin'): ?>
                                        <option value="all">Do wszystkich (Admin)</option>
                                    <?php endif; ?>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user; ?>"><?php echo $user; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <input type="file" name="file" class="form-control" id="fileInput">
                            </div>
                        </div>
                        <div class="input-group">
                            <input type="text" name="message" id="messageInput" class="form-control" placeholder="Wpisz wiadomość...">
                            <button class="btn btn-primary" type="submit">Wyślij</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer mt-auto py-3 bg-white border-top">
    <div class="container text-center">
        <span class="text-muted">Komunikator &copy; 2026 | Projekt: Techniki Multimedialne</span>
    </div>
</footer>

<script>
const currentUser = '<?php echo $currentUser; ?>';
const chatBox = document.getElementById('chat-box');
const sendForm = document.getElementById('send-form');

async function fetchMessages() {
    try {
        const response = await fetch('api_fetch.php');
        const messages = await response.json();
        
        // Zapisz pozycję skrolla przed aktualizacją
        const isScrolledToBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 1;

        chatBox.innerHTML = '';
        messages.forEach(msg => {
            const isSentByMe = msg.user === currentUser;
            const msgClass = isSentByMe ? 'message-sent' : 'message-received';
            
            let mediaHtml = '';
            if (msg.file_path) {
                if (msg.file_type === 'image') {
                    mediaHtml = `<div class="media-content"><a href="${msg.file_path}" target="_blank"><img src="${msg.file_path}" alt="image"></a></div>`;
                } else if (msg.file_type === 'video') {
                    mediaHtml = `<div class="media-content"><video src="${msg.file_path}" autoplay muted loop controls></video></div>`;
                } else if (msg.file_type === 'audio') {
                    mediaHtml = `<div class="media-content"><audio src="${msg.file_path}" controls></audio></div>`;
                } else {
                    mediaHtml = `<div class="media-content"><a href="${msg.file_path}" class="btn btn-sm btn-outline-secondary" download>Pobierz plik</a></div>`;
                }
            }

            const html = `
                <div class="message ${msgClass}">
                    <div class="bubble">
                        <div class="small fw-bold">${msg.user} &rarr; ${msg.recipient}</div>
                        <div>${msg.message}</div>
                        ${mediaHtml}
                    </div>
                    <div class="message-info">${msg.datetime}</div>
                </div>
            `;
            chatBox.innerHTML += html;
        });

        // Jeśli był na dole, przeskocz na dół po nowych wiadomościach
        if (isScrolledToBottom) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    } catch (error) {
        console.error('Błąd podczas pobierania wiadomości:', error);
    }
}

sendForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(sendForm);
    
    if (!formData.get('message') && !document.getElementById('fileInput').files.length) {
        return;
    }

    try {
        const response = await fetch('api_send.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.status === 'success') {
            sendForm.reset();
            fetchMessages();
        } else {
            alert('Błąd: ' + result.message);
        }
    } catch (error) {
        console.error('Błąd podczas wysyłania:', error);
    }
});

// Polling co 3 sekundy
setInterval(fetchMessages, 3000);
fetchMessages(); // Pierwsze załadowanie
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>