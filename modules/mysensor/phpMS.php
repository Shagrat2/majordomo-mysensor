<?php

/*
VERSION: 2015.07.08 - 11:01  
*/

// Constants for the MySensor class

$mysensor_presentation = array(
  0 => Array("S_DOOR",              "Door and window sensors",                              'V_TRIPPED, V_ARMED'),
  1 => Array("S_MOTION",            "Motion sensors",                                       'V_TRIPPED, V_ARMED'),
  2 => Array("S_SMOKE",             "Smoke sensor",                                         'V_TRIPPED, V_ARMED'),
  3 => Array("S_LIGHT",             "Light Actuator (on/off)",                              'V_STATUS (or V_LIGHT), V_WATT'),
  4 => Array("S_DIMMER",            "Dimmable device of some kind",                         'V_STATUS (on/off), V_DIMMER (dimmer level 0-100), V_WATT'),
  5 => Array("S_COVER",             "Window covers or shades",                              'V_UP, V_DOWN, V_STOP, V_PERCENTAGE'),
  6 => Array("S_TEMP",              "Temperature sensor",                                   'V_TEMP, V_ID'),
  7 => Array("S_HUM",               "Humidity sensor",                                      'V_HUM'),
  8 => Array("S_BARO",              "Barometer sensor (Pressure)",                          'V_PRESSURE, V_FORECAST'),
  9 => Array("S_WIND",              "Wind sensor",                                          'V_WIND, V_GUST'),
  10 => Array("S_RAIN",             "Rain sensor",                                          'V_RAIN, V_RAINRATE'),
  11 => Array("S_UV",               "UV sensor",                                            'V_UV'),
  12 => Array("S_WEIGHT",           "Weight sensor for scales etc.",                        'V_WEIGHT, V_IMPEDANCE'),
  13 => Array("S_POWER",            "Power measuring device, like power meters",            'V_WATT, V_KWH'),
  14 => Array("S_HEATER",           "Heater device",                                        'V_HVAC_SETPOINT_HEAT, V_HVAC_FLOW_STATE, V_TEMP'),
  15 => Array("S_DISTANCE",         "Distance sensor",                                      'V_DISTANCE, V_UNIT_PREFIX'),
  16 => Array("S_LIGHT_LEVEL",      "Light sensor",                                         'V_LIGHT_LEVEL (uncalibrated percentage), V_LEVEL (light level in lux)'),
  17 => Array("S_ARDUINO_NODE",     "Arduino node device",                                  ''),
  18 => Array("S_ARDUINO_RELAY",    "Arduino repeating node device",                        ''),
  19 => Array("S_LOCK",             "Lock device",                                          'V_LOCK_STATUS'),
  20 => Array("S_IR",               "Ir sender/receiver device",                            'V_IR_SEND, V_IR_RECEIVE'),
  21 => Array("S_WATER",            "Water meter",                                          'V_FLOW, V_VOLUME'),
  22 => Array("S_AIR_QUALITY",      "Air quality sensor e.g. MQ-2",                         'V_LEVEL, V_UNIT_PREFIX'),
  23 => Array("S_CUSTOM",           "Use this for custom sensors where no other fits",      ''),
  24 => Array("S_DUST",             "Dust level sensor",                                    'V_LEVEL, V_UNIT_PREFIX'),
  25 => Array("S_SCENE_CONTROLLER", "Scene controller device",                              'V_SCENE_ON, V_SCENE_OFF'),
  26 => Array("S_RGB_LIGHT",        "RGB light",                                            'V_RGB, V_WATT'),
  27 => Array("S_RGBW_LIGHT",       "RGBW light (with separate white component)",           'V_RGBW, V_WATT'),
  28 => Array("S_COLOR_SENSOR",     "Color sensor",                                         'V_RGB'),
  29 => Array("S_HVAC",             "Thermostat/HVAC device",                               'V_HVAC_SETPOINT_HEAT, V_HVAC_SETPOINT_COLD, V_HVAC_FLOW_STATE, V_HVAC_FLOW_MODE, V_HVAC_SPEED'),
  30 => Array("S_MULTIMETER",       "Multimeter device",                                    'V_VOLTAGE, V_CURRENT, V_IMPEDANCE'),
  31 => Array("S_SPRINKLER",        "Sprinkler device",                                     'V_STATUS (turn on/off), V_TRIPPED (if fire detecting device)'),
  32 => Array("S_WATER_LEAK",       "Water leak sensor",                                    'V_TRIPPED, V_ARMED'),
  33 => Array("S_SOUND",            "Sound sensor",                                         'V_LEVEL (in dB), V_TRIPPED, V_ARMED'),
  34 => Array("S_VIBRATION",        "Vibration sensor",                                     'V_LEVEL (vibration in Hz), V_TRIPPED, V_ARMED'),
  35 => Array("S_MOISTURE",         "Moisture sensor",                                      'V_LEVEL (water content or moisture in percentage?), V_TRIPPED, V_ARMED')
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
  21 => Array("V_HVAC_FLOW_STATE", 'Mode of header. One of "Off", "HeatOn", "CoolOn", or "AutoChangeOver"'),
  22 => Array("V_HVAC_SPEED", 'HVAC/Heater fan speed ("Min", "Normal", "Max", "Auto")'),
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
  37 => Array("V_LEVEL", "Used for sending level-value"),
  38 => Array("V_VOLTAGE", "Voltage level"),
  39 => Array("V_CURRENT", "Current level"),
  40 => Array("V_RGB", 'RGB value transmitted as ASCII hex string (I.e "ff0000" for red)'),
  41 => Array("V_RGBW", 'RGBW value transmitted as ASCII hex string (I.e "ff0000ff" for red + full white)'),
  42 => Array("V_ID", 'Optional unique sensor id (e.g. OneWire DS1820b ids)'),
  43 => Array("V_UNIT_PREFIX", 'Allows sensors to send in a string representing the unit prefix to be displayed in GUI. This is not parsed by controller! E.g. cm, m, km, inch.'),
  44 => Array("V_HVAC_SETPOINT_COOL", 'HVAC cold setpoint (Integer between 0-100)'),
  45 => Array("V_HVAC_SETPOINT_HEAT", 'HVAC/Heater setpoint (Integer between 0-100)'),
  46 => Array("V_HVAC_FLOW_MODE", 'Flow mode for HVAC ("Auto", "ContinuousOn", "PeriodicOn")'),
);

abstract class MySensorMaster{    
  public $debug = true;            /* should output debug messages */ 
  public $subscribe = [];
  private $lastTime = -1;  

  /**
   * connect
   *
   * Connect the socket
   *
   * @return bool
   */
  function connect(){
    // Set time out    
    $this->lastTime = round(microtime(true) * 1000);
    $this->send(0, 0, 3, 0, 14, 'Gateway startup complete');
  }
  
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
    // Test reconnect    
    $currentMillis = round(microtime(true) * 1000);
    if ($currentMillis - $this->lastTime > 15*60*1000){
      $this->disconnect();
      if($this->debug) echo "Reconnect\n";
      $this->connect();
    }    
  
    //---- Send ----
    if(function_exists($this->subscribe['sendproc'])){
      call_user_func($this->subscribe['sendproc'],$arr);
    }    
    
    //---- Read ----
    //if($this->debug) echo "start wait\n";    
    $read_data = $this->read();
    
    if ($read_data != ''){      
      // Reset timer
      $this->lastTime = $currentMillis;
      
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
