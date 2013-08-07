<?php

require_once "../include/crud/db.php";
require_once "../include/flickr/flickr.php";
require_once "../include/functions.php";

$db = new DB();


if($_POST['title']){
	if($_POST['image']){
		$sizes = getSize($_POST['image']);
		$bg_image = $sizes[7]["source"]; 
		$bg_small = $sizes[2]["source"];
	} else {
		$bg_image = "";
		$bg_small = "";
	}

	$title = addslashes($_POST['title']);
	$slug = slugify($_POST['title']);
	$desc = addslashes($_POST['desc']);
	$time = strftime("%Y-%m-%d %H:%M:%S", time()+60*60*5+60*30);
		
	if($db->query("INSERT INTO idea (`slug`,`title`,`desc`,`bg_img`,`bg_img_small`,`bg_color`,`created`,`updated`) VALUES ('$slug', '$title', '$desc', '$bg_image', '$bg_small', '{$_POST['color']}', '$time', '$time')")){
		$idea = $db->_query("SELECT * FROM idea ORDER BY `iid` DESC LIMIT 1");

		echo json_encode($idea);
	} else {
		echo $db->msql_error();
	}
}
?>
