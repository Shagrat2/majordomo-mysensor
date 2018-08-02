<?php
/**
* Ukranian language file for NUT module
*
*/
$dictionary=array(
/* general */
'CONNECTION_TYPE'=>'Тип з`єднання',
'MEASURE'=>'Вимірюванн',
'AUTOID'=>'Авто ID',
'NEXTID'=>'Слідуюче ID',
'GATES'=>'Gates',
'GATE'=>'Gate',
'NODES'=>'Вузли',
'NODE'=>'Вузол',
'MESH'=>'Мережа',
'PRESENTATION'=>'Заява',
'SENSOR'=>'Сенсор',
'VERSION'=>'Версія',
'PROTOCOL'=>'Протокол',
'LASTREBOOT'=>'Останнє перезавантаження',
'REBOOT'=>'Перезавантажити',
'REBOOT_INFO'=>'Перезавантажити вузол',
'CLEAN'=>'Очистити',
'ACK_INFO'=>'ACK - Встановити, якщо ви бажаєте щоб вузол, що запитується відповіда підтвердженням ACK назад до цього вузла.',
'REQ_INFO'=>'REQ - Запитує данні при старті модуля.',
'MESH_INFO'=>'Це дерево оновлюється коли вузол перезавантажиться',
'RESETINFO'=>'Скидання даних',
'PRESENTATION_INFO'=>'Запросить інформацію про нод (ver 2.0)',
'HEARTBEAT'=>'HeartBeat',
'HEARTBEAT_INFO'=>'Перевірити чи живий (ver 2.0)',
'RESET_INFO'=>'Скидання інформації про нод',
'PRESENTATION'=>'Презентація',
'SETVALUE'=>'Встановити',
'REQUEST'=>'Запросити',
'FIRMWARE'=>'Прошивка',
'FILE'=>'Файл',
'DISCOVER'=>'Пошук',
'DISCOVER_INFO'=>'Пошук вузлів',
'INFO'=>'Інформація',
'QUEUING'=>'Черга',
/* end module names */
);
foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
