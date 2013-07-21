<?php

require_once "../crud/db.php";
require_once "md/markdown.php";
require_once "md/smartypants.php";

$db = new DB();
$return = array();

if($_POST['id']){
	$body = addslashes($_POST['text']);
	$upd = $db->query("UPDATE post SET `body`='{$body}', `link`='{$_POST['link']}' WHERE `pid`={$_POST['id']}");
	if($upd){
		$return['html'] = SmartyPants(Markdown($_POST['text']));
		$return['raw']	= $_POST['text'];
		$return['link']	= $_POST['link'];
	} else {
		$return['error'] = mysql_error();
	}
} else {
	$body = addslashes($_POST['text']);
	$crea = strftime("%Y-%m-%d %H:%M:%S", (time()+60*60*5+60*30));
	$new = $db->query("INSERT INTO post (`iid`,`created`,`body`,`link`) VALUES ({$_POST['iid']}, '$crea', '$body', '{$_POST['link']}') ");
	if($new){
		$post = $db->_query("SELECT * FROM post WHERE `iid`={$_POST['iid']} ORDER BY `pid` DESC LIMIT 1");
		$return['pid'] = $post['pid'];
		$return['iid'] = $post['iid'];
		$return['created'] = $crea;
		$return['created_substr'] = substr($crea, 0, 10);
		$return['raw'] = $post['body'];
		$return['html'] = SmartyPants(Markdown($post['body']));
		$return['link'] = $post['link'];

		$db->query("UPDATE idea SET `updated`='$crea' WHERE `iid`={$post['iid']}");
	}
}

echo json_encode($return);
?>
