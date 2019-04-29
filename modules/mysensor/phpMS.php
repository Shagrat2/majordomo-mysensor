<?php

/*
VERSION: 2015.07.08 - 11:01  
*/

global $MSType;
global $MSPresentation;
global $MSProperty;
global $MSInternal;
global $MSStream;

// Constants for the MySensor class
$MSType = array(
  0 => "Presentation",
  1 => "Set",
  2 => "Req",
  3 => "Internal",
  4 => "Stream",
);

$MSPresentation = array(
  0 => Array("S_DOOR",              "Door and window sensors",                              'V_TRIPPED, V_ARMED'),
  1 => Array("S_MOTION",            "Motion sensors",                                       'V_TRIPPED, V_ARMED'),
  2 => Array("S_SMOKE",             "Smoke sensor",                                         'V_TRIPPED, V_ARMED'),
  3 => Array("S_BINARY",            "Binary device (on/off)",                               'V_STATUS (or V_LIGHT), V_WATT'),
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
  17 => Array("S_ARDUINO_NODE",           "Arduino node device",                            ''),
  18 => Array("S_ARDUINO_REPEATER_NODE",  "Arduino repeating node device",                  ''),
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
  35 => Array("S_MOISTURE",         "Moisture sensor",                                      'V_LEVEL (water content or moisture in percentage?), V_TRIPPED, V_ARMED'),
  36 => Array("S_INFO",             "LCD text device",                                      'V_TEXT'),
  37 => Array("S_GAS",              "Gas meter",                                      	    'V_FLOW, V_VOLUME'),
  38 => Array("S_GPS",				      "GPS Sensor",											                      'V_POSITION'),
  39 => Array("S_WATER_QUALITY",	  "Water quality sensor",									                'V_TEMP, V_PH, V_ORP, V_EC, V_STATUS'),
);

// Index, Type, Title, Major Domo Smartdevice name
$MSProperty = array(
  0 => Array("V_TEMP", "Temperature", "sensor_temp"),
  1 => Array("V_HUM", "Humidity", "sensor_humidity"),
  2 => Array("V_STATUS", "Binary status. 0=off 1=on", "sensor_state"),
  3 => Array("V_PERCENTAGE", "Percentage value. 0-100 (%)", "sensor_percentage"),
  4 => Array("V_PRESSURE", "Atmospheric Pressure", "sensor_pressure"),
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
  16 => Array("V_TRIPPED", "Tripped status of a security sensor. 1=Tripped, 0=Untripped", "sensor_state"),
  17 => Array("V_WATT", "Watt value for power meters", "sensor_power"),
  18 => Array("V_KWH", "Accumulated number of KWH for a power meter", "sensor_power"),
  19 => Array("V_SCENE_ON", "Turn on a scene"),
  20 => Array("V_SCENE_OFF", "Turn of a scene"),
  21 => Array("V_HVAC_FLOW_STATE", 'Mode of header. One of "Off", "HeatOn", "CoolOn", or "AutoChangeOver"'),
  22 => Array("V_HVAC_SPEED", 'HVAC/Heater fan speed ("Min", "Normal", "Max", "Auto")'),
  23 => Array("V_LIGHT_LEVEL", "Light level. 0-100%", "sensor_light"),
  24 => Array("V_VAR1", "Custom value", "sensor_general"),
  25 => Array("V_VAR2", "Custom value", "sensor_general"),
  26 => Array("V_VAR3", "Custom value", "sensor_general"),
  27 => Array("V_VAR4", "Custom value", "sensor_general"),
  28 => Array("V_VAR5", "Custom value", "sensor_general"),
  29 => Array("V_UP", "Window covering. Up."),
  30 => Array("V_DOWN", "Window covering. Down."),
  31 => Array("V_STOP", "Window covering. Stop."),
  32 => Array("V_IR_SEND", "Send out an IR-command", "sensor_general"),
  33 => Array("V_IR_RECEIVE", "This message contains a received IR-command", "sensor_general"),
  34 => Array("V_FLOW", "Flow of water (in meter)", "sensor_general"),
  35 => Array("V_VOLUME", "Water volume", "counter"),
  36 => Array("V_LOCK_STATUS", "Set or get lock status. 1=Locked, 0=Unlocked"),
  37 => Array("V_LEVEL", "Used for sending level-value", "sensor_general"),
  38 => Array("V_VOLTAGE", "Voltage level", "sensor_voltage"),
  39 => Array("V_CURRENT", "Current level", "sensor_current"),
  40 => Array("V_RGB", 'RGB value transmitted as ASCII hex string (I.e "ff0000" for red)', "rgb"),
  41 => Array("V_RGBW", 'RGBW value transmitted as ASCII hex string (I.e "ff0000ff" for red + full white)', "rgb"),
  42 => Array("V_ID", 'Optional unique sensor id (e.g. OneWire DS1820b ids)'),
  43 => Array("V_UNIT_PREFIX", 'Allows sensors to send in a string representing the unit prefix to be displayed in GUI. This is not parsed by controller! E.g. cm, m, km, inch.'),
  44 => Array("V_HVAC_SETPOINT_COOL", 'HVAC cold setpoint (Integer between 0-100)'),
  45 => Array("V_HVAC_SETPOINT_HEAT", 'HVAC/Heater setpoint (Integer between 0-100)'),
  46 => Array("V_HVAC_FLOW_MODE", 'Flow mode for HVAC ("Auto", "ContinuousOn", "PeriodicOn")'),
  47 => Array("V_TEXT", 'S_INFO. Text message to display on LCD or controller device'),
  48 => Array("V_CUSTOM", 'Custom messages used for controller/inter node specific commands, preferably using S_CUSTOM device type.'),
  49 => Array("V_POSITION", 'GPS position and altitude. Payload: latitude;longitude;altitude(m). E.g. "55.722526;13.017972;18"'),
  50 => Array("V_IR_RECORD", 'Record IR codes S_IR for playback'),
  51 => Array("V_PH", 'Water PH'),
  52 => Array("V_ORP", 'Water ORP : redox potential in mV'),
  53 => Array("V_EC", 'Water electric conductivity μS/cm (microSiemens/cm)'),
  54 => Array("V_VAR", 'Reactive power: volt-ampere reactive (var)'),
  55 => Array("V_VA", 'Apparent power: volt-ampere (VA)'),
  56 => Array("V_POWER_FACTOR", 'Ratio of real power to apparent power: floating point value in the range [-1,..,1]'),
);

