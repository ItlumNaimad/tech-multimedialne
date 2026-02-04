<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Face SVG - Lab 11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="face.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Powrót do menu</a>
    </div>
</nav>

<div class="container">
    <h3>Osadzanie SVG na różne sposoby</h3>
    <hr>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card p-2">
                <h6>1. img src</h6>
                <img src="face.svg" alt="face" width="45">
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card p-2">
                <h6>2. php file_get_contents</h6>
                <?php echo file_get_contents("face.svg"); ?>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card p-2">
                <h6>3. php readfile</h6>
                <?php readfile("face.svg"); ?>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card p-2">
                <h6>4. object</h6>
                <object type="image/svg+xml" data="face.svg" width="45" height="65"></object>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card p-2">
                <h6>5. iframe</h6>
                <iframe src="face.svg" scrolling="no" style="border:1px solid black; overflow:hidden; height:70px; width:50px;"></iframe>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>