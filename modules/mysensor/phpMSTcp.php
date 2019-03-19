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
  
  function MySensorMasterTCP($GId, $host, $port){
    $this->GId = $GId;
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
    $this->AddLog(cLogDebug, "Connecting TCP: '$this->host':$this->port");
      
    // TCP socket
    $this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);         
    if ($this->sock === FALSE)
      return false;
    
    socket_set_block($this->sock);
    
    //socket_set_option($this->sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 200000));
    //socket_set_option($this->sock, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 250000));		
    socket_set_option($this->sock, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 50000));
    socket_set_option($this->sock, SOL_SOCKET, SO_KEEPALIVE, 1);
    
    // Connect the socket
    $result = @socket_connect($this->sock, $this->host, $this->port);
    if ($result === false) {
      $this->AddLog(cLogError, "socket_connect() failed. Reason: ".socket_strerror(socket_last_error($this->sock)));
      socket_close($this->sock);
      return $result;
    } 

    $this->AddLog(cLogDebug, "Connected");
    
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
  	$this->AddLog(cLogDebug, "Disconnected");
  }   
  
  /**
   * Read
   *
   * Read the socket
   */
  function read(){      
    $data = "";
    while (true){			
      $c = @socket_read($this->sock, 1);			
      
      if ($c === false || $c == "") return "";
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
	  $this->AddLog(cLogDebug, "Send: $data");
    return $ret;
  }
} 
?>
