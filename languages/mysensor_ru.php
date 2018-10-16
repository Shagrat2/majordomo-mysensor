<?php
/**
* Russian language file for NUT module
*
*/

$dictionary=array(

/* general */
'CONNECTION_TYPE'=>'Тип соединения',
'MEASURE'=>'Измерение',
'AUTOID'=>'Авто ID',
'NEXTID'=>'Следующий ID',
'GATES'=>'Gates',
'GATE'=>'Gate',
'NODES'=>'Узлы',
'NODE'=>'Узел',
'MESH'=>'Сеть',
'PRESENTATION'=>'Заявление',
'SENSOR'=>'Сенсор',
'VERSION'=>'Версия',
'PROTOCOL'=>'Протокол',
'LASTREBOOT'=>'Последняя перезагрузка',
'REBOOT'=>'Перезагрузить',
'REBOOT_INFO'=>'Перезагрузить узел',
'CLEAN'=>'Очистить',
'ACK_INFO'=>'ACK - Установите, если вы хотите чтобы запрашиваемый узел отвечал подтверждением ACK назад к этому узлу.',
'REQ_INFO'=>'REQ - Запрашивает данные при старте модуля.',
'MESH_INFO'=>'Это дерево обновляется когда узел перезагрузится',
'RESETINFO'=>'Сброс данных',
'PRESENTATION_INFO'=>'Запросить информацию о ноде (ver 2.0)',
'HEARTBEAT'=>'HeartBeat',
'HEARTBEAT_INFO'=>'Проверить жив ли (ver 2.0)',
'RESET_INFO'=>'Сброс информации о ноде',
'PRESENTATION'=>'Презентация',
'SETVALUE'=>'Установить',
'REQUEST'=>'Запросить',
'FIRMWARE'=>'Прошивка',
'FILE'=>'Файл',
'DISCOVER'=>'Поиск',
'DISCOVER_INFO'=>'Поиск узлов',
'INFO'=>'Информация',
'QUEUING'=>'Очередь',
/* end module names */

);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}

?>