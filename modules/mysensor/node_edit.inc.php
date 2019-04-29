<?php

require_once ("phpMS.php");

if ($this->mode=='setvalue') {
   global $prop_id;
   global $new_value;
   global $id;
   $this->setProperty($prop_id, $new_value, 1);
   $this->redirect("?id=".$id."&view_mode=".$this->view_mode."&edit_mode=".$this->edit_mode."&tab=".$this->tab);
} 

if ($this->mode=='cmd') {
  global $gid;
  global $data;  
  $this->cmd($gid, $data);
  $this->redirect("?id=".$id."&view_mode=".$this->view_mode."&edit_mode=".$this->edit_mode."&tab=".$this->tab);
}

if ($this->mode=='resetinfo'){
  global $id;
  global $gid;
  global $nid;
  
  // msnodes
	$table_name='msnodes';
	$rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
	$rec['BATTERY'] = "";
	$rec['BOOTVER'] = "";
	SQLUpdate($table_name, $rec); // update
    
  // msnodestate
  SQLExec( "DELETE FROM msnodestate WHERE GID='$gid' AND NID='$nid'" );

  $this->redirect("?id=".$id."&view_mode=".$this->view_mode."&edit_mode=".$this->edit_mode."&tab=".$this->tab);
}

if ($this->mode=='respfw'){
  global $nid;
  global $gid;
	
	$ret = $this->ResponseFW($gid, $nid);
	if ($ret !== true){
		$out['ERR_RESPFW']=1;
		$out['ERR_RESPFW_TEXT'] = $ret;
	} else {
		$this->redirect("?id=".$id."&view_mode=".$this->view_mode."&edit_mode=".$this->edit_mode."&tab=".$this->tab);
	}
}
  
if ($this->owner->name=='panel') {
  $out['CONTROLPANEL']=1;
}

$table_name='msnodes';
$rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");

if (($this->tab=="sensors") || ($this->tab=="presentation")){
  if ($rec['ID']) {
    $sensors=SQLSelect("SELECT * FROM msnodeval WHERE GID='".$rec['GID']."' AND NID='".$rec['NID']."' ORDER BY SID");
    $presentation=SQLSelect("SELECT * FROM msnodesens WHERE GID='".$rec['GID']."' AND NID='".$rec['NID']."' ORDER BY SID");
  }
}
 
if ($this->mode=='update') { 
  $ok=1;
  if ($this->tab=='') {

    // GID
    global $gid;
    $rec['GID']=$gid;
    if ($rec['GID']=='') {
      $out['ERR_GID']=1;
      $ok=0;
    }

    // NID
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
    global $location_id;
    $rec['LOCATION_ID']= '0'.$location_id;
      
    // Dev type
    global $devtype;
    $rec['DEVTYPE'] = $devtype;
    
    // FIRMWARE
    global $firmware;
    $rec['FIRMWARE'] = '0'.$firmware;

    // Battery
    $old_bat_object=$rec['BAT_OBJECT'];
    $old_bat_property=$rec['BAT_PROPERTY'];

    global $bat_object;
    $rec['BAT_OBJECT']="".$bat_object;

    global $bat_property;
    $rec['BAT_PROPERTY']="".$bat_property;
		
    // Heartbeat
    $old_heartbeat_object=$rec['HEARTBEAT_OBJECT'];
    $old_heartbeat_property=$rec['HEARTBEAT_PROPERTY'];

    global $heartbeat_object;
    $rec['HEARTBEAT_OBJECT']="".$heartbeat_object;

    global $heartbeat_property;
    $rec['HEARTBEAT_PROPERTY']="".$heartbeat_property;

    //UPDATING RECORD
    if ($ok) {
      if ($rec['ID']) {
        SQLUpdate($table_name, $rec); // update
      } else {
        $new_rec=1;
        $rec['ID']=SQLInsert($table_name, $rec); // adding new record
      }

	    // Battery
      if ($rec['BAT_OBJECT'] && $rec['BAT_PROPERTY']) {
        addLinkedProperty($rec['BAT_OBJECT'], $rec['BAT_PROPERTY'], $this->name);
      }
      if ($old_bat_object && $old_bat_property && ($old_bat_object!=$rec['BAT_OBJECT'] || $old_bat_property!=$rec['BAT_PROPERTY'])) {
        removeLinkedProperty($old_bat_object, $old_bat_property, $this->name);
      }
			
	    // Hearbeat
      if ($rec['HEARTBEAT_OBJECT'] && $rec['HEARTBEAT_PROPERTY']) {
        addLinkedProperty($rec['HEARTBEAT_OBJECT'], $rec['HEARTBEAT_PROPERTY'], $this->name);
      }
      if ($old_heartbeat_object && $old_heartbeat_property && ($old_heartbeat_object!=$rec['HEARTBEAT_OBJECT'] || $old_heartbeat_property!=$rec['HEARTBEAT_PROPERTY'])) {
        removeLinkedProperty($old_heartbeat_object, $old_heartbeat_property, $this->name);
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
			    if ($sensors[$i]['UPDATED'] == 0)
				  $sensors[$i]['UPDATED'] = date('Y-m-d H:i:s'); 
					
          global ${'linked_object'.$sensors[$i]['ID']};
          global ${'linked_property'.$sensors[$i]['ID']};
              
          global ${'ack'.$sensors[$i]['ID']};					
          if (${'ack'.$sensors[$i]['ID']}) {
            $sensors[$i]['ACK']=1;            
          } else {
            $sensors[$i]['ACK']=0;            
          } 
              
          global ${'req'.$sensors[$i]['ID']};
          if (${'req'.$sensors[$i]['ID']}) {
            $sensors[$i]['REQ']=1;            
          } else {
            $sensors[$i]['REQ']=0;            
          } 
          SQLUpdate('msnodeval', $sensors[$i]);					
              
          // Battery
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

          if ($old_linked_object&& $old_linked_property && ($old_linked_object!=$sensors[$i]['LINKED_OBJECT']  || $old_linked_property!=$sensors[$i]['LINKED_PROPERTY'])) {
            removeLinkedProperty($old_linked_object, $old_linked_property, $this->name);          
          }
					
		      // Heartheat
          $old_linked_object=$sensors[$i]['HEARTBEAT_OBJECT'];
          $old_linked_property=$sensors[$i]['HEARTBEAT_PROPERTY'];
          
          if (${'heartbeat_object'.$sensors[$i]['ID']} && ${'heartbeat_property'.$sensors[$i]['ID']}) {
            $sensors[$i]['HEARTBEAT_OBJECT']=${'heartbeat_object'.$sensors[$i]['ID']};
            $sensors[$i]['HEARTBEAT_PROPERTY']=${'heartbeat_property'.$sensors[$i]['ID']};
            SQLUpdate('msnodeval', $sensors[$i]);
          } elseif ($sensors[$i]['HEARTBEAT_OBJECT'] || $sensors[$i]['HEARTBEAT_PROPERTY']) {
            $sensors[$i]['HEARTBEAT_OBJECT']='';
            $sensors[$i]['HEARTBEAT_PROPERTY']='';
            SQLUpdate('msnodeval', $sensors[$i]);
          }

          if ($sensors[$i]['HEARTBEAT_OBJECT'] && $sensors[$i]['HEARTBEAT_PROPERTY']) {
            addLinkedProperty($sensors[$i]['HEARTBEAT_OBJECT'], $sensors[$i]['HEARTBEAT_PROPERTY'], $this->name);          
          }

          if ($old_linked_object&& $old_linked_property && ($old_linked_object!=$sensors[$i]['HEARTBEAT_OBJECT']  || $old_linked_property!=$sensors[$i]['HEARTBEAT_PROPERTY'])) {
            removeLinkedProperty($old_linked_object, $old_linked_property, $this->name);          
          }
        }
      }
    }
  }
}

