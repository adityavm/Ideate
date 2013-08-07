<?php

require_once "../include/crud/db.php";
require_once "../include/md/markdown.php";
require_once "../include/md/smartypants.php";

$db = new DB();

if($_GET['id'] && ctype_digit($_GET['id'])):
	$ret;
	$post = $db->_query("SELECT `title`,`body`,`cover`,`cover_text` FROM longform WHERE `pid`={$_GET['id']}");
	
	$ret['title'] = $post['title'];
	$ret['raw'] = $post['body'];
	$ret['cover'] = $post['cover'];
	$ret['covertxt'] = $post['cover_text'];

	echo json_encode($ret);
else:
	die();
endif;
