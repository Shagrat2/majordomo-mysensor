<?php

require("phpMS.php");

//$qry="1";

// SEARCH RESULTS  
$res=SQLSelect("SELECT ID, TITLE, VER FROM msbins"); // WHERE $qry ORDER BY id");

if ($res[0]['ID']) {  
	$out['BINS']=$res;
}

?>