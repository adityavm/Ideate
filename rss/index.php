<?php

header("Content-type:application/rss+xml; charset:utf-8");

require_once "../idea/md/smartypants.php";
require_once "../idea/md/markdown.php";
require_once "../crud/db.php";
$db = new DB();

if(($_GET['id'] || $_GET['id']==0) && ctype_digit($_GET['id'])){
	$idea = $db->_query("SELECT `title` FROM idea WHERE `iid`={$_GET['id']} LIMIT 1");
	$posts = $db->query("SELECT * FROM post WHERE `iid`={$_GET['id']} ORDER BY `pid` DESC LIMIT 5");
} else {
	$posts = $db->query("SELECT * FROM post ORDER BY `pid` DESC LIMIT 5");
}

echo '<?xml version="1.0" encoding="utf-8" ?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">

<channel>
	<title>Aditya Mukherjee</title>
	<link>http://adityamukherjee.com</link>
	<atom:link href="http://adityamukherjee.com/rss/<?php echo ($_GET['id']) ? $_GET['id'] : ""; ?>" rel="self" type="application/rss+xml" />
	<description><? echo ($_GET['id']) ? $idea['title'] : "The best and worst of Aditya Mukherjee."; ?></description>
<?php
	while($post = $posts->fetch_assoc()):
?>
	<item>
  	<title><?php 
		$body = $post['body'];
		$body = SmartyPants(Markdown($body));
		if($post['iid'] == 0):
			echo htmlentities($post['title']);
			$link = "http://adityamukherjee.com/longform/" . $post['pid'];
		else:
			echo substr(strip_tags($body), 0, 100), "..."; 
			$link = "http://adityamukherjee.com/idea/" . $post['pid'];
		endif;
	?></title>
		<link><?php echo $link ?></link>
		<guid><?php echo $link ?></guid>
		<description><![CDATA[
			<?php echo $body ?>
		]]></description>
	</item>
<?php endwhile; ?>
</channel>

</rss>
