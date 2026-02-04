<!DOCTYPE html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 8 - MuseScore & Audio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #fff; padding-top: 70px; }
        .navbar { background-color: #1db954; } /* Kolor Spotify */
        .card { background-color: #282828; border: none; margin-bottom: 20px; }
        .btn-spotify { background-color: #1db954; color: black; font-weight: bold; border-radius: 20px; }
        .nav-link { color: white !important; font-weight: 500; }
        .nav-link.active { text-decoration: underline; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/html-midi-player@latest/dist/midi-player.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold text-black" href="index.php">Lab 8: Audio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="classical.php">Klasyczna</a></li>
                <li class="nav-item"><a class="nav-link" href="rock.php">Rock</a></li>
                <li class="nav-item"><a class="nav-link" href="audacity.php">Audacity</a></li>
                <li class="nav-item"><a class="nav-link" href="mixer.php">Mikser</a></li>
                <li class="nav-item"><a class="nav-link" href="piano.php">Pianino</a></li>
                <li class="nav-item ms-3"><a class="btn btn-sm btn-dark" href="../index.php">Wróć do menu</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">