<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>SVG Visibility - Lab 11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript">
        function myShowLayer(id) {
            document.getElementById('layer4').style.visibility = "hidden";
            document.getElementById('layer5').style.visibility = "hidden";
            document.getElementById('layer6').style.visibility = "hidden";
            document.getElementById('layer7').style.visibility = "hidden";
            document.getElementById(id).style.visibility = "visible";
        }
    </script>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Powrót do menu</a>
    </div>
</nav>

<div class="container" style="position: relative; height: 300px; border: 1px solid #ccc; background: white;">
    <div id="layer4" style="position:absolute; left:20px; top:50px; visibility:visible;">
        <svg width="100" height="100">
            <circle cx="30" cy="30" r="20" stroke="red" stroke-width="4" fill="yellow" />
            <text fill="black" font-size="14" font-family="Verdana" x="15" y="34">L4</text>
        </svg>
    </div>

    <div id="layer5" style="position:absolute; left:20px; top:50px; visibility:hidden;">
        <svg width="100" height="100">
            <rect width="50" height="40" style="fill:yellow; stroke-width:4; stroke:red" />
            <text fill="black" font-size="15" font-family="Verdana" x="10" y="20">L5</text>
        </svg>
    </div>

    <div id="layer6" style="position:absolute; left:20px; top:50px; visibility:hidden;">
        <svg width="100" height="100">
            <rect x="5" y="5" rx="20" ry="20" width="50" height="50" style="fill:yellow; stroke:black; stroke-width:2;">
                <animate attributeType="CSS" attributeName="opacity" from="1" to="0" dur="5s" repeatCount="indefinite" />
            </rect>
            <text fill="black" font-size="14" font-family="Verdana" x="15" y="25">L6</text>
        </svg>
    </div>

    <div id="layer7" style="position:absolute; left:20px; top:50px; visibility:hidden;">
        <svg height="150" width="200">
            <defs>
                <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:rgb(255,255,0); stop-opacity:1" />
                    <stop offset="100%" style="stop-color:rgb(255,0,0); stop-opacity:1" />
                </linearGradient>
            </defs>
            <ellipse cx="100" cy="70" rx="45" ry="25" fill="url(#grad1)" />
            <text fill="black" font-size="22" font-family="Verdana" font-weight="bold" x="70" y="77">L7</text>
        </svg>
    </div>
</div>

<div class="container mt-4">
    <div class="card p-3">
        <h6>Wybierz kształt SVG:</h6>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="myShowLayer('layer4');" checked> <label class="form-check-label">Circle</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="myShowLayer('layer5');"> <label class="form-check-label">Rectangle</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="myShowLayer('layer6');"> <label class="form-check-label">Rounded</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="myShowLayer('layer7');"> <label class="form-check-label">Oval</label>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>