<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj Animację - Lab 11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Powrót do menu</a>
        <div class="navbar-nav">
            <a class="nav-link" href="scada.php">Wizualizacja</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Dodaj nową kulkę animowaną</h5>
                </div>
                <div class="card-body">
                    <form action="dodaj.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pozycja X0</label>
                                <input type="number" name="x0" class="form-control" value="100" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pozycja Y0</label>
                                <input type="number" name="y0" class="form-control" value="100" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">X Delta (przesunięcie)</label>
                                <input type="number" name="x_delta" class="form-control" value="200" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Y Delta (przesunięcie)</label>
                                <input type="number" name="y_delta" class="form-control" value="0" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Średnica</label>
                                <input type="number" name="diameter" class="form-control" value="20" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Czas trwania (s)</label>
                                <input type="number" step="0.1" name="time" class="form-control" value="2" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Opóźnienie (begin s)</label>
                                <input type="number" step="0.1" name="begin" class="form-control" value="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kolor (np. red, #ff0000)</label>
                                <input type="text" name="color" class="form-control" value="blue" required>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Dodaj do bazy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>