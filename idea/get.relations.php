<?php

require_once "../include/crud/db.php";

$db = new DB();

$ret = array();

$ideas = $db->query("SELECT `iid` FROM rels WHERE `pid`={$_GET['pid']}");

while($idea = $ideas->fetch_assoc()):
	$ret[] = $idea['iid'];
endwhile;

echo json_encode($ret);
?>
