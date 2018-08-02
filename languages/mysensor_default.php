<?php
/**
* Russian language file for NUT module
*
*/

$dictionary=array(

/* general */
'CONNECTION_TYPE'=>'Connection type',
'MEASURE'=>'Measure',
'AUTOID'=>'Auto ID',
'NEXTID'=>'Next ID',
'GATES'=>'Gates',
'GATE'=>'Gate',
'NODES'=>'Nodes',
'NODE'=>'Node',
'MESH'=>'Mesh',
'PRESENTATION'=>'Presentation',
'SENSOR'=>'Sensor',
'VERSION'=>'Version',
'PROTOCOL'=>'Protocol',
'LASTREBOOT'=>'Last reboot',
'REBOOT'=>'Reboot',
'REBOOT_INFO'=>'Reboot node',
'CLEAN'=>'Clean',
'ACK_INFO'=>'ACK - Set this to true if you want destination node to send ack back to this node.',
'REQ_INFO'=>'REQ - Get value from node when module start.',
'MESH_INFO'=>'This tree make only when node reboot',
'RESETINFO'=>'Reset info',
'RESET_INFO'=>'Reset node info',
'PRESENTATION'=>'Presentation',
'PRESENTATION_INFO'=>'Get node Presentation',
'HEARTBEAT'=>'HeartBeat',
'HEARTBEAT_INFO'=>'Get HeartBeat',
'SETVALUE'=>'Set value',
'REQUEST'=>'Request',
'FIRMWARE'=>'Firmware',
'FILE'=>'File',
'DISCOVER'=>'Discover',
'DISCOVER_INFO'=>'Discover nodes',
'INFO'=>'Information',
'QUEUING'=>'Queuing',
/* end module names */

);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}

?>