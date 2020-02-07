<?php
/**
 * MySesnor
 *
 * @package project
 * @author Ivan Z. <ivan@jad.ru>
 * @copyright http://www.pstd.ru/ (c)
 * @version 0.1 (wizard, 9:33[Jul 08, 2015])
 */

include_once("intelhex.php");
include_once("crcs.php");

const cOfflineTime = 2*60*60;
 
class mysensor extends module {
	public $tryTimeout = 2; // 2 second
	public $RxExpireTimeout = 21600; // 6*60*60 = 6 hours
	public $MY_CORE_MIN_VERSION = 2;
	public $node_bins = array();
	public $Gates = array();
	
	/**
	 * mySensor
	 *
	 * Module class constructor
	 *
	 * @access private
	 *        
	 */
	function mysensor() {		
		$this->name = "mysensor";
		$this->title = "MySensors";
		$this->module_category = "<#LANG_SECTION_DEVICES#>";
		$this->checkInstalled ();
	}
	
	/**
	 * saveParams
	 *
	 * Saving module parameters
	 *
	 * @access public
	 *        
	 */
	function saveParams($data = 1) {
		$p = array ();
		if (IsSet( $this->id )) {
			$p["id"] = $this->id;
		}
		if (IsSet( $this->view_mode )) {
			$p["view_mode"] = $this->view_mode;
		}
		if (IsSet( $this->edit_mode )) {
			$p["edit_mode"] = $this->edit_mode;
		}
		if (IsSet( $this->data_souce )) {
			$p["data_source"] = $this->data_source;
		}
		if (IsSet( $this->tab )) {
			$p["tab"] = $this->tab;
		}
		return parent::saveParams( $p );
	}
	/**
	 * getParams
	 *
	 * Getting module parameters from query string
	 *
	 * @access public
	 *        
	 */
	function getParams() {
		global $id;
		global $mode;
		global $view_mode;
		global $edit_mode;
		global $data_source;
		global $tab;
		if (isset( $id )) {
			$this->id = $id;
		}
		if (isset( $mode )) {
			$this->mode = $mode;
		}
		if (isset( $view_mode )) {
			$this->view_mode = $view_mode;
		}
		if (isset( $edit_mode )) {
			$this->edit_mode = $edit_mode;
		}
		if (isset( $data_source )) {
			$this->data_source = $data_source;
		}
		if (isset( $tab )) {
			$this->tab = $tab;
		}
	}
	/**
	 * Run
	 *
	 * Description
	 *
	 * @access public
	 *        
	 */
	function run() {
		global $session;
		
		$out = array ();
		if ($this->action == 'admin') {
			$this->admin( $out );
		} else {
			$this->usual( $out );
		}
		if (IsSet( $this->owner->action )) {
			$out['PARENT_ACTION'] = $this->owner->action;
		}
		if (IsSet( $this->owner->name )) {
			$out['PARENT_NAME'] = $this->owner->name;
		}
		$out['VIEW_MODE'] = $this->view_mode;
		$out['EDIT_MODE'] = $this->edit_mode;
		$out['MODE'] = $this->mode;
		$out['ACTION'] = $this->action;
		$out['DATA_SOURCE'] = $this->data_source;
		$out['TAB'] = $this->tab;
		if ($this->single_rec) {
			$out['SINGLE_REC'] = 1;
		}
		$this->data = $out;
		$p = new parser( DIR_TEMPLATES . $this->name . "/" . $this->name . ".html", $this->data, $this );
		$this->result = $p->result;
	}
	/**
	 * BackEnd
	 *
	 * Module backend
	 *
	 * @access public
	 *        
	 */
	function admin(&$out) {
		global $ajax;
		global $filter;
		global $atype;
		
		if ($ajax) {
			header( "HTTP/1.0: 200 OK\n" );
			header( 'Content-Type: text/html; charset=utf-8' );
						
			if ($atype == "info") {
				$arr = array();
				
				$res=SQLSelect(
					"SELECT msnodes.ID, msnodes.GID, msnodes.NID, msnodes.BATTERY, msnodes.devtype, msnodestate.last, msnodestate.state, ".
					"(SELECT count(id) FROM mssendstack WHERE msnodes.GID=mssendstack.GID AND msnodes.NID=mssendstack.NID) AS TOTAL ".
					"FROM msnodes ".
					"LEFT JOIN msnodestate ON msnodes.gid=msnodestate.gid AND msnodes.nid=msnodestate.nid "
				);
				$total=count($res);
				for($i=0;$i<$total;$i++) {
					$info = nodeInfo($res[$i]);
					$arr[] = array("I"=>$res[$i]["ID"], "D"=>$info);
				}
				echo json_encode($arr);				
				exit ();
			}
			
			$limit = 50;
			
			// Find last midifed
			$LastModified = array();
			$res_lines = array();
			$FileName = array();
			
			$FilePatern = ROOT . 'cms/debmes/log_*-cycle_mysensor.php.txt';
			foreach( glob( $FilePatern ) as $file ) {
				$LastModified[] = filemtime( $file );
				$FileName[] = $file;
			}
			$files = array_multisort( $LastModified, SORT_NUMERIC, SORT_ASC, $FileName );
			if (count($LastModified) != 0){
				$lastIndex = count( $LastModified ) - 1;
				
				// Open file
				$data = LoadFile( $FileName[$lastIndex] );
				
				$lines = explode( "\n", $data );
				$lines = array_reverse( $lines );				
				$total = count( $lines );
				$added = 0;
				for($i = 0; $i < $total; $i ++) {
					if (trim( $lines[$i] ) == '') {
						continue;
					}
					
					if ($filter && preg_match( '/' . preg_quote( $filter ) . '/is', $lines[$i] )) {
						$res_lines[] = $lines[$i];
						$added ++;
					} elseif (! $filter) {
						$res_lines[] = $lines[$i];
						$added ++;
					}
					
					if ($added >= $limit) {
						break;
					}
				}
			}
			
			echo implode( "<br/>", $res_lines );
			exit ();
		}
		if (isset( $this->data_source ) && ! $_GET['data_source'] && ! $_POST['data_source']) {
			$out['SET_DATASOURCE'] = 1;
		}
		
		$this->getConfig ();
		$out['MS_MEASURE'] = $this->config['MS_MEASURE'];
		$out['MS_AUTOID'] = $this->config['MS_AUTOID'];
		$out['MS_NEXTID'] = $this->config['MS_NEXTID'];		
		
		$out['CYCLERUN'] = ((time() - gg('cycle_mysensorRun')) < 300 ) ? 1 : 0;
		
		if ($out['MS_MEASURE'] == "") {
			$out['MS_MEASURE'] = 'M';
			$this->config['MS_MEASURE'] = $out['MS_MEASURE'];
			$this->saveConfig ();
		}
		if ($out['MS_AUTOID'] == "") {
			$out['MS_AUTOID'] = 1;
			$this->config['MS_AUTOID'] = $out['MS_AUTOID'];
			$this->saveConfig ();
		}
		if ($out['MS_NEXTID'] == "") {
			$out['MS_NEXTID'] = 10;
			$this->config['MS_NEXTID'] = $out['MS_NEXTID'];
			$this->saveConfig ();
		}
		
		if ($this->data_source == 'mysensors' || $this->data_source == '') {
			if ($this->view_mode == 'update_settings') {
				global $ms_measure;
				global $ms_autoid;
				global $ms_nextid;
				
				$this->config['MS_MEASURE'] = $ms_measure;
				$this->config['MS_AUTOID'] = $ms_autoid;
				$this->config['MS_NEXTID'] = $ms_nextid;
				$this->saveConfig ();				
				
				setGlobal('cycle_mysensorControl', 'restart');
				
				$this->redirect( "?" );
			}
			
			if ($this->view_mode == '' || $this->view_mode == 'nodes') {		
				if ($this->tab == 'gates') {		
					$this->gates( $out );
				} else if ($this->tab == 'firmware') {
					$this->bins( $out );
				} else if ($this->tab == 'log') {
					$this->log( $out );
				} else if ($this->tab == 'queuing') {
					$this->queuing( $out );
				} else {
					$this->nodes( $out );
				}
			}
			if ($this->view_mode == 'gate_edit') {
				$this->edit_gate( $out, $this->id );
			}
			if ($this->view_mode == 'gate_delete') {
				$this->delete_gate( $this->id );
				$this->redirect( "?" );
			}
			if ($this->view_mode == 'node_edit') {
				$this->edit_node( $out, $this->id );
			}
			if ($this->view_mode == 'node_delete') {
				$this->delete_ms( $this->id );
				$this->redirect( "?" );
			}
			if ($this->view_mode == 'bin_edit') {
				$this->edit_bin( $out, $this->id );
			}
			if ($this->view_mode == 'bin_delete') {
				$this->delete_bin( $this->id );
				$this->redirect( "?data_source=$this->data_source&tab=firmware" );
			}
			if ($this->view_mode == 'sensor_add') {
				$this->add_sensor( $out, $this->id );
			}
			if ($this->view_mode == 'sensor_delete') {
				$this->delete_sensor( $this->id );
				
				global $pid;
				$this->redirect( "?data_source=$this->data_source&view_mode=node_edit&id=$pid&tab=sensors" );
			}
			if ($this->view_mode == 'presentation_clean') {
				$this->clean_presentation( $this->id );
				$this->redirect( "?data_source=$this->data_source&view_mode=node_edit&id=$this->id&tab=presentation" );
			}
			if ($this->view_mode == 'queuing_clean') {
				$this->clean_queuing( $this->id );
				$this->redirect( "?data_source=$this->data_source&tab=queuing" );
			}
			if ($this->view_mode == 'queuing_delete') {
				$this->delete_queuing( $this->id );
				$this->redirect( "?data_source=$this->data_source&tab=queuing" );
			}
		}
	}
	/**
	 * FrontEnd
	 *
	 * Module frontend
	 *
	 * @access public
	 *        
	 */
	function usual(&$out) {
		$this->admin( $out );
	}
	/**
	 * Search gates
	 *
	 * @access public
	 *        
	 */
	function gates(&$out) {
		require (DIR_MODULES . $this->name . '/gates.inc.php');
	}
	/**
	 * Search nodes
	 *
	 * @access public
	 *        
	 */
	function nodes(&$out) {
		require (DIR_MODULES . $this->name . '/nodes.inc.php');
	}
	/**
	 * Search bins
	 *
	 * @access public
	 *        
	 */
	function bins(&$out) {
		require (DIR_MODULES . $this->name . '/bins.inc.php');
	}
	/**
	 * Search log
	 *
	 * @access public
	 *        
	 */
	function log(&$out) {
		require (DIR_MODULES . $this->name . '/log.inc.php');
	}
	/**
	 * Search queuing
	 *
	 * @access public
	 *        
	 */
	function queuing(&$out) {
		require (DIR_MODULES . $this->name . '/queuing.inc.php');
	}
	/**
	 * Search sensors
	 *
	 * @access public
	 *        
	 */
	function node_sensors(&$out, $id) {
		require (DIR_MODULES . $this->name . '/sensors_search.inc.php');
	}
	
