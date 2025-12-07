<?php require_once 'database/database_z6a.php';
// Pobieramy gatunki do selecta
$stmt = $pdo->query("SELECT * FROM musictype");
$types = $stmt->fetchAll();
?>

<div class="max-w-md mx-auto bg-gray-800 p-8 rounded-xl shadow-lg mt-10">
    <h2 class="text-2xl font-bold mb-6 text-center">Dodaj nową piosenkę</h2>

    <form action="scripts/upload_music.php" method="POST" enctype="multipart/form-data" class="space-y-4">

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-1">Tytuł</label>
            <input type="text" name="title" required class="w-full bg-gray-700 border-none rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-1">Wykonawca</label>
            <input type="text" name="musician" required class="w-full bg-gray-700 border-none rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-1">Gatunek</label>
            <select name="idmt" class="w-full bg-gray-700 border-none rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-green-500 outline-none">
                <?php foreach($types as $type): ?>
                    <option value="<?= $type['idmt'] ?>"><?= $type['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-1">Plik MP3</label>
            <input type="file" name="music_file" accept=".mp3" required class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700">
        </div>

        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-full transition mt-4">
            Wrzuć na serwer
        </button>
    </form>
</div>