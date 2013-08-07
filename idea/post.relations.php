<?php

require_once "../include/crud/db.php";

$db = new DB();

if($_POST['pid']):
	$iids = $_POST['iids'];
	if(!count($iids))
		$db->query("DELETE FROM rels WHERE `pid`={$_POST['pid']}");
	else
		for($i=0;$i<count($iids);$i++)
			$db->query("INSERT INTO rels (`pid`, `iid`, `rel_to`) VALUES ({$_POST['pid']}, {$_POST['iid']}, {$iids[$i]})");

	echo json_encode(array("result"=>"success"));
else:
	die();
endif;

?>