	/**
	 * Delete gate
	 *
	 * @access public
	 *        
	 */
	function delete_gate($id) {
		$rec = SQLSelectOne( "SELECT * FROM msgates WHERE ID='$id'" );
		
		// some action for related tables
		SQLExec( "DELETE FROM msnodesens WHERE GID='".$rec['ID']."'" );
		SQLExec( "DELETE FROM msnodeval WHERE GID='".$rec['ID']."'" );
		SQLExec( "DELETE FROM msnodes WHERE GID='".$rec['ID']."'" );
		SQLExec( "DELETE FROM msgates WHERE ID='".$rec['ID']."'" );
	}
	
	/**
	 * Edit/add
	 *
	 * @access public
	 *        
	 */
	function edit_gate(&$out, $id) {
		require (DIR_MODULES . $this->name . '/gate_edit.inc.php');
	}
	/**
	 * Edit/add
	 *
	 * @access public
	 *        
	 */
	function edit_node(&$out, $id) {
		require (DIR_MODULES . $this->name . '/node_edit.inc.php');
	}
	/**
	 * Delete node
	 *
	 * @access public
	 *        
	 */
	function delete_ms($id) {
		$rec = SQLSelectOne( "SELECT * FROM msnodes WHERE ID='$id'" );
		// some action for related tables
		SQLExec( "DELETE FROM msnodesens WHERE GID='".$rec['GID']."' AND NID='".$rec['NID']."'" );
		SQLExec( "DELETE FROM msnodeval WHERE GID='".$rec['GID']."' AND NID='".$rec['NID']."'" );
		SQLExec( "DELETE FROM msnodes WHERE ID='".$rec['ID']."'" );
	}
	/**
	 * Edit/add
	 *
	 * @access public
	 *        
	 */
	function edit_bin(&$out, $id) {
		require (DIR_MODULES . $this->name . '/bin_edit.inc.php');
	}
	/**
	 * Delete node
	 *
	 * @access public
	 *        
	 */
	function delete_bin($id) {
		$rec = SQLSelectOne( "SELECT * FROM msbins WHERE ID='$id'" );
		// some action for related tables
		SQLExec( "DELETE FROM msbins WHERE ID='" . $rec['ID'] . "'" );
	}
	
