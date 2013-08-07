<?php

header("Content-type:application/rss+xml; charset:utf-8");

require_once $BASE_URL . "/include/md/smartypants.php";
require_once $BASE_URL . "/include/md/markdown.php";
require_once $BASE_URL . "/include/crud/db.php";

$db = new DB();

if($POST_IDENT){
	if(ctype_digit($POST_IDENT)){
		$idea = $db->_query("SELECT `title` FROM idea WHERE `iid`={$_GET['id']} LIMIT 1");
		$posts = $db->query("SELECT * FROM post WHERE `iid`={$_GET['id']} ORDER BY `pid` DESC LIMIT 5");
	} else if (ctype_print($POST_IDENT)){
		$idea = $db->_query("SELECT `title` FROM idea WHERE `slug` LIKE '{$_GET['id']}' LIMIT 1");
		$posts = $db->query("SELECT * FROM post WHERE `iid`={$idea['iid']} ORDER BY `pid` DESC LIMIT 5");
	} else if($POST_IDENT == "longform"){
		$posts = $db->query("SELECT * FROM longform ORDER BY `pid` DESC LIMIT 5");
	}
} else {
	$union = $db->query("SELECT 'longform' as tableName, pid, created FROM longform UNION SELECT 'post' as tableName, pid, created FROM post ORDER BY created DESC LIMIT 5");
	$posts = array();
	while($u = $union->fetch_assoc()):
		if($u['tableName'] == "post"):
			$post = $db->_query("SELECT * FROM {$u['tableName']} WHERE `pid`={$u['pid']}");
			$post = array_merge($post, 
								$db->_query("SELECT `slug` FROM `idea` WHERE `iid`={$post['iid']}"),
								array("type"=>"post")
							   );
			$posts[] = $post;
		else:
			$posts[] = array_merge($db->_query("SELECT * FROM {$u['tableName']} WHERE `pid`={$u['pid']}"), 
								   array("type"=>"longform")
							      );
		endif;

	endwhile;
}

if($_GET['id'])
	while($p = $posts->fetch_assoc())
		$out[] = $p;
else
	$out = $posts;

echo '<?xml version="1.0" encoding="utf-8" ?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">

<channel>
	<title>Aditya Mukherjee</title>
	<link>http://adityamukherjee.com</link>
	<atom:link href="http://adityamukherjee.com/rss/<?php echo ($POST_IDENT) ? $POST_IDENT : ""; ?>" rel="self" type="application/rss+xml" />
	<description><? echo ($_GET['id']) ? $idea['title'] : "The best and worst of Aditya Mukherjee."; ?></description>
<?php
	for($i=0;$i<count($out);$i++):
		$o = $out[$i];
?>
	<item>
  	<title><?php 
		$body = $o['body'];
		$body = SmartyPants(Markdown($body));
		if($o['type'] == "longform"):
			echo htmlentities($o['title']);
			$link = "http://adityamukherjee.com/longform/" . $o['slug'];
		else:
			// replace multiple \n\t and \s with one space in $body[0-100]
			echo preg_replace("/\s{2,}/", " ", preg_replace("/(\n\t*)/", " ", substr(strip_tags($body), 0, 100))), "..."; 
			$link = "http://adityamukherjee.com/idea/" . $o['slug'] . "#" . $o['pid'];
		endif;
	?></title>
		<link><?php echo $link ?></link>
		<guid><?php echo $link ?></guid>
		<description><![CDATA[
			<?php echo $body ?>
		]]></description>
	</item>
<?php endfor; ?>
</channel>

</rss>
