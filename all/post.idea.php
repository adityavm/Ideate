<?php

require_once "../crud/db.php";
require_once "../crud/flickr/flickr.php";

$db = new DB();

if($_POST['title']){
	if($_POST['image']){
		$sizes = getSize($_POST['image']);
		$bg_image = $sizes[7]["source"]; 
		$bg_small = $sizes[1]["source"];
	} else {
		$bg_image = NULL;
		$bg_small = NULL;
	}

	$title = addslashes($_POST['title']);
	$desc = addslashes($_POST['desc']);
	$time = strftime("%Y-%m-%d %H:%M:%S", time()+60*60*5+60*30);
		
	if($db->query("INSERT INTO idea (`title`,`desc`,`bg_img`,`bg_img_small`,`bg_color`,`created`,`updated`) VALUES ('{$title}', '{$desc}', '{$bg_image}', '{$bg_small}', '{$_POST['color']}', '{$time}', '{$time}')")){
		$idea = $db->_query("SELECT ` FROM idea ORDER BY `iid` DESC LIMIT 1");

		echo json_encode($idea);
	}
}
?>
