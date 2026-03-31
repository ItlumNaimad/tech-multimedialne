<?php
session_start();
// Czyścimy wszystkie zmienne sesyjne
$_SESSION = array();

// Jeśli sesja korzysta z ciasteczek, usuń także ciasteczko sesyjne
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
    );
}
// Ostatecznie niszczymy sesję
session_destroy();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Damian Skonieczny - Tech Multimedialne</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b0f19;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent: #3b82f6;
            --card-bg: rgba(30, 41, 59, 0.4);
            --card-border: rgba(255, 255, 255, 0.08);
            --hover-border: rgba(59, 130, 246, 0.5);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            cursor: none; /* Hide default cursor to use custom one */
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
            position: relative;
        }

        #bg-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            padding: 4rem 2rem;
            z-index: 1;
        }

        header {
            text-align: center;
            margin-bottom: 4rem;
            animation: fadeInDown 1s ease-out;
        }

        h1 {
            font-size: 3.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.subtitle {
            color: var(--text-secondary);
            font-size: 1.2rem;
            max-width: 500px;
            margin: 0 auto;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            animation: fadeIn 1.2s ease-out;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(800px circle at var(--mouse-x, 0) var(--mouse-y, 0), rgba(255, 255, 255, 0.06), transparent 40%);
            z-index: 0;
            opacity: 0;
            transition: opacity 0.5s;
        }

        .card:hover::before {
            opacity: 1;
        }

        .card:hover {
            transform: translateY(-4px);
            border-color: var(--hover-border);
            box-shadow: 0 10px 30px -10px rgba(59, 130, 246, 0.3);
            background: rgba(30, 41, 59, 0.7);
        }

        .card span, .card .card-icon {
            position: relative;
            z-index: 1;
        }

        .card span {
            font-weight: 400;
            font-size: 1.1rem;
            letter-spacing: 0.3px;
        }

        .card-icon {
            color: var(--text-secondary);
            transition: all 0.3s ease;
            transform: translateX(0);
        }

        .card:hover .card-icon {
            color: var(--accent);
            transform: translateX(4px);
        }

        .card.projekt {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(168, 85, 247, 0.05));
            border-color: rgba(168, 85, 247, 0.2);
            grid-column: 1 / -1;
            padding: 2rem;
            justify-content: center;
            gap: 1rem;
        }

        .card.projekt span {
            font-size: 1.3rem;
            font-weight: 600;
            background: linear-gradient(135deg, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .card.projekt:hover {
            border-color: rgba(168, 85, 247, 0.6);
            box-shadow: 0 10px 30px -10px rgba(168, 85, 247, 0.3);
            transform: translateY(-4px) scale(1.01);
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Custom cursor elements */
        .cursor-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 999;
            transform: translate(-50%, -50%);
            transition: width 0.2s, height 0.2s;
        }
        .cursor-outline {
            width: 40px;
            height: 40px;
            border: 1px solid rgba(59, 130, 246, 0.5);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 998;
            transform: translate(-50%, -50%);
            transition: width 0.2s, height 0.2s, background-color 0.2s, transform 0.15s ease-out;
        }
        
        @media (max-width: 768px) {
            * { cursor: auto; }
            .cursor-dot, .cursor-outline { display: none; }
            h1 { font-size: 2.5rem; }
            .container { padding: 2rem 1rem; }
            .card.projekt { grid-column: auto; justify-content: space-between; padding: 1.5rem 2rem; }
        }
    </style>
</head>
<body>
    <div class="cursor-dot" id="cursor-dot"></div>
    <div class="cursor-outline" id="cursor-outline"></div>
    <canvas id="bg-canvas"></canvas>

    <div class="container">
        <header>
            <h1>Tech Multimedialne</h1>
            <p class="subtitle">Wybierz zadanie do wyświetlenia</p>
        </header>

        <div class="grid" id="cards-container">
            <a href="z1" class="card"><span>Logowanie, Sesja, Rejestracja</span><div class="card-icon">→</div></a>
            <a href="z2" class="card"><span>Zadanie 2</span><div class="card-icon">→</div></a>
            <a href="z3" class="card"><span>Zadanie 3</span><div class="card-icon">→</div></a>
            <a href="z4" class="card"><span>Zadanie 4</span><div class="card-icon">→</div></a>
            <a href="z5" class="card"><span>Zadanie 5</span><div class="card-icon">→</div></a>
            <a href="z6a" class="card"><span>Zadanie 6 A</span><div class="card-icon">→</div></a>
            <a href="z6b" class="card"><span>Zadanie 6 B</span><div class="card-icon">→</div></a>
            <a href="z7" class="card"><span>Zadanie 7</span><div class="card-icon">→</div></a>
            <a href="z8" class="card"><span>Zadanie 8</span><div class="card-icon">→</div></a>
            <a href="z9" class="card"><span>Zadanie 9</span><div class="card-icon">→</div></a>
            <a href="z10" class="card"><span>Zadanie 10</span><div class="card-icon">→</div></a>
            <a href="z11" class="card"><span>Zadanie 11</span><div class="card-icon">→</div></a>
            <a href="z12" class="card"><span>Zadanie 12</span><div class="card-icon">→</div></a>
            <a href="z13" class="card"><span>Zadanie 13</span><div class="card-icon">→</div></a>
            <a href="z14" class="card"><span>Zadanie 14</span><div class="card-icon">→</div></a>
            <a href="z15" class="card"><span>Zadanie 15</span><div class="card-icon">→</div></a>
            <a href="projekt" class="card projekt"><span>Projekt</span><div class="card-icon">✨</div></a>
        </div>
    </div>

    <script>
        // Custom Cursor Logic
        const cursorDot = document.getElementById("cursor-dot");
        const cursorOutline = document.getElementById("cursor-outline");
        let cursorX = window.innerWidth / 2, cursorY = window.innerHeight / 2;
        let outlineX = cursorX, outlineY = cursorY;

        window.addEventListener("mousemove", function(e) {
            cursorX = e.clientX;
            cursorY = e.clientY;
            
            cursorDot.style.left = cursorX + "px";
            cursorDot.style.top = cursorY + "px";
            
            // Hover effect on cards
            document.querySelectorAll('.card').forEach(card => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                card.style.setProperty('--mouse-x', `${x}px`);
                card.style.setProperty('--mouse-y', `${y}px`);
            });
        });

        // Smooth outline animation
        function animateCursor() {
            let distX = cursorX - outlineX;
            let distY = cursorY - outlineY;
            
            outlineX += distX * 0.15;
            outlineY += distY * 0.15;
            
            cursorOutline.style.left = outlineX + "px";
            cursorOutline.style.top = outlineY + "px";
            
            requestAnimationFrame(animateCursor);
        }
        animateCursor();

        // Cursor interactive state
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('mouseenter', () => {
                cursorDot.style.transform = 'translate(-50%, -50%) scale(0.5)';
                cursorOutline.style.transform = 'translate(-50%, -50%) scale(1.5)';
                cursorOutline.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';
            });
            link.addEventListener('mouseleave', () => {
                cursorDot.style.transform = 'translate(-50%, -50%) scale(1)';
                cursorOutline.style.transform = 'translate(-50%, -50%) scale(1)';
                cursorOutline.style.backgroundColor = 'transparent';
            });
        });

        // Background Particles Canvas
        const canvas = document.getElementById('bg-canvas');
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];

        function initCanvas() {
            width = window.innerWidth;
            height = window.innerHeight;
            canvas.width = width;
            canvas.height = height;
        }

        window.addEventListener('resize', initCanvas);
        initCanvas();

        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.size = Math.random() * 2;
                this.vx = (Math.random() - 0.5) * 0.5;
                this.vy = (Math.random() - 0.5) * 0.5;
                this.baseOpacity = Math.random() * 0.5 + 0.1;
                this.opacity = this.baseOpacity;
            }

            update() {
                this.x += this.vx;
                this.y += this.vy;

                if (this.x < 0) this.x = width;
                if (this.x > width) this.x = 0;
                if (this.y < 0) this.y = height;
                if (this.y > height) this.y = 0;

                const dx = cursorX - this.x;
                const dy = cursorY - this.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                const interactionDistance = 250;

                // Przyciągaj kropki w stronę kursora
                if (distance < interactionDistance) {
                    const force = (interactionDistance - distance) / interactionDistance;
                    this.x += dx * force * 0.02;
                    this.y += dy * force * 0.02;
                    this.opacity = this.baseOpacity + force * 0.5;
                } else {
                    this.opacity = this.baseOpacity;
                }
            }

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(148, 163, 184, ${this.opacity})`;
                ctx.fill();
            }
        }

        // Generate particles
        for (let i = 0; i < 80; i++) {
            particles.push(new Particle());
        }

        function animateCanvas() {
            ctx.clearRect(0, 0, width, height);

            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();
                
                // Połączenia (linie) między kropkami, które są blisko siebie
                for (let j = i; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < 120) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(59, 130, 246, ${0.1 * (1 - distance / 120)})`; // delikatnie znikające linie
                        ctx.lineWidth = 1;
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animateCanvas);
        }

        animateCanvas();
    </script>
</body>
</html>
