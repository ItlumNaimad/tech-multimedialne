<nav class="bg-black border-b border-gray-800">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="index.php?page=home" class="text-2xl font-bold text-green-500 flex items-center gap-2">
                    <i class="bi bi-spotify"></i> mySpotify
                </a>
                <div class="hidden md:flex gap-4">
                    <a href="index.php?page=home" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Biblioteka</a>
                    <a href="index.php?page=upload" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Dodaj Utwór</a>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-400">Witaj, <?= htmlspecialchars($_SESSION['username'] ?? 'Gość') ?></span>
                <a href="../z2/wyloguj.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-full text-sm font-bold transition">Wyloguj</a>
            </div>
        </div>
    </div>
</nav>