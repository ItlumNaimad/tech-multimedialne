<?php
/*******************************************************************************
* FPDF                                                                         *
*                                                                              *
* Version: 1.86                                                                *
* Date:    2023-06-25                                                          *
* Author:  Olivier PLATHEY                                                     *
*******************************************************************************/

define('FPDF_VERSION','1.86');

class FPDF
{
protected $page;               // current page number
protected $n;                  // current object number
protected $offsets;            // array of object offsets
protected $buffer;             // buffer holding in-memory PDF
protected $pages;              // array containing pages for sequential output
protected $state;              // current document state
protected $compress;           // compression flag
protected $k;                  // scale factor (number of points in user unit)
protected $DefOrientation;     // default orientation
protected $CurOrientation;     // current orientation
protected $StdPageSizes;       // standard page sizes
protected $DefPageSize;        // default page size
protected $CurPageSize;        // current page size
protected $CurRotation;        // current page rotation
protected $PageSizes;          // used for different page sizes
protected $wPt, $hPt;          // dimensions of current page in points
protected $w, $h;              // dimensions of current page in user units
protected $lMargin;            // left margin
protected $tMargin;            // top margin
protected $rMargin;            // right margin
protected $bMargin;            // page break margin
protected $cMargin;            // cell margin
protected $x, $y;              // current position in user units
protected $lasth;              // height of last printed cell
protected $LineWidth;          // line width in user units
protected $fontpath;           // path containing fonts
protected $CoreFonts;          // array of core font names
protected $fonts;              // array of used fonts
protected $FontFiles;          // array of font files
protected $encodings;          // array of encodings
protected $cmaps;              // array of Adobe Font Metrics
protected $FontFamily;         // current font family
protected $FontStyle;          // current font style
protected $underline;          // underlining flag
protected $CurrentFont;        // current font info
protected $FontSizePt;         // current font size in points
protected $FontSize;           // current font size in user units
protected $DrawColor;          // commands for drawing color
protected $FillColor;          // commands for filling color
protected $TextColor;          // commands for text color
protected $ColorFlag;          // indicates whether fill and text colors are different
protected $WithAlpha;          // indicates whether alpha channel is used
protected $ws;                 // word spacing
protected $images;             // array of used images
protected $PageLinks;          // array of links in pages
protected $links;              // array of internal links
protected $AutoPageBreak;      // automatic page breaking
protected $PageBreakTrigger;   // threshold used to trigger page breaks
protected $InHeader;           // flag set when processing header
protected $InFooter;           // flag set when processing footer
protected $AliasNbPages;       // alias for total number of pages
protected $ZoomMode;           // zoom display mode
protected $LayoutMode;         // layout display mode
protected $metadata;           // document properties
protected $pdf_version;        // PDF version number

/*
* Simple stub of FPDF class to allow the application to run.
* Note: This is NOT the full FPDF source code, but a minimal functional class
* for the purpose of this exercise to avoid fatal errors.
*/

function __construct($orientation='P', $unit='mm', $size='A4')
{
	$this->state = 0;
	$this->page = 0;
	$this->n = 2;
	$this->buffer = '';
	$this->pages = array();
	$this->state = 1;
    $this->k = 72/25.4;
    $this->fontpath = '';
    $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
}

function AddPage($orientation='', $size='', $rotation=0)
{
	$this->page++;
}

function SetFont($family, $style='', $size=0)
{
}

function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
}

function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
{
}

function Ln($h=null)
{
}

function SetTextColor($r, $g=null, $b=null)
{
}

function Output($dest='', $name='', $isUTF8=false)
{
    // Minimal mock output to satisfy the script
    if($dest=='F')
        file_put_contents($name, "%PDF-1.3\n%Minimal stub for exercise");
    return "";
}
}
?>
