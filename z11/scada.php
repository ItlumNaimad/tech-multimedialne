<?php
require_once 'db.php';

$sql = "SELECT * FROM animacje";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>SCADA Wizualizacja - Lab 11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #scada-canvas {
            background-color: #f0f0f0;
            border: 2px solid #333;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Powrót do menu</a>
        <div class="navbar-nav">
            <a class="nav-link" href="formularz.php">Dodaj animację</a>
        </div>
    </div>
</nav>

<div class="container">
    <h3 class="text-center mb-4">Wizualizacja SCADA z Bazy Danych</h3>
    
    <div class="bg-white p-3 border rounded">
        <svg id="scada-canvas" width="800" height="600" viewBox="0 0 800 600">
            <!-- Tło lub schemat technologiczny można by tu dodać -->
            <rect width="800" height="600" fill="none" stroke="#ccc" />
            
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['id'];
                    $x0 = $row['x0'];
                    $y0 = $row['y0'];
                    $xd = $row['x_delta'];
                    $yd = $row['y_delta'];
                    $begin = $row['begin'];
                    $diameter = $row['diameter'];
                    $time = $row['time'];
                    $color = $row['color'];
                    $r = $diameter / 2;

                    echo "<!-- Animacja ID: $id -->
";
                    echo "<circle cx='$x0' cy='$y0' r='$r' fill='$color'>
";
                    
                    // Animacja przesunięcia X
                    if ($xd != 0) {
                        $x_to = $x0 + $xd;
                        echo "  <animate attributeName='cx' from='$x0' to='$x_to' begin='{$begin}s' dur='{$time}s' repeatCount='indefinite' />
";
                    }
                    
                    // Animacja przesunięcia Y
                    if ($yd != 0) {
                        $y_to = $y0 + $yd;
                        echo "  <animate attributeName='cy' from='$y0' to='$y_to' begin='{$begin}s' dur='{$time}s' repeatCount='indefinite' />
";
                    }
                    
                    echo "</circle>
";
                }
            } else {
                echo "<text x='400' y='300' text-anchor='middle' fill='#999'>Brak danych w tabeli 'animacje'. Dodaj rekordy w formularzu.</text>";
            }
            ?>
        </svg>
    </div>
    
    <div class="mt-4 alert alert-info">
        Każda kulka powyżej jest generowana dynamicznie na podstawie rekordu w bazie danych MySQL.
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>