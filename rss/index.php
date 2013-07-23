<?php

header("Content-type:application/rss+xml; charset:utf-8");

require_once "../idea/md/smartypants.php";
require_once "../idea/md/markdown.php";
require_once "../crud/db.php";
$db = new DB();

if($_GET['id'] && ctype_digit($_GET['id'])){
	$idea = $db->_query("SELECT `title` FROM idea WHERE `iid`={$_GET['id']} LIMIT 1");
	$posts = $db->query("SELECT * FROM post WHERE `iid`={$_GET['id']} ORDER BY `pid` DESC LIMIT 5");
} else if($_GET['id'] == "longform"){
	$posts = $db->query("SELECT * FROM longform ORDER BY `pid` DESC LIMIT 5");
} else {
	$union = $db->query("SELECT 'longform' as tableName, pid, created FROM longform UNION SELECT 'post' as tableName, pid, created FROM post ORDER BY created DESC LIMIT 5");
	$posts = array();
	while($u = $union->fetch_assoc()):
		$posts[] = $db->_query("SELECT * FROM {$u['tableName']} WHERE `pid`={$u['pid']}");
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
	<atom:link href="http://adityamukherjee.com/rss/<?php echo ($_GET['id']) ? $_GET['id'] : ""; ?>" rel="self" type="application/rss+xml" />
	<description><? echo ($_GET['id']) ? $idea['title'] : "The best and worst of Aditya Mukherjee."; ?></description>
<?php
	for($i=0;$i<count($out);$i++):
		$o = $out[$i];
?>
	<item>
  	<title><?php 
		$body = $o['body'];
		$body = SmartyPants(Markdown($body));
		if(!isset($o['iid'])):
			echo htmlentities($o['title']);
			$link = "http://adityamukherjee.com/longform/" . $o['pid'];
		else:
			echo substr(strip_tags($body), 0, 100), "..."; 
			$link = "http://adityamukherjee.com/idea/" . $o['iid'] . "#" . $o['pid'];
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
