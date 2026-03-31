# Poradnik: Interaktywne tło kosmiczne / cząsteczkowe w HTML5 Canvas

Ten dokument opisuje krok po kroku, w jaki sposób zrealizowano modernistyczny i zaawansowany efekt interaktywnego tła ze śledzeniem kursora znanego ze strony `index.php`. Rozwiązanie działa z wykorzystaniem standardowej technologii `HTML5 Canvas` połączonej z fizyką odpychania i grawitacji napisaną w języku JavaScript.

## 1. Technologia użyta do efektu (Canvas)

Do narysowania efektu użyliśmy elementu HTML `<canvas id="bg-canvas"></canvas>`. Canvas to po prostu płótno do rysowania za pomocą skryptów w locie. 
Aby tło znajdowało się naturalnie za zawartością, w CSS zastosowano układ omijający interakcje (tzw. z-index mniejszy od zera i pointer-events równe "none"):

```css
#bg-canvas {
    position: fixed; /* dopasowuje obszar rysowania zawsze do wielkości okna */
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1; /* podkłada tło pod całą stronę (teksty, sekcje, przyciski) */
    pointer-events: none; /* blokuje przypadkowe najechania ukrywające przyciski na stronie */
}
```

## 2. Pętla Życia (Particle Animation Loop)

Aby osiągnąć płynną animację, musimy rysować naszą klatkę około 60 razy na sekundę. W tym celu użyto natywnej funkcji `requestAnimationFrame`:

```javascript
function animateCanvas() {
    // 1. Wyczyszczenie klatki z poprzedniego rysowania
    ctx.clearRect(0, 0, width, height);

    // 2. Przeliczenie i rysowanie nowych pozycji np. z grawitacją
    for (let i = 0; i < particles.length; i++) {
        particles[i].update(); // update - aktualizacja matematycznych pozycji X, Y
        particles[i].draw();   // draw - narysowanie koła na podstawie parametrów z update
        
        // [...] sprawdzanie zderzeń, rysowanie linii łączących cząsteczki
    }
    
    // 3. Po zrealizowaniu całej klatki, poproś przeglądarkę o wywołanie klatki następnej
    requestAnimationFrame(animateCanvas);
}
```

## 3. Matematyka Fizyki (Przyciąganie do kursora i zderzenia cząsteczek)

To tutaj dzieje się cała magia i "życie" cząsteczek. Każdy element, który się porusza, posiada swoje parametry: `x, y` (swoją pozycję na ekranie), `vx, vy` (własną prędkość oraz kierunek dryfowania).
Najważniejsze jest jednak liczenie przemieszczenia oraz dystansu między daną kropką a kursem myszy.

Z pomocą twierdzenia Pitagorasa ($a^2 + b^2 = c^2$) w prosty sposób liczymy wirtualną pozycję dystansu miedzy punktem A (kropka) i punktem B (kursor / lub inna kropka):
```javascript
const dx = mouseX - this.x; // Różnica w położeniu horyzontalnym (na osi X)
const dy = mouseY - this.y; // Różnica w położeniu wertykalnym (na osi Y)
const distance = Math.sqrt(dx * dx + dy * dy); // Całkowita odległość w linii prostej
```

### 3a. Grawitacja wobec myszy 🧲📌

Zmniejszamy całkowitą odległość w danej klatce, aby zbliżyć pozycję cząsteczki (`this`) w ułamkach podążając w miejsce myszy (`cursorX`, `cursorY`):

```javascript
if (distance < interactionDistance) {
    // Oblicz moc od 0 do 1 (im bliżej kursor, tym większa siła)
    const force = (interactionDistance - distance) / interactionDistance;
    
    if (distance > 40) {
        // Normalne przyciąganie, łagodne "zasysanie"
        this.x += dx * force * 0.02; 
        this.y += dy * force * 0.02;
    } else if (distance > 0) {
        // Martwa strefa / Odpychanie blisko centrum
        // Rozpychanie upewnia się, że nie zachowają się tak, jakby scaliły
        // się w 1 czarny punkt idealnie z kursorem.
        this.x -= (dx / distance) * force * 1.5; 
        this.y -= (dy / distance) * force * 1.5; 
    }
}
```

### 3b. Odpychanie od siebie (Anty-Kolizja Grawitacyjna) 🪐

Jeżeli połączymy 80 kropek blisko jednego wskaźnika kursora zaczną zachowywać się jak zbity sześcian kulek - stworzą sztywne "ciało". Dlatego wprowadzono wzajemne odpychanie kropek, kiedy dystans obu z nich wyniesie poniżej 35 pikseli. Skrypt odczytuje wektor wejścia (skąd kolizja nadchodzi) na podstawie `dx`/`dy` i odejmuje pozycję od jednej a przypisuje odrzut na drugą, aby się naturalnie odrzuciły i "pływały" jako zawiesina:

```javascript
// Dwie wewnętrzne pętle (dostajemy i-tą "lewą" oraz j-tą "prawą" kroplę)
// dla każdej j=i+1 unikamy duplikowania pętli kolizji w parach (1. -> 2.) <=> (2. -> 1.)

// dystans pomiędzy obiektem `i` a obiektem `j` mniejszy niż ustalony rewir?
if (distance < 35 && distance > 0) {
    const repulsion = (35 - distance) / 35; // procent skali z jakiej dystans nachodzi
    const forceX = (dx / distance) * repulsion * 0.5; // dystans i moc odrzutu X
    const forceY = (dy / distance) * repulsion * 0.5; // dystans i moc odrzutu Y
    
    // fizyczne odwrócenie i dodanie siły odrzutu w obydwie strony 
    particles[i].x += forceX;
    particles[i].y += forceY;

    particles[j].x -= forceX;
    particles[j].y -= forceY;
}
```

## 4. Wyrysowanie połączeń (Linie konstelacji) 🌠

Analogicznie do fizyki odpychającej - mierzymy dystans kropek wzajemnie, jednak tym razem poszukujemy oddalenia, które łapie się w promień poniżej ~`120 px`. Jeśli kropka `A` znajduje się 110 px od kropki `B` zostanie podpięta linia `ctx.moveTo(x,y) -> ctx.lineTo(x,y)` z przeźroczystością zależną od skali oddalenia:

```javascript
if (distance < 120) {
    // delikatnie znikające linie od 0% do 10% powiązane dystansem
    ctx.strokeStyle = `rgba(59, 130, 246, ${0.1 * (1 - distance / 120)})`; 
    ctx.lineWidth = 1;
    ctx.moveTo(particles[i].x, particles[i].y);
    ctx.lineTo(particles[j].x, particles[j].y);
    ctx.stroke();
}
```

## Werdykt

Dzięki temu efektowi łączenia Canvas (rysuje super wydajnie do 100 kropek z natywnym akceleratorem) z podstawami fizyki "wektorowej" wykreowaliśmy realistyczną zawiesinę podążającą i odpychającą się niczym chmury plazmy - nadając całemu web dizajn`owi najwyższej jakości sznyt. Podobnie jak w wielu wielkich rozwiązanych opartych na interakcjach. Taki efekt ożywia interfejs.
