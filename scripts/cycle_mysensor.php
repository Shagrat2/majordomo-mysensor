<?php

chdir(dirname(__FILE__) . '/../');

include_once("./config.php");
include_once("./lib/loader.php");
include_once("./lib/threads.php");

set_time_limit(0);

// connecting to database
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
 
include_once("./load_settings.php");
include_once(DIR_MODULES . "control_modules/control_modules.class.php");
include_once(DIR_MODULES . 'mysensor/mysensor.class.php');

set_time_limit(0);
$ms = new mysensor();
$ms->getConfig();

echo date("H:i:s") . " running " . basename(__FILE__) . PHP_EOL;

// Make gates
$gates = array();

$rec = SQLSelect("SELECT * FROM msgates WHERE active=1");

$total=count($rec);
if ($total) {
	for($i=0;$i<$total;$i++) {
	
		// Make class
		if ($rec[$i]['GTYPE'] == 1) {
			require_once("./modules/mysensor/phpMSCom.php");

			$gate = new MySensorMasterCom($rec[$i]['ID'], $rec[$i]['URL']);
		} else {		
			require_once("./modules/mysensor/phpMSTcp.php");

			$arr = explode(":", $rec[$i]['URL']);
	
			$host = $arr[0];
			$port =	$arr[1];		
			if ($port == ""){
				$port = 5003;
			} 	
			
			$gate = new MySensorMasterTcp($rec[$i]['ID'], $host, $port);		
		}
		
		if (!$gate->connect()){
			continue;
		}

		$gates[ $rec[$i]['ID'] ] = $gate;
		
		$gate->subscribe(array(  
			"presentation" => "doPresentation",
			"set" => "doSet",
			"req" => "doReq",
			"internal" => "doInternal",    
			"stream" => "doStream",
			"sendproc" => "doSend",
		));

		//=== Req values ===
		$recReq = SQLSelect("SELECT * FROM msnodeval WHERE GID=".$rec[$i]['ID']." AND req=1;");
		$totalReq=count($recReq);
		if ($totalReq) {
			for($i=0;$i<$totalReq;$i++) {
				// TODO: $ms->cmd($recReq[$i]['NID'].";".$recReq[$i]['SID'].";2;".$recReq[$i]['ACK'].";".$recReq[$i]['SUBTYPE'].";");
			}
		}
	}	
}   

if (count($gates) == 0) {
	DebMes("No gates found");
	$db->Disconnect();
	exit(1);
}

//== Cycle ===
$previousMillis = 0;
while (true){
	
	foreach ($gates as $GId => $gate) {				
		//echo date("Y-m-d H:i:s")." Process: ".$GId."\n";
	
		$ret = $gate->proc();
		if ($ret === false)	{
			echo "Error process\n";			
			sleep(5);	// Sleep 5 seconds
			continue;
		}
			
		//echo  date("Y-m-d H:i:s")." system\n";

		$currentMillis = round(microtime(true) * 10000);   
		if ($currentMillis - $previousMillis > 10000){
			$previousMillis = $currentMillis;

			// Check reboot
			if (file_exists('./reboot') || file_exists('./stop_mysensor') || IsSet($_GET['onetime'])){

				// Close all gates
				foreach ($gates as $GId => $gate) {
					$gate->disconnect();  
				}

				$db->Disconnect();
				echo "Stop cycle\n";
				exit;
			}
		} 	
	}

	//echo  date("Y-m-d H:i:s")." Sleep\n";
	usleep(50000);
	//echo  date("Y-m-d H:i:s")." End\n";

	// Set run state
	setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);
}

function doPresentation($gate, $arr){
	global $ms;
	$ms->Presentation($gate, $arr);	
}
  
function doSet($gate, $arr){
	global $ms;
	$ms->Set($gate, $arr);
}

function doReq($gate, $arr){
	global $ms;
	return $ms->Req($gate, $arr);  
}

function doInternal($gate, $arr){
	global $ms;  
	$ms->Internal($gate, $arr); 
}
  
function doStream($gate, $arr){
	global $ms;  
	$ms->Stream($gate, $arr);  
}

function doSend($gate){
	global $ms;
	$ms->doSend($gate);
}

// Close all gates
foreach ($gates as $GId => $gate) {
	$gate->disconnect();  
}

$db->Disconnect(); // closing database connection 
 
DebMes("Unexpected close of cycle: " . basename(__FILE__));
 
?>