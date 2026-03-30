<?php
/**
 * Ulepszona, minimalistyczna wersja FPDF, generująca poprawną strukturę binarną.
 */
class FPDF {
    protected $page = 0;
    protected $pages = [];
    protected $state = 0;
    protected $offsets = [];
    protected $buffer = "";

    function __construct($orientation='P', $unit='mm', $size='A4') {
        $this->state = 1;
    }

    function AddPage() {
        $this->page++;
        $this->pages[$this->page] = "";
    }

    function SetFont($family, $style='', $size=0) { }
    function SetTextColor($r, $g=null, $b=null) { }
    function Ln($h=null) { $this->pages[$this->page] .= " 0 -15 Td "; }

    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
        $txt = str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $txt);
        $this->pages[$this->page] .= "(" . $txt . ") Tj ";
        if ($ln > 0) $this->Ln();
    }

    function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false) {
        $lines = explode("\n", $txt);
        foreach ($lines as $line) $this->Cell($w, $h, $line, 0, 1);
    }

    protected function _put($s) { $this->buffer .= $s . "\n"; }

    function Output($dest='', $name='', $isUTF8=false) {
        $this->buffer = "%PDF-1.3\n";
        
        // Obiekty
        $this->offsets[1] = strlen($this->buffer);
        $this->_put("1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj");
        
        $this->offsets[2] = strlen($this->buffer);
        $kids = "";
        for($i=1;$i<=$this->page;$i++) $kids .= ($i+2)." 0 R ";
        $this->_put("2 0 obj << /Type /Pages /Kids [$kids] /Count ".$this->page." >> endobj");

        for($i=1;$i<=$this->page;$i++) {
            $content = "BT /F1 12 Tf 50 750 Td " . $this->pages[$i] . " ET";
            
            $this->offsets[$i+2] = strlen($this->buffer);
            $this->_put(($i+2)." 0 obj << /Type /Page /Parent 2 0 R /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >> /Contents ".($i+$this->page+2)." 0 R >> endobj");
            
            $this->offsets[$i+$this->page+2] = strlen($this->buffer);
            $this->_put(($i+$this->page+2)." 0 obj << /Length ".strlen($content)." >> stream\n".$content."\nendstream\nendobj");
        }

        $cross_ref_pos = strlen($this->buffer);
        $this->_put("xref\n0 ".($this->page*2+3)."\n0000000000 65535 f ");
        for($i=1;$i<=$this->page*2+2;$i++) {
            $this->_put(sprintf("%010d 00000 n ", $this->offsets[$i]));
        }
        
        $this->_put("trailer\n<< /Size ".($this->page*2+3)." /Root 1 0 R >>");
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
