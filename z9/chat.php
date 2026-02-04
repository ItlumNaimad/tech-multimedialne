<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['recipient'])) {
    header("Location: index.php");
    exit();
}

$currentUser = $_SESSION['username'];
$recipient = mysqli_real_escape_string($conn, $_GET['recipient']);

// Verify if recipient exists
$check_user = mysqli_query($conn, "SELECT id FROM users WHERE username = '$recipient'");
if (mysqli_num_rows($check_user) == 0) {
    die("Użytkownik nie istnieje.");
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Czat z <?php echo $recipient; ?> - Komunikator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">Komunikator</a>
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3 text-white">Zalogowany jako: <strong><?php echo $currentUser; ?></strong></span>
            <a class="btn btn-outline-light btn-sm" href="index.php">Powrót do listy</a>
        </div>
    </div>
</nav>

<div class="container my-4 flex-grow-1">
    <div class="row justify-content-center h-100">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Rozmowa z: <strong><?php echo $recipient; ?></strong></h5>
                </div>
                <div class="card-body">
                    <div id="chat-box">
                        <!-- Wiadomości będą ładowane przez JS -->
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <form id="send-form" enctype="multipart/form-data">
                        <input type="hidden" name="recipient" value="<?php echo $recipient; ?>">
                        <div class="mb-2">
                            <input type="file" name="file" class="form-control form-control-sm" id="fileInput">
                        </div>
                        <div class="input-group">
                            <input type="text" name="message" id="messageInput" class="form-control" placeholder="Wpisz wiadomość..." autocomplete="off">
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
        <span class="text-muted">Komunikator &copy; 2026</span>
    </div>
</footer>

<script>
const currentUser = '<?php echo $currentUser; ?>';
const recipientUser = '<?php echo $recipient; ?>';
const chatBox = document.getElementById('chat-box');
const sendForm = document.getElementById('send-form');

let lastMessageId = 0;

async function fetchMessages() {
    try {
        const response = await fetch(`api_fetch.php?recipient=${encodeURIComponent(recipientUser)}`);
        const messages = await response.json();
        
        if (messages.length === 0) return;

        const isScrolledToBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 50;
        
        // Filtrujemy tylko wiadomości, których jeszcze nie mamy
        const newMessages = messages.filter(msg => parseInt(msg.id) > lastMessageId);
        
        if (newMessages.length > 0) {
            newMessages.forEach(msg => {
                const isSentByMe = msg.user === currentUser;
                const msgClass = isSentByMe ? 'message-sent' : 'message-received';
                
                let mediaHtml = '';
                if (msg.file_path) {
                    if (msg.file_type === 'image') {
                        mediaHtml = `<div class="media-content"><a href="${msg.file_path}" target="_blank"><img src="${msg.file_path}" class="img-fluid rounded mt-2" style="max-height: 200px;"></a></div>`;
                    } else if (msg.file_type === 'video') {
                        mediaHtml = `<div class="media-content mt-2"><video src="${msg.file_path}" autoplay muted loop controls style="max-width: 100%; border-radius: 8px;"></video></div>`;
                    } else if (msg.file_type === 'audio') {
                        mediaHtml = `<div class="media-content mt-2"><audio src="${msg.file_path}" controls style="width: 100%;"></audio></div>`;
                    } else if (msg.file_type === 'pdf') {
                        mediaHtml = `<div class="media-content mt-2"><iframe src="${msg.file_path}" style="width: 100%; height: 300px; border: none; border-radius: 8px;"></iframe><br><a href="${msg.file_path}" class="btn btn-sm btn-outline-info mt-1" target="_blank">Otwórz PDF w nowym oknie</a></div>`;
                    } else {
                        mediaHtml = `<div class="media-content mt-2"><a href="${msg.file_path}" class="btn btn-sm btn-outline-secondary" download>Pobierz plik (${msg.file_path.split('.').pop()})</a></div>`;
                    }
                }

                const div = document.createElement('div');
                div.className = `message ${msgClass}`;
                div.innerHTML = `
                    <div class="bubble shadow-sm">
                        <div class="small fw-bold opacity-75">${msg.user}</div>
                        <div class="text-break">${msg.message}</div>
                        ${mediaHtml}
                    </div>
                    <div class="message-info text-muted small mt-1">${msg.datetime}</div>
                `;
                chatBox.appendChild(div);
                
                lastMessageId = Math.max(lastMessageId, parseInt(msg.id));
            });

            if (isScrolledToBottom) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }
    } catch (error) {
        console.error('Błąd podczas pobierania wiadomości:', error);
    }
}

sendForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(sendForm);
    
    if (!formData.get('message').trim() && !document.getElementById('fileInput').files.length) {
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

setInterval(fetchMessages, 3000);
fetchMessages();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>