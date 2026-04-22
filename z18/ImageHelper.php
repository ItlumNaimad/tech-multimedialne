<?php
/**
 * z18/ImageHelper.php
 * Klasa do operacji na plikach (znaki wodne, filtry GD)
 */

class ImageHelper {
    
    // Nałożenie znaku wodnego z pliku watermark.png
    public static function applyWatermark($filePath) {
        $watermarkPath = __DIR__ . '/watermark.png';
        if (!file_exists($watermarkPath)) {
            // Generujemy w locie, jesli brakuje
            $w = 400; $h = 100;
            $tmp = imagecreatetruecolor($w, $h);
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            $bg = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
            imagefilledrectangle($tmp, 0, 0, $w, $h, $bg);
            $textColor = imagecolorallocatealpha($tmp, 255, 255, 255, 50); // pólprzezroczysty bialy text
            // Aby tekst byl widoczny powiedzmy uzyjemy najwiekszej wbudowanej czcionki '5'
            imagestring($tmp, 5, 20, 40, 'Copyright-restricted', $textColor);
            imagepng($tmp, $watermarkPath);
            imagedestroy($tmp);
        }

        $img = self::createImageFromFile($filePath);
        if (!$img) return false;

        $watermark = imagecreatefrompng($watermarkPath);
        if (!$watermark) return false;

        $img_w = imagesx($img);
        $img_h = imagesy($img);
        $wtrmrk_w = imagesx($watermark);
        $wtrmrk_h = imagesy($watermark);

        // Skalowanie znaku wodnego, aby pasował (np 30% szerokości zdjęcia)
        $new_wtrmrk_w = $img_w * 0.3;
        $new_wtrmrk_h = ($wtrmrk_h / $wtrmrk_w) * $new_wtrmrk_w;

        $resized_wtrmrk = imagecreatetruecolor($new_wtrmrk_w, $new_wtrmrk_h);
        imagealphablending($resized_wtrmrk, false);
        imagesavealpha($resized_wtrmrk, true);
        $transparent = imagecolorallocatealpha($resized_wtrmrk, 255, 255, 255, 127);
        imagefilledrectangle($resized_wtrmrk, 0, 0, $new_wtrmrk_w, $new_wtrmrk_h, $transparent);
        imagecopyresampled($resized_wtrmrk, $watermark, 0, 0, 0, 0, $new_wtrmrk_w, $new_wtrmrk_h, $wtrmrk_w, $wtrmrk_h);

        // Pozycja: prawy dolny róg
        $dst_x = $img_w - $new_wtrmrk_w - 10;
        $dst_y = $img_h - $new_wtrmrk_h - 10;

        imagecopy($img, $resized_wtrmrk, $dst_x, $dst_y, 0, 0, $new_wtrmrk_w, $new_wtrmrk_h);
        self::saveImageToFile($img, $filePath);

        imagedestroy($img);
        imagedestroy($watermark);
        imagedestroy($resized_wtrmrk);
        
        return true;
    }

    public static function applyFilter($filePath, $filter) {
        $img = self::createImageFromFile($filePath);
        if (!$img) return false;

        if ($filter === 'greyscale') {
            imagefilter($img, IMG_FILTER_GRAYSCALE);
        } elseif ($filter === 'sepia') {
            imagefilter($img, IMG_FILTER_GRAYSCALE);
            imagefilter($img, IMG_FILTER_COLORIZE, 112, 66, 20);
        } elseif ($filter === 'negatyw') {
            imagefilter($img, IMG_FILTER_NEGATE);
        }

        self::saveImageToFile($img, $filePath);
        imagedestroy($img);
        return true;
    }

    private static function createImageFromFile($filePath) {
        $info = getimagesize($filePath);
        if ($info === false) return false;

        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                return imagecreatefrompng($filePath);
            case 'image/gif':
                return imagecreatefromgif($filePath);
            case 'image/webp':
                return imagecreatefromwebp($filePath);
            default:
                return false;
        }
    }

    private static function saveImageToFile($img, $filePath) {
        $info = getimagesize($filePath);
        if ($info === false) return false;
        
        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($img, $filePath, 90);
                break;
            case 'image/png':
                imagepng($img, $filePath);
                break;
            case 'image/gif':
                imagegif($img, $filePath);
                break;
            case 'image/webp':
                imagewebp($img, $filePath, 90);
                break;
        }
    }
}
?>
