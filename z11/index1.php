<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Z-index - Lab 11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript">
        function ontop(id) {
            document.getElementById('layer1').style.zIndex = 0;
            document.getElementById('layer2').style.zIndex = 0;
            document.getElementById('layer3').style.zIndex = 0;
            document.getElementById('layer4').style.zIndex = 0;
            document.getElementById('layer5').style.zIndex = 0;
            document.getElementById(id).style.zIndex = 1;
        }
    </script>
    <style>
        .layer-box {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
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

<div class="container" style="position: relative; height: 300px;">
    <div id="layer1" class="layer-box" style="width:150px; height:150px; background-color:yellow; left:100px; top:60px; z-index:0;">Yellow</div>
    <div id="layer2" class="layer-box" style="width:100px; height:100px; background-color:orange; left:70px; top:90px; z-index:0;">Orange</div>
    <div id="layer3" class="layer-box" style="width:100px; height:100px; background-color:red; left:130px; top:120px; z-index:0;">Red</div>
    <div id="layer4" class="layer-box" style="width:100px; height:100px; background-color:blue; left:160px; top:50px; color:white; z-index:0;">Blue</div>
    <div id="layer5" class="layer-box" style="width:120px; height:120px; background-color:green; left:200px; top:100px; color:white; z-index:0;">Green</div>
</div>

<div class="container mt-4">
    <div class="card p-3">
        <h6>Wybierz warstwę na wierzch:</h6>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="ontop('layer1');"> <label class="form-check-label">Yellow</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="ontop('layer2');"> <label class="form-check-label">Orange</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="ontop('layer3');"> <label class="form-check-label">Red</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="ontop('layer4');"> <label class="form-check-label">Blue</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="ontop('layer5');"> <label class="form-check-label">Green</label>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>