<?php
/**
* MySesnor
*
* MySesnor
*
* @package project
* @author Ivan Z. <ivan@jad.ru>
* @copyright http://www.pstd.ru/ (c)
* @version 0.1 (wizard, 9:33 [Jul 08, 2015])
*/
//

class mysensor extends module {
  
  public $tryTimeout = 2;

/**
* mySensor
*
* Module class constructor
*
* @access private
*/
function mysensor() {
  $this->name="mysensor";
  $this->title="MySensor";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
}

/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams() {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->data_souce)) {
  $p["data_source"]=$this->data_source;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $data_source;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($data_source)) {
   $this->data_source=$data_source;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
  global $session;
  
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['DATA_SOURCE']=$this->data_source;
  $out['TAB']=$this->tab;
  if ($this->single_rec) {
   $out['SINGLE_REC']=1;
  }
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
  if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
    $out['SET_DATASOURCE']=1;
  }

  $this->getConfig();
  $out['MS_HOST']=$this->config['MS_HOST'];
  $out['MS_PORT']=$this->config['MS_PORT'];
  $out['MS_MEASURE']=$this->config['MS_MEASURE'];
  $out['MS_AUTOID']=$this->config['MS_AUTOID'];
  $out['MS_NEXTID']=$this->config['MS_NEXTID'];
 
  if ($out['MS_HOST']=="") {
    $out['MS_HOST']='10.9.0.253';    
    $this->config['MS_HOST']=$out['MS_HOST'];
    $this->saveConfig();
  }
  if ($out['MS_PORT']=="") {
    $out['MS_PORT']='5003';    
    $this->config['MS_PORT']=$out['MS_PORT'];
    $this->saveConfig();
  }  
  if ($out['MS_MEASURE']=="") {
    $out['MS_MEASURE']='M';
    $this->config['MS_MEASURE']=$out['MS_MEASURE'];
    $this->saveConfig();    
  }
  if ($out['MS_AUTOID']=="") {
    $out['MS_AUTOID']=1;
    $this->config['MS_AUTOID']=$out['MS_AUTOID'];
    $this->saveConfig();        
  }
  if ($out['MS_NEXTID']=="") {
    $out['MS_NEXTID']=10;
    $this->config['MS_NEXTID']=$out['MS_NEXTID'];
    $this->saveConfig();    
  }
    
  if ($this->data_source=='mysensors' || $this->data_source=='') {
    if ($this->view_mode=='update_settings') {
      global $ms_host;
      global $ms_port;
      global $ms_measure;
      global $ms_autoid;
      global $ms_nextid;
   
      $this->config['MS_HOST']=$ms_host;
      $this->config['MS_PORT']=(int)$ms_port;   
      $this->config['MS_MEASURE']=$ms_measure;
      $this->config['MS_AUTOID']=$ms_autoid;
      $this->config['MS_NEXTID']=$ms_nextid;
      $this->saveConfig();
      $this->redirect("?");      
    }
  
    if ($this->view_mode=='' || $this->view_mode=='search_ms') {
      if ($this->tab=='mesh'){
        $this->search_mesh($out);
      } else {
        $this->search_ms($out);
      }
    }
    if ($this->view_mode=='node_edit') {
      $this->edit_ms($out, $this->id);
    }
    if ($this->view_mode=='node_delete') {
      $this->delete_ms($this->id);
      $this->redirect("?");
    }
    if ($this->view_mode=='sensor_add'){
      $this->add_sensor($out, $this->id);      
    }
    if ($this->view_mode=='sensor_delete') {
      $this->delete_sensor($this->id);
      
      global $pid;
      $this->redirect("?data_source=$this->data_source&view_mode=node_edit&id=$pid&tab=sensors");
    }     
    if ($this->view_mode=='presentation_clean'){
      $this->clean_presentation($this->id);
      $this->redirect("?data_source=$this->data_source&view_mode=node_edit&id=$this->id&tab=presentation");
    }
  }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* Search nodes
