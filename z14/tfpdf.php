<?php
/*******************************************************************************
* tFPDF (Unicode Edition)                                                      *
*******************************************************************************/
require_once('fpdf.php');

class tFPDF extends FPDF {
    protected $unifontSubset = true;

    function AddFont($family, $style='', $file='', $uni=true) {
        if(!$uni) return parent::AddFont($family, $style, $file);
        $family = strtolower($family);
        $style = strtoupper($style);
        $fontkey = $family.$style;
        if(isset($this->fonts[$fontkey])) return;
        
        $ttffilename = $file;
        require_once('font/unifont/ttfonts.php');
        $ttf = new TTFontFile();
        $ttf->getMetrics($ttffilename);
        
        $this->fonts[$fontkey] = [
            'i' => count($this->fonts) + 1,
            'type' => 'TTF',
            'name' => $ttf->name,
            'desc' => ['Ascent'=>round($ttf->ascent),'Descent'=>round($ttf->descent),'CapHeight'=>round($ttf->capHeight),'Flags'=>32,'FontBBox'=>'['.round($ttf->bbox[0]).' '.round($ttf->bbox[1]).' '.round($ttf->bbox[2]).' '.round($ttf->bbox[3]).']','ItalicAngle'=>0,'StemV'=>70,'MissingWidth'=>500],
            'up' => -100, 'ut' => 50, 'cw' => array_fill(0,256,600), 'enc' => '', 'ttffile' => $ttffilename, 'subset' => range(0,255)
        ];
        $this->FontFiles[$fontkey] = ['length1'=>filesize($ttffilename), 'type'=>'TTF', 'ttffile'=>$ttffilename];
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
        if($this->unifontSubset) $txt = $this->UTF8ToUTF16BE($txt);
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }
    
    function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false) {
        if($this->unifontSubset) $txt = $this->UTF8ToUTF16BE($txt);
        parent::MultiCell($w, $h, $txt, $border, $align, $fill);
    }

    function _putfonts() {
        foreach($this->fonts as $k=>$font) {
            if($font['type']=='TTF') {
                $this->_newobj();
                $this->_out('<</Type /Font /Subtype /TrueType /BaseFont /'.$font['name'].' /Encoding /WinAnsiEncoding /FontDescriptor '.($this->n+1).' 0 R >>');
                $this->_out('endobj');
                $this->_newobj();
                $s = '<</Type /FontDescriptor /FontName /'.$font['name'];
                foreach($font['desc'] as $kd=>$v) $s .= ' /'.$kd.' '.$v;
                $s .= ' /FontFile2 '.($this->n+1).' 0 R >>';
                $this->_out($s);
                $this->_out('endobj');
                $this->_newobj();
                $data = file_get_contents($font['ttffile']);
                $this->_out('<</Length '.strlen($data).' /Length1 '.strlen($data).' >>');
                $this->_putstream($data);
                $this->_out('endobj');
            } else {
                parent::_putfonts();
            }
        }
    }
}
?>
