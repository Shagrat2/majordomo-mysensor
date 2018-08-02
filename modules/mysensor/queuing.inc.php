<?php

require_once("phpMS.php");

$res=SQLSelect("SELECT * FROM mssendstack WHERE SENDRX=1 ORDER BY id");

if ($res[0]['ID']) {  
	$total=count($res);
    for($i=0;$i<$total;$i++) {
		$res[$i]['EXPIRE'] = date("Y-m-d H:i:s", $res[$i]['EXPIRE']);
		
		$mtype = $res[$i]['MType'];
		$mssubtype = $res[$i]['SUBTYPE'];
		
		$res[$i]['MType'] = $MSType[$mtype];
		$res[$i]['SUBTYPE'] = SubTypeDecode($mtype, $mssubtype);
	}
	$out['QUEUING']=$res;
}

?>