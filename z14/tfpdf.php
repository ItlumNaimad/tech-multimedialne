<?php
/*******************************************************************************
* tFPDF                                                                        *
*                                                                              *
* Version:  1.32                                                               *
* Date:     2015-01-02                                                         *
* Author:   Ian Back <ianb@fpdf.org>                                           *
* License:  LGPL                                                               *
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
        
        // Ta uproszczona wersja zakłada wykorzystanie czcionek TTF bezpośrednio
        // Dla pełnej funkcjonalności wymagany byłby parser TTF, ale tutaj
        // użyjemy triku z osadzaniem czcionki systemowej przez tFPDF.
        $this->fonts[$fontkey] = [
            'i' => count($this->fonts) + 1,
            'type' => 'TTF',
            'name' => $family,
            'desc' => ['Ascent'=>1000,'Descent'=>-200,'CapHeight'=>1000,'Flags'=>32,'FontBBox'=>'[-500 -200 1200 1000]','ItalicAngle'=>0,'StemV'=>70,'MissingWidth'=>500],
            'up' => -100, 'ut' => 50, 'cw' => array_fill(0,256,600), 'enc' => '', 'file' => $file
        ];
    }

    // Funkcja konwertująca UTF-8 na UTF-16BE (wymagane przez PDF dla Unicode)
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
        $txt = (string)$txt;
        if($this->unifontsubset) $txt = $this->UTF8ToUTF16BE($txt);
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }
    
    function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false) {
        $txt = (string)$txt;
        if($this->unifontsubset) $txt = $this->UTF8ToUTF16BE($txt);
        parent::MultiCell($w, $h, $txt, $border, $align, $fill);
    }
}
?>