	/**
	 * Delete queuing
	 *
	 * @access public
	 *        
	 */
	function delete_queuing($id) {
		$rec = SQLSelectOne( "SELECT * FROM mssendstack WHERE ID='$id'" );
		// some action for related tables
		SQLExec( "DELETE FROM mssendstack WHERE ID='".$rec['ID']."'" );
	}
	
	/**
	 * Clear queuing
	 *
	 * @access public
	 *        
	 */
	function clean_queuing($id) {
		// some action for related tables
		SQLExec( "DELETE FROM mssendstack");
	}
	
	/**
	 * Add sensor
	 *
	 * @access public
	 *        
	 */
	function add_sensor(&$out, $id) {
		require (DIR_MODULES . $this->name . '/sensor_add.inc.php');
	}
	/**
	 * Delete sensor
	 *
	 * @access public
	 *        
	 */
	function delete_sensor($id) {
		$rec = SQLSelectOne( "SELECT * FROM msnodeval WHERE ID='$id'" );
		// some action for related tables
		SQLExec( "DELETE FROM msnodeval WHERE ID='" . $rec['ID'] . "'" );
	}
	/**
	 * Handle property object
	 *
	 * @access public
	 *        
	 */
	function propertySetHandle($object, $property, $value) {
		$properties = SQLSelect( "SELECT ID FROM msnodeval WHERE LINKED_OBJECT LIKE '" . DBSafe( $object ) . "' AND LINKED_PROPERTY LIKE '" . DBSafe( $property ) . "'" );
		$total = count( $properties );
		if ($total) {
			for($i = 0; $i < $total; $i ++) {
				$this->setProperty( $properties[$i]['ID'], $value );
			}
		}
	}
	/**
	 * Clean presentation
	 *
	 * @access public
	 *        
	 */
	function clean_presentation($id) {
		global $gid;
		global $nid;
		
		SQLExec( "DELETE FROM msnodesens WHERE GID='$gid' AND NID='$nid'" );
	}
	/**
	 * Receive Presentation
	 *
	 * @access public
	 *        
	 */
	function Presentation($gate, $arr) {

		// GId
		$GId = $gate->GId;
		
		// Node
		$NId = $arr[0];
		if ($NId == "") return;
		
		$SId = $arr[1];		
		$SubType = $arr[4];
		$info = $arr[5];
		
		// Log
		$Ack = $arr[3];
		$gate->AddLog(cLogMessage, ">> 0:Presentation; Gate:$GId; Node:$NId; Sensor:$SId; Ack:$Ack; Sub:$SubType:".SubTypeDecode(C_PRESENTATION, $SubType)."; Msg:$info");
		
		$node = SQLSelectOne( "SELECT * FROM msnodes WHERE GID LIKE '".DBSafe($GId)."' AND NID LIKE '".DBSafe($NId)."';" );
		if (!$node['ID'])
			if (! $this->RegistNewNode( $gate, $node, $NId ))
				return;
			
		// Arduino Node
		if ($SId == 255) {

			switch ($SubType) {
			case S_ARDUINO_NODE:
				$node['REPEATER'] = 0;
				$node['PROT'] = $arr[5];
				break;

			case S_ARDUINO_REPEATER_NODE:
				$node['REPEATER'] = 1;
				$node['PROT'] = $arr[5];
				break;

			default :
				return;
			}
			
			if ($node['LASTREBOOT'] == 0)
				$node['LASTREBOOT'] = date( 'Y-m-d H:i:s' );
			
			SQLUpdate( 'msnodes', $node );
			
		} else {
			// Sensor
			$sens = SQLSelectOne( "SELECT * FROM msnodesens WHERE GID=$GId AND NID LIKE '".DBSafe($NId)."' AND SID LIKE '".DBSafe($SId)."' AND SUBTYPE LIKE '".DBSafe( $SubType )."';" );
			if (! $sens['ID']) {
				$sens['GID'] = $GId;
				$sens['NID'] = $NId;
				$sens['SID'] = $SId;
				$sens['SUBTYPE'] = $SubType;
				$sens['INFO'] = utf8_encode($info);
				$sens['ID'] = SQLInsert( 'msnodesens', $sens );
			} else {
				$sens['INFO'] = utf8_encode($info);
				SQLUpdate( 'msnodesens', $sens );
			}
		}
		
		$this->updateNState($GId, $NId, false);
	}
	/**
	 * Receive Set
	 *
	 * @access public
	 *        
	 */
	function Set($gate, $arr) {
		// Node
		$GId = $gate->GId;
		$NId = $arr[0];
		$SId = $arr[1];
		$SubType = $arr[4];
		$val = utf8_encode($arr[5]);
		if ($NId == "")	return;
		
		// Log
		$Ack = $arr[3];
		$gate->AddLog(cLogMessage, ">> 1:Set; Gate:$GId; Node:$NId; Sensor:$SId; Ack:$Ack; Sub:$SubType:".SubTypeDecode(C_SET, $SubType)."; Msg:$val");
		
		$node = SQLSelectOne( "SELECT * FROM msnodes WHERE GID LIKE '".DBSafe( $GId )."' AND NID LIKE '".DBSafe( $NId )."';" );
		if (! $node['ID'])
			if (! $this->RegistNewNode( $gate, $node, $NId ))
				return;
		
		// Sensor
		$sens = SQLSelectOne( "SELECT * FROM msnodeval WHERE GID LIKE '".DBSafe( $GId )."' AND NID LIKE '".DBSafe( $NId )."' AND SID LIKE '".DBSafe( $SId )."' AND SUBTYPE LIKE '".DBSafe( $SubType )."';" );
		if (! $sens['ID']) {
			$sens['GID'] = $GId;
			$sens['NID'] = $NId;
			$sens['SID'] = $SId;
			$sens['SUBTYPE'] = $SubType;
			$sens['ID'] = SQLInsert( 'msnodeval', $sens );
		}
		
		// Delete ACK
		if ($arr[3] == 1) {
			SQLExec( "DELETE FROM mssendstack WHERE GID='".$GId."' AND NID='".$NId."' AND SID='".$SId."' AND MType='".$arr[2]."' AND SUBTYPE='".$SubType."' AND MESSAGE='".$val."' AND SENDRX=0" );
		}
				
		// Set
		$sens['UPDATED'] = date( 'Y-m-d H:i:s' );
		$sens['VAL'] = $val;
		if (strlen($sens['VAL']) > 32) return;
		
		SQLUpdate( 'msnodeval', $sens );
				
		if ($sens['LINKED_OBJECT'] && $sens['LINKED_PROPERTY']) {
			setGlobal( $sens['LINKED_OBJECT'] . '.' . $sens['LINKED_PROPERTY'], $val, array($this->name => '0') );
		}
		
		$this->updateNState($GId, $NId, false);
	}
	/**
	 * Title
	 *
	 * Description
	 *
	 * @access public
	 *        
	 */
	function setProperty($prop_id, $value, $set_linked = 0) {
		$rec = SQLSelectOne( "SELECT * FROM msnodeval WHERE msnodeval.id=$prop_id" );
		if (! $rec['ID'])
			return 0;
		
		$rec['UPDATED'] = date( 'Y-m-d H:i:s' );
		
		// Set to node value
		if ($rec['VAL'] != $value) {
			// Not set for rollback --- $rec['VAL'] = $value;
			SQLUpdate( 'msnodeval', $rec );
		}
		
		$this->cmd( $rec['GID'], $rec['NID'].";".$rec['SID'].";1;".$rec['ACK'].";".$rec['SUBTYPE'].";".$value );
	}
	/**
	 * Title
	 *
	 * Description
	 *
	 * @access public
	 *        
	 */
	function cmd($GId, $str, $immediately = false) {		
		$arr = explode( ';', $str, 6 );
		
		// For sleep node
		$sendrx = 0;
		$rec = SQLSelectOne( "SELECT devtype FROM msnodes WHERE gid=".$GId." AND nid=".$arr[0] );

		$data['GID'] = $GId;
		$data['NID'] = $arr[0];
		$data['SID'] = $arr[1];
		$data['MType'] = $arr[2];
		$data['ACK'] = $arr[3];
		$data['SUBTYPE'] = $arr[4];
		$data['MESSAGE'] = $arr[5];
		
		if (($rec['devtype'] == 1) && (!$immediately)) {
			$data['EXPIRE'] = time () + $this->RxExpireTimeout;
			$data['SENDRX'] = 1;

			$rec = SQLSelectOne( "SELECT id FROM mssendstack WHERE gid=".$GId." AND nid=".$arr[0]." AND sid=".$arr[1]." AND MType=".$arr[2]." AND SUBTYPE=".$arr[4] );

			if (! $rec['id']) {
				SQLInsert( 'mssendstack', $data );
			} else {		
				$data['ID']	= $rec['id'];
				SQLUpdate( 'mssendstack', $data );
			}
			
		} else {
			$data['EXPIRE'] = time () + $this->tryTimeout;
			$data['SENDRX'] = 0;
			
			SQLInsert( 'mssendstack', $data );
		}		
		//DebMes("Prepare send: ".print_r($data, true));
	}
	/**
	 * Receive req
	 *
	 * @access public
	 *        
	 */
	function Req($gate, $arr) {
		
		// Node
		$GId = $gate->GId;
		$NId = $arr[0];
		$SId = $arr[1];
		$mType = 1; // $arr[2];
		$Ack = $arr[3];
		$SubType = $arr[4];
		if ($NId == "")	return;
		
		// Log		
		$gate->AddLog(cLogMessage, ">> 2:Req; Gate:$GId; Node:$NId; Sensor:$SId; Ack:$Ack; Sub:$SubType:".SubTypeDecode(C_REQ, $SubType)."; Msg:$val");
		
		$node = SQLSelectOne( "SELECT * FROM msnodes WHERE GID LIKE '".DBSafe( $GId )."' AND NID LIKE '".DBSafe( $NId )."';" );
		if (! $node['ID'])
			if (! $this->RegistNewNode( $gate, $node, $NId ))
				return;
		
		// Sensor
		$sens = SQLSelectOne( "SELECT * FROM msnodeval WHERE GID LIKE '".DBSafe( $GId )."' AND NID LIKE '".DBSafe( $NId )."' AND SID LIKE '".DBSafe( $SId )."' AND SUBTYPE LIKE '".DBSafe( $SubType )."';" );
		if (! $sens['ID']) {
			$sens['GID'] = $GId;
			$sens['NID'] = $NId;
			$sens['SID'] = $SId;
			$sens['ACK'] = $Ack;
			$sens['SUBTYPE'] = $SubType;
			$sens['ID'] = SQLInsert( 'msnodeval', $sens );
		}
		
		// Req
		$val = $sens['VAL'];
		if ($sens['LINKED_OBJECT'] && $sens['LINKED_PROPERTY']) {
			$val = getGlobal( $sens['LINKED_OBJECT'] . '.' . $sens['LINKED_PROPERTY'] );
		}
		
		$this->cmd( $GId, "$NId;$SId;$mType;" . $sens['ACK'] . ";$SubType;" . $val, true );
		
		$this->updateNState($GId, $NId, false);
		return false;
	}
	/**
	 * Receive Set
	 *
	 * @access public
	 *        
	 */
	function Internal($gate, $arr) {
		$this->getConfig();
		
		// Node
		$GId = $gate->GId;
		$NId = $arr[0];
		$SubType = $arr[4];
		$val = $arr[5];
		if ($NId == "")	return;
		
		// Log		
		$SId = $arr[1];
		$Ack = $arr[3];
		$gate->AddLog(cLogMessage, ">> 3:Internal; Gate:$GId; Node:$NId; Sensor:$SId; Ack:$Ack; Sub:$SubType:".SubTypeDecode(C_INTERNAL, $SubType)."; Msg:$val");
			
		// Skip tester present
		if ($NId == 255) { // ($NId == 0) ||
			$node = false;
		} else {
			$node = SQLSelectOne( "SELECT * FROM msnodes WHERE GID LIKE '".DBSafe( $GId )."' AND NID LIKE '".DBSafe( $NId )."';" );
			if (! $node['ID'])
				if (! $this->RegistNewNode( $gate, $node, $NId ))
					return;
		}
		
		if ($node['LASTREBOOT'] == 0)
			$node['LASTREBOOT'] = date( 'Y-m-d H:i:s' );
		
		switch ($SubType) {
			// Battery
			case I_BATTERY_LEVEL :
				if ($node) {
					$node['BATTERY'] = $val;
					SQLUpdate( 'msnodes', $node );
					
					if ($node['BAT_OBJECT'] && $node['BAT_PROPERTY']) {
						setGlobal( $node['BAT_OBJECT'] . '.' . $node['BAT_PROPERTY'], $val, array($this->name => '0') );
					}
				}
				break;
			
			// Time
			case I_TIME :
				$this->cmd( $GId, $NId . ";255;3;0;" . I_TIME . ";" . time (), true );
				break;
			
			// Version
			case I_VERSION :
				if ($node) {
					$node['VER'] = $val;
					SQLUpdate( 'msnodes', $node );
				}
				break;
			
			// Request data
			case I_ID_REQUEST :
				$AutoIdType = $this->config['MS_AUTOID'];
				if ($AutoIdType == '') {
					$AutoIdType == 1;
				}
				
				switch ($AutoIdType) {
					// Reject
					case 0: 
						$gate->AddLog(cLogError, "Req new ID: rejected");
						break;
					// Auto
					case 1:
						$nextid = $this->config['MS_NEXTID'];
						
						// Check ready has
						while ( true ) {
							$node = SQLSelectOne( "SELECT * FROM msnodes WHERE GID LIKE '".DBSafe( $GId )."' AND NID LIKE '".DBSafe( $nextid )."';" );
							if ($node['ID']) {
								$nextid ++;
								continue;
							}
							break;
						}
						
						if ($nextid < 255) {
							// Send new id
							$this->cmd( $GId, "255;255;3;0;" . I_ID_RESPONSE . ";" . $nextid, true );
							$gate->AddLog(cLogDebug, "Req new ID: $nextid");
						} else { 
							$gate->AddLog(cLogError, "Req new ID: out of range");
						}
						break;
					// Manual
					case 2:
						$nextid = $this->config['MS_NEXTID'];
						$this->cmd( $GId, "255;255;3;0;" . I_ID_RESPONSE . ";" . $nextid, true );
						
						$gate->AddLog(cLogDebug, "Req new ID: $nextid");
						break;						
						
					default:
						$gate->AddLog(cLogError, "Req new ID: Error type");
						break;
				}
				break;
			
			// INCLUSION MODE
			case I_INCLUSION_MODE :
				// TODO: indicate
				break;
			
			// CONFIG
			case I_CONFIG :
				if ($node) {
					$node['PID'] = $val;
					$node['LASTREBOOT'] = date( 'Y-m-d H:i:s' );
					SQLUpdate( 'msnodes', $node );
				}
				
				// Send ansver - metric
				$this->cmd( $GId, $NId.";255;3;0;".I_CONFIG.";".$this->config['MS_MEASURE'], true );
				break;
			
			// LOG_MESSAGE
			case I_LOG_MESSAGE :
				$gate->AddLog(cLogMessage, "Log message: Gate:$GId; Node:$NId - $val");
			
			// SKETCH_NAME
			case I_SKETCH_NAME :
				if ($node) {
					$node['SKETCH'] = utf8_encode($val);
					SQLUpdate( 'msnodes', $node );
				}
				break;
			
			// SKETCH_VERSION
			case I_SKETCH_VERSION :
				if ($node) {
					$node['VER'] = utf8_encode($val);
					SQLUpdate( 'msnodes', $node );
				}
				break;
			
			// I_GATEWAY_READY
			case I_GATEWAY_READY :
				break;
			
			// I_REQUEST_SIGNING
			case I_SIGNING_PRESENTATION :
				switch ($val) {
					case - 1 :
						break;
					
					default :
						$gate->AddLog(cLogError, "Unknow SIGNING_PRESENTATION Gate:$GId; ID:$NId; Sub:$SubType - Val:$val");
						break;
				}
				
				break;
			
			// I_DISCOVER_RESPONSE
			case I_DISCOVER_RESPONSE :
				if ($node) {
					$node['PID'] = utf8_encode($val);
					SQLUpdate( 'msnodes', $node );
				}
				break;
						
			// I_HEARTBEAT_RESPONSE, I_PRE_SLEEP_NOTIFICATION
			case I_HEARTBEAT_RESPONSE:
			case I_PRE_SLEEP_NOTIFICATION:
				if ($node) {
					// Smart sleep
					if ($node['DEVTYPE'] == 1)
						$this->doSend($gate, $NId);
				
					$node['HEARTBEAT'] = date( 'Y-m-d H:i:s' );
					SQLUpdate( 'msnodes', $node );
					
					if ($node['HEARTBEAT_OBJECT'] && $node['HEARTBEAT_PROPERTY']) {
						setGlobal( $node['HEARTBEAT_OBJECT'] . '.' . $node['HEARTBEAT_PROPERTY'], $val, array($this->name => '0') );
					}
				}
				break;
			
			// I_PING
			case I_PING :
				// Send I_PONG
				$this->cmd( $GId, $NId.";255;3;0;".I_PONG.";".$val, true );
				break;
			
			// I_REGISTRATION_REQUEST
			case I_REGISTRATION_REQUEST :
				// Register request to GW
				$val = $val >= $this->MY_CORE_MIN_VERSION;
				$this->cmd( $Gid, $NId.";255;3;0;".I_REGISTRATION_RESPONSE.";".$val, true );
				break;
				
			// I_DEBUG
			case I_DEBUG:
				$gate->AddLog(cLogMessage, "Debug: Gate:$GId; Node:$NId = $val");
				break;
			
			// I_POST_SLEEP_NOTIFICATION
			case I_POST_SLEEP_NOTIFICATION:
				break;
			
			default :
				$gate->AddLog(cLogError, "Unknow internal command: Gate:$GId; Node:$NId Sub:$SubType Val:$val");
				break;
			
			// @@@ 7 - FIND_PARENT
			// 9 - LOG_MESSAGE
			// @@@ 14 - GATEWAY_READY
		}
	
		$this->updateNState($GId, $NId, false);
	}
	
