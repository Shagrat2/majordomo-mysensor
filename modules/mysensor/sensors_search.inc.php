<?
  require("./lib/mysensor/phpMS.php");

  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='msnodes';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");

  if ($this->mode=='update') {
    $ok=1;

    //UPDATING RECORD
    if ($ok) {
      $out['OK']=1;
    } else {
      $out['ERR']=1;
    }
  }

  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  
  outHash($rec, $out);
  
  if ($rec['ID']) {
    $sensors=SQLSelect("SELECT * FROM msnodeval WHERE NID='".$rec['NID']."' ORDER BY SID");
    $presentation=SQLSelect("SELECT * FROM msnodesens WHERE NID='".$rec['NID']."' ORDER BY SID");
    
    if ($this->mode=='update') {
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
    
    if (is_array($sensors)) 
    {
      foreach($sensors as $k=>$v) {       
        
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
  
?>