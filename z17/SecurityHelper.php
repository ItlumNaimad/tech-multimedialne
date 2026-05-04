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
            return $domains ? array_flip($domains) : [];
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

        // Szersze wyszukiwanie linków w tekście i sprawdzanie domeny (obsługuje domeny bez http:// i www.)
        // Grupa 1 przechwytuje samą domenę do sprawdzenia
        $pattern = '/\b(?:https?:\/\/)?([a-z0-9\-]+\.[a-z0-9\-\.]+)[^\s()<>]*/i';
        
        $filteredText = preg_replace_callback($pattern, function($matches) use ($domains) {
            $url = $matches[0];
            $hostCandidate = mb_strtolower($matches[1], 'UTF-8');
            
            // Wyczyść ewentualne www. do sprawdzenia hosta
            $hostCandidate = preg_replace('/^www\./', '', $hostCandidate);
            
            if (isset($domains[$hostCandidate])) {
                // Zwracamy zamazany link w bezpiecznym HTML (ponieważ sam link jest po htmlspecialchars, można go wstawić)
                return '<span class="blurred-link" style="filter: blur(5px); user-select: none; text-decoration: line-through; pointer-events: none;" title="Zablokowany szkodliwy link" data-bs-toggle="tooltip" data-bs-placement="top">' . $url . '</span>';
            }
            
            return $url; // Zwracamy oryginał jeśli jest bezpieczny
        }, $text);

        return $filteredText;
    }
}
?>
