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
    protected $unifontsubset;

    function AddFont($family, $style='', $file='', $uni=false) {
        if ($uni) {
            $family = strtolower($family);
            $style = strtoupper($style);
            if ($style == 'IB') $style = 'BI';
            $fontkey = $family . $style;
            if (isset($this->fonts[$fontkey])) return;
            $dir = 'font/unifont/';
            $info = include($dir . $file);
            $this->fonts[$fontkey] = $info;
        } else {
            parent::AddFont($family, $style, $file);
        }
    }

    function SetFont($family, $style='', $size=0) {
        parent::SetFont($family, $style, $size);
        $fontkey = $this->FontFamily . $this->FontStyle;
        if (isset($this->fonts[$fontkey]['type']) && $this->fonts[$fontkey]['type'] == 'TTF') {
            $this->unifontsubset = true;
        } else {
            $this->unifontsubset = false;
        }
    }

    // Tu powinny być metody obsługujące UTF-8, ale klasa tFPDF jest duża.
    // Zamiast pisać ją od zera, spróbuję użyć FPDF w wersji, która JUŻ DZIAŁA z UTF-8.
}
?>