	/**
	 * Stream packet - Response FW
	 *
	 * @access public
	 *        
	 */
	function ResponseFW($GId, $NId){

		// Delete cashed bin
		unset($this->node_bins[$GId][$NId]);		
	
		// Load bin									
		$rec = SQLSelectOne( "SELECT * FROM msbins WHERE ID=( SELECT FIRMWARE FROM msnodes WHERE GID LIKE '".DBSafe( $GId )."' AND NID LIKE '".DBSafe( $NId )."');" );
		if (!$rec['ID']){
			return "Binary for: Gate:$GId; Node:$NId - not found";
		}
		
		// Parse HEX
		$parser = new IntelHex();
		if (!$parser->Parse($rec['BIN'])) {			
			return "Error load bin Gate:$GId; Node:$NId : $parser->LastError";
		}
		if ($parser->FirstAddr != 0){
			return "Error load bin Gate:$GId; Node:$NId : First adress $parser->FirstAddr";
		}
		$parser->NormalizePage(cNormalPage);
		
		// Make CRC15
		$crc = crc16($parser->Data);
		
		// Sent to cashed
		$this->node_bins[$GId][$NId] = array(
			"data" => $parser->Data,
			"crc" => bin2hex($crc),
			"bloks" => bin2hex( pack("S", strlen($parser->Data)/16) )
		);
		
		// Send				
		$data = sprintf("%04x", $NId)."0100".$this->node_bins[$GId][$NId]["bloks"].$this->node_bins[$GId][$NId]["crc"];
		$this->cmd( $GId, "$NId;0;4;0;1;". $data, true );
		
		return true;
	}
	/**
	 * Stream packet
	 *
	 * @access public
	 *        
	 */
	function Stream($gate, $arr) {
		//$this->getConfig ();
		
		// Node
		$GId = $gate->GId;
		$NId = $arr[0];		
		$SubType = $arr[4];
		$val = $arr[5];
		if ($NId == "")	return;
		$nstate = false;
		
		// Log				
		$Ack = $arr[3];
		$SId = $arr[1];
		$gate->AddLog(cLogMessage, ">> 4:Stream; Gate:$GIg; Node:$NId; Sensor:$SId; Ack:$Ack; Sub:$SubType:".SubTypeDecode(C_STREAM, $SubType)."; Msg:$val");
		
		$node = SQLSelectOne( "SELECT * FROM msnodes WHERE GID LIKE '".DBSafe( $GId )."' AND NID LIKE '".DBSafe( $NId )."';" );
		if (!$node['ID'])
			if (! $this->RegistNewNode($gate, $node, $NId))
				return;
					
		switch ($SubType) {
			// Request new FW, payload contains current FW details
			case 0x00:				
				// $NID = substr( $val, 1, 4 );
				$CVer = substr( $val, 4, 4 );
				$CBloks = hexdec( substr( $val, 8, 4 ) );
				$CCrc = hexdec( substr( $val, 12, 4 ) );
				$BLh = hexdec( substr( $val, 16, 2 ) );
				$BLl = hexdec( substr( $val, 18, 2 ) );
				
				// Test version	
				$gate->AddLog(cLogDebug, "CV=$CVer ; BLV=$BLh.$BLl");
				
				$node['BOOTVER'] = "OTA:$BLh.$BLl";
				SQLUpdate( 'msnodes', $node );
			
				$ret = $this->ResponseFW($GId, $NId);
				if ($ret !== true){
					$gate->AddLog(cLogError, $ret);
				}
				
				$nstate = "";
				
				break;
				
			// Request FW block
			case 0x02:
				$ndata = $this->node_bins[$GId][$NId];
				if (empty($ndata)){
					$gate->AddLog(cLogError, "Cashed bin Gate:$GId Node:$NId - not found");
					return;
				}
				$size = strlen($ndata["data"]);
				
				// Type
				$CVer = substr( $val, 4, 4 );
				$CBlok = substr( $val, 8, 4 );
				
				$Data2 = unpack("S", hex2bin($CBlok));
				$BlockP = $Data2[1]*16;
				
				// Test version
				if ($CVer != "0100"){
					$gate->AddLog(cLogError, "Unknow boot version Gate:$GId Node:$NId - $CVer");
					return;
				}
				
				if ($BlockP+16 > $size) {
					$gate->AddLog(cLogError, "Out of data: Gate:$GId; Node:$NId - $BlockP");
					return;
				}
				$data = sprintf("%04x", $NId)."0100".$CBlok.bin2hex( substr($ndata["data"], $BlockP, 16));
				
				// usleep(100000);
				
				$gate->send($NId, 0, 4, 0, 3, $data);				
				$gate->AddLog(cLogMessage, "<@ 4:Stream; Gate:$GId; Node:$NId; Sensor:0; Ack:0; Sub:3:ST_FIRMWARE_RESPONSE; Msg:$data");
				
				// State
				$nstate = "";
				if ($BlockP != 0){
					$nstate = Round(100-($BlockP*100 / $size), 2)."%";
				}
				
				break;
			
			default :
				$gate->AddLog(cLogError, "Unknow stream command: Gate:$GId Node:$NId; Sub:$SubType; Val:$val");
				break;
		}
		
		$this->updateNState($GId, $NId, $nstate);
	}
	
