<?php

global $session;
global $filter;
global $id;
global $gid;
global $nid;

$out['FILTER']=$filter;
$out['NOTHASLOG'] = !defined('LOG_CYCLES');
$out['ID'] = $id;
$out['GID'] = $gid;
$out['NID'] = $nid;

$rec = array();

outHash($rec, $out);

?>