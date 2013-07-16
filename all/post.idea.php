<?php

require_once "../crud/db.php";

$db = new DB();

if($_POST['title']){
	if($_POST['image']){
		$bgimg = pathinfo($_POST['image']);
		$bg_small = $bgimg['dirname'] . "/" . $bgimg['filename'] . "s." . $bgimg['extension'];
	}

	$title = addslashes($_POST['title']);
	$time = strftime("%Y-%m-%d %H:%M:%S", time()+60*60*5+60*30);
		
	if($db->query("INSERT INTO idea (`title`,`bg_img`,`bg_img_small`,`bg_color`,`created`,`updated`) VALUES ('{$title}', '{$_POST['image']}', '{$bg_small}', '{$_POST['color']}', '{$time}', '{$time}')")){
		$idea = $db->_query("SELECT ` FROM idea ORDER BY `iid` DESC LIMIT 1");

		echo json_encode($idea);
	}
}
?>
