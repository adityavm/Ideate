<?php

require_once "../include/crud/db.php";
require_once "../include/md/markdown.php";
require_once "../include/md/smartypants.php";

$db = new DB();

if($_GET['id'] && ctype_digit($_GET['id'])):
	$ret;
	$post = $db->_query("SELECT `body`,`link` FROM post WHERE `pid`={$_GET['id']}");
	
	$ret['raw'] = $post['body'];
	$ret['link'] = $post['link'];

	echo json_encode($ret);
else:
	die();
endif;
