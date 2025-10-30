<h3>Część A: Testy diagnostyczne serwera</h3>
<p>Ta strona uruchamia serię testów diagnostycznych z instrukcji. Wiele z nich może nie działać na hostingu współdzielonym, co jest normalne i zgodne z instrukcją.</p>

<hr>

<h4>Zadanie 5: `phpinfo()`</h4>
<p>Wyświetla pełną konfigurację PHP na serwerze. To zawsze działa.</p>
<a href="pages/phpinfo.php" target="_blank" class="btn btn-primary mb-3">Pokaż phpinfo() w nowej karcie</a>

<hr>

<h4>Zadanie 6: `exec('whoami')` </h4>
<p>Próba wyświetlenia nazwy użytkownika, na którym działa proces PHP.</p>
<pre class="bg-dark text-white p-2 rounded">Użytkownik: <?php
    if (function_exists('exec')) {
        echo htmlspecialchars(exec('whoami'));
    } else {
        echo "BŁĄD: Funkcja exec() jest wyłączona.";
    }
    ?></pre>

<hr>

<h4>Zadanie 7: `exec('top')` [cite: 110-112]</h4>
<p>Próba wyświetlenia listy procesów serwera (prawie na pewno zablokowane).</p>
<pre class="bg-dark text-white p-2 rounded"><?php
    if (function_exists('exec')) {
        exec('TERM=xterm /usr/bin/top n 1 bi', $top, $error);
        if ($error) {
            echo "BŁĄD: Nie można wykonać polecenia 'top'.";
        } else {
            echo nl2br(implode("\n", $top));
        }
    } else {
        echo "BŁĄD: Funkcja exec() jest wyłączona.";
    }
    ?></pre>

<hr>

<h4>Zadanie 8: `shell_exec('ls -al')` </h4>
<p>Próba wyświetlenia listy plików w bieżącym katalogu.</p>
<pre class="bg-dark text-white p-2 rounded"><?php
    if (function_exists('shell_exec')) {
        echo htmlspecialchars(shell_exec('ls -al'));
    } else {
        echo "BŁĄD: Funkcja shell_exec() jest wyłączona.";
    }
    ?></pre>

<hr>

<h4>Zadanie 9: Testy DNS i IP</h4>
<p>Wyświetlanie informacji o adresach IP i DNS.</p>
<pre class="bg-dark text-white p-2 rounded"><?php
    echo "<b>Rekordy DNS dla pbs.edu.pl:</b>\n";
    print_r(dns_get_record("pbs.edu.pl"));
    echo "\n<b>IP dla pbs.edu.pl:</b>\n";
    echo gethostbyname('pbs.edu.pl') . "\n";
    echo "\n<b>Twój adres IP (REMOTE_ADDR):</b>\n";
    echo $_SERVER['REMOTE_ADDR'] . "\n";
    echo "\n<b>Hostname dla 8.8.8.8:</b>\n";
    echo gethostbyaddr("8.8.8.8") . "\n";
    echo "\n<b>Twój hostname:</b>\n";
    echo gethostbyaddr($_SERVER['REMOTE_ADDR']) . "\n";
    ?></pre>
