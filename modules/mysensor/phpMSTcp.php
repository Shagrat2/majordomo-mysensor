<?php

require_once 'phpMS.php'; 

/**
 * MySensorTCP
 * 
 */
class MySensorMasterTCP extends MySensorMaster {
  private $sock;
  public $host = "192.168.1.2";
  public $port = "5003";    
  
  function MySensorMasterTCP($host, $port){
    $this->host = $host; 
  	$this->port = $port;	  
  }

  /**
  * connect
  *
  * Connect the socket
  *
  * @return bool
  */
  function connect(){
		if($this->debug) echo date("Y-m-d H:i:s")." Connecting TCP\n";
		
    // TCP socket
    $this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);         
      
    socket_set_option($this->sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 200000));
    socket_set_option($this->sock, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 250000));
		
		socket_set_option($this->sock, SOL_SOCKET, SO_KEEPALIVE, 1);
    
    // Connect the socket
    $result = @socket_connect($this->sock, $this->host, $this->port);
    if ($result === false) {
			  echo "socket_connect() failed. Reason: ".socket_strerror(socket_last_error($this->sock))."\n";
				return $result;
    } 
        
    if($this->debug) echo date("Y-m-d H:i:s")." Connected\n";        
    
    MySensorMaster::connect();
    
    return true;        
  }
  
  /**
   * disconnect
   *
   * Disconnect the socket
   */
  function disconnect(){    
    socket_close($this->sock);
	  if($this->debug) echo date("Y-m-d H:i:s")."Disconnected\n";
  }   
  
  /**
   * Read
   *
   * Read the socket
   */
  function read(){      
    $data = "";
    while (true){			
      $c = socket_read($this->sock, 1);			
      
      if ($c === false) return "";
      if ($c == "\n") return $data;
      $data .= $c;
    }     
  }
  
  /**
   * Send
   *
   * Send the socket
   */
  function send($nid, $sid, $mtype, $ack, $subtype, $msg, $log = true){
    $data = "$nid;$sid;$mtype;$ack;$subtype;$msg\n";    
    $ret = socket_write($this->sock, $data);
    if ($log)
      echo date("Y-m-d H:i:s")." Send: $data";
    return $ret;
  }
} 
?>