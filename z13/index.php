<?php
/**
 * Plik: index.php
 * Cel: Główny panel pracownika aplikacji ToDo z analityką, modyfikacją i monitami.
 */
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: pages/logowanie.php"); 
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// --- HANDLERY POST (DODAWANIE/USUWANIE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add_task') {
        $nazwa_zadania = $_POST['nazwa_zadania'] ?? '';
        if (!empty($nazwa_zadania)) {
            $stmt = $pdo->prepare("INSERT INTO zadanie (idp, nazwa_zadania) VALUES (:idp, :nazwa)");
            $stmt->execute(['idp' => $user_id, 'nazwa' => $nazwa_zadania]);
        }
    } elseif ($action === 'add_subtask') {
        $idz = $_POST['idz'] ?? '';
        $idp_worker = $_POST['idp_worker'] ?? '';
        $nazwa_podzadania = $_POST['nazwa_podzadania'] ?? '';
        if (!empty($idz) && !empty($idp_worker) && !empty($nazwa_podzadania)) {
            $stmt = $pdo->prepare("INSERT INTO podzadanie (idz, idp, nazwa_podzadania, stan) VALUES (:idz, :idp, :nazwa, 0)");
            $stmt->execute(['idz' => $idz, 'idp' => $idp_worker, 'nazwa' => $nazwa_podzadania]);
        }
    } elseif ($action === 'delete_task') {
        $idz = $_POST['idz'] ?? '';
        // Weryfikacja czy user jest PM-em tego zadania
        $stmt = $pdo->prepare("DELETE FROM zadanie WHERE idz = :idz AND idp = :user_id");
        $stmt->execute(['idz' => $idz, 'user_id' => $user_id]);
    } elseif ($action === 'delete_subtask') {
        $idpz = $_POST['idpz'] ?? '';
        // Weryfikacja przez PMa zadania
        $stmt = $pdo->prepare("DELETE pz FROM podzadanie pz 
                               JOIN zadanie z ON pz.idz = z.idz 
                               WHERE pz.idpz = :idpz AND z.idp = :user_id");
        $stmt->execute(['idpz' => $idpz, 'user_id' => $user_id]);
    } elseif ($action === 'mark_read') {
        $idm = $_POST['idm'] ?? '';
        $stmt = $pdo->prepare("UPDATE monit SET is_read = 1 WHERE idm = :idm AND idp_to = :user_id");
        $stmt->execute(['idm' => $idm, 'user_id' => $user_id]);
    }

    header("Location: index.php");
    exit();
}