$MSInternal = array(
	0 => array("I_BATTERY_LEVEL", ""),
	1 => array("I_TIME", ""),
	2 => array("I_VERSION", ""),
	3 => array("I_ID_REQUEST", ""),
	4 => array("I_ID_RESPONSE", ""),
	5 => array("I_INCLUSION_MODE", ""),
	6 => array("I_CONFIG", ""),
	7 => array("I_FIND_PARENT", ""),
	8 => array("I_FIND_PARENT_RESPONSE", ""),
	9 => array("I_LOG_MESSAGE", ""),
	10 => array("I_CHILDREN", ""),
	11 => array("I_SKETCH_NAME", ""),
	12 => array("I_SKETCH_VERSION", ""),
	13 => array("I_REBOOT", ""),
	14 => array("I_GATEWAY_READY", ""),
	15 => array("I_SIGNING_PRESENTATION", ""),
	16 => array("I_NONCE_REQUEST", ""),
	17 => array("I_NONCE_RESPONSE", ""),
	18 => array("I_HEARTBEAT_REQUEST", ""),
	19 => array("I_PRESENTATION", ""),
	20 => array("I_DISCOVER_REQUEST", ""),
	21 => array("I_DISCOVER_RESPONSE", ""),
	22 => array("I_HEARTBEAT_RESPONSE", ""),
	23 => array("I_LOCKED", ""),
	24 => array("I_PING", ""),
	25 => array("I_PONG", ""),
	26 => array("I_REGISTRATION_REQUEST", ""),
	27 => array("I_REGISTRATION_RESPONSE", ""),
	28 => array("I_DEBUG", ""),
	29 => array("I_SIGNAL_REPORT_REQUEST", ""),
	30 => array("I_SIGNAL_REPORT_REVERSE", ""),
	31 => array("I_SIGNAL_REPORT_RESPONSE", ""),
	32 => array("I_PRE_SLEEP_NOTIFICATION", ""),
	33 => array("I_POST_SLEEP_NOTIFICATION", ""),
);

