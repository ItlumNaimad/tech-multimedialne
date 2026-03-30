<?php
/*******************************************************************************
* tFPDF (Unicode Edition)                                                      *
*******************************************************************************/
require_once('fpdf.php');

class tFPDF extends FPDF {
    protected $unifontsubset = true;

    function AddFont($family, $style='', $file='', $uni=true) {
        if(!$uni) return parent::AddFont($family, $style, $file);
        $family = strtolower($family);
        $style = strtoupper($style);
        $fontkey = $family.$style;
        if(isset($this->fonts[$fontkey])) return;
        
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
        $nb = strlen((string)$s);
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

    function Text($x, $y, $txt) {
        if($this->unifontsubset) $txt = $this->UTF8ToUTF16BE($txt);
        parent::Text($x, $y, $txt);
    }

    function _putfonts() {
        $nf = $this->n;
        foreach($this->fonts as $k=>$font) {
            if($font['type']=='TTF') {
                $this->_newobj();
                $this->_out('<</Type /Font');
                $this->_out('/BaseFont /'.$font['name']);
                $this->_out('/Subtype /TrueType');
                $this->_out('/Encoding /Identity-H');
                $this->_out('/FontDescriptor '.($this->n+1).' 0 R');
                $this->_out('>>');
                $this->_out('endobj');
                
                $this->_newobj();
                $this->_out('<</Type /FontDescriptor');
                $this->_out('/FontName /'.$font['name']);
                $this->_out('/Flags 32');
                $this->_out('/FontBBox [-500 -200 1200 1000]');
                $this->_out('/ItalicAngle 0');
                $this->_out('/Ascent 1000');
                $this->_out('/Descent -200');
                $this->_out('/CapHeight 1000');
                $this->_out('/StemV 70');
                $this->_out('/FontFile2 '.($this->n+1).' 0 R');
                $this->_out('>>');
                $this->_out('endobj');
                
                $this->_newobj();
                $s = file_get_contents($font['file']);
                $this->_out('<</Length '.strlen($s));
                $this->_out('/Length1 '.strlen($s));
                $this->_out('>>');
                $this->_putstream($s);
                $this->_out('endobj');
            } else {
                $this->fonts[$k]['n'] = $this->n + 1;
                parent::_putfonts();
            }
        }
    }
}
?>
