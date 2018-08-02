<?php

require("phpMS.php");

$table_name='msgates';
$rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");

if ($this->mode=='update') { 
	$ok=1; 

	global $title;
	$rec['TITLE']=$title;
	if ($rec['TITLE']=='') {
		$out['ERR_TITLE']=1;
		$ok=0;
	}
	
	global $gtype;
	$rec['GTYPE']=$gtype;
	if ($rec['GTYPE']=='') {
		$out['ERR_GTYPE']=1;
		$ok=0;
	}
	
	global $active;
	if ($active=="") {
		$rec['ACTIVE'] = 0;
	} else {
		$rec['ACTIVE'] = 1;
	}	
    
    global $url;
	$rec['URL']=$url;
	if ($rec['URL']=='') {
		$out['ERR_URL']=1;
		$ok=0;
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