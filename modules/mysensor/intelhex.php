<?php

class IntelHex 
{
	public $LastError = "";
	public $FirstAddr = false;
	public $Data = "";
	
	// Constructor
	public function IntelHex(){		
	}
	
	public function Resize($start, $len){
		// Append in head
		if ($start < $this->FirstAddr) {
			$this->Data = str_repeat(chr(0xFF), $this->FirstAddr-$start).$this->Data;
			$this->FirstAddr = $start;
		}
		
		// Append in bottom
		$offset = $start+$len;
		$end = $this->FirstAddr+strlen($this->Data);
		if ($offset >= $end) {
			$this->Data = $this->Data . str_repeat(chr(0xFF), $offset-$end);
		}
	}
	
	public function NormalizePage($pagesize){
		$size = strlen($this->Data);
		$pages = round( $size / $pagesize, 0, PHP_ROUND_HALF_DOWN);
		if ($size % $pagesize != 0) $pages++;		
		$this->Resize($this->FirstAddr, $pages * $pagesize );
	}
	
	// Destructor
	public function Parse($text){
		$lines = preg_split('/\\r\\n?|\\n/', $text);
		
		for ($n=0; $n<count($lines); $n++){
			$line = trim($lines[$n]);
			if ($line == "") continue;
			
			// :
			if (substr($line, 0, 1) != ":") {				
				$this->LastError = "Error start char ':' - $n:1";
				return false;
			}

			$len = hexdec(substr($line, 1, 2));
			$adr = hexdec(substr($line, 3, 4));
			$type = hexdec(substr($line, 7, 2));
			$data = substr($line, 9, $len*2);
			$checksum = hexdec(substr($line, -2));
			
			// Test cs
			$cs = 0;
			for ($i=1; $i<strlen($line);$i+=2){				
				$cs += hexdec(substr($line, $i, 2));
			}
			if (($cs%256) != 0){
				$this->LastError = "Error cs - $n";
				return false;
			}

			// First Address
			if ($this->FirstAddr === false) 
				$this->FirstAddr = $adr;			

			switch ($type) {
				// Data Record
				case 0:
					// Resize data
					$this->Resize($adr, $len);
				
					// Set data
					for ($i=0; $i<$len; $i++){		
						$c = chr(hexdec(substr($data, $i*2, 2)));
						$this->Data[$adr-$this->FirstAddr+$i] = $c;
					}
					
					break;
					
				// End Of File Record
				case 1:
					return true;
					
				default:
					$this->LastError = "Error type of record $type - $n";
					return false;
			}
		}
		
		return true;
	}
}

/* == Test ==
$data = file_get_contents("Lighting.ino.hex");

$cnv = new IntelHex;
$cnv->Parse($data);
$cnv->NormalizePage(32);

$size = strlen($cnv->Data);
echo "Last error: ".$cnv->LastError."<br/>\n";
echo "First adress: ".dechex($cnv->FirstAddr)."<br/>\n";
echo "Size: ".$size."<br/>\n";
//echo "CRC16: ". bin2hex(crc16($cnv->Data))."<br/>\n";
//echo bin2hex($cnv->Data)."<br/>\n";


echo "<code>";
for ($i=0; $i<strlen($cnv->Data); $i++){
	if ($i%16 == 0) echo "<br/>\n";
	$d = ord($cnv->Data[$i]);
	echo sprintf("%02X", $d)." ";
}
echo "</code>";

file_put_contents("Lighting.ino.bin", $cnv->Data);


echo "<br>\nEnd";
*/

?>