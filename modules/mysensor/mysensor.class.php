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
  
  public $tryTimeout = 2; // 2 second
  public $RxExpireTimeout = 21600;// 6*60*60 = 6 hours
	public $MY_CORE_MIN_VERSION = 2;

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
function saveParams($data=1) {
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
	global $ajax; 
	global $filter;
	global $atype;
  
	if ($ajax) {
		header ("HTTP/1.0: 200 OK\n");
		header ('Content-Type: text/html; charset=utf-8');
		
		if ($atype == "incmode")
		{			
			$this->getConfig();
			echo $this->config['MS_INCLUSION_MODE'];
			exit;
		}
		
		$limit=50;
		
    // Find last midifed
    $filename=ROOT.'debmes/log_*-cycle_mysensor.php.txt';
    foreach(glob($filename) as $file) {      
      $LastModified[] = filemtime($file);
      $FileName[] = $file;
    }    
    $files = array_multisort($LastModified, SORT_NUMERIC, SORT_ASC, $FileName);
    $lastIndex = count($LastModified) - 1;
    
    // Open file
		$data=LoadFile( $FileName[$lastIndex] );    
    
		$lines=explode("\n", $data);
     $lines=array_reverse($lines);
     $res_lines=array();
     $total=count($lines);
     $added=0;
     for($i=0;$i<$total;$i++) {

      if (trim($lines[$i])=='') {
       continue;
      }

      if ($filter && preg_match('/'.preg_quote($filter).'/is', $lines[$i])) {
       $res_lines[]=$lines[$i];
       $added++;
      } elseif (!$filter) {
       $res_lines[]=$lines[$i];
       $added++;
      }

      if ($added>=$limit) {
       break;
      }
     }

    echo implode("<br/>", $res_lines);
		exit;
	}
  if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
    $out['SET_DATASOURCE']=1;
  }

  $this->getConfig();	
	$out['MS_CONTYPE']=$this->config['MS_CONTYPE'];
  $out['MS_HOST']=$this->config['MS_HOST'];
  $out['MS_PORT']=$this->config['MS_PORT'];
	$out['MS_SERIAL']=$this->config['MS_SERIAL'];	
  $out['MS_MEASURE']=$this->config['MS_MEASURE'];
  $out['MS_AUTOID']=$this->config['MS_AUTOID'];
  $out['MS_NEXTID']=$this->config['MS_NEXTID'];	
	$out['MS_INCLUSION_MODE'] = $this->config['MS_INCLUSION_MODE'];
 
	if ($out['MS_CONTYPE']=="") {
    $out['MS_CONTYPE']=0;    
    $this->config['MS_CONTYPE']=$out['MS_CONTYPE'];
    $this->saveConfig();
  }
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
	if ($out['MS_SERIAL']=="") {
    $out['MS_SERIAL']='/dev/ttyMySensorsGateway';    
    $this->config['MS_SERIAL']=$out['MS_SERIAL'];
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
			global $ms_contype;
      global $ms_host;
      global $ms_port;
			global $ms_serial;
      global $ms_measure;
      global $ms_autoid;
      global $ms_nextid;
   
	    $this->config['MS_CONTYPE']=$ms_contype;
      $this->config['MS_HOST']=$ms_host;
      $this->config['MS_PORT']=(int)$ms_port;   
			$this->config['MS_SERIAL']=$ms_serial;   
      $this->config['MS_MEASURE']=$ms_measure;
      $this->config['MS_AUTOID']=$ms_autoid;
      $this->config['MS_NEXTID']=$ms_nextid;
      $this->saveConfig();
      $this->redirect("?");      
    }
  
    if ($this->view_mode=='' || $this->view_mode=='search_ms') {
      if ($this->tab=='mesh'){
        $this->search_mesh($out);
      } else if ($this->tab=='log'){
        $this->search_log($out);
      } else {
        $this->search_ms($out);
      }
    }
    if ($this->view_mode=='node_edit') {
      $this->edit_ms($out, $this->id);
    }
		if ($this->view_mode=='inc_mode') {
			$NId = 0;
			$SId = 0;
			$mType = 3;
			$ack = 0;
			$SubType = 5;
			
			if ($this->config['MS_INCLUSION_MODE'] == 0)			
				$val = 1;
			else			
				$val = 0;			
			$this->config['MS_INCLUSION_MODE'] = $val;
			$this->saveConfig();
			
			$this->cmd( "$NId;$SId;$mType;$ack;$SubType;".$val);
			$this->redirect("?");
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
* Search log
*
* @access public
*/
function search_log(&$out) {
  require(DIR_MODULES.$this->name.'/ms_log.inc.php');
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
  if (!$node['ID']) 
		if (!$this->RegistNewNode($node, $NId))
			return;
  
  // Arduino Node
  if ($SId == 255){
    $node['PROT'] = $arr[5]; 
		
		if ($node['LASTREBOOT'] == 0)
			$node['LASTREBOOT'] = date('Y-m-d H:i:s');
		
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
    } else {
      $sens['INFO'] = $info;
      SQLUpdate('msnodesens', $sens);
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
	if ($NId == "") return;
	
  $node=SQLSelectOne("SELECT * FROM msnodes WHERE NID LIKE '".DBSafe($NId)."';"); 
  if (!$node['ID'])
		if (!$this->RegistNewNode($node, $NId))
			return;
	
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
    SQLExec("DELETE FROM mssendstack WHERE NID='".$NId."' AND SID='".$SId."' AND MType='".$arr[2]."' AND SUBTYPE='".$SubType."' AND MESSAGE='".$val."' AND SENDRX=0");
  }
  
	//echo  date("Y-m-d H:i:s")." Proc 3\n";
	//echo print_r($sens, true)."\n";
	
  // Set
  $sens['UPDATED']=date('Y-m-d H:i:s'); 
  $sens['VAL']=$val; 
  SQLUpdate('msnodeval', $sens);
	
	//echo "set:".print_r($sens)."\n";	
	//echo  date("Y-m-d H:i:s")." Proc 4\n";
  
  if ($sens['LINKED_OBJECT'] && $sens['LINKED_PROPERTY']) {		
		//echo  date("Y-m-d H:i:s")." Start set\n";
		//echo "Set ".$sens['LINKED_OBJECT'].'.'.$sens['LINKED_PROPERTY']."=".$val."\n";		
    setGlobal($sens['LINKED_OBJECT'].'.'.$sens['LINKED_PROPERTY'], $val, array($this->name=>'0'));		
		//echo  date("Y-m-d H:i:s")." End set\n";
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
  $rec=SQLSelectOne("SELECT * FROM msnodeval WHERE msnodeval.id=$prop_id");	
  if (!$rec['ID']) 
    return 0;  
	
	$rec['UPDATED']=date('Y-m-d H:i:s'); 
	
	// Set to node value
	if ($rec['VAL'] != $value)
  {
    // Not set for rollback --- $rec['VAL'] = $value;
		SQLUpdate('msnodeval', $rec);
  }
  
  $this->cmd($rec['NID'].";".$rec['SID'].";1;".$rec['ACK'].";".$rec['SUBTYPE'].";".$value);
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
	
	// For sleep node
	$sendrx = 0;
	$rec=SQLSelectOne("SELECT devtype FROM msnodes WHERE nid=".$arr[0]);
  
  if ($rec['devtype'] == 1)
  {
    $sendrx = 1;
    $expire = time()+$this->RxExpireTimeout;
  }
  else
  {
    $sendrx = 0;
    $expire = time()+$this->tryTimeout;
  }	   
  
  $data['NID'] = $arr[0];
  $data['SID'] = $arr[1];
  $data['MType'] = $arr[2];
  $data['ACK'] = $arr[3];
  $data['SUBTYPE'] = $arr[4];
  $data['MESSAGE'] = $arr[5];
  $data['EXPIRE'] = $expire;
  $data['SENDRX'] = $sendrx;
  SQLInsert('mssendstack', $data);
  
  //DebMes("Prepare send: ".print_r($data, true));
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
  $mType = 1; // $arr[2];
  $Ack = $arr[3];
  $SubType = $arr[4];
  if ($NId == "") return;
  
  $node=SQLSelectOne("SELECT * FROM msnodes WHERE NID LIKE '".DBSafe($NId)."';"); 
  if (!$node['ID'])
		if (!$this->RegistNewNode($node, $NId))
			return;
  
  // Sensor
  $sens=SQLSelectOne("SELECT * FROM msnodeval WHERE NID LIKE '".DBSafe($NId)."' AND SID LIKE '".DBSafe($SId)."' AND SUBTYPE LIKE '".DBSafe($SubType)."';"); 
  if (!$sens['ID']) {
    $sens['NID'] = $NId;
    $sens['SID'] = $SId;
	$sens['ACK'] = $Ack;
    $sens['SUBTYPE'] = $SubType;
    $sens['ID']=SQLInsert('msnodeval', $sens);
  }
  
  // Req
  $val = $sens['VAL'];
  if ($sens['LINKED_OBJECT'] && $sens['LINKED_PROPERTY']) {		
    $val = getGlobal($sens['LINKED_OBJECT'].'.'.$sens['LINKED_PROPERTY']);		
		//echo "Get from: ".$sens['LINKED_OBJECT'].".".$sens['LINKED_PROPERTY']." = ".$val."\n";
  } 	
  //echo "Set: ".$val."\n";
	
  $this->cmd( "$NId;$SId;$mType;".$sens['ACK'].";$SubType;".$val);
  return false;
}
/**
* Receive Set
*
* @access public
*/
function Internal($arr){	
	$this->getConfig();
	
  // Node
  $NId = $arr[0];  
  $SubType = $arr[4];
  $val = $arr[5];
	if ($NId == "") return;
  
  // Skip tester present
  if ($NId == 255){ // ($NId == 0) ||
    $node = false;
  } else {
    $node=SQLSelectOne("SELECT * FROM msnodes WHERE NID LIKE '".DBSafe($NId)."';"); 
    if (!$node['ID'])
			if (!$this->RegistNewNode($node, $NId))
			  return;
  }
	
	if ($node['LASTREBOOT'] == 0)
		$node['LASTREBOOT'] = date('Y-m-d H:i:s');
  
  switch ($SubType){
    // Battery
    case I_BATTERY_LEVEL:
      if ($node){
        $node['BATTERY'] = $val;
        SQLUpdate('msnodes', $node);
      
        if ($node['BAT_OBJECT'] && $node['BAT_PROPERTY']) {
          setGlobal($node['BAT_OBJECT'].'.'.$node['BAT_PROPERTY'], $val, array($this->name=>'0'));
        } 
      }      
      break;    
      
    // Time
    case I_TIME:
      $this->cmd( $NId.";255;3;0;".I_TIME.";".time() );
      break;
			
		// Version
		case I_VERSION:
			if ($node){
        $node['VER'] = $val;
        SQLUpdate('msnodes', $node);       
      }      
      break;    
      
    // Request data
    case I_ID_REQUEST:          
      
      if (($this->config['MS_AUTOID'] == '') || ($this->config['MS_AUTOID'] == 1)){
        $nextid = $this->config['MS_NEXTID'];
				
				// Check ready has
				while (true)
				{
					$node = SQLSelectOne("SELECT * FROM msnodes WHERE NID LIKE '".DBSafe($nextid)."';");
					if ($node['ID']) {
						$nextid++;
						continue;
					}					
					break;
				}				
        
        if ($nextid < 255){
          // Send new id
          $this->cmd( "255;255;3;0;".I_ID_RESPONSE.";".$nextid );
          echo "Req new ID: $nextid\n";          
        } else {
          echo "Req new ID: out of range\n";
        }
      } else {
        echo "Req new ID: rejected\n";
      }              
      break;
      
    // INCLUSION MODE
    case I_INCLUSION_MODE:      
      $this->config['MS_INCLUSION_MODE']=$val;
      $this->saveConfig();
      break;      
      
    // CONFIG
    case I_CONFIG:
      if ($node){
        $node['PID'] = $val;
				$node['LASTREBOOT'] = date('Y-m-d H:i:s');
        SQLUpdate('msnodes', $node);
      }
      
      // Send ansver - metric      
      $this->cmd( $NId.";255;3;0;".I_CONFIG.";".$this->config['MS_MEASURE'] );
      break;
      
    // SKETCH_NAME
    case I_SKETCH_NAME:
      if ($node){
        $node['SKETCH'] = $val;
        SQLUpdate('msnodes', $node);
      }      
      break;
      
    // SKETCH_VERSION
    case I_SKETCH_VERSION:
      if ($node){
        $node['VER'] = $val;
        SQLUpdate('msnodes', $node);
      }
      
      break;
			
		// I_GATEWAY_READY
		case I_GATEWAY_READY:
			break;
			
	  // I_REQUEST_SIGNING
		case I_SIGNING_PRESENTATION:
			switch ($val)
			{
				case -1:
					break;
					
				default:
					echo  date("Y-m-d H:i:s")." Unknow SIGNING_PRESENTATION ID:".$NId." Sub:".$SubType." Val:".$val."\n";
					break;
			}
			
			break;
			
		// I_HEARTBEAT_RESPONSE
		case I_HEARTBEAT_RESPONSE:
			if ($node){
        $node['HEARTBEAT'] = date('Y-m-d H:i:s');
        SQLUpdate('msnodes', $node);
      
        if ($node['HEARTBEAT_OBJECT'] && $node['HEARTBEAT_PROPERTY']) {
          setGlobal($node['HEARTBEAT_OBJECT'].'.'.$node['HEARTBEAT_PROPERTY'], $val, array($this->name=>'0'));
        } 
      }      
      break;
			
		// I_PING
		case I_PING:
		  // Send I_PONG
			$this->cmd( $NId.";255;3;0;".I_PONG.";".$val);
      break;
			
	  // I_REGISTRATION_REQUEST
		case I_REGISTRATION_REQUEST:
			// Register request to GW
			$val = $val >= $this->MY_CORE_MIN_VERSION;		
			$this->cmd( $NId.";255;3;0;".I_REGISTRATION_RESPONSE.";".$val);
			break;
			
	  default:
			echo  date("Y-m-d H:i:s")." Unknow internal command: ID:".$NId." Sub:".$SubType." Val:".$val."\n";
			break;					
         
    // @@@ 7 - FIND_PARENT
    // 9 - LOG_MESSAGE    
    // @@@ 14 - GATEWAY_READY
  }
}

function RegistNewNode(&$node, $NId)
{
	$ms_autoid = $this->config['MS_AUTOID'];			
	if ($ms_autoid == "0")
		return false;
	
	// Next id
	$nextid = $this->config['MS_NEXTID'];
	
	if ($NId >= $nextid)
	{				
		$this->config['MS_NEXTID']=$NId+1;
		$this->saveConfig();
	}

	// Set new
	$node['NID']=$NId;
	$node['PID'] = 0;
	$node['TITLE']=$NId;
	$node['ID']=SQLInsert('msnodes', $node);
	
	return true;
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
                `SENDRX` int(10) NOT NULL,
                PRIMARY KEY (`ID`)
               ) ENGINE = MEMORY DEFAULT CHARSET=utf8;";

  SQLExec($sqlQuery);
  
  $data = <<<EOD
  msnodes: ID int(10) unsigned NOT NULL auto_increment
  msnodes: NID int(10) NOT NULL
  msnodes: PID int(10) NOT NULL
  msnodes: TITLE varchar(255) NOT NULL DEFAULT ''    
  msnodes: BATTERY varchar(32) NOT NULL DEFAULT ''  
	msnodes: HEARTBEAT datetime
  msnodes: SKETCH varchar(32) NOT NULL DEFAULT ''  
  msnodes: VER varchar(32) NOT NULL DEFAULT ''  
  msnodes: PROT varchar(32) NOT NULL DEFAULT ''  
  msnodes: BAT_OBJECT varchar(255) NOT NULL DEFAULT ''
  msnodes: BAT_PROPERTY varchar(255) NOT NULL DEFAULT ''
	msnodes: HEARTBEAT_OBJECT varchar(255) NOT NULL DEFAULT ''
  msnodes: HEARTBEAT_PROPERTY varchar(255) NOT NULL DEFAULT ''
  msnodes: LOCATION_ID int(10) NOT NULL DEFAULT '0' 
  msnodes: LASTREBOOT datetime
  msnodes: DEVTYPE int(10) DEFAULT '0'
  
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
  msnodeval: REQ int(3) unsigned NOT NULL DEFAULT '0'
EOD;
  parent::dbInstall($data);

 }
// --------------------------------------------------------------------
}

?>