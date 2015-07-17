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
    
    // print_r($presentation);
    
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
    
    //print_r($sensors);
    
    $out['SENSORS']=$sensors;
  }  
  
?>