*
* @access public
*/
function search_ms(&$out) {
  require(DIR_MODULES.$this->name.'/ms_search.inc.php');
}
/**
* Search mesh
*
* @access public
*/
function search_mesh(&$out) {
  require(DIR_MODULES.$this->name.'/ms_mesh.inc.php');
}
/**
* Search sensors
*
* @access public
*/
function node_sensors(&$out, $id) {
  require(DIR_MODULES.$this->name.'/sensors_search.inc.php');
}
/**
* Edit/add
*
* @access public
*/
function edit_ms(&$out, $id) {
  require(DIR_MODULES.$this->name.'/ms_edit.inc.php');
}
/**
* Delete node
*
* @access public
*/
function delete_ms($id) {
  $rec=SQLSelectOne("SELECT * FROM msnodes WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM msnodesens WHERE NID='".$rec['NID']."'"); 
  SQLExec("DELETE FROM msnodeval WHERE NID='".$rec['NID']."'"); 
  SQLExec("DELETE FROM msnodes WHERE ID='".$rec['ID']."'"); 
}
/**
* Add sensor
*
* @access public
*/
function add_sensor(&$out, $id) {
  require(DIR_MODULES.$this->name.'/sensor_add.inc.php');
}
/**
* Delete sensor
*
* @access public
*/
function delete_sensor($id) {
  $rec=SQLSelectOne("SELECT * FROM msnodeval WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM msnodeval WHERE ID='".$rec['ID']."'");   
}
/**
* Handle property object
*
* @access public
*/
function propertySetHandle($object, $property, $value) {
  $properties=SQLSelect("SELECT ID FROM msnodeval WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
  $total=count($properties);
  if ($total) {
    for($i=0;$i<$total;$i++) {
      $this->setProperty($properties[$i]['ID'], $value);
    }
  }  
}
/**
* Clean presentation
*
* @access public
*/
function clean_presentation($id) {
  global $nid;
  
  SQLExec("DELETE FROM msnodesens WHERE NID='".$nid."'");   
}
/**
* Receive Presentation
*
* @access public
*/
function Presentation($arr){
  // Node
  $NId = $arr[0];
  if ($NId == "") return;
  
  $SId = $arr[1];
  $SubType = $arr[4];
  $info = $arr[5];
  
  $node=SQLSelectOne("SELECT * FROM msnodes WHERE NID LIKE '".DBSafe($NId)."';"); 
  if (!$node['ID']) {
    $node['NID']=$NId;
    $node['TITLE']=$NId;
    $node['ID']=SQLInsert('msnodes', $node);    
  }  
  
  // Arduino Node
  if ($SId == 255){
    $node['PROT'] = $arr[5]; 
    SQLUpdate('msnodes', $node);
  } else {
    // Sensor
    $sens=SQLSelectOne("SELECT * FROM msnodesens WHERE NID LIKE '".DBSafe($NId)."' AND SID LIKE '".DBSafe($SId)."' AND SUBTYPE LIKE '".DBSafe($SubType)."';"); 
    if (!$sens['ID']) {
      $sens['NID'] = $NId;
      $sens['SID'] = $SId;
      $sens['SUBTYPE'] = $SubType;
      $sens['INFO'] = $info;
      $sens['ID']=SQLInsert('msnodesens', $sens);
    }  
  }
}
/**
* Receive Set
*
* @access public
*/
function Set($arr){
  // Node
  $NId = $arr[0];
  $SId = $arr[1];
  $SubType = $arr[4];
  $val = $arr[5];
  
  $node=SQLSelectOne("SELECT * FROM msnodes WHERE NID LIKE '".DBSafe($NId)."';"); 
  if (!$node['ID']) {
    $node['NID']=$NId;
    $node['TITLE']=$NId;
    $node['ID']=SQLInsert('msnodes', $node);
  }
    
  // Sensor
  $sens=SQLSelectOne("SELECT * FROM msnodeval WHERE NID LIKE '".DBSafe($NId)."' AND SID LIKE '".DBSafe($SId)."' AND SUBTYPE LIKE '".DBSafe($SubType)."';"); 
  if (!$sens['ID']) {
    $sens['NID'] = $NId;
    $sens['SID'] = $SId;
    $sens['SUBTYPE'] = $SubType;
    $sens['ID']=SQLInsert('msnodeval', $sens);
  }
  
  // Delete ACK
  if ($arr[3] == 1){            
    SQLExec("DELETE FROM mssendstack WHERE NID='".$NId."' AND SID='".$SId."' AND MType='".$arr[2]."' AND SUBTYPE='".$SubType."' AND MESSAGE='".$val."'");
  }
  
  // Set
  $sens['UPDATED']=date('Y-m-d H:i:s'); 
  $sens['VAL']=$val; 
  SQLUpdate('msnodeval', $sens);
  
  if ($sens['LINKED_OBJECT'] && $sens['LINKED_PROPERTY']) {
    setGlobal($sens['LINKED_OBJECT'].'.'.$sens['LINKED_PROPERTY'], $val, array($this->name=>'0'));
  } 
}
/**
* Title
*
* Description
*
* @access public
*/
function setProperty($prop_id, $value, $set_linked=0) {
  $rec=SQLSelectOne("SELECT * FROM msnodeval WHERE ID='".$prop_id."'");
  if (!$rec['ID']) {
   return 0;
  }
    
  $data['NID'] = $rec['NID'];
  $data['SID'] = $rec['SID'];
  $data['MType'] = 1; // 
  $data['ACK'] = $rec['ACK'];
  $data['SUBTYPE'] = $rec['SUBTYPE'];
  $data['MESSAGE'] = $value;
  $data['EXPIRE'] = time()+$this->tryTimeout;
  SQLInsert('mssendstack', $data);
}
/**
* Title
*
* Description
*
* @access public
*/
function cmd($str) {
  $arr = explode(';', $str, 6);
  
  $data['NID'] = $arr[0];
  $data['SID'] = $arr[1];
  $data['MType'] = $arr[2];
  $data['ACK'] = $arr[3];
  $data['SUBTYPE'] = $arr[4];
  $data['MESSAGE'] = $arr[5];
  $data['EXPIRE'] = time()+$this->tryTimeout;
  SQLInsert('mssendstack', $data);
}
/**
* Receive req
*
* @access public
*/
function req($arr){
  // Node
  $NId = $arr[0];
  $SId = $arr[1];
  $mType = $arr[2];
  $SubType = $arr[4];
  
  $node=SQLSelectOne("SELECT * FROM msnodes WHERE NID LIKE '".DBSafe($NId)."';"); 
  if (!$node['ID']) {
    $node['NID']=$NId;
    $node['TITLE']=$NId;
    $node['ID']=SQLInsert('msnodes', $node);
  }  
  
  // Sensor
  $sens=SQLSelectOne("SELECT * FROM msnodeval WHERE NID LIKE '".DBSafe($NId)."' AND SID LIKE '".DBSafe($SId)."' AND SUBTYPE LIKE '".DBSafe($SubType)."';"); 
  if (!$sens['ID']) {
    $sens['NID'] = $NId;
    $sens['SID'] = $SId;
    $sens['SUBTYPE'] = $SubType;
    $sens['ID']=SQLInsert('msnodeval', $sens);
  }
  
  // Req
  $this->cmd( "$NId;$SId;$mType;".$sens['ACK'].";$SubType;".$sens['VAL']);
  return false;
}
/**
* Receive Set
*
* @access public
*/
function Internal($arr){
  // Node
  $NId = $arr[0];  
  $SubType = $arr[4];
  $val = $arr[5];

  if (($NId == 0) || ($NId == 255)){
    $node = false;
  } else {
    $node=SQLSelectOne("SELECT * FROM msnodes WHERE NID LIKE '".DBSafe($NId)."';"); 
    if (!$node['ID']) {
      $node['NID']=$NId;
      $node['TITLE']=$NId;
      $node['ID']=SQLInsert('msnodes', $node);
    }
  }
  
  switch ($SubType){
    // Battery
    case 0:
      if ($node){
        $node['BATTERY'] = $val;
        SQLUpdate('msnodes', $node);
      
        if ($node['BAT_OBJECT'] && $node['BAT_PROPERTY']) {
          setGlobal($node['BAT_OBJECT'].'.'.$node['BAT_PROPERTY'], $val, array($this->name=>'0'));
        } 
      }      
      break;    
      
    // Time
    case 1:
      $this->cmd( $NId.";255;3;0;1;".time() );
      break;
      
    // ID_REQUEST
    case 3:    
      $this->getConfig();
      
      if (($this->config['MS_AUTOID'] == '') || ($this->config['MS_AUTOID'] == 1)){
        $nextid = $this->config['MS_NEXTID'];
        
        if ($nextid < 255){
          // Send new id
          $this->cmd( "255;255;3;0;4;".$nextid );
          echo "Req new ID: $nextid\n";
          
          $nextid++;
          $this->config['MS_NEXTID']=$nextid;
          $this->saveConfig();        
        } else {
          echo "Req new ID: out of range\n";
        }
      } else {
        echo "Req new ID: rejected\n";
      }              
      break;
      
    // INCLUSION_MODE  
    case 5:
      $this->getConfig();
      $this->config['MS_INCLUSION_MODE']=$val;
      $this->saveConfig();
      break;      
      
    // CONFIG
    case 6:
      if ($node){
        $node['PID'] = $val;
        SQLUpdate('msnodes', $node);
      }
      
      // Send ansver - metric
      $this->getConfig();
      $this->cmd( $NId.";255;3;0;6;".$this->config['MS_MEASURE'] );
      break;
      
    // SKETCH_NAME
    case 11:
      if ($node){
        $node['SKETCH'] = $val;
        SQLUpdate('msnodes', $node);
      }      
      break;
      
    // SKETCH_VERSION
    case 12:
      if ($node){
        $node['VER'] = $val;
        SQLUpdate('msnodes', $node);
      }
      
      break;
    
    // @@@ 2 - Version        
    // @@@ 7 - FIND_PARENT
    // 9 - LOG_MESSAGE    
    // @@@ 14 - GATEWAY_READY
  }
}
/**
* Install
*
* Module installation routine
*
* @access private
*/
function install($data='') {
  parent::install();
}
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
function dbInstall($data) { 
 
  // Send message
  SQLExec("DROP TABLE IF EXISTS `mssendstack`;");
  
  $sqlQuery = "CREATE TABLE IF NOT EXISTS `mssendstack`
               (`ID`  int(10) unsigned NOT NULL auto_increment,
                `NID` int(10) NOT NULL,
                `SID` int(10) NOT NULL,
                `MType` int(10) NOT NULL,
                `ACK` int(10) NOT NULL,
                `SUBTYPE` int(10) NOT NULL,
                `MESSAGE` varchar(32) NOT NULL,
                `EXPIRE` BIGINT NOT NULL,
                PRIMARY KEY (`ID`)
               ) ENGINE = MEMORY DEFAULT CHARSET=utf8;";

  SQLExec($sqlQuery);
  
  $data = <<<EOD
  msnodes: ID int(10) unsigned NOT NULL auto_increment
  msnodes: NID int(10) NOT NULL
  msnodes: PID int(10) NOT NULL
  msnodes: TITLE varchar(255) NOT NULL DEFAULT ''    
  msnodes: BATTERY varchar(32) NOT NULL DEFAULT ''  
  msnodes: SKETCH varchar(32) NOT NULL DEFAULT ''  
  msnodes: VER varchar(32) NOT NULL DEFAULT ''  
  msnodes: PROT varchar(32) NOT NULL DEFAULT ''  
  msnodes: BAT_OBJECT varchar(255) NOT NULL DEFAULT ''
  msnodes: BAT_PROPERTY varchar(255) NOT NULL DEFAULT ''
  msnodes: LOCATION_ID int(10) NOT NULL DEFAULT '0' 
  
  msnodesens: ID int(10) unsigned NOT NULL auto_increment
  msnodesens: NID int(10) NOT NULL 
  msnodesens: SID int(10) NOT NULL    
  msnodesens: SUBTYPE int(10) NOT NULL
  msnodesens: INFO varchar(255) NOT NULL DEFAULT ''
  
  msnodeval: ID int(10) unsigned NOT NULL auto_increment
  msnodeval: NID int(10) NOT NULL  
  msnodeval: SID int(10) NOT NULL  
  msnodeval: SUBTYPE int(10) NOT NULL  
  msnodeval: VAL varchar(32) NOT NULL DEFAULT ''  
  msnodeval: UPDATED datetime
  msnodeval: LINKED_OBJECT varchar(255) NOT NULL DEFAULT ''
  msnodeval: LINKED_PROPERTY varchar(255) NOT NULL DEFAULT ''
  msnodeval: ACK int(3) unsigned NOT NULL DEFAULT '0'
EOD;
  parent::dbInstall($data);

 }
// --------------------------------------------------------------------
}

?>