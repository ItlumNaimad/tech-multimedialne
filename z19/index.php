<?php
require_once 'auth.php';
require_once 'db_connect.php';

// Nadanie uprawnień administratora (Debug)
if (isLoggedIn() && isset($_POST['action']) && $_POST['action'] === 'make_admin') {
    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
    if ($stmt->execute([getCurrentUserId()])) {
        $_SESSION['user_role'] = 'admin';
    }
    header("Location: index.php");
    exit;
}

require_once 'header.php';
?>

<?php if (isLoggedIn() && !isAdmin()): ?>
<div class="alert alert-warning text-center mt-3">
    <strong>Tryb testowy:</strong> Twoje konto nie ma uprawnień administratora. Niektóre funkcje (np. zamykanie ticketów CRM) są zablokowane.
    <form method="post" class="d-inline ms-3">
        <input type="hidden" name="action" value="make_admin">
        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-shield-lock"></i> Nadaj mi uprawnienia Administratora</button>
    </form>
</div>
<?php endif; ?>

<div class="row mb-5">
    <div class="col-md-12 text-center">
        <h1 class="display-4 fw-bold">Zbijanie Bąków S.A.</h1>
        <p class="lead text-muted">Liderzy w profesjonalnym spędzaniu wolnego czasu.</p>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h3 class="card-title text-primary"><i class="bi bi-info-circle"></i> O firmie</h3>
                <p class="card-text mt-3">
                    Nasza firma od stu lat zajmuje się zbijaniem bąków na najwyższym, światowym poziomie. 
                    Posiadamy certyfikaty ISO 9001 w zakresie rzucania grochem o ścianę oraz liczne nagrody 
                    w kategorii "Przesypianie deadlinów". Nasz zespół składa się z wybitnych specjalistów, 
                    którzy wiedzą, jak odpoczywać i nic nie robić z wielką klasą.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h3 class="card-title text-success"><i class="bi bi-card-checklist"></i> Oferta</h3>
                <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item"><strong>Analiza leżenia bykiem:</strong> Kompleksowy audyt 360 stopni.</li>
                    <li class="list-group-item"><strong>Wdrożenia:</strong> Implementacja procedur zbijania bąków w MŚP.</li>
                    <li class="list-group-item"><strong>Szkolenia:</strong> Certyfikowane kursy z patrzenia się w sufit.</li>
                    <li class="list-group-item text-muted"><em>* Wszystkie nasze usługi są w 100% legalne i wolne od wysiłku.</em></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h3>Skorzystaj z naszych wewnętrznych systemów</h3>
                <p>Jako zalogowany użytkownik masz dostęp do modułów firmy:</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
                    <a href="todo.php" class="btn btn-outline-primary"><i class="bi bi-check2-square"></i> System ToDo</a>
                    <a href="elearning.php" class="btn btn-outline-success"><i class="bi bi-mortarboard"></i> e-Learning</a>
                    <a href="crm.php" class="btn btn-outline-danger"><i class="bi bi-headset"></i> CRM (Reklamacje)</a>
                    <a href="forum.php" class="btn btn-outline-info"><i class="bi bi-chat-text"></i> Forum Dyskusyjne</a>
                    <a href="gallery.php" class="btn btn-outline-warning"><i class="bi bi-images"></i> Galeria Zdjęć</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
