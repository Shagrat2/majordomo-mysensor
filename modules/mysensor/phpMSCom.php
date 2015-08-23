<?php

require_once 'phpMS.php'; 

include_once('PhpSerial.php');

/**
 * MySensorTCP
 * 
 */
class MySensorMasterCom extends MySensorMaster {
  public $Serial;
    
  function MySensorMasterCom($device){
    $serial = new phpSerial;
    $serial->deviceSet($device);        

    $serial->confBaudRate(115200);
    $serial ->confCharacterLength(8);
    $serial->confParity("none");
    $serial->confStopBits(1);
    $serial->confFlowControl("none");

    $this->Serial = $serial;
  }
  
  /**
  * connect
  *
  * Connect the socket
  *
  * @return bool
  */
  function connect(){    
    $result = $this->Serial->deviceOpen("w+b");
    
    if ($result === false) {
        throw new Exception("serrial.open() failed");
    } 
      
    if($this->debug) echo "Connected\n";
    
    stream_set_timeout($this->Serial->_dHandle, 0, 250000);    
		
    MySensorMaster::connect();
        
    return true;            
  }
  
  /**
   * disconnect
   *
   * Disconnect the socket
   */
  function disconnect(){    
    $this->Serial->deviceClose();    
	  if($this->debug) echo "Disconnected\n";
  }   
  
  /**
   * Read
   *
   * Read the socket
   */
  function read(){    
    //echo "Start read ".time()." \n";
    $lastTime = round(microtime(true) * 1000);
    $data = "";
    while (true){
      $c = fread($this->Serial->_dHandle, 1);
      if ($c === false) return "";

      $currentMillis = round(microtime(true) * 1000);
      if ($currentMillis - $lastTime > 500){ 
        return "";
      }

      if ($c == "") continue;
      if ($c == "\n") return $data;
      $data .= $c;
      $lastTime = $currentMillis;
    }        
  }
  
  /**
   * Send
   *
   * Send the socket
   */
  function send($nid, $sid, $mtype, $ack, $subtype, $msg){
    $data = "$nid;$sid;$mtype;$ack;$subtype;$msg\n";    
    $this->Serial->sendMessage($data);
    echo "Send: $data";
    return true;
  }
} 
?>