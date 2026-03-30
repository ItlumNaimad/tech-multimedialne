<?php
/**
 * Stabilna, minimalistyczna wersja FPDF z poprawną obsługą tekstu i kolorów.
 */
class FPDF {
    protected $page = 0;
    protected $pages = [];
    protected $offsets = [];
    protected $buffer = "";
    protected $curColor = "0 g";
    protected $y = 750;

    function __construct($orientation='P', $unit='mm', $size='A4') { }

    function AddPage() {
        $this->page++;
        $this->pages[$this->page] = "BT /F1 12 Tf 50 750 Td "; // Start strony i czcionki
        $this->y = 750;
    }

    function SetFont($family, $style='', $size=0) { }

    function SetTextColor($r, $g=0, $b=0) {
        $r /= 255; $g /= 255; $b /= 255;
        $color = sprintf("%.3f %.3f %.3f rg ", $r, $g, $b);
        $this->pages[$this->page] .= "ET " . $color . " BT 0 0 Td ";
    }

    function Ln($h=15) {
        $this->y -= $h;
        $this->pages[$this->page] .= "ET BT 50 " . $this->y . " Td ";
    }

    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
        $txt = str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $txt);
        $this->pages[$this->page] .= "(" . $txt . ") Tj ";
        if ($ln > 0) $this->Ln($h > 0 ? $h : 15);
    }

    function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false) {
        $lines = explode("\n", $txt);
        foreach ($lines as $line) $this->Cell($w, $h, $line, 0, 1);
    }

    protected function _put($s) { $this->buffer .= $s . "\n"; }

    function Output($dest='', $name='', $isUTF8=false) {
        $this->buffer = "%PDF-1.3\n";
        $this->offsets[1] = strlen($this->buffer);
        $this->_put("1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj");
        $this->offsets[2] = strlen($this->buffer);
        $kids = "";
        for($i=1;$i<=$this->page;$i++) $kids .= ($i+2)." 0 R ";
        $this->_put("2 0 obj << /Type /Pages /Kids [$kids] /Count ".$this->page." >> endobj");
        
        // Czcionka z kodowaniem WinAnsi (najlepsze dla polskich znaków bez ttf)
        $this->offsets[3] = strlen($this->buffer);
        $this->_put("3 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >> endobj");

        for($i=1;$i<=$this->page;$i++) {
            $content = $this->pages[$i] . " ET";
            $this->offsets[$i+3] = strlen($this->buffer);
            $this->_put(($i+3)." 0 obj << /Type /Page /Parent 2 0 R /Resources << /Font << /F1 3 0 R >> >> /Contents ".($i+$this->page+3)." 0 R >> endobj");
            $this->offsets[$i+$this->page+3] = strlen($this->buffer);
            $this->_put(($i+$this->page+3)." 0 obj << /Length ".strlen($content)." >> stream\n".$content."\nendstream\nendobj");
        }

        $cross_ref_pos = strlen($this->buffer);
        $this->_put("xref\n0 ".($this->page*2+4)."\n0000000000 65535 f ");
        for($i=1;$i<=$this->page*2+3;$i++) $this->_put(sprintf("%010d 00000 n ", $this->offsets[$i]));
        $this->_put("trailer\n<< /Size ".($this->page*2+4)." /Root 1 0 R >>");
        $this->_put("startxref\n".$cross_ref_pos."\n%%EOF");

        if($dest == 'F') file_put_contents($name, $this->buffer);
        else {
            header('Content-Type: application/pdf');
            echo $this->buffer;
        }
    }
}
?>
