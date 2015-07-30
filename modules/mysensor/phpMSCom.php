<?php

require_once 'phpMS.php'; 

include_once('/scripts/php_serial.class.php');

/**
 * MySensorTCP
 * 
 */
class MySensorMasterCom extends MySensorMaster {
  public $Serial;
    
  function MySensorMasterCom($serrial){
    $this->Serial = new phpSerial();
    $this->Serial->deviceSet($serial);
  }
  
  /**
  * connect
  *
  * Connect the socket
  *
  * @return bool
  */
  function connect(){
    $result = $this->Serial->deviceOpen();
    
    if ($result === false) {
        throw new Exception("serrial.open() failed");
    } else {
      if($this->debug) echo "Connected\n";
      return true;        
    }            
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
    /*
    $data = "";
    while (true){
      $c = socket_read($this->sock, 1);
      
      if ($c === false) return "";
      if ($c == "\n") return $data;
      $data .= $c;
    }    
    */
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