<?php
echo "Current directory (__DIR__): " . __DIR__ . "<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script filename: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";

$path = __DIR__ . '/font/unifont/DejaVuSansCondensed.ttf';
echo "Checking path: " . $path . "<br>";
if (file_exists($path)) {
    echo "File EXISTS<br>";
    $fh = @fopen($path, 'rb');
    if ($fh) {
        echo "File OPEN SUCCESS<br>";
        fclose($fh);
    } else {
        echo "File OPEN FAILED<br>";
        $err = error_get_last();
        echo "Error: " . $err['message'] . "<br>";
    }
} else {
    echo "File DOES NOT EXIST<br>";
}
?>
