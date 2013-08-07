<?php

require_once "../include/crud/db.php";
require_once "../include/functions.php";
require_once "../include/md/markdown.php";
require_once "../include/md/smartypants.php";

$db = new DB();
$return = array();

if($_POST['id']){
	$text = addslashes($_POST['text']);
	$title = addslashes($_POST['title']);
	$slug = slugify($_POST['title']);
	$covertxt = addslashes($_POST['covertxt']);
	$return['action'] = "update";
	$upd = $db->query("UPDATE longform SET `body`='$text', `title`='$title', `slug`='$slug' WHERE `pid`={$_POST['id']}");
	if($upd){
		$return['html'] = SmartyPants(Markdown($_POST['text']));
		$return['raw']	= $_POST['text'];
		$return['title'] = $_POST['title'];
		$return['covertxt'] = SmartyPants(Markdown($_POST['covertxt']));
	} else {
		$return['error'] = mysql_error($db);
	}
} else {
	$text = addslashes($_POST['text']);
	$title = addslashes($_POST['title']);
	$slug = slugify($_POST['title']);
	$crea = strftime("%Y-%m-%d %H:%M:%S", time()+60*60*5+60*30);
	$return['action'] = "new";
	$new = $db->query("INSERT INTO longform (`created`,`title`,`slug`,`body`) VALUES ('$crea', '$title', '$slug', '$text')");
	if($new){
		$post = $db->_query("SELECT * FROM longform ORDER BY `pid` DESC LIMIT 1");
		$return['pid'] = $post['pid'];
		$return['created'] = $crea;
		$return['created_substr'] = substr($crea, 0, 10);
		$return['title'] = $post['title'];
		$return['slug'] = $post['slug'];
		$return['raw'] = $post['body'];
		$return['html'] = SmartyPants(Markdown($post['body']));
	} else
		$return['error'] = mysql_error($db);
}

echo json_encode($return);
?>
