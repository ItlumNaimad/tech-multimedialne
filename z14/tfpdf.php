<?php
/*******************************************************************************
* tFPDF (Lekka wersja z obsługą UTF-8)                                         *
*******************************************************************************/
require_once('fpdf.php');

class tFPDF extends FPDF {
    protected $unifontsubset = true;

    function AddFont($family, $style='', $file='', $uni=false) {
        if(!$uni) return parent::AddFont($family, $style, $file);
        $family = strtolower($family);
        $style = strtoupper($style);
        $fontkey = $family.$style;
        if(isset($this->fonts[$fontkey])) return;
        
        // W tej wersji używamy uproszczonego mechanizmu osadzania czcionek TTF
        $this->fonts[$fontkey] = [
            'i' => count($this->fonts) + 1,
            'type' => 'TTF',
            'name' => $family,
            'desc' => ['Ascent'=>1000,'Descent'=>-200,'CapHeight'=>1000,'Flags'=>32,'FontBBox'=>'[-500 -200 1200 1000]','ItalicAngle'=>0,'StemV'=>70,'MissingWidth'=>500],
            'up' => -100, 'ut' => 50, 'cw' => array_fill(0,256,600), 'enc' => '', 'file' => $file
        ];
    }

    function UTF8ToUTF16BE($s) {
        $res = "\xFE\xFF";
        $nb = strlen($s);
        $i = 0;
        while($i < $nb) {
            $c1 = ord($s[$i++]);
            if($c1 >= 224) {
                $c2 = ord($s[$i++]); $c3 = ord($s[$i++]);
                $res .= chr((($c1 & 0x0F) << 4) | (($c2 & 0x3C) >> 2));
                $res .= chr((($c2 & 0x03) << 6) | ($c3 & 0x3F));
            } elseif($c1 >= 192) {
                $c2 = ord($s[$i++]);
                $res .= chr(($c1 & 0x1C) >> 2);
                $res .= chr((($c1 & 0x03) << 6) | ($c2 & 0x3F));
            } else {
                $res .= "\x00".chr($c1);
            }
        }
        return $res;
    }

    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
        if($this->unifontsubset) $txt = $this->UTF8ToUTF16BE($txt);
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }
    
    function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false) {
        if($this->unifontsubset) $txt = $this->UTF8ToUTF16BE($txt);
        parent::MultiCell($w, $h, $txt, $border, $align, $fill);
    }
}
?>
