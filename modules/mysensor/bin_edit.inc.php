<?php

require("phpMS.php");
include_once("intelhex.php");
include_once("crcs.php");

$table_name='msbins';
$rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");

if ($this->mode=='update') { 
	$ok=1; 

	global $title;
	$rec['TITLE']=$title;
	if ($rec['TITLE']=='') {
		$out['ERR_TITLE']=1;
		$ok=0;
	}
	
	global $ver;
	$rec['VER']=$ver;
	if ($rec['VER']=='') {
		$out['ERR_VER']=1;
		$ok=0;
	} 
	
	global $bin;
	if ($bin != "") {
		$rec['BIN']=LoadFile($bin);
	}
	
	//=== Test bin
	$parser = new IntelHex();
	if (!$parser->Parse($rec['BIN'])) {
		$ok=0;
		$out['ERR_FILE']=1;		
	}
	if ($parser->FirstAddr != 0){
		$ok=0;
		$out['ERR_FILE']=1;
	}
	$parser->NormalizePage(cNormalPage);

	// Make CRC MySensors
	$crc = crc16($parser->Data);
	
	// Make CRC_OptiBoot	
	//$crc = crcA001($parser->Data);
	//DebMes("CRC: ".$crc);

	// Sent to cashed
	$rec['CRC'] = bin2hex($crc);
	$rec['BLOKS'] =  bin2hex( pack("S", strlen($parser->Data)/16) );
	
	if ($ok) { 
		if ($rec['ID']) { 
			SQLUpdate($table_name, $rec); // update
		} else {
			$rec['ID']=SQLInsert($table_name, $rec); // adding new record
		}
	
	  $out['OK']=1; 
	} else {
      $out['ERR']=1;
    } 
} 

outHash($rec, $out);
	
?>