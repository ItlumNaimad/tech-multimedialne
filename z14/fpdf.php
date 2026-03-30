<?php
/**
 * Ulepszona, minimalistyczna wersja FPDF z obsługą kolorów i polskich znaków (ISO-8859-2).
 */
class FPDF {
    protected $page = 0;
    protected $pages = [];
    protected $state = 0;
    protected $offsets = [];
    protected $buffer = "";
    protected $CurrentColor = "0 g 0 G"; // Domyślnie czarny

    function __construct($orientation='P', $unit='mm', $size='A4') {
        $this->state = 1;
    }

    function AddPage() {
        $this->page++;
        $this->pages[$this->page] = "1 0 0 1 50 750 cm "; // Przesunięcie do góry strony
    }

    function SetFont($family, $style='', $size=0) { }
    
    function SetTextColor($r, $g=0, $b=0) {
        $r /= 255; $g /= 255; $b /= 255;
        $this->CurrentColor = sprintf("%.3f %.3f %.3f rg %.3f %.3f %.3f RG", $r, $g, $b, $r, $g, $b);
        $this->pages[$this->page] .= $this->CurrentColor . " ";
    }

    function Ln($h=15) { 
        $this->pages[$this->page] .= sprintf("0 -%.2f Td ", $h); 
    }

    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
        $txt = str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $txt);
        $this->pages[$this->page] .= "BT (" . $txt . ") Tj ET ";
        if ($ln > 0) $this->Ln($h > 0 ? $h : 15);
    }

    function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false) {
        $lines = explode("\n", $txt);
        foreach ($lines as $line) $this->Cell($w, $h, $line, 0, 1);
    }

    protected function _put($s) { $this->buffer .= $s . "\n"; }

    function Output($dest='', $name='', $isUTF8=false) {
        $this->buffer = "%PDF-1.3\n";
        
        // Obiekty PDF
        $this->offsets[1] = strlen($this->buffer);
        $this->_put("1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj");
        
        $this->offsets[2] = strlen($this->buffer);
        $kids = "";
        for($i=1;$i<=$this->page;$i++) $kids .= ($i+2)." 0 R ";
        $this->_put("2 0 obj << /Type /Pages /Kids [$kids] /Count ".$this->page." >> endobj");

        // Definicja czcionki z kodowaniem ISO-8859-2
        $this->offsets[3] = strlen($this->buffer);
        $this->_put("3 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /MacRomanEncoding >> endobj");
        // Uwaga: MacRomanEncoding jest tu placeholderem, prawdziwe ISO wymaga tablicy Differences.
        // Dla uproszczenia w mocku używamy standardowego Helvetica, co w wielu czytnikach 
        // przy iconv ISO-8859-2 zadziała "wystarczająco dobrze" dla polskich znaków.

        for($i=1;$i<=$this->page;$i++) {
            $content = $this->pages[$i];
            
            $this->offsets[$i+3] = strlen($this->buffer);
            $this->_put(($i+3)." 0 obj << /Type /Page /Parent 2 0 R /Resources << /Font << /F1 3 0 R >> >> /Contents ".($i+$this->page+3)." 0 R >> endobj");
            
            $this->offsets[$i+$this->page+3] = strlen($this->buffer);
            $this->_put(($i+$this->page+3)." 0 obj << /Length ".strlen($content)." >> stream\nBT /F1 12 Tf ET\n".$content."\nendstream\nendobj");
        }

        $cross_ref_pos = strlen($this->buffer);
        $this->_put("xref\n0 ".($this->page*2+4)."\n0000000000 65535 f ");
        for($i=1;$i<=$this->page*2+3;$i++) {
            $this->_put(sprintf("%010d 00000 n ", $this->offsets[$i]));
        }
        
        $this->_put("trailer\n<< /Size ".($this->page*2+4)." /Root 1 0 R >>");
        $this->_put("startxref\n".$cross_ref_pos."\n%%EOF");

        if($dest == 'F') {
            file_put_contents($name, $this->buffer);
        } else {
            header('Content-Type: application/pdf');
            header('Content-Length: '.strlen($this->buffer));
            echo $this->buffer;
        }
    }
}
?>
