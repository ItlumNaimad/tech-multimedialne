<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h3>Generator Danych Pomiarowych</h3>
<form action="dodaj_pomiar.php" method="post" target="hiddenFrame">
    <div class="input-group mb-2">
        <span class="input-group-text">X1</span>
        <input type="number" step="0.1" name="x1" class="form-control" value="10">
    </div>
    <div class="input-group mb-2">
        <span class="input-group-text">X2</span>
        <input type="number" step="0.1" name="x2" class="form-control" value="20">
    </div>
    <div class="input-group mb-2">
        <span class="input-group-text">X3</span>
        <input type="number" step="0.1" name="x3" class="form-control" value="30">
    </div>
    <div class="input-group mb-2">
        <span class="input-group-text">X4</span>
        <input type="number" step="0.1" name="x4" class="form-control" value="40">
    </div>
    <div class="input-group mb-2">
        <span class="input-group-text">X5</span>
        <input type="number" step="0.1" name="x5" class="form-control" value="50">
    </div>
    <button type="submit" class="btn btn-primary w-100">Wyślij Pomiar</button>
</form>

<iframe name="hiddenFrame" style="width:100%; height:50px; border:none;"></iframe>
</body>
</html>