<?php

/*
VERSION: 2015.07.08 - 11:01  
*/

// Constants for the MySensor class

$mysensor_presentation = array(
  0 => Array("S_DOOR",              "Door and window sensors"),
  1 => Array("S_MOTION",            "Motion sensors"),
  2 => Array("S_SMOKE",             "Smoke sensor"),
  3 => Array("S_LIGHT",             "Light Actuator (on/off)"),
  4 => Array("S_DIMMER",            "Dimmable device of some kind"),
  5 => Array("S_COVER",             "Window covers or shades"),
  6 => Array("S_TEMP",              "Temperature sensor"),
  7 => Array("S_HUM",               "Humidity sensor"),
  8 => Array("S_BARO",              "Barometer sensor (Pressure)"),
  9 => Array("S_WIND",              "Wind sensor"),
  10 => Array("S_RAIN",             "Rain sensor"),
  11 => Array("S_UV",               "UV sensor"),
  12 => Array("S_WEIGHT",           "Weight sensor for scales etc."),
  13 => Array("S_POWER",            "Power measuring device, like power meters"),
  14 => Array("S_HEATER",           "Heater device"),
  15 => Array("S_DISTANCE",         "Distance sensor"),
  16 => Array("S_LIGHT_LEVEL",      "Light sensor"),
  17 => Array("S_ARDUINO_NODE",     "Arduino node device"),
  18 => Array("S_ARDUINO_RELAY",    "Arduino repeating node device"),
  19 => Array("S_LOCK",             "Lock device"),
  20 => Array("S_IR",               "Ir sender/receiver device"),
  21 => Array("S_WATER",            "Water meter"),
  22 => Array("S_AIR_QUALITY",      "Air quality sensor e.g. MQ-2"),
  23 => Array("S_CUSTOM",           "Use this for custom sensors where no other fits"),
  24 => Array("S_DUST",             "Dust level sensor"),
  25 => Array("S_SCENE_CONTROLLER", "Scene controller device")
); 

$mysensor_property = array(
  0 => Array("V_TEMP", "Temperature"),
  1 => Array("V_HUM", "Humidity"),
  2 => Array("V_LIGHT", "Light status. 0=off 1=on"),
  3 => Array("V_DIMMER", "Dimmer value. 0-100%"),
  4 => Array("V_PRESSURE", "Atmospheric Pressure"),
  5 => Array("V_FORECAST", "Whether forecast. One of \"stable\", \"sunny\", \"cloudy\", \"unstable\", \"thunderstorm\" or \"unknown\""),
  6 => Array("V_RAIN", "Amount of rain"),
  7 => Array("V_RAINRATE", "Rate of rain"),
  8 => Array("V_WIND", "Windspeed"),
  9 => Array("V_GUST", "Gust"),
  10 => Array("V_DIRECTION", "Wind direction"),
  11 => Array("V_UV", "UV light level"),
  12 => Array("V_WEIGHT", "Weight (for scales etc)"),
  13 => Array("V_DISTANCE", "Distance"),
  14 => Array("V_IMPEDANCE", "Impedance value"),
  15 => Array("V_ARMED", "Armed status of a security sensor. 1=Armed, 0=Bypassed"),
  16 => Array("V_TRIPPED", "Tripped status of a security sensor. 1=Tripped, 0=Untripped"),
  17 => Array("V_WATT", "Watt value for power meters"),
  18 => Array("V_KWH", "Accumulated number of KWH for a power meter"),
  19 => Array("V_SCENE_ON", "Turn on a scene"),
  20 => Array("V_SCENE_OFF", "Turn of a scene"),
  21 => Array("V_HEATER", "Mode of header. One of \"Off\", \"HeatOn\", \"CoolOn\", or \"AutoChangeOver\""),
  22 => Array("V_HEATER_SW", "Heater switch power. 1=On, 0=Off"),
  23 => Array("V_LIGHT_LEVEL", "Light level. 0-100%"),
  24 => Array("V_VAR1", "Custom value"),
  25 => Array("V_VAR2", "Custom value"),
  26 => Array("V_VAR3", "Custom value"),
  27 => Array("V_VAR4", "Custom value"),
  28 => Array("V_VAR5", "Custom value"),
  29 => Array("V_UP", "Window covering. Up."),
  30 => Array("V_DOWN", "Window covering. Down."),
  31 => Array("V_STOP", "Window covering. Stop."),
  32 => Array("V_IR_SEND", "Send out an IR-command"),
  33 => Array("V_IR_RECEIVE", "This message contains a received IR-command"),
  34 => Array("V_FLOW", "Flow of water (in meter)"),
  35 => Array("V_VOLUME", "Water volume"),
  36 => Array("V_LOCK_STATUS", "Set or get lock status. 1=Locked, 0=Unlocked"),
  37 => Array("V_DUST_LEVEL", "Dust level"),
  38 => Array("V_VOLTAGE", "Voltage level"),
  39 => Array("V_CURRENT", "Current level")
);

abstract class MySensorMaster{    
  public $debug = true;            /* should output debug messages */ 
  public $subscribe = [];

  /**
   * connect
   *
   * Connect the socket
   *
   * @return bool
   */
  abstract function connect();
  
  /**
   * disconnect
   *
   * Disconnect the socket
   */
  abstract function disconnect();
  
  /**
   * Read
   *
   * Read the socket
   */
  abstract function read();

  /**
   * Send
   *
   * Send the socket
   */
  abstract function send($nid, $sid, $mtype, $ack, $subtype, $msg);
  
  /**
  * subscribe
  */
  function subscribe($params){
    $this->subscribe = $params;
  }
  
 
   /* proc: the processing loop for an "allways on" client */      
  function proc(){  
    //---- Send ----
    if(function_exists($this->subscribe['sendproc'])){
      call_user_func($this->subscribe['sendproc'],$arr);
    }    
    
    //---- Read ----
    //if($this->debug) echo "start wait\n";    
    $read_data = $this->read();
    
    if ($read_data != ''){      
      $arr = explode(';', $read_data, 6);
      //if($this->debug) echo "Receive: Node:$arr[0]; Sensor:$arr[1]; Type:$arr[2]; Ack:$arr[3]; Sub:$arr[4]; Msg:$arr[5]\n";
      
      $mType = $arr[2];
      switch ($mType){
        case 0:          
          if(function_exists($this->subscribe['presentation'])){
            call_user_func($this->subscribe['presentation'],$arr);
          }    
          break;
        case 1: 
          if(function_exists($this->subscribe['set'])){
            call_user_func($this->subscribe['set'],$arr);
          }    
          break;        
        case 2: 
          $val='';
          if(function_exists($this->subscribe['req'])){
            $val = call_user_func($this->subscribe['req'],$arr);
            
            if ($val !== false){
              send($arr[0], $arr[1], $arr[2], 0, $arr[4], $val);
            }
          }                        
          
          break;  
        case 3: 
          if(function_exists($this->subscribe['internal'])){
            call_user_func($this->subscribe['internal'],$arr);
          }    
          break;   
        case 4: 
          if(function_exists($this->subscribe['stream'])){
            call_user_func($this->subscribe['stream'],$arr);
          }    
          break; 
      }                                 
    }
    return true;     
  }   
}

?>
