<?php
require_once 'auth.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'] ?? 1;
    $type = $_POST['type'] ?? 'sprzedaz';
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $area = (float)($_POST['area'] ?? 0);
    $zipcode = trim($_POST['zipcode'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $geoportal_url = trim($_POST['geoportal_url'] ?? '');

    if (empty($title) || empty($price) || empty($zipcode) || empty($city)) {
        $error = "Wypełnij wszystkie wymagane pola (Tytuł, Cena, Kod pocztowy, Miasto).";
    } else {
        // Automatyczne pobieranie współrzędnych (Geocoding - Photon API z bazą OSM)
        $lat = null;
        $lng = null;
        // Optymalizujemy zapytanie omijając kod pocztowy, jeśli to tylko miasto i ulica
        $searchQuery = urlencode($city . ', ' . $address);
        
        $options = [
            "http" => [
                "header" => "User-Agent: PortalOgloszeniowy/1.0\r\n"
            ]
        ];
        $context = stream_context_create($options);
        $photonUrl = "https://photon.komoot.io/api/?q=" . $searchQuery . "&limit=1";
        
        try {
            $response = @file_get_contents($photonUrl, false, $context);
            if ($response) {
                $data = json_decode($response, true);
                if (!empty($data['features']) && isset($data['features'][0]['geometry']['coordinates'])) {
                    $lng = (float)$data['features'][0]['geometry']['coordinates'][0];
                    $lat = (float)$data['features'][0]['geometry']['coordinates'][1];
                }
            }
        } catch (Exception $e) {
            // Ignorujemy błędy połączenia z API
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO offers (user_id, category_id, type, title, description, price, area, zipcode, city, address, lat, lng, geoportal_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                getCurrentUserId(), $category_id, $type, $title, $description, $price, $area, $zipcode, $city, $address, $lat, $lng, $geoportal_url
            ]);
            $offer_id = $pdo->lastInsertId();

            // Obsługa zdjęć
            if (!empty($_FILES['photos']['name'][0])) {
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $files = $_FILES['photos'];
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                        $filename = uniqid('img_') . '.' . $ext;
                        $destination = $uploadDir . $filename;
                        if (move_uploaded_file($files['tmp_name'][$i], $destination)) {
                            $stmtImg = $pdo->prepare("INSERT INTO offer_photos (offer_id, filename) VALUES (?, ?)");
                            $stmtImg->execute([$offer_id, $filename]);
                        }
                    }
                }
            }

            $success = "Ogłoszenie dodane pomyślnie!";
            header("Location: offer.php?id=" . $offer_id);
            exit;
        } catch (PDOException $e) {
            $error = "Błąd bazy danych: " . $e->getMessage();
        }
    }
}

require_once 'header.php';
?>

<div class="card shadow-sm p-4">
    <h2 class="mb-4">Dodaj nowe ogłoszenie</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tytuł ogłoszenia *</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Typ *</label>
                <select name="type" class="form-select">
                    <option value="sprzedaz">Sprzedaż</option>
                    <option value="wynajem">Wynajem</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Kategoria *</label>
                <select name="category_id" class="form-select">
                    <?php
                    $cats = $pdo->query("SELECT * FROM categories")->fetchAll();
                    foreach ($cats as $cat) {
                        echo '<option value="'.$cat['id'].'">'.htmlspecialchars(ucfirst($cat['name'])).'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Opis</label>
            <textarea name="description" class="form-control" rows="4"></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Cena (PLN) *</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Powierzchnia (m²)</label>
                <input type="number" step="0.01" name="area" class="form-control">
            </div>
        </div>

        <h4 class="mt-4 mb-3 border-bottom pb-2">Lokalizacja</h4>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Kod pocztowy *</label>
                <input type="text" name="zipcode" id="zipcode" class="form-control" placeholder="00-000" required>
                <small class="text-muted" id="zipcode-status"></small>
            </div>
            <div class="col-md-5 mb-3">
                <label class="form-label fw-bold">Miasto *</label>
                <input type="text" name="city" id="city" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Adres / Ulica</label>
                <input type="text" name="address" class="form-control">
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-bold">Link do działki z Geoportalu (opcjonalnie)</label>
            <input type="url" name="geoportal_url" class="form-control" placeholder="https://geoportal.gov.pl/...">
        </div>

        <h4 class="mt-4 mb-3 border-bottom pb-2">Zdjęcia</h4>
        <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle-fill"></i> <strong>Wskazówka:</strong> Aby dodać kilka zdjęć do jednej nieruchomości, <strong>przytrzymaj klawisz CTRL</strong> na klawiaturze podczas wybierania plików w oknie.
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold">Wybierz zdjęcia</label>
            <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-save"></i> Dodaj Ogłoszenie</button>
    </form>
</div>

<script>
document.getElementById('zipcode').addEventListener('input', function() {
    let zip = this.value.trim();
    if (zip.match(/^\d{2}-\d{3}$/)) {
        let status = document.getElementById('zipcode-status');
        status.innerText = "Szukam miasta...";
        fetch('https://kodpocztowy.intami.pl/api/' + zip, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            if(!response.ok) throw new Error("Nie znaleziono");
            return response.json();
        })
        .then(data => {
            if (data && data.length > 0) {
                document.getElementById('city').value = data[0].miejscowosc;
                status.innerText = "Znaleziono: " + data[0].miejscowosc;
                status.className = "text-success";
            }
        })
        .catch(err => {
            status.innerText = "Nie znaleziono kodu";
            status.className = "text-danger";
        });
    }
});
</script>

<?php require_once 'footer.php'; ?>