	/**
	 * Send data
	 *
	 * @access public
	 *        
	 */
	function doSend($gate, $NId = -1) {
		global $MSType;

		$GId = $gate->GId;
		
		if ($NId == -1) {
			$rec=SQLSelectOne("SELECT * FROM mssendstack WHERE SENDRX=0 AND GID=$GId;");
		} else {
			$rec=SQLSelectOne("SELECT * FROM mssendstack WHERE SENDRX=1 AND GID=$GId AND NID=$NId;");
		}
		if (!$rec['ID']) return;
		
		$expire = $rec['EXPIRE'] < time();
		  
		// Del not ACK packet
		if (($rec['ACK'] == 0) || $expire){
			SQLExec("DELETE FROM mssendstack WHERE ID='".$rec['ID']."'");
		}
		  
		if ($expire){
			$gate->AddLog(cLogMessage, "Expire $ID ".date("Y-m-d H:i:s", $rec['EXPIRE'])." <> ".date("Y-m-d H:i:s"));

			$sens=SQLSelectOne("SELECT * FROM msnodeval WHERE GID='".$rec['GID']."' AND NID='".$rec['NID']."' AND SID='".$rec['SID']."' AND SUBTYPE='".$rec['SUBTYPE']."';"); 
			if ($sens['LINKED_OBJECT'] && $sens['LINKED_PROPERTY']) {
				$gate->AddLog(cLogMessage, "Expire send set rollback : ".$sens['LINKED_OBJECT'].'.'.$sens['LINKED_PROPERTY']."=".$sens['VAL']);
				// Rollback value if not comin
				setGlobal($sens['LINKED_OBJECT'].'.'.$sens['LINKED_PROPERTY'], $rec['VAL'], array($this->name => '0'));
			}
			
			return;
		}
		
		// Send
		$NId = $rec['NID'];
		$SId = $rec['SID'];
		$MType = $rec['MType'];
		$Ack = $rec['ACK'];
		$SubType = $rec['SUBTYPE'];
		$Data = $rec['MESSAGE'];
		$gate->send($NId, $SId, $MType, $Ack, $SubType, $Data);
		
		$gate->AddLog(cLogMessage, "<@ $MType:".$MSType[$MType]."; Gate:$GId; Node:$NId; Sensor:$SId; Ack:$Ack; Sub:$SubType:".SubTypeDecode($MType, $SubType)."; Msg:$Data");
	}
		
