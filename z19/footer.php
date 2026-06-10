</div> <!-- /container -->

<footer class="mt-5 py-3 bg-dark text-white-50 text-center">
    <div class="container">
        <small>&copy; <?php echo date('Y'); ?> Zbijanie Bąków S.A. | Projekt Z19 (Custom PHP)</small>
    </div>
</footer>

<!-- Chatbot Plugin Simulation -->
<div id="chatbot-widget" style="position: fixed; bottom: 20px; right: 20px; width: 320px; background: white; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.25); z-index: 1000; display: none; flex-direction: column;">
    <div style="background: #0d6efd; color: white; padding: 10px; border-radius: 10px 10px 0 0; font-weight: bold; display: flex; justify-content: space-between;">
        <span><i class="bi bi-robot"></i> Asystent AI</span>
        <span style="cursor: pointer;" onclick="toggleChatbot(false)">✕</span>
    </div>
    <div id="chat-messages" style="height: 200px; overflow-y: auto; padding: 10px; font-size: 0.9em; background: #f8f9fa;">
        <div class="mb-2"><strong>Bot:</strong> Cześć! Jestem wirtualnym asystentem. Zapytaj mnie o naszą ofertę!</div>
    </div>
    <div style="display: flex; padding: 10px; border-top: 1px solid #eee;">
        <input type="text" id="chat-input" class="form-control form-control-sm" placeholder="Napisz wiadomość..." onkeypress="if(event.key === 'Enter') sendChatMessage()">
        <button class="btn btn-primary btn-sm ms-2" onclick="sendChatMessage()"><i class="bi bi-send"></i></button>
    </div>
</div>

<button id="chatbot-toggle" class="btn btn-primary rounded-circle shadow" style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; z-index: 999; font-size: 24px;" onclick="toggleChatbot(true)">
    <i class="bi bi-chat-dots-fill"></i>
</button>

<script>
// Pobieranie klucza z pliku .env (załadowanego m.in. przez db_connect.php)
const GEMINI_API_KEY = '<?= htmlspecialchars(getenv("GEMINI_API_KEY") ?: "") ?>';
const GEMINI_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=${GEMINI_API_KEY}`;

// Historia konwersacji (multi-turn)
let chatHistory = [];

// System prompt – kontekst firmy
const SYSTEM_PROMPT = `Jesteś wirtualnym asystentem firmy "Zbijanie Bąków S.A." z Bydgoszczy.
Firma zajmuje się profesjonalnym zbijaniem bąków (to jest żart – firma jest fikcyjna, stworzona na potrzeby projektu studenckiego).
Oto dane firmy:
- Adres: ul. Leniwa 15, 85-000 Bydgoszcz
- Telefon: +48 123 456 789
- E-mail: biuro@zbijanie-bakow.pl
- Godziny pracy: 12:00 - 12:15 (pon-pt)
- Oferta: Analiza leżenia bykiem (500 zł), Szkolenia ze spania (300 zł/osobę), Wdrożenia w MŚP (wycena indywidualna)
Odpowiadaj krótko (2-3 zdania max), przyjaźnie, po polsku. Bądź w klimacie żartu ale profesjonalny.`;

function toggleChatbot(show) {
    const widget = document.getElementById('chatbot-widget');
    const toggle = document.getElementById('chatbot-toggle');
    if (show) {
        widget.style.display = 'flex';
        toggle.style.display = 'none';
    } else {
        widget.style.display = 'none';
        toggle.style.display = 'block';
    }
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

async function sendChatMessage() {
    let input = document.getElementById('chat-input');
    let msg = input.value.trim();
    if(!msg) return;
    
    let chatBox = document.getElementById('chat-messages');
    chatBox.innerHTML += `<div class="mb-2 text-end text-primary"><strong>Ty:</strong> ${escapeHtml(msg)}</div>`;
    input.value = '';
    input.disabled = true;

    // Pokaż "pisze..."
    const typingId = 'typing-' + Date.now();
    chatBox.innerHTML += `<div id="${typingId}" class="mb-2 text-muted"><em><i class="bi bi-three-dots"></i> AI myśli...</em></div>`;
    chatBox.scrollTop = chatBox.scrollHeight;

    // Dodaj wiadomość użytkownika do historii
    chatHistory.push({ role: "user", parts: [{ text: msg }] });

    try {
        const response = await fetch(GEMINI_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                system_instruction: { parts: [{ text: SYSTEM_PROMPT }] },
                contents: chatHistory
            })
        });

        const data = await response.json();
        
        // Usuń "pisze..."
        document.getElementById(typingId)?.remove();

        if (data.candidates && data.candidates[0]?.content?.parts?.[0]?.text) {
            const reply = data.candidates[0].content.parts[0].text;
            // Dodaj odpowiedź bota do historii
            chatHistory.push({ role: "model", parts: [{ text: reply }] });
            chatBox.innerHTML += `<div class="mb-2"><strong>🤖 Gemini:</strong> ${escapeHtml(reply)}</div>`;
        } else {
            chatBox.innerHTML += `<div class="mb-2 text-danger"><strong>Bot:</strong> Przepraszam, nie mogłem przetworzyć odpowiedzi.</div>`;
        }
    } catch (error) {
        document.getElementById(typingId)?.remove();
        chatBox.innerHTML += `<div class="mb-2 text-danger"><strong>Bot:</strong> Błąd połączenia z API. Sprawdź internet.</div>`;
    }

    input.disabled = false;
    input.focus();
    chatBox.scrollTop = chatBox.scrollHeight;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
