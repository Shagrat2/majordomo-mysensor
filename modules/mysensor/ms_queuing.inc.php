<?php

require("phpMS.php");

$res=SQLSelect("SELECT * FROM mssendstack WHERE SENDRX=1 ORDER BY id");

if ($res[0]['ID']) {  
	$total=count($res);
    for($i=0;$i<$total;$i++) {
		$res[$i]['EXPIRE'] = date("Y-m-d H:i:s", $res[$i]['EXPIRE']);
		
		$mstype = $res[$i]['MType'];
		$mssubtype = $res[$i]['SUBTYPE'];
		
		switch ($mstype) {
		  case C_PRESENTATION:
			if ($mssubtype >= count($mysensor_presentation)) 
			  $ret = "-Unknown-";
			else
			  $ret = $mysensor_presentation[$mssubtype][0];
		    break;
		  case C_SET:
			if ($mssubtype >= count($mysensor_property)) 
			  $ret = "-Unknown-";
			else
			  $ret = $mysensor_property[$mssubtype][0];
		    break;
		  case C_REQ:
			if ($mssubtype >= count($mysensor_property)) 
			  $ret = "-Unknown-";
			else
			  $ret = $mysensor_property[$mssubtype][0];
		    break;
		  case C_INTERNAL:
			if ($mssubtype >= count($mysensor_internal)) 
			  $ret = "-Unknown-";
			else
			  $ret = $mysensor_internal[$mssubtype][0];
		    break;
		  case C_STREAM:
			if ($mssubtype >= count($mysensor_stream)) 
			  $ret = "-Unknown-";
			else
			  $ret = $mysensor_stream[$mssubtype][0];
		    break;;
		  default:
			$ret = "-Error-";
		}

		$res[$i]['MType'] = $mysensor_type[$mstype];
		$res[$i]['SUBTYPE'] = $ret;
	}
	$out['QUEUING']=$res;
}

?>

?>