	function RegistNewNode($gate, &$node, $NId) {		

		$ms_autoid = $this->config['MS_AUTOID'];
		if ($ms_autoid == "0")
			return false;
					
		// Set new
		$node['GID'] = $gate->GId;
		$node['NID'] = $NId;
		$node['PID'] = 0;
		$node['TITLE'] = $NId;
		$node['ID'] = SQLInsert( 'msnodes', $node );
		
		return true;
	}
	
	/**
	 * Install
	 *
	 * Module installation routine
	 *
	 * @access private
	 *        
	 */
	function install($data = '') {
		parent::install ();
		
		// Move configuration to table		
		$rec = SQLSelectOne("SELECT * FROM msgates");
		if (!$rec['ID']) {
			$this->getConfig();			

			$url = "";
			if ($this->config['MS_CONTYPE'] == "" || $this->config['MS_CONTYPE'] == "0")
				$url = $this->config['MS_HOST'].":".$this->config['MS_PORT'];
			else 
				$url = $this->config['MS_SERIAL'];

			$data = [];
			$data["TITLE"] = "Main";
			$data["GTYPE"] = $this->config['MS_CONTYPE'];
			$data["URL"] = $url;			

			SQLInsert( 'msgates', $data );
		}
	}
		
	function updateNState($GId, $NId, $state) {
		$time = time();
		if ($state === false) {
			SQLExec ("INSERT INTO msnodestate (GID,NID,last) VALUES ($GId,$NId,'".$time."' ) ON DUPLICATE KEY UPDATE last='".$time."';");
		} else {
			SQLExec ("INSERT INTO msnodestate (GID,NID,state,last) VALUES ($GId,$NId,'".$state."', '".$time."' ) ON DUPLICATE KEY UPDATE state='".$state."', last='".$time."';");
		}
	}
	/**
	 * dbInstall
	 *
	 * Database installation routine
	 *
	 * @access private
	 *        
	 */
	function dbInstall($data) {
		
		// Send message
		SQLExec( "DROP TABLE IF EXISTS `mssendstack`;" );
		
		$sqlQuery = "CREATE TABLE IF NOT EXISTS `mssendstack`
               (`ID`  int(10) unsigned NOT NULL auto_increment,
			    `GID` int(10) NOT NULL DEFAULT 1,
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
		
		SQLExec( $sqlQuery );
		
		// Nodestate
		SQLExec( "DROP TABLE IF EXISTS `msnodestate`;" );
		
		$sqlQuery = "CREATE TABLE IF NOT EXISTS `msnodestate`
               (`GID` int(10) NOT NULL DEFAULT 1,
				`NID` int(10) NOT NULL,
				`State` varchar(32) NULL DEFAULT NULL,
				`last` BIGINT NOT NULL,
			   PRIMARY KEY (`NID`,`GID`)
               ) ENGINE = MEMORY DEFAULT CHARSET=utf8;";
		
		SQLExec( $sqlQuery );

		// msgates - Gates
		// msnodes - Nodes
		// msnodeval - Sensors
		// msnodesens - Presents
		// msbins - Binares
		// mssendstack - Steck of send msgs
		// msnodestate - Node status
		
		$data = <<<EOD

	msgates: ID int(10) unsigned NOT NULL auto_increment
	msgates: TITLE varchar(255) NOT NULL DEFAULT ''
	msgates: ACTIVE int(3) unsigned NOT NULL DEFAULT '1'
	msgates: GTYPE int(10) NOT NULL DEFAULT 0
	msgates: URL varchar(255) NOT NULL DEFAULT ''	

	msnodes: ID int(10) unsigned NOT NULL auto_increment
	msnodes: GID int(10) NOT NULL DEFAULT 1
	msnodes: NID int(10) NOT NULL
	msnodes: PID int(10) NOT NULL
	msnodes: TITLE varchar(255) NOT NULL DEFAULT ''
	msnodes: BATTERY varchar(32) NOT NULL DEFAULT ''
	msnodes: HEARTBEAT datetime
	msnodes: SKETCH varchar(32) NOT NULL DEFAULT ''
	msnodes: VER varchar(32) NOT NULL DEFAULT ''
	msnodes: PROT varchar(32) NOT NULL DEFAULT ''
	msnodes: FIRMWARE int(10) NOT NULL DEFAULT -1
	msnodes: BAT_OBJECT varchar(255) NOT NULL DEFAULT ''
	msnodes: BAT_PROPERTY varchar(255) NOT NULL DEFAULT ''
	msnodes: HEARTBEAT_OBJECT varchar(255) NOT NULL DEFAULT ''
	msnodes: HEARTBEAT_PROPERTY varchar(255) NOT NULL DEFAULT ''
	msnodes: LOCATION_ID int(10) NOT NULL DEFAULT '0' 
	msnodes: LASTREBOOT datetime
	msnodes: DEVTYPE int(10) DEFAULT '0'
	msnodes: BOOTVER varchar(255) NOT NULL DEFAULT ''
	msnodes: REPEATER int(10) DEFAULT '0'
	
	msnodesens: ID int(10) unsigned NOT NULL auto_increment
	msnodesens: GID int(10) NOT NULL DEFAULT 1
	msnodesens: NID int(10) NOT NULL 
	msnodesens: SID int(10) NOT NULL    
	msnodesens: SUBTYPE int(10) NOT NULL
	msnodesens: INFO varchar(255) NOT NULL DEFAULT ''

	msnodeval: ID int(10) unsigned NOT NULL auto_increment
	msnodeval: GID int(10) NOT NULL DEFAULT 1
	msnodeval: NID int(10) NOT NULL  
	msnodeval: SID int(10) NOT NULL  
	msnodeval: SUBTYPE int(10) NOT NULL  
	msnodeval: VAL varchar(32) NOT NULL DEFAULT ''  
	msnodeval: UPDATED datetime
	msnodeval: LINKED_OBJECT varchar(255) NOT NULL DEFAULT ''
	msnodeval: LINKED_PROPERTY varchar(255) NOT NULL DEFAULT ''
	msnodeval: LINKED_METHOD varchar(100) NOT NULL DEFAULT ''
	msnodeval: ACK int(3) unsigned NOT NULL DEFAULT '0'
	msnodeval: REQ int(3) unsigned NOT NULL DEFAULT '0'
	
	msbins: ID int(10) unsigned NOT NULL auto_increment
	msbins: TITLE varchar(255) NOT NULL DEFAULT ''
	msbins: VER varchar(255) NOT NULL DEFAULT ''
	msbins: BIN LONGBLOB
	msbins: CRC char(4)
	msbins: BLOKS char(4)
EOD;
		parent::dbInstall( $data );
		
		SQLExec("ALTER TABLE `msbins` CHANGE `VER` `VER` varchar(255) NOT NULL DEFAULT ''");
		SQLExec("ALTER TABLE `msbins` CHANGE `CRC` `CRC` char(4)");
		SQLExec("ALTER TABLE `msbins` CHANGE `BLOKS` `BLOKS` char(4)");
		SQLExec("ALTER TABLE `msbins` CHANGE `BIN` `BIN` LONGBLOB");
	}
	// --------------------------------------------------------------------
}

function nodeInfo($res) {
	$info = "";
	
	//$info .= "DT: ".print_r($res, true)."<br/>";
	
	// Battery
	$blevel = $res['BATTERY'];
	
	$battery = $res['BATTERY'];
	$bicon = '';//'ban';
	$bcolor = 'black';
	if ($blevel != null){
		$battery .= "%";
				
		if ($blevel >= 80) {
			$bicon = 'battery-full fa-rotate-270';
			$bcolor = 'green';
		} else if ($blevel >= 60) {
			$bicon = 'battery-three-quarters fa-rotate-270';
			$bcolor = 'green';
		} else if ($blevel >= 40) {
			$bicon = 'battery-half fa-rotate-270';
			$bcolor = 'green';
		} else if ($blevel >= 20) {
			$bicon = 'battery-quarter fa-rotate-270';
			$bcolor = 'orange';
		} else {
			$bicon = 'battery-empty fa-rotate-270';
			$bcolor = 'red';
		}
	}

	if ($bicon != ""){
		$info .= "<i class=\"fa fa-$bicon\" style=\"color:$bcolor\" title=\"Battery\"></i>".$battery;
	}
	
	// State
	if ($res['state']) {
		if ($info != "") $info .= "<br/>";
		$info .= "Write: ".$res['state'];
	}
	
	// Message stack
	if ($res['TOTAL']) {
		if ($info != "") $info .= "<br/>";
		$info .= "Messages: ".$res['TOTAL'];
	}

	// Last
	$iclass = "label-danger";
	if ($res['last'] == "") {
		$itime = "Offline";
	} else {
		$itime = $res['last'];		
		if (time()-$itime < cOfflineTime) {
			$iclass = "label-success";				
		}
		if (function_exists('getPassedText')){
		  $itime = getPassedText($itime);
		} else {
		  $itime = date('d.m.Y H:i',$itime);
		}
	}
	if ($info != "") $info .= "<br/>";
	$info .= "<span class=\"label $iclass\">$itime</span>";
	
	return $info;
}

?>
