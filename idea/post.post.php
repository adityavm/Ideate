<?php

require_once "../crud/db.php";
require_once "md/markdown.php";
require_once "md/smartypants.php";

$db = new DB();
$return = array();

if($_POST['id']){
	$text = addslashes($_POST['text']);
	$title = addslashes($_POST['title']);
	$upd = $db->query("UPDATE post SET `body`='{$text}', `link`='{$_POST['link']}', `title`='{$_POST['title']}' WHERE `pid`={$_POST['id']}");
	if($upd){
		$return['html'] = SmartyPants(Markdown($_POST['text']));
		$return['raw']	= $_POST['text'];
		$return['title'] = $_POST['title'];
	} else {
		$return['error'] = mysql_error();
	}
} else {
	$text = addslashes($_POST['text']);
	$title = addslashes($_POST['title']);
	$crea = strftime("%Y-%m-%d %H:%M:%S", time()+60*60*5+60*30);
	$new = $db->query("INSERT INTO post (`iid`,`created`,`title`,`body`,`link`) VALUES ({$_POST['iid']}, '$crea', '$title', '$text', '{$_POST['link']}') ");
	if($new){
		$post = $db->_query("SELECT * FROM post WHERE `iid`={$_POST['iid']} ORDER BY `pid` DESC LIMIT 1");
		$return['pid'] = $post['pid'];
		$return['iid'] = $post['iid'];
		$return['created'] = $crea;
		$return['created_substr'] = substr($crea, 0, 10);
		$return['title'] = $post['title'];
		$return['raw'] = $post['body'];
		$return['html'] = SmartyPants(Markdown($post['body']));
		$return['link'] = $post['link'];

		$db->query("UPDATE idea SET `updated`='$crea' WHERE `iid`={$post['iid']}");
	}
}

echo json_encode($return);
?>
