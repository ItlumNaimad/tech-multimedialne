<?php
/**
 * Super-stabilna wersja FPDF z pozycjonowaniem absolutnym (Tm).
 * Rozwiązuje problem pustych stron i błędnego wyświetlania tekstu.
 */
class FPDF {
    protected $page = 0;
    protected $pages = [];
    protected $offsets = [];
    protected $buffer = "";
    protected $y = 750;
    protected $r = 0, $g = 0, $b = 0;

    function __construct($orientation='P', $unit='mm', $size='A4') { }

    function AddPage() {
        $this->page++;
        $this->pages[$this->page] = "";
        $this->y = 750;
    }

    function SetFont($family, $style='', $size=0) { }

    function SetTextColor($r, $g=0, $b=0) {
        $this->r = $r / 255;
        $this->g = $g / 255;
        $this->b = $b / 255;
    }

    function Ln($h=10) {
        $this->y -= ($h > 0 ? $h : 10);
    }

    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
        $txt = str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $txt);
        // Używamy Tm (Text Matrix) dla pozycjonowania absolutnego (x=50, y=$this->y)
        $color = sprintf("%.3f %.3f %.3f rg ", $this->r, $this->g, $this->b);
        $this->pages[$this->page] .= "BT /F1 12 Tf $color 1 0 0 1 50 " . $this->y . " Tm (" . $txt . ") Tj ET ";
        if ($ln > 0) $this->Ln($h > 0 ? $h : 7);
    }

    function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false) {
        $lines = explode("\n", $txt);
        foreach ($lines as $line) {
            $this->Cell($w, $h, $line, 0, 1);
        }
    }

    protected function _put($s) { $this->buffer .= $s . "\n"; }

    function Output($dest='', $name='', $isUTF8=false) {
        $this->buffer = "%PDF-1.3\n";
        
        // 1. Catalog
        $this->offsets[1] = strlen($this->buffer);
        $this->_put("1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj");
        
        // 2. Pages tree
        $this->offsets[2] = strlen($this->buffer);
        $kids = "";
        for($i=1;$i<=$this->page;$i++) $kids .= ($i+2)." 0 R ";
        $this->_put("2 0 obj << /Type /Pages /Kids [$kids] /Count ".$this->page." >> endobj");
        
        // 3. Font (Standard Helvetica z WinAnsi dla polskich znaków)
        $this->offsets[3] = strlen($this->buffer);
        $this->_put("3 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >> endobj");

        for($i=1;$i<=$this->page;$i++) {
            $content = $this->pages[$i];
            
            // Page object
            $this->offsets[$i+3] = strlen($this->buffer);
            $this->_put(($i+3)." 0 obj << /Type /Page /Parent 2 0 R /Resources << /Font << /F1 3 0 R >> >> /Contents ".($i+$this->page+3)." 0 R >> endobj");
            
            // Content stream
            $this->offsets[$i+$this->page+3] = strlen($this->buffer);
            $this->_put(($i+$this->page+3)." 0 obj << /Length ".strlen($content)." >> stream\n".$content."\nendstream\nendobj");
        }

        // Cross-reference table
        $cross_ref_pos = strlen($this->buffer);
        $this->_put("xref\n0 ".($this->page*2+4)."\n0000000000 65535 f ");
        for($i=1;$i<=$this->page*2+3;$i++) {
            $this->_put(sprintf("%010d 00000 n ", $this->offsets[$i]));
        }
        
        // Trailer
        $this->_put("trailer\n<< /Size ".($this->page*2+4)." /Root 1 0 R >>");
        $this->_put("startxref\n".$cross_ref_pos."\n%%EOF");

        if($dest == 'F') {
            file_put_contents($name, $this->buffer);
        } else {
            header('Content-Type: application/pdf');
            header('Content-Length: ' . strlen($this->buffer));
            echo $this->buffer;
        }
    }
}
?>
