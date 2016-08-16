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
  // Создаём временные массивы на корректные элементы
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
SQLSelect("SET @@sql_mode = \"\"");
$res=SQLSelect("SELECT msnodes.*, max(msnodeval.UPDATED) as UPDATED FROM msnodes, msnodeval WHERE msnodes.nid=msnodeval.nid GROUP BY NID ORDER BY NID");
//$res=SQLSelect("SELECT * FROM msnodes ORDER BY nid");
  
$tree = tree($res, 'NID', 'PID');

function Display($arr, $level = 0){  
	if (is_array($arr))
	{
		foreach ($arr as $k=>$v){
			if ((time()-strtotime($v['UPDATED'])) > 3*60*60)			
				$res .= "<tr style=\"color: red\">";
			else
				$res .= "<tr>";
			
			$res .= "<td>";
			
			for ($i=0;$i<$level;$i++) $res .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 			
			// Title
			$res .= "<a href=\"?view_mode=node_edit&id=".$v['ID']."\">".$v['NID']." : ".$v['TITLE']."</a></td>";
			
			// Updated
			$res .= "<td>".$v['UPDATED']."<td>";
			
			// Start time
			$res .= "<td>".$v['LASTREBOOT']."<td>";		
			
			$res .= "</tr>";
			
			if ($v['children'])
				$res .= Display($v['children'], $level+1);			
		}
	}		
  
  return $res;
}
	
	
$out['TREE'] = 
	'<table class="table table-stripped"><thead><tr><th><#LANG_TITLE#></th><th><#LANG_UPDATED#></th><th><#LANG_LASTREBOOT#></th></tr>'.Display($tree)."</table>";

?>