<?php

require_once("phpMS.php");

if ($this->owner->name=='panel') {
  $out['CONTROLPANEL']=1;
}

global $id;

$table_name='msnodes';
$sens=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");

//print_r($sens);

if ($this->mode=='update') { 
  $ok=1;

  // GId
  global $gid;
  $rec['GID']=$gid;
  if ($rec['GID']=='') {
    $out['ERR_GID']=1;
    $ok=0;
  }
  
  // NId
  global $nid;
  $rec['NID']=$nid;
  if ($rec['NID']=='') {
    $out['ERR_NID']=1;
    $ok=0;
  }
  
  // sid
  global $sid;
  $rec['SID']=$sid;
  if ($rec['SID']=='') {
    $out['ERR_SID']=1;
    $ok=0;
  }  
  
  // subtype
  global $subtype;
  $rec['SUBTYPE']=$subtype;
  if ($rec['SUBTYPE']=='') {
    $out['ERR_SUBTYPE']=1;
    $ok=0;
  }    
  
  // val
  global $val;
  $rec['VAL']=$val;
  
   //updating 'LINKED_OBJECT' (varchar) 
  global $linked_object;
  $rec['LINKED_OBJECT']=$linked_object;
  
  //updating 'LINKED_PROPERTY' (varchar)
  global $linked_property;
  $rec['LINKED_PROPERTY']=$linked_property;
  
  $table_name = 'msnodeval';
  $rec['ID']=SQLInsert($table_name, $rec); // adding new record 

  if ($rec['LINKED_OBJECT'] && $rec['LINKED_PROPERTY']) {
    addLinkedProperty($rec['LINKED_OBJECT'], $rec['LINKED_PROPERTY'], $this->name);
  }
  
  $this->redirect("?id=".$sens['ID']."&view_mode=node_edit&tab=sensors");
}

if ($sens['ID']) {  
  $rec['ID'] = $sens['ID'];
  $rec['GID'] = $sens['GID'];
  $rec['NID'] = $sens['NID'];
  $rec['TITLE'] = $sens['TITLE'];
  
  $presentation=SQLSelect("SELECT * FROM msnodesens WHERE GID='".$sens['GID']."' AND NID='".$sens['NID']."' ORDER BY SID");
  
  // Sensors
  $sens = array();
  
  foreach ($presentation as $val){    
    $subtype = $val['SUBTYPE'];		
    $title = $MSPresentation[ $subtype ][0];    
    $sens[] = array('ID'=>$val['SID'], 'TITLE'=>$val['SID']." : $title");
  }
  $out['SID_OPTIONS']=$sens;
}

// SubType
$subtype = array();
foreach ($MSProperty as $k=>$v){
  $subtype[] = array('ID'=>$k, 'TITLE'=>$k." : ".$v[0]);
}
$out['SUBTYPE_OPTIONS']=$subtype;

//print_r($rec);

outHash($rec, $out);

?>