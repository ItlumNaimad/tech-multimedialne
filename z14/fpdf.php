<?php
/**
 * Minimalna, funkcjonalna klasa FPDF (tylko do celów demonstracyjnych),
 * aby zapobiec błędom "0 stron" i umożliwić otwarcie pliku.
 * UWAGA: Dla pełnej funkcjonalności zaleca się pobranie oficjalnej biblioteki ze strony fpdf.org.
 */
class FPDF {
    protected $buffer = "";
    protected $page = 0;
    protected $pages = [];
    protected $state = 0;
    protected $CurrentFont = ['family' => 'arial', 'style' => '', 'size' => 12];

    function __construct($orientation='P', $unit='mm', $size='A4') {
        $this->state = 1;
    }

    function AddPage() {
        $this->page++;
        $this->pages[$this->page] = "";
    }

    function SetFont($family, $style='', $size=0) {
        $this->CurrentFont = ['family' => strtolower($family), 'style' => strtoupper($style), 'size' => $size];
    }

    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
        $this->pages[$this->page] .= " (".$txt.") Tj ";
    }

    function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false) {
        $this->pages[$this->page] .= " (".$txt.") Tj ";
    }

    function Ln($h=null) { }
    function SetTextColor($r, $g=null, $b=null) { }

    function Output($dest='', $name='', $isUTF8=false) {
        // Bardzo uproszczona struktura PDF, aby czytniki go rozpoznały
        $pdf = "%PDF-1.3\n";
        $pdf .= "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n";
        $pdf .= "2 0 obj << /Type /Pages /Kids [";
        for($i=1;$i<=$this->page;$i++) $pdf .= ($i+2)." 0 R ";
        $pdf .= "] /Count ".$this->page." >> endobj\n";
        
        for($i=1;$i<=$this->page;$i++) {
            $content = "BT /F1 12 Tf 100 700 Td ".$this->pages[$i]." ET";
            $pdf .= ($i+2)." 0 obj << /Type /Page /Parent 2 0 R /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >> /Contents ".($i+$this->page+2)." 0 R >> endobj\n";
            $pdf .= ($i+$this->page+2)." 0 obj << /Length ".strlen($content)." >> stream\n".$content."\nendstream\nendobj\n";
        }
        $pdf .= "%%EOF";

        if($dest == 'F') {
            file_put_contents($name, $pdf);
        } else {
            header('Content-Type: application/pdf');
            echo $pdf;
        }
    }
}
?>
