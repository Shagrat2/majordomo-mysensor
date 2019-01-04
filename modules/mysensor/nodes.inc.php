<?php

  include_once("mysensor.class.php");

  global $session;
  global $node_bins;
    
  $qry="1";

  global $nid;
  if ($nid!='') {
    $qry.=" AND msnodes.NID LIKE '%".DBSafe($nid)."%'";
    $out['NID']=$nid;
  }
  
  global $title;
  if ($title!='') {
    $qry.=" AND msnodes.TITLE LIKE '%".DBSafe($title)."%'";
    $out['TITLE']=$title;
  }
  
  global $location_id;
  if ($location_id) {
    $qry.=" AND msnodes.LOCATION_ID='".(int)$location_id."'";
    $out['LOCATION_ID']=(int)$location_id;
  }

  if (IsSet($this->location_id)) {
    $location_id=$this->location_id;
    $qry.=" AND msnodes.LOCATION_ID='".$this->location_id."'";
  } else {
    global $location_id;
  }
  
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
    $qry=$session->data['mysensor_qry'];
  } else {
    $session->data['mysensor_qry']=$qry;
  }
  if (!$qry) $qry="1";
  
  // FIELDS ORDER
  global $sortby_mysensor;
  if (!$sortby_mysensor) {
    $sortby_mysensor=$session->data['mysensor_sort'];
  } else {
    if ($session->data['mysensor_sort']==$sortby_mysensor) {
      if (Is_Integer(strpos($sortby_mysensor, ' DESC'))) {
        $sortby_mysensor=str_replace(' DESC', '', $sortby_mysensor);
      } else {
        $sortby_mysensor=$sortby_mysensor." DESC";
      }
    }
    $session->data['mysensor_sort']=$sortby_mysensor;
  }
  if (!$sortby_mysensor) $sortby_mysensor="NID";
  $out['SORTBY']=$sortby_mysensor;

  if ($sortby_mysensor != "") {
    $sortby_mysensor = "msnodes.".$sortby_mysensor;
  }
  
  // SEARCH RESULTS  
  $res=SQLSelect(
	"SELECT msnodes.*, msgates.title AS gtitle, msnodestate.last, ".
	"(SELECT count(id) FROM mssendstack WHERE msnodes.GID=mssendstack.GID AND msnodes.NID=mssendstack.NID) AS TOTAL ".
	"FROM msnodes ".
	"LEFT JOIN msgates ON msnodes.gid=msgates.id ".
	"LEFT JOIN msnodestate ON msnodes.gid=msnodestate.gid AND msnodes.nid=msnodestate.nid ".
	"WHERE $qry ORDER BY ".$sortby_mysensor);
  if ($res[0]['ID']) {
    colorizeArray($res);
    $total=count($res);
    for($i=0;$i<$total;$i++) {
	
		$DevType = "";		
		
		// some action for every record if required		 
		if ($res[$i]['DEVTYPE'] == 1)
			$DevType .= "<i class=\"fa fa-battery-full\"></i>&nbsp;";
    else
      if ($res[$i]['REPEATER'] == 1)
        $DevType .= "<i class=\"fa fa-wifi\"></i>&nbsp;";
      else
        $DevType .= "<i class=\"fa fa-plug\"></i>&nbsp;";

		if ($res[$i]['BOOTVER'] != ""){
			$DevType .= $res[$i]['BOOTVER'];
		}
		
		$res[$i]['DEVINFO'] = $DevType;
		$res[$i]['INFO'] .= nodeInfo($res[$i]);
    }
    $out['RESULT']=$res;
  }  
  
  $out['LOCATIONS']=SQLSelect("SELECT * FROM locations ORDER BY TITLE");
?>
