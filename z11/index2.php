<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Visibility - Lab 11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript">
        function myShowLayer(id) {
            document.getElementById('layer1').style.visibility = "hidden";
            document.getElementById('layer2').style.visibility = "hidden";
            document.getElementById('layer3').style.visibility = "hidden";
            document.getElementById('layer4').style.visibility = "hidden";
            document.getElementById(id).style.visibility = "visible";
        }
    </script>
    <style>
        .layer-box {
            position: absolute;
            width: 100px;
            height: 100px;
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Powrót do menu</a>
    </div>
</nav>

<div class="container" style="position: relative; height: 260px;">
    <div id="layer1" class="layer-box" style="background-color:yellow; left:50px; top:50px; visibility:visible;"></div>
    <div id="layer2" class="layer-box" style="background-color:brown; left:150px; top:50px; visibility:hidden;"></div>
    <div id="layer3" class="layer-box" style="background-color:red; left:250px; top:50px; visibility:hidden;"></div>
    <!-- Green rectangle -->
    <div id="layer4" class="layer-box" style="background-color:green; width:150px; height:80px; left:125px; top:150px; visibility:hidden;"></div>
</div>

<div class="container mt-4">
    <div class="card p-3">
        <h6>Pokaż element:</h6>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="myShowLayer('layer1');" checked> <label class="form-check-label">yellow</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="myShowLayer('layer2');"> <label class="form-check-label">brown</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="myShowLayer('layer3');"> <label class="form-check-label">red</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rd1" onclick="myShowLayer('layer4');"> <label class="form-check-label">green rect</label>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>