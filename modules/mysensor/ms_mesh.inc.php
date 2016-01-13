<?php

global $session;

function tree($a,$i,$p,$r=0,$c='children'){
  if (!is_array($a)) return false;		
  
	// Внутренн€€ рекурсивна€ функци€
  function tree_node($index,$root,$cn) {
    $_ret = array();
    foreach ($index[$root] as $k => $v) {
      $_ret[$k] = $v;
      if (isset($index[$k])) $_ret[$k][$cn] = tree_node($index,$k,$cn);
    }
    return $_ret;
  }

  $ids = array(); // Временный индексный массив
  // —оздаЄм временные массивы на корректные элементы
  foreach ($a as $k => $v) {
    if (is_array($v) && ($k != 0)) {
			if ((isset($v[$i]) || ($i === false)) && isset($v[$p])) {
				$key = ($i === false)?$k:$v[$i];
				$parent = $v[$p];
				$ids[$parent][$key] = $v;
			}
    }
  }

  return (isset($ids[$r]))?tree_node($ids,$r,$c):false;
}

// SEARCH RESULTS  
$res=SQLSelect("SELECT * FROM msnodes ORDER BY nid");
  
$tree = tree($res, 'NID', 'PID');

function Display($arr){
  $res = "<ul>";
  foreach ($arr as $k=>$v){
    $res .= "<li>";
    $res .= "<a href=\"?view_mode=node_edit&id=".$v['ID']."\">".$v['NID']." : ".$v['TITLE']."</a>";
    if ($v['children']){
      $res .= Display($v['children']);
    }
    $res .= "</li>";
  }
  
  $res .= "</ul>";
  
  return $res;
}

$out['TREE'] = Display($tree);

?>