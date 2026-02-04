<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Prosta animacja SVG - Lab 11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .svg-container {
            position: relative;
            height: 250px;
            background: white;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .layer {
            position: absolute;
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Powrót do menu</a>
    </div>
</nav>

<div class="container">
    <div class="svg-container">
        <!-- Layer 1: Red ball moving horizontally -->
        <div class="layer" style="left: 10px; top: 10px;">
            <svg width="300" height="30">
                <circle cx="10" cy="15" r="10" fill="red">
                    <animate attributeName="cx" from="10" to="200" begin="0s" dur="2s" repeatCount="indefinite" />
                </circle>
            </svg>
        </div>

        <!-- Layer 2: Blue ball moving horizontally backwards -->
        <div class="layer" style="left: 10px; top: 50px;">
            <svg width="300" height="30">
                <circle cx="10" cy="15" r="10" fill="blue">
                    <animate attributeName="cx" from="200" to="10" begin="0s" dur="2s" repeatCount="indefinite" />
                </circle>
            </svg>
        </div>

        <!-- Layer 3: Green ball moving vertically -->
        <div class="layer" style="left: 60px; top: 10px;">
            <svg width="30" height="200">
                <circle cx="15" cy="10" r="10" fill="green">
                    <animate attributeName="cy" from="10" to="140" begin="0s" dur="2s" repeatCount="indefinite" />
                </circle>
            </svg>
        </div>

        <!-- Layer 4: Orange ball moving vertically backwards -->
        <div class="layer" style="left: 100px; top: 10px;">
            <svg width="30" height="200">
                <circle cx="15" cy="10" r="10" fill="orange">
                    <animate attributeName="cy" from="140" to="10" begin="0s" dur="2s" repeatCount="indefinite" />
                </circle>
            </svg>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>