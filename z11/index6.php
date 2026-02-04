<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Transformacje - Lab 11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .rotate-div {
            width: 50px;
            height: 50px;
            margin: 20px;
            float: left;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            transition: all 2s ease;
            transform-origin: center center;
        }

        /* Red DIVs from instruction */
        .red-rotate {
            background-color: red;
            transform: translate(-15px, 90px) scale(2);
            transform-origin: 0 100%;
        }
        .red-rotate:hover {
            background-color: black;
            transform: translate(-15px, 0px) scale(1) rotate(50deg);
        }

        /* Non-rotating Yellow DIV */
        .no-rotate {
            background-color: yellow;
            color: black;
        }

        /* Blue DIV: 720 degrees */
        .blue-rotate {
            background-color: blue;
        }
        .blue-rotate:hover {
            transform: rotate(720deg);
        }

        /* Green DIV: 360 degrees */
        .green-rotate {
            background-color: green;
        }
        .green-rotate:hover {
            transform: rotate(360deg);
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Powrót do menu</a>
    </div>
</nav>

<div class="container bg-white p-5 border rounded">
    <div class="clearfix">
        <div class="rotate-div red-rotate">1</div>
        <div class="rotate-div red-rotate">2</div>
        <div class="rotate-div no-rotate">3</div>
        <div class="rotate-div blue-rotate">Blue</div>
        <div class="rotate-div green-rotate">Green</div>
    </div>
    
    <div class="mt-5 p-3 bg-light border">
        <p>Najedź kursorem na kwadraty, aby zobaczyć transformacje.</p>
        <ul>
            <li>Czerwone: Transformacje z instrukcji (skala, przesunięcie, obrót 50st).</li>
            <li>Niebieski: Obrót o 720 stopni.</li>
            <li>Zielony: Obrót o 360 stopni.</li>
        </ul>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>