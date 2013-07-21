<?php

require_once "../crud/db.php";
require_once "../idea/md/markdown.php";
require_once "../idea/md/smartypants.php";

$db = new DB();
$return = array();

if($_POST['id']){
	$text = addslashes($_POST['text']);
	$title = addslashes($_POST['title']);
	$covertxt = addslashes($_POST['covertxt']);
	$upd = $db->query("UPDATE longform SET `body`='$text', `cover`='{$_POST['cover']}', `title`='$title', `cover_text`='$covertxt' WHERE `pid`={$_POST['id']}");
	if($upd){
		$return['html'] = SmartyPants(Markdown($_POST['text']));
		$return['raw']	= $_POST['text'];
		$return['title'] = $_POST['title'];
		$return['covertxt'] = SmartyPants(Markdown($_POST['covertxt']));
	} else {
		$return['error'] = mysql_error();
	}
} else {
	$text = addslashes($_POST['text']);
	$title = addslashes($_POST['title']);
	$covertxt = addslashes($_POST['covertxt']);
	$crea = strftime("%Y-%m-%d %H:%M:%S", time()+60*60*5+60*30);
	$new = $db->query("INSERT INTO post (`created`,`title`,`body`,`cover`,`covertxt`) VALUES ('$crea', '$title', '$text', '{$_POST['cover']}', '$covertxt') ");
	if($new){
		$post = $db->_query("SELECT * FROM longform ORDER BY `pid` DESC LIMIT 1");
		$return['pid'] = $post['pid'];
		$return['created'] = $crea;
		$return['created_substr'] = substr($crea, 0, 10);
		$return['title'] = $post['title'];
		$return['raw'] = $post['body'];
		$return['html'] = SmartyPants(Markdown($post['body']));
		$return['cover'] = $post['cover'];
		$return['covertxt'] = $post['covertxt'];
	}
}

echo json_encode($return);
?>
