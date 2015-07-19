<?php

require("./lib/mysensor/phpMS.php");

if ($this->owner->name=='panel') {
  $out['CONTROLPANEL']=1;
}

global $nid;

$table_name='msnodes';
$rec=SQLSelectOne("SELECT * FROM $table_name WHERE NID='$nid'");

if ($rec['ID']) {
  $presentation=SQLSelect("SELECT * FROM msnodesens WHERE NID='".$rec['NID']."' ORDER BY SID");
}

outHash($rec, $out);

// Sensors
$sens = array();
foreach ($mysensor_presentation as $k=>$v){
  $sens[] = array('ID'=>$k, 'TITLE'=>$v[0]);
}

//print_r($sens);

$out['SENSORS_PRES']=$sens;

?>