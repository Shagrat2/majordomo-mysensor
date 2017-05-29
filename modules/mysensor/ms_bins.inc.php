<?php

require("phpMS.php");

// SEARCH RESULTS  
$res=SQLSelect("SELECT ID, TITLE, VER, CRC, BLOKS FROM msbins");

if ($res[0]['ID']) {  
	$out['BINS']=$res;
}

?>