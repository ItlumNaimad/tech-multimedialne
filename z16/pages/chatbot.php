<div class="text-center mb-4">
    <h1>Czatuj z naszym botem</h1>
    <div id="avatar-container" class="my-3">
        <?php include 'avatar.svg'; ?>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="chat-box" style="height: 300px; overflow-y: scroll; border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9;">
            <div class="bot-msg text-start mb-2">
                <span class="badge bg-secondary">Bot</span> Witaj! W czym mogę Ci dzisiaj pomóc?
            </div>
        </div>
        
        <form id="chatbot-form">
            <input type="hidden" name="id_cms" value="<?php echo $id_cms; ?>">
            <div class="input-group">
                <input type="text" id="question-input" name="question" class="form-control" placeholder="Wpisz swoje pytanie..." required>
                <button class="btn btn-primary" type="submit">Wyślij</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('chatbot-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('question-input');
    const question = input.value;
    const chatBox = document.getElementById('chat-box');
    const mouth = document.getElementById('mouth');

    if (!question) return;

    // Dodaj pytanie użytkownika
    chatBox.innerHTML += `<div class="user-msg text-end mb-2"><span class="badge bg-primary">Ty</span> ${question}</div>`;
    input.value = '';
    chatBox.scrollTop = chatBox.scrollHeight;

    // Animacja ust bota (początek rozmowy)
    mouth.classList.add('talking');

    // Wyślij do API
    const formData = new FormData(this);
    fetch('api_chatbot.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        setTimeout(() => {
            chatBox.innerHTML += `<div class="bot-msg text-start mb-2"><span class="badge bg-secondary">Bot</span> ${data.answer}</div>`;
            chatBox.scrollTop = chatBox.scrollHeight;
            mouth.classList.remove('talking');
        }, 500); // Małe opóźnienie dla realizmu
    })
    .catch(error => {
        console.error('Error:', error);
        mouth.classList.remove('talking');
    });
});
</script>
