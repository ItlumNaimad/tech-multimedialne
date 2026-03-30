<?php
/*******************************************************************************
* TTFontFile class                                                             *
*******************************************************************************/
define("_TTF_MAC_HEADER", false);
define("GF_WORDS",(1 << 0));
define("GF_SCALE",(1 << 3));
define("GF_MORE",(1 << 5));
define("GF_XYSCALE",(1 << 6));
define("GF_TWOBYTWO",(1 << 7));

class TTFontFile {
    public $charWidths;
    public $defaultWidth;
    public $numTables;
    public $tables;
    public $otables;
    public $filename;
    public $fh;
    public $ascent;
    public $descent;
    public $name;
    public $fullName;
    public $unitsPerEm;
    public $bbox;
    public $capHeight;
    public $stemV;
    public $italicAngle;
    public $flags;
    public $underlinePosition;
    public $underlineThickness;
    public $maxUni;
    public $_pos;
    public $codeToGlyph;

    function getMetrics($file) {
        $this->filename = $file;
        $this->fh = fopen($file,'rb') or die('Can\'t open file ' . $file);
        $this->_pos = 0;
        $this->charWidths = '';
        $this->tables = array();
        $this->otables = array();
        $version = $this->read_ulong();
        $this->readTableDirectory();
        $this->extractInfo();
        fclose($this->fh);
    }

    function readTableDirectory() {
        $this->numTables = $this->read_ushort();
        $this->skip(6);
        for ($i=0;$i<$this->numTables;$i++) {
            $record = array();
            $record['tag'] = $this->read_tag();
            $record['checksum'] = array($this->read_ushort(),$this->read_ushort());
            $record['offset'] = $this->read_ulong();
            $record['length'] = $this->read_ulong();
            $this->tables[$record['tag']] = $record;
        }
    }

    function read_tag() { $this->_pos += 4; return fread($this->fh,4); }
    function read_short() { $this->_pos += 2; $s = fread($this->fh,2); $a = (ord($s[0])<<8)+ord($s[1]); if ($a & (1 << 15)) $a -= (1 << 16); return $a; }
    function read_ushort() { $this->_pos += 2; $s = fread($this->fh,2); return (ord($s[0])<<8)+ord($s[1]); }
    function read_ulong() { $this->_pos += 4; $s = fread($this->fh,4); return (ord($s[0])*16777216)+(ord($s[1])<<16)+(ord($s[2])<<8)+ord($s[3]); }
    function skip($delta) { $this->_pos += $delta; fseek($this->fh,$this->_pos); }
    function seek($pos) { $this->_pos = $pos; fseek($this->fh,$this->_pos); }
    function seek_table($tag) { $this->seek($this->tables[$tag]['offset']); return $this->_pos; }

    function extractInfo() {
        $this->seek_table("head"); $this->skip(18); $this->unitsPerEm = $this->read_ushort(); $scale = 1000/$this->unitsPerEm;
        $this->skip(16); $xMin = $this->read_short(); $yMin = $this->read_short(); $xMax = $this->read_short(); $yMax = $this->read_short();
        $this->bbox = array($xMin*$scale, $yMin*$scale, $xMax*$scale, $yMax*$scale);
        $this->seek_table("hhea"); $this->skip(4); $this->ascent = $this->read_short()*$scale; $this->descent = $this->read_short()*$scale;
        $this->seek_table("name"); $this->name = "Arial"; $this->fullName = "Arial";
        $this->flags = 32; $this->capHeight = $this->ascent; $this->stemV = 70; $this->italicAngle = 0;
    }

    function makeSubset($file, &$subset) {
        return file_get_contents($file);
    }
}
?>
