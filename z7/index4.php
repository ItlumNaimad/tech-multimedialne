<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zadanie 7b - SCADA (Wizualizacja)</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #222; color: white; text-align: center; }
        h1 { margin-top: 20px; color: #ffcc00; }
        .scada-container { display: flex; justify-content: center; gap: 30px; margin-top: 50px; flex-wrap: wrap; }
        .tank-wrapper { display: flex; flex-direction: column; align-items: center; }
        .tank { width: 80px; height: 200px; border: 4px solid #888; border-radius: 0 0 10px 10px; background-color: #333; position: relative; overflow: hidden; box-shadow: inset 0 0 20px black; }
        .liquid { width: 100%; height: 0%; background: linear-gradient(to top, #0044cc, #00ccff); position: absolute; bottom: 0; transition: height 0.8s ease-in-out; opacity: 0.8; }
        .lines { position: absolute; width: 100%; height: 100%; background: repeating-linear-gradient(to bottom, transparent 0%, transparent 19%, rgba(255,255,255,0.3) 20%); z-index: 2; pointer-events: none; }
        .label { margin-top: 10px; font-weight: bold; color: #ccc; }
        .value-display { font-family: 'Courier New', monospace; font-size: 18px; color: #00ccff; background: black; padding: 5px; border: 1px solid #555; margin-top: 5px; width: 60px; }
    </style>
</head>
<body>

<h1>Wizualizacja SCADA (SSE + CSV)</h1>
<p>Zmieniaj wartości w formularzu (0-100), aby sterować poziomem cieczy.</p>

<div class="scada-container">
    <?php for($i=1; $i<=5; $i++): ?>
        <div class="tank-wrapper">
            <div class="tank">
                <div class="lines"></div>
                <div class="liquid" id="liquid<?php echo $i; ?>"></div>
            </div>
            <div class="label">Zbiornik <?php echo $i; ?></div>
            <div class="value-display" id="val<?php echo $i; ?>">0</div>
        </div>
    <?php endfor; ?>
</div>

<script>
    const source = new EventSource('csv_from_db.php');

    source.onmessage = function(event) {
        const data = event.data.split("\t");
        if (data.length >= 5) {
            for (let i = 0; i < 5; i++) {
                let val = parseFloat(data[i]);
                let tankIndex = i + 1;

                // Aktualizacja tekstu
                document.getElementById('val' + tankIndex).innerText = val;

                // Aktualizacja grafiki (ograniczenie 0-100%)
                let height = val > 100 ? 100 : (val < 0 ? 0 : val);
                const liquid = document.getElementById('liquid' + tankIndex);
                liquid.style.height = height + '%';

                // Alarm (czerwony kolor > 90)
                if (val > 90) {
                    liquid.style.background = "linear-gradient(to top, #cc0000, #ff4444)";
                } else {
                    liquid.style.background = "linear-gradient(to top, #0044cc, #00ccff)";
                }
            }
        }
    };
    source.onerror = function() { console.log("Błąd połączenia SSE."); };
</script>

<br><br>
<a href="index.php" style="color: white;">&larr; Powrót do Menu</a> |
<a href="formularz_pomiary.php" target="_blank" style="color: #00ccff;">Otwórz Panel Sterowania</a>

</body>
</html>