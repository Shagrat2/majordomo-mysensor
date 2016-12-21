<?php

require("phpMS.php");

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