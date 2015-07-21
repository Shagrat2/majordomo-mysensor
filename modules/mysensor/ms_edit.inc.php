<?php

require("./lib/mysensor/phpMS.php");

if ($this->mode=='setvalue') {
   global $prop_id;
   global $new_value;
   global $id;
   $this->setProperty($prop_id, $new_value);
   $this->redirect("?id=".$id."&view_mode=".$this->view_mode."&edit_mode=".$this->edit_mode."&tab=".$this->tab);
} 
  
if ($this->owner->name=='panel') {
  $out['CONTROLPANEL']=1;
}

$table_name='msnodes';
$rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");

if (($this->tab=="sensors") || ($this->tab=="presentation")){
  if ($rec['ID']) {
    $sensors=SQLSelect("SELECT * FROM msnodeval WHERE NID='".$rec['NID']."' ORDER BY SID");
    $presentation=SQLSelect("SELECT * FROM msnodesens WHERE NID='".$rec['NID']."' ORDER BY SID");
  }
}
 
if ($this->mode=='update') { 
  $ok=1;
  if ($this->tab=='') {
  	  
    // NId
    global $nid;
    $rec['NID']=$nid;
    if ($rec['NID']=='') {
      $out['ERR_NID']=1;
      $ok=0;
    }
  
    // Title
    global $title;
    $rec['TITLE']=$title;
    if ($rec['TITLE']=='') {
      $out['ERR_TITLE']=1;
      $ok=0;
    }

    //updating 'LOCATION_ID' (select)
    if (IsSet($this->location_id)) {
      $rec['LOCATION_ID']=$this->location_id;
    } else {
      global $location_id;
      $rec['LOCATION_ID']=$location_id;
    }

    $old_bat_object=$rec['BAT_OBJECT'];
    $old_bat_property=$rec['BAT_PROPERTY'];

    global $bat_object;
    $rec['BAT_OBJECT']=$bat_object;

    global $bat_property;
    $rec['BAT_PROPERTY']=$bat_property;

    //UPDATING RECORD
    if ($ok) {
      if ($rec['ID']) {
        SQLUpdate($table_name, $rec); // update
      } else {
        $new_rec=1;
        $rec['ID']=SQLInsert($table_name, $rec); // adding new record
      }

      if ($rec['BAT_OBJECT'] && $rec['BAT_PROPERTY']) {
        addLinkedProperty($rec['BAT_OBJECT'], $rec['BAT_PROPERTY'], $this->name);
      }
      if ($old_bat_object && $old_bat_object!=$rec['BAT_OBJECT'] && $old_bat_property && $old_bat_property!=$rec['BAT_PROPERTY']) {
        removeLinkedProperty($old_bat_object, $old_bat_property, $this->name);
      }

      $out['OK']=1;
    } else {
      $out['ERR']=1;
    }
  }
  
  if ($this->tab=='sensors') {	
    $ok=1;

    //UPDATING RECORD
    if ($ok) {
      $out['OK']=1;
    } else {
      $out['ERR']=1;
    }
    
    if ($ok) {
      if ($rec['ID']) {
        $total=count($sensors);
        for($i=0;$i<$total;$i++) {
          global ${'linked_object'.$sensors[$i]['ID']};
          global ${'linked_property'.$sensors[$i]['ID']};
          
          $old_linked_object=$sensors[$i]['LINKED_OBJECT'];
          $old_linked_property=$sensors[$i]['LINKED_PROPERTY'];
          
          if (${'linked_object'.$sensors[$i]['ID']} && ${'linked_property'.$sensors[$i]['ID']}) {
            $sensors[$i]['LINKED_OBJECT']=${'linked_object'.$sensors[$i]['ID']};
            $sensors[$i]['LINKED_PROPERTY']=${'linked_property'.$sensors[$i]['ID']};
            SQLUpdate('msnodeval', $sensors[$i]);
          } elseif ($sensors[$i]['LINKED_OBJECT'] || $sensors[$i]['LINKED_PROPERTY']) {
            $sensors[$i]['LINKED_OBJECT']='';
            $sensors[$i]['LINKED_PROPERTY']='';
            SQLUpdate('msnodeval', $sensors[$i]);
          }

          if ($sensors[$i]['LINKED_OBJECT'] && $sensors[$i]['LINKED_PROPERTY']) {
            addLinkedProperty($sensors[$i]['LINKED_OBJECT'], $sensors[$i]['LINKED_PROPERTY'], $this->name);          
          }

          if ($old_linked_object && $old_linked_object!=$sensors[$i]['LINKED_OBJECT'] && $old_linked_property && $old_linked_property!=$sensors[$i]['LINKED_PROPERTY']) {
            removeLinkedProperty($old_linked_object, $old_linked_property, $this->name);          
          }
        }
      }
    }
  }
}

outHash($rec, $out);
  
if ($this->tab == ""){    
  //options for 'LOCATION_ID' (select)
  $tmp=SQLSelect("SELECT ID, TITLE FROM locations ORDER BY TITLE");
  $locations_total=count($tmp);
  for($locations_i=0;$locations_i<$locations_total;$locations_i++) {
    $location_id_opt[$tmp[$locations_i]['ID']]=$tmp[$locations_i]['TITLE'];
  }
  for($i=0;$i<count($tmp);$i++) {
    if ($rec['LOCATION_ID']==$tmp[$i]['ID']) $tmp[$i]['SELECTED']=1;
  }
  $out['LOCATION_ID_OPTIONS']=$tmp;
  if (is_array($rec)) {
    foreach($rec as $k=>$v) {
    if (!is_array($v)) {
      $rec[$k]=htmlspecialchars($v);
    }
    }
  }
  
  //print_r($tmp);
}
if ($this->tab == "sensors"){  
  if (is_array($sensors)) {
    foreach($sensors as $k=>$v) {           
      $sensors[$k]['PID'] = $id;
        
      foreach($presentation as $itm){
        if (($itm['NID'] == $rec['NID']) && ($itm['SID'] == $v['SID'])){
          $pres = $itm['SUBTYPE'];
          $sensors[$k]['STITLE'] = $mysensor_presentation[$pres][0];
          $sensors[$k]['SDESCR'] = $mysensor_presentation[$pres][1];  
          break;
        }
      }
        
      $subtype = $v['SUBTYPE'];        
      $sensors[$k]['SUBTITLE'] = $mysensor_property[ $subtype ][0];
      $sensors[$k]['SUBDESCR'] = $mysensor_property[ $subtype ][1];
    }
  }    
    
  $out['SENSORS']=$sensors;
}
if ($this->tab == "presentation"){
  if (is_array($presentation)) {    
    foreach($presentation as $k=>$v){      
      $pres = $v['SUBTYPE'];
      $presentation[$k]['TITLE'] = $mysensor_presentation[$pres][0];            
    }    
  }    
    
  $out['PRESENTATIONS']=$presentation;
}

?>
