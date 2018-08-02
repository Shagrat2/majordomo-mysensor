<?php

require("phpMS.php");

// SEARCH RESULTS  
global $sortby_mysensor;

if (!$sortby_mysensor) {
  $sortby_mysensor=$session->data['mysensor_sort'];
} else {
  if ($session->data['mysensor_sort']==$sortby_mysensor) {
    if (Is_Integer(strpos($sortby_mysensor, ' DESC'))) {
      $sortby_mysensor=str_replace(' DESC', '', $sortby_mysensor);
    } else {
      $sortby_mysensor=$sortby_mysensor." DESC";
    }
  }
  $session->data['mysensor_sort']=$sortby_mysensor;
}
   
if (!$sortby_mysensor) $sortby_mysensor="ID";
$out['SORTBY']=$sortby_mysensor;
  
$res=SQLSelect("SELECT ID, TITLE, VER, CRC, BLOKS FROM msbins ORDER BY ".$sortby_mysensor);

if ($res[0]['ID']) {  
	$out['BINS']=$res;
}

?>