if ($rec['NID'] != "") {
  $rec['TITLE'] = (empty($rec['TITLE']))?"[".$rec['NID']."] ".$rec['TITLE']:$rec['TITLE'];
}

outHash($rec, $out);
  
if ($this->tab == ""){    
  //options for 'GID' (select)
  $tmp=SQLSelect("SELECT ID, TITLE FROM msgates ORDER BY TITLE");
  $gates_total=count($tmp);
/*  
  for($gates_i=0;$gates_i<$gates_total;$gates_i++) {
    $gates_opt[$tmp[$gates_i]['ID']]=$tmp[$gates_i]['TITLE'];
  }
*/  
  for($i=0;$i<count($tmp);$i++) {
    if ($rec['GID']==$tmp[$i]['ID']) {
		$tmp[$i]['SELECTED']=1;
		$out['GID_TITLE']=$tmp[$i]['TITLE'];
	}
    if ($rec['GID']=="" && $tmp[$i]['ID']==1) $tmp[$i]['SELECTED']=1;
  }
  $out['GID_OPTIONS']=$tmp;
  
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
  
  //options for 'FIRMWARE_ID' (select)
  $tmp=SQLSelect("SELECT ID, TITLE FROM msbins ORDER BY TITLE");
  $firmware_total=count($tmp);
  for($firmware_i=0;$firmware_i<$firmware_total;$firmware_i++) {
    $firmware_id_opt[$tmp[$firmware_i]['ID']]=$tmp[$firmware_i]['TITLE'];
  }
  for($i=0;$i<count($tmp);$i++) {
    if ($rec['FIRMWARE']==$tmp[$i]['ID']) $tmp[$i]['SELECTED']=1;
  }
  $out['FIRMWARE_OPTIONS']=$tmp;
  
  // Parse
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
        if (($itm['GID'] == $rec['GID']) && ($itm['NID'] == $rec['NID']) && ($itm['SID'] == $v['SID'])){
          $pres = $itm['SUBTYPE'];
          $sensors[$k]['STITLE'] = $MSPresentation[$pres][0];
          $sensors[$k]['SDESCR'] = $MSPresentation[$pres][1];
		      $sensors[$k]['NOTE'] = $itm['INFO'];
          break;
        }
      }
        
      $subtype = $v['SUBTYPE'];        
      $sensors[$k]['SUBTITLE'] = $MSProperty[ $subtype ][0];
      $sensors[$k]['SUBDESCR'] = $MSProperty[ $subtype ][1];
	  
	  $subType = $MSProperty[$sensors[$k]['SUBTYPE']][2];
	  if ($subType == ''){
		  $subType = 'sensor_general';
	  }
	  	  
	  $sensors[$k]['SDEVICE_TYPE'] = $subType;
    }
  }    
    
  $out['SENSORS']=$sensors;
}
if ($this->tab == "presentation"){
  if (is_array($presentation)) {    
    foreach($presentation as $k=>$v){      
      $pres = $v['SUBTYPE'];
      $presentation[$k]['TITLE'] = $MSPresentation[$pres][0];
    }    
  }    
    
  $out['PRESENTATIONS']=$presentation;
}

?>
