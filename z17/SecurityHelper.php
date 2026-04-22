<?php
/**
 * z17/SecurityHelper.php
 * Klasa do obsługi cenzury wulgaryzmów oraz filtrowania niebezpiecznych linków.
 */

class SecurityHelper {
    private static $certDomainsFile = __DIR__ . '/cert_domains.txt';
    private static $certDomainsUrl = 'https://hole.cert.pl/domains/v2/domains.txt';
    private static $cacheTime = 3600; // 1 godzina

    /**
     * Zwraca true, jeśli tekst zawiera wulgaryzmy.
     */
    public static function hasProfanity($text) {
        require __DIR__ . '/bad_words.php'; // Ładuje $wulgaryzmy_pl
        $badWords = $wulgaryzmy_pl ?? [];
        $badWords[] = 'cholera'; // Dodajemy testowe słowo

        $textLower = mb_strtolower($text, 'UTF-8');
        // Usuwamy interpunkcję do ułatwienia sprawdzania
        $textAlphanum = preg_replace('/[^\p{L}\p{N}\s]/u', '', $textLower);
        $words = explode(' ', $textAlphanum);

        foreach ($words as $word) {
            $word = trim($word);
            if (empty($word)) continue;
            if (in_array($word, $badWords)) {
                return true;
            }
        }
        
        // Czasem trzeba też sprawdzić czy nie znajduje się gdzieś w środku (choć to może powodować false-positives np. 'komin' dla min).
        // Bezpieczniej sprawdzać jako pojedyncze słowa.
        return false;
    }

    /**
     * Aktualizuje zasoby domen CERT jeśli cache wygasł, i zwraca tablicę domen.
     */
    private static function getCertDomains() {
        if (!file_exists(self::$certDomainsFile) || (time() - filemtime(self::$certDomainsFile)) > self::$cacheTime) {
            $content = file_get_contents(self::$certDomainsUrl);
            if ($content !== false) {
                file_put_contents(self::$certDomainsFile, $content);
            }
        }

        if (file_exists(self::$certDomainsFile)) {
            $domains = file(self::$certDomainsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            return $domains ? $domains : [];
        }
        return [];
    }

    /**
     * Zastępuje niebezpieczne linki wymaganym komunikatem.
     */
    public static function filterBadLinks($text) {
        $domains = self::getCertDomains();
        if (empty($domains)) {
            return $text; // brak domen do odfiltrowania
        }

        // Proste wyszukiwanie linków w tekście i sprawdzanie domeny
        // Łapiemy wszystkie URLe
        $pattern = '/\b(?:https?:\/\/|www\.)[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/))/i';
        
        $filteredText = preg_replace_callback($pattern, function($matches) use ($domains) {
            $url = $matches[0];
            $host = parse_url((strpos($url, 'http') === 0 ? $url : 'http://' . $url), PHP_URL_HOST);
            if ($host) {
                $host = preg_replace('/^www\./', '', $host);
                if (in_array($host, $domains)) {
                    $date = date('Y-m-d H:i');
                    return $date . " Usunięto niebezpieczny link";
                }
            }
            return $url; // Zwracamy oryginał jeśli jest bezpieczny
        }, $text);

        return $filteredText;
    }
}
?>