$MSStream = array(
	0 => array("ST_FIRMWARE_CONFIG_REQUEST", ""),
	1 => array("ST_FIRMWARE_CONFIG_RESPONSE", ""),
	2 => array("ST_FIRMWARE_REQUEST", ""),
	3 => array("ST_FIRMWARE_RESPONSE", ""),
	4 => array("ST_SOUND", ""),
  5 => array("ST_IMAGE", ""),
  6 => array("ST_FIRMWARE_CONFIRM", ""),
	7 => array("ST_FIRMWARE_RESPONSE_RLE", ""),
);

const C_PRESENTATION 	= 0;	// Sent by a node when they present attached sensors. This is usually done in presentation() at startup.
const C_SET 			    = 1;	// This message is sent from or to a sensor when a sensor value should be updated.
const C_REQ 		    	= 2;	// Requests a variable value (usually from an actuator destined for controller).
const C_INTERNAL		  = 3;	// Internal MySensors messages (also include common messages provided/generated by the library).
const C_STREAM		  	= 4;	// For firmware and other larger chunks of data that need to be divided into pieces.

const S_ARDUINO_NODE            = 17;  // Arduino node device
const S_ARDUINO_REPEATER_NODE   = 18;  // Arduino repeating node device

const I_BATTERY_LEVEL				    = 0;	// Battery level
const	I_TIME					    	    = 1;	// Time
const	I_VERSION				    	    = 2;	// Version
const	I_ID_REQUEST			  	    = 3;	// ID request
const	I_ID_RESPONSE			  	    = 4;	// ID response
const	I_INCLUSION_MODE			    = 5;	// Inclusion mode
const	I_CONFIG				    	    = 6;	// Config
const	I_FIND_PARENT			  	    = 7;	// Find parent
const	I_FIND_PARENT_RESPONSE    = 8;	// Find parent response
const	I_LOG_MESSAGE				      = 9;	// Log message
const	I_CHILDREN					      = 10;	// Children
const	I_SKETCH_NAME			  	    = 11;	// Sketch name
const	I_SKETCH_VERSION			    = 12;	// Sketch version
const	I_REBOOT					        = 13;	// Reboot request
const	I_GATEWAY_READY				    = 14;	// Gateway ready
const	I_SIGNING_PRESENTATION    = 15;	// Provides signing related preferences (first byte is preference version)
const	I_NONCE_REQUEST				    = 16;	// Request for a nonce
const	I_NONCE_RESPONSE			    = 17;	// Payload is nonce data
const	I_HEARTBEAT				  	    = 18;	// Heartbeat request
const	I_PRESENTATION				    = 19;	// Presentation message
const	I_DISCOVER				  	    = 20;	// Discover request
const	I_DISCOVER_RESPONSE		    = 21;	// Discover response
const	I_HEARTBEAT_RESPONSE	    = 22;	// Heartbeat response
const	I_LOCKED					        = 23;	// Node is locked (reason in string-payload)
const	I_PING						        = 24;	// Ping sent to node, payload incremental hop counter
const	I_PONG						        = 25;	// In return to ping, sent back to sender, payload incremental hop counter
const	I_REGISTRATION_REQUEST		= 26;	// Register request to GW
const	I_REGISTRATION_RESPONSE		= 27;	// Register response from GW
const	I_DEBUG					        	= 28; 	// Debug message
const	I_SIGNAL_REPORT_REQUEST		= 29;	// Device signal strength request
const	I_SIGNAL_REPORT_REVERSE		= 30;	//
const	I_SIGNAL_REPORT_RESPONSE	= 31;	// Device signal strength response (RSSI)
const	I_PRE_SLEEP_NOTIFICATION	= 32;	// Message sent before node is going to sleep
const	I_POST_SLEEP_NOTIFICATION	= 33;	// Message sent after node woke up (if enabled)

const	cLogDebug = 0; // Debug
const cLogError = 1; // Error
const	cLogMessage = 2; // Message

const cNormalPage = 32; // Memory page size

