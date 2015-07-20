<?php

require("./lib/mysensor/phpMS.php");

if ($this->owner->name=='panel') {
  $out['CONTROLPANEL']=1;
}

global $nid;

$table_name='msnodes';
$rec=SQLSelectOne("SELECT * FROM $table_name WHERE NID='$nid'");

outHash($rec, $out);

if ($rec['ID']) {
  $presentation=SQLSelect("SELECT * FROM msnodesens WHERE NID='".$rec['NID']."' ORDER BY SID");
}

// Sensors
$sens = array();
foreach ($mysensor_presentation as $k=>$v){
  $sens[] = array('ID'=>$k, 'TITLE'=>$v[0]);
}
$out['SID_OPTIONS']=$sens;

// SubType
$subtype = array();
foreach ($mysensor_property as $k=>$v){
  $subtype[] = array('ID'=>$k, 'TITLE'=>$v[0]);
}
$out['SUBTYPE_OPTIONS']=$subtype;

//print_r($subtype);

?>