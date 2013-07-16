<?php

require_once "../crud/db.php";

$db = new DB();

$ret = array();

$ideas = $db->query("SELECT `iid`,`title`,`bg_color`,`bg_img`,`bg_img_small` FROM idea ORDER BY `title`");

while($idea = $ideas->fetch_assoc()):
	$ret[] = $idea;
endwhile;

echo json_encode($ret);
?>