function SubTypeDecode($mtype, $mssubtype){
	global $MSPresentation;
	global $MSProperty;
	global $MSInternal;
	global $MSStream;
	
	switch ($mtype) {
	  case C_PRESENTATION:		
		if ($mssubtype >= count($MSPresentation)) 
		  return "-Unknown-";
		else
		  return $MSPresentation[$mssubtype][0];		
		break;
	  case C_SET:
		if ($mssubtype >= count($MSProperty)) 
		  return "-Unknown-";
		else
		  return $MSProperty[$mssubtype][0];
		break;
	  case C_REQ:
		if ($mssubtype >= count($MSProperty)) 
		  return "-Unknown-";
		else
		  return $MSProperty[$mssubtype][0];
		break;
	  case C_INTERNAL:
		if ($mssubtype >= count($MSInternal))
		  return "-Unknown-";
		else 
		  return $MSInternal[$mssubtype][0];
		break;
	  case C_STREAM:
		if ($mssubtype >= count($MSStream)) 
		  return "-Unknown-";
		else
		  return $MSStream[$mssubtype][0];
		break;;
	  default:
		return "-Error-";
	}
}
	
abstract class MySensorMaster{    
  public $debug = false;            /* should output debug messages */ 
  public $subscribe = array();
  private $lastTime = -1;
  public $alivetime = 16000; // 16 sec
  public $testtime = 5000; // 5 sec
  private $testsend = false;
  public $GId;

  /**
   * connect
   *
   * Connect the socket
   *
   * @return bool
   */
  function connect(){
	  $this->AddLog(cLogDebug, "Connecting main");
		
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
  abstract function send($nid, $sid, $mtype, $ack, $subtype, $msg, $log = true);
  
  /**
  * Append to log
  */
  function AddLog($level, $data){
	  list($usec, $sec) = explode(" ", microtime());
	
	  switch ($level){
		  case cLogDebug:
			if($this->debug) echo date("H:i:s", $sec)." ".sprintf('%03d', $usec*1000)." $data\n";
			  break;
		  default:
		    echo date("H:i:s", $sec)." ".sprintf('%03d', $usec*1000)." $data\n";
			  break;
	  }
  }
  
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
    
    //---- Send ----
    if(function_exists($this->subscribe['sendproc'])){
      $this->AddLog(cLogDebug, "Start send");
      call_user_func($this->subscribe['sendproc'], $this);
    }    
		
    //---- Read ----
	  $this->AddLog(cLogDebug, "Start read");
    $read_data = $this->read();
	  $this->AddLog(cLogDebug, "End read");

    if ($read_data != ''){
      // Original data
      $this->AddLog(cLogDebug, ">>> $read_data");

      // Reset timer
      $this->lastTime = $currentMillis;
      $this->testsend = false;
      
      // #Patch
      if ($read_data[0] == ";") {
        $read_data = "0".$read_data;
      }

      $arr = explode(';', $read_data, 6);
        
      // Check data format
      for ($i=0; $i<5; $i++)			
        if ((is_numeric($arr[$i]) === false) || (is_float($arr[$i]) !== false))	{	
          $this->AddLog(cLogDebug, "### $read_data");
          return true;
        }
        
      $mType = $arr[2];
      switch ($mType){
        case C_PRESENTATION:          
          if(function_exists($this->subscribe['presentation']))
            call_user_func($this->subscribe['presentation'], $this, $arr);
          break;
        case C_SET: 
          if(function_exists($this->subscribe['set']))
            call_user_func($this->subscribe['set'], $this, $arr);
          break;        
        case C_REQ:
          $val='';
          if(function_exists($this->subscribe['req'])){
            $val = call_user_func($this->subscribe['req'], $this, $arr);
            if ($val !== false){
              $this->send($arr[0], $arr[1], $arr[2], 0, $arr[4], $val);
            }
          }                        

          break;  
        case C_INTERNAL: 
          // Tester present
          if (($arr[0] == 0) && ($arr[4] == 2)) break;
          if(function_exists($this->subscribe['internal']))
            call_user_func($this->subscribe['internal'], $this, $arr);
          break;   
        case C_STREAM: 
          if(function_exists($this->subscribe['stream']))
            call_user_func($this->subscribe['stream'], $this, $arr);
          break; 
      }                                 
    }
    
    // Tester present
    if ($currentMillis - $this->lastTime > $this->testtime && !$this->testsend){
      $this->AddLog(cLogDebug, "Tester presend");
      $this->send(0, 0, 3, 0, 2, "Tester present", false);
      $this->testsend = true;
    }

    // Reconnect
    if ($currentMillis - $this->lastTime > $this->alivetime){
      $this->disconnect();

      $this->AddLog(cLogDebug, "Reconnect");

      $result = $this->connect();
      if ($result === false)
        return false;			
    }    
      
    return true;     
  }   
}

?>