try {
    // 0. Pobranie nieprzeczytanych monitów dla zalogowanego
    $stmt_monits = $pdo->prepare("SELECT m.*, z.nazwa_zadania, pz.nazwa_podzadania 
                                   FROM monit m 
                                   JOIN podzadanie pz ON m.idpz = pz.idpz 
                                   JOIN zadanie z ON pz.idz = z.idz 
                                   WHERE m.idp_to = :idp AND m.is_read = 0");
    $stmt_monits->execute(['idp' => $user_id]);
    $my_monits = $stmt_monits->fetchAll();

    // 1. Obsługa przełączania/filtrowania zadań dla pracownika (nowa funkcjonalność)
    $stmt_assigned_tasks = $pdo->prepare("SELECT DISTINCT z.idz, z.nazwa_zadania FROM zadanie z JOIN podzadanie pz ON z.idz = pz.idz WHERE pz.idp = :idp");
    $stmt_assigned_tasks->execute(['idp' => $user_id]);
    $assigned_tasks = $stmt_assigned_tasks->fetchAll();

    $idz_filter = $_GET['idz_filter'] ?? null;
    $sql_my_subtasks = "SELECT pz.*, z.nazwa_zadania, pm.login AS pm_login 
                        FROM podzadanie pz 
                        JOIN zadanie z ON pz.idz = z.idz 
                        JOIN pracownik pm ON z.idp = pm.idp 
                        WHERE pz.idp = :idp";
    if ($idz_filter) $sql_my_subtasks .= " AND z.idz = :idz";
    
    $stmt_my_subtasks = $pdo->prepare($sql_my_subtasks);
    $params = ['idp' => $user_id];
    if ($idz_filter) $params['idz'] = $idz_filter;
    $stmt_my_subtasks->execute($params);
    $my_subtasks = $stmt_my_subtasks->fetchAll();

    // 2. Twoje zadania (Project Manager)
    $stmt_my_tasks = $pdo->prepare("SELECT * FROM zadanie WHERE idp = :idp");
    $stmt_my_tasks->execute(['idp' => $user_id]);
    $my_tasks_raw = $stmt_my_tasks->fetchAll();

    $tasks_with_subtasks = [];
    foreach ($my_tasks_raw as $task) {
        $stmt_sub = $pdo->prepare("SELECT pz.*, p.login AS worker_login 
                                   FROM podzadanie pz 
                                   JOIN pracownik p ON pz.idp = p.idp 
                                   WHERE pz.idz = :idz");
        $stmt_sub->execute(['idz' => $task['idz']]);
        $subtasks = $stmt_sub->fetchAll();
        
        // Obliczanie średniej realizacji zadania (punkt 10a)
        $avg_progress = 0;
        if (count($subtasks) > 0) {
            $sum = array_sum(array_column($subtasks, 'stan'));
            $avg_progress = round($sum / count($subtasks));
        }
        
        $task['subtasks'] = $subtasks;
        $task['avg_progress'] = $avg_progress;
        $tasks_with_subtasks[] = $task;
    }

    $stmt_workers = $pdo->query("SELECT idp, login FROM pracownik ORDER BY login");
    $workers = $stmt_workers->fetchAll();

} catch (PDOException $e) {
    die("Błąd bazy danych: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Pracownika - ToDo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stan-0 { color: red; }
        .stan-1-99 { color: black; }
        .stan-100 { color: green; }
        .progress-avg { width: 100px; display: inline-block; vertical-align: middle; margin-right: 10px; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">ToDo System</a>
        <div class="ms-auto text-white">
            Witaj, <span class="fw-bold"><?= htmlspecialchars($username) ?></span>
            <?php if ($username === 'admin'): ?>
                <a href="admin.php" class="btn btn-outline-light btn-sm ms-3">Panel Admina</a>
            <?php endif; ?>
            <a href="database/logout_handler.php" class="btn btn-danger btn-sm ms-2">Wyloguj</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    
    <!-- SEKCOJA: MONITY (Powiadomienia) -->
    <?php foreach ($my_monits as $monit): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
        <strong>MONIT!</strong> PM wysłał przypomnienie do podzadania 
        <strong>"<?= htmlspecialchars($monit['nazwa_podzadania']) ?>"</strong> 
        w projekcie <strong>"<?= htmlspecialchars($monit['nazwa_zadania']) ?>"</strong>.
        <form method="POST" class="d-inline ms-3">
            <input type="hidden" name="action" value="mark_read">
            <input type="hidden" name="idm" value="<?= $monit['idm'] ?>">
            <button type="submit" class="btn btn-sm btn-outline-secondary">Oznacz jako przeczytane</button>
        </form>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endforeach; ?>

    <!-- SEKCOJA: TWOJE PODZADANIA (Wykonawca) -->
    <div class="card mb-4 shadow-sm border-primary">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Twoje podzadania <small class="text-muted">(Jesteś wykonawcą)</small></h4>
            <div class="d-flex align-items-center">
                <label class="me-2 small fw-bold">Pokaż projekt:</label>
                <select class="form-select form-select-sm" style="width: 200px;" onchange="location.href='index.php?idz_filter='+this.value">
                    <option value="">-- Wszystkie --</option>
                    <?php foreach ($assigned_tasks as $at): ?>
                        <option value="<?= $at['idz'] ?>" <?= $idz_filter == $at['idz'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($at['nazwa_zadania']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Zadanie (Manager)</th>
                            <th>Nazwa Podzadania</th>
                            <th style="width: 200px;">Postęp prac</th>
                            <th style="width: 60px;">Stan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($my_subtasks as $sub): 
                            $stan_class = 'stan-1-99';
                            if ($sub['stan'] == 0) $stan_class = 'stan-0';
                            elseif ($sub['stan'] == 100) $stan_class = 'stan-100';
                        ?>
                        <tr class="<?= $stan_class ?>">
                            <td><?= htmlspecialchars($sub['nazwa_zadania']) ?> (<?= htmlspecialchars($sub['pm_login']) ?>)</td>
                            <td><?= htmlspecialchars($sub['nazwa_podzadania']) ?></td>
                            <td>
                                <input type="range" class="form-range update-stan" 
                                       data-idpz="<?= $sub['idpz'] ?>" 
                                       data-idz="<?= $sub['idz'] ?>"
                                       min="0" max="100" 
                                       value="<?= $sub['stan'] ?>">
                            </td>
                            <td>
                                <span class="fw-bold stan-val-label" data-idpz="<?= $sub['idpz'] ?>"><?= $sub['stan'] ?>%</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($my_subtasks)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Brak przypisanych podzadań.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SEKCJA: TWOJE ZADANIA (Project Manager) -->
    <div class="card mb-4 shadow-sm border-dark">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Twoje zadania <small class="text-light">(Jesteś Project Managerem)</small></h4>
        </div>
        <div class="card-body">
            <?php foreach ($tasks_with_subtasks as $task): 
                $avg_class = 'stan-1-99';
                if ($task['avg_progress'] == 0) $avg_class = 'stan-0';
                elseif ($task['avg_progress'] == 100) $avg_class = 'stan-100';
            ?>
                <div class="mb-4 p-3 border rounded bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h5 id="task-title-<?= $task['idz'] ?>" class="fw-bold mb-0 <?= $avg_class ?>">
                            <?= htmlspecialchars($task['nazwa_zadania']) ?> 
                            <span id="task-avg-<?= $task['idz'] ?>" class="badge bg-secondary ms-2"><?= $task['avg_progress'] ?>%</span>
                        </h5>
                        <form method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć całe zadanie?')">
                            <input type="hidden" name="action" value="delete_task">
                            <input type="hidden" name="idz" value="<?= $task['idz'] ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">Usuń Zadanie</button>
                        </form>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Podzadanie</th>
                                    <th>Wykonawca</th>
                                    <th style="width: 200px;">Zmiana stanu</th>
                                    <th style="width: 60px;">Stan</th>
                                    <th style="width: 250px;">Opcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($task['subtasks'] as $sub): ?>
                                <tr>
                                    <td><?= htmlspecialchars($sub['nazwa_podzadania']) ?></td>
                                    <td><?= htmlspecialchars($sub['worker_login']) ?></td>
                                    <td>
                                        <input type="range" class="form-range update-stan" 
                                               data-idpz="<?= $sub['idpz'] ?>" 
                                               data-idz="<?= $task['idz'] ?>"
                                               min="0" max="100" 
                                               value="<?= $sub['stan'] ?>">
                                    </td>
                                    <td>
                                        <span id="stan-val-<?= $sub['idpz'] ?>" class="fw-bold"><?= $sub['stan'] ?>%</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-outline-warning btn-sm send-monit" 
                                                    data-idpz="<?= $sub['idpz'] ?>" 
                                                    data-worker="<?= $sub['idp'] ?>">
                                                Wyślij Monit
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno usunąć podzadanie?')">
                                                <input type="hidden" name="action" value="delete_subtask">
                                                <input type="hidden" name="idpz" value="<?= $sub['idpz'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Usuń</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- FORMULARZE DODAWANIA -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white"><h5>Nowe zadanie</h5></div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add_task">
                        <input type="text" name="nazwa_zadania" class="form-control mb-3" required placeholder="Nazwa zadania...">
                        <button type="submit" class="btn btn-success w-100">Dodaj Zadanie</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white"><h5>Nowe podzadanie</h5></div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add_subtask">
                        <select name="idz" class="form-select mb-2" required>
                            <option value="" disabled selected>-- Wybierz zadanie --</option>
                            <?php foreach ($my_tasks_raw as $t): ?>
                                <option value="<?= $t['idz'] ?>"><?= htmlspecialchars($t['nazwa_zadania']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="nazwa_podzadania" class="form-control mb-2" required placeholder="Nazwa podzadania...">
                        <select name="idp_worker" class="form-select mb-2" required>
                            <option value="" disabled selected>-- Wykonawca --</option>
                            <?php foreach ($workers as $w): ?>
                                <option value="<?= $w['idp'] ?>"><?= htmlspecialchars($w['login']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-info w-100 text-white">Dodaj Podzadanie</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// AJAX dla aktualizacji stanu
document.querySelectorAll('.update-stan').forEach(slider => {
    slider.addEventListener('change', function() {
        const idpz = this.getAttribute('data-idpz');
        const idz = this.getAttribute('data-idz'); // Pobierz ID zadania nadrzędnego
        const newVal = this.value;
        
        document.getElementById('stan-val-' + idpz).innerText = newVal + '%';

        // Logika przeliczania średniej dla zadania głównego (tylko w panelu PM)
        if (idz) {
            const allSliders = document.querySelectorAll(`.update-stan[data-idz="${idz}"]`);
            let sum = 0;
            allSliders.forEach(s => sum += parseInt(s.value));
            const avg = Math.round(sum / allSliders.length);
            
            const avgBadge = document.getElementById('task-avg-' + idz);
            const taskTitle = document.getElementById('task-title-' + idz);
            
            if (avgBadge) {
                avgBadge.innerText = avg + '%';
                
                // Aktualizacja kolorystyki (opcjonalnie)
                taskTitle.classList.remove('stan-0', 'stan-1-99', 'stan-100');
                if (avg == 0) taskTitle.classList.add('stan-0');
                else if (avg == 100) taskTitle.classList.add('stan-100');
                else taskTitle.classList.add('stan-1-99');
            }
        }

        fetch('api_update_stan.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `idpz=${idpz}&stan=${newVal}`
        });
    });
    slider.addEventListener('input', function() {
        document.getElementById('stan-val-' + this.getAttribute('data-idpz')).innerText = this.value + '%';
    });
});

// AJAX dla Monitów
document.querySelectorAll('.send-monit').forEach(btn => {
    btn.addEventListener('click', function() {
        const idpz = this.getAttribute('data-idpz');
        const idp_to = this.getAttribute('data-worker');
        
        btn.disabled = true;
        btn.innerText = 'Wysyłanie...';

        fetch('api_send_monit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `idpz=${idpz}&idp_to=${idp_to}&message=PM prosi o aktualizację statusu podzadania!`
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                btn.classList.replace('btn-outline-warning', 'btn-success');
                btn.innerText = 'Wysłano!';
            } else {
                alert('Błąd: ' + data.message);
                btn.disabled = false;
                btn.innerText = 'Wyślij Monit';
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
