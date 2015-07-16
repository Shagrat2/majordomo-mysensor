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
 
  if (!$out['MS_HOST']) {
    $out['MS_HOST']='10.9.0.253';
  }
  if (!$out['MS_PORT']) {
    $out['MS_PORT']='5003';
  } 
  
  if ($this->view_mode=='update_settings') {
    global $ms_host;
    global $ms_port;
   
    $this->config['MS_HOST']=$ms_host;
    $this->config['MS_PORT']=(int)$ms_port;   
    $this->saveConfig();
    $this->redirect("?");
  }

  if (!$this->config['MS_HOST']) {
    $this->config['MS_HOST']='10.9.0.253';
    $this->saveConfig();
  }
  if (!$this->config['MS_PORT']) {
    $this->config['MS_PORT']='5003';
    $this->saveConfig();
  }  
  
  if ($this->data_source=='ms' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_ms') {
   $this->search_ms($out);
  }
  if ($this->view_mode=='edit_ms') {
   $this->edit_ms($out, $this->id);
  }
  if ($this->view_mode=='delete_ms') {
   $this->delete_ms($this->id);
   $this->redirect("?");
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
* Search
*
* @access public
*/
function search_ms(&$out) {
  require(DIR_MODULES.$this->name.'/ms_search.inc.php');
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
* Receive Presentation
*
* @access public
*/
function Presentation($arr){
  // Node
  $NId = $arr[0];
  $SId = $arr[1];
  $SubType = $arr[4];
  
  $node=SQLSelectOne("SELECT * FROM msnodes WHERE NID LIKE '".DBSafe($NId)."';"); 
  if (!$node['ID']) {
    $node['NID']=$NId;
    $node['TITLE']=$NId;
    $node['ID']=SQLInsert('msnodes', $node);
  }
    
  // Sensor
  $sens=SQLSelectOne("SELECT * FROM msnodesens WHERE NID LIKE '".DBSafe($NId)."' AND SID LIKE '".DBSafe($SId)."' AND SUBTYPE LIKE '".DBSafe($SubType)."';"); 
  if (!$sens['ID']) {
    $sens['NID'] = $NId;
    $sens['SID'] = $SId;
    $sens['SUBTYPE'] = $SubType;
    $sens['ID']=SQLInsert('msnodesens', $sens);
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
  
  // Set
  $sens['UPDATED']=date('Y-m-d H:i:s'); 
  $sens['VAL']=$val; 
  SQLUpdate('msnodeval', $sens);
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
  return $sens['VAL'];  
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

  $node=SQLSelectOne("SELECT * FROM msnodes WHERE NID LIKE '".DBSafe($NId)."';"); 
  if (!$node['ID']) {
    $node['NID']=$NId;
    $node['TITLE']=$NId;
    $node['ID']=SQLInsert('msnodes', $node);
  }
  
  switch ($SubType){
    // Battery
    case 0:
      $node['BATTERY'] = $val;
      SQLUpdate('msnodes', $node);
      break;
    // @@@ 1 -Time
    // @@@ 2 - Version
    // @@@ 3 - ID_REQUEST    
    // @@@ 5 - INCLUSION_MODE
    // @@@ 6 - CONFIG
    // @@@ 7 - FIND_PARENT
    // 9 - LOG_MESSAGE
    // SKETCH_NAME
    case 11:
      $node['SKETCH'] = $val;
      SQLUpdate('msnodes', $node);
      break;
    // SKETCH_VERSION
    case 12:
      $node['VER'] = $val;
      SQLUpdate('msnodes', $node);
      break;    
  }
  
/*  
  // Sensor
  $sens=SQLSelectOne("SELECT * FROM msnodeval WHERE NID LIKE '".DBSafe($NId)."' AND SID LIKE '".DBSafe($SId)."' AND SUBTYPE LIKE '".DBSafe($SubType)."';"); 
  if (!$sens['ID']) {
    $sens['NID'] = $NId;
    $sens['SID'] = $SId;
    $sens['SUBTYPE'] = $SubType;
    $sens['ID']=SQLInsert('msnodeval', $sens);
  }
  
  // Set
  $sens['UPDATED']=date('Y-m-d H:i:s'); 
  $sens['VAL']=$arr[5]; 
  SQLUpdate('msnodeval', $sens);
*/  
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
  /*
  msnodes
  */  
  $data = <<<EOD
  msnodes: ID int(10) unsigned NOT NULL auto_increment
  msnodes: NID int(10) NOT NULL
  msnodes: TITLE varchar(255) NOT NULL DEFAULT ''    
  msnodes: BATTERY varchar(32) NOT NULL DEFAULT ''  
  msnodes: SKETCH varchar(32) NOT NULL DEFAULT ''  
  msnodes: VER varchar(32) NOT NULL DEFAULT ''  
  
  msnodesens: ID int(10) unsigned NOT NULL auto_increment
  msnodesens: NID int(10) NOT NULL 
  msnodesens: SID int(10) NOT NULL    
  msnodesens: SUBTYPE int(10) NOT NULL
  
  msnodeval: ID int(10) unsigned NOT NULL auto_increment
  msnodeval: NID int(10) NOT NULL  
  msnodeval: SID int(10) NOT NULL  
  msnodeval: SUBTYPE int(10) NOT NULL  
  msnodeval: VAL varchar(32) NOT NULL DEFAULT ''  
  msnodeval: UPDATED datetime
EOD;
  parent::dbInstall($data);

 }
// --------------------------------------------------------------------
}

?>