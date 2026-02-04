<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Checkbox Layer - Lab 11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript">
        function ToggleLayer(id) {
            var x = document.getElementById(id);
            if (x.style.visibility === 'hidden') {
                x.style.visibility = 'visible';
            } else {
                x.style.visibility = 'hidden';
            }
        }
    </script>
    <style>
        .layer-box {
            position: absolute;
            width: 100px;
            height: 100px;
            border: 1px solid #000;
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Powrót do menu</a>
    </div>
</nav>

<div class="container" style="position: relative; height: 250px; background: white; border: 1px solid #ddd;">
    <div id="layer1" class="layer-box" style="background-color:yellow; left:50px; top:50px; visibility:visible;"></div>
    <div id="layer2" class="layer-box" style="background-color:red; left:150px; top:50px; visibility:visible;"></div>
    <div id="layer3" class="layer-box" style="background-color:blue; left:250px; top:50px; visibility:visible;"></div>
    <div id="layer4" class="layer-box" style="background-color:brown; left:350px; top:50px; visibility:visible;"></div>
    <div id="layer5" class="layer-box" style="background-color:green; left:450px; top:50px; visibility:visible;"></div>
</div>

<div class="container mt-4">
    <div class="card p-3">
        <h6>Przełącz widoczność warstw:</h6>
        <div class="btn-group" role="group">
            <input type="checkbox" class="btn-check" id="btn-check-1" autocomplete="off" onclick="ToggleLayer('layer1')" checked>
            <label class="btn btn-outline-primary" for="btn-check-1">Layer 1 (Yellow)</label>

            <input type="checkbox" class="btn-check" id="btn-check-2" autocomplete="off" onclick="ToggleLayer('layer2')" checked>
            <label class="btn btn-outline-primary" for="btn-check-2">Layer 2 (Red)</label>

            <input type="checkbox" class="btn-check" id="btn-check-3" autocomplete="off" onclick="ToggleLayer('layer3')" checked>
            <label class="btn btn-outline-primary" for="btn-check-3">Layer 3 (Blue)</label>

            <input type="checkbox" class="btn-check" id="btn-check-4" autocomplete="off" onclick="ToggleLayer('layer4')" checked>
            <label class="btn btn-outline-primary" for="btn-check-4">Layer 4 (Brown)</label>

            <input type="checkbox" class="btn-check" id="btn-check-5" autocomplete="off" onclick="ToggleLayer('layer5')" checked>
            <label class="btn btn-outline-primary" for="btn-check-5">Layer 5 (Green)</label>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>