<?php

error_reporting(E_PARSE);// in-case of direct access

require_once $BASE_URL ."/include/crud/db.php";
require_once $BASE_URL ."/include/md/markdown.php";
require_once $BASE_URL ."/include/md/smartypants.php";

# initialise db
$db = new DB();
$auth = $db->_query("SELECT * FROM auth LIMIT 1");

if($_COOKIE[$auth['cookie']])
	$LOGGED = true;

# get post ID from URL
if(isset($POST_IDENT)){
	$pid = $POST_IDENT;
	if(ctype_digit($pid)){
		$post = $db->_query("SELECT * FROM longform WHERE `pid`=$pid");
	} else if(ctype_print($pid)){
		$post = $db->_query("SELECT * FROM longform WHERE `slug` LIKE '$pid'");
	}
} else
	header("Location: /404");

if($LOGGED){
	echo "<!--\n";
	echo '$post:',  "\n", var_export($posts), "\n";
	echo '$POST_IDENT:', "\n", var_export($POST_IDENT), "\n";
	echo "-->";
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $post['title'] ?> + Aditya Mukherjee</title>

	<meta charset='utf-8'> 

	<link rel="stylesheet" type="text/css" href="/tb/longform/style/longform.less" />
	<?php if($LOGGED){ ?>
		<link rel="stylesheet" href="/tb/include/cm/codemirror.css" />
		<link rel="stylesheet" href="/tb/include/cm/elegant.css" />
		<link rel="stylesheet" type="text/css" href="/tb/longform/style/longform-admin.less" />
	<? } ?>
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

	<?php if($LOGGED){ ?>
		<script src="/tb/include/cm/codemirror.js"></script>
		<script src="/tb/include/cm/markdown.js"></script>
		<script src="/tb/include/sd/showdown.js"></script>
		<script src="/tb/longform/longform-admin.js" type="text/javascript"></script>
	<? } ?>

	<script src="/tb/longform/longform.js" type="text/javascript"></script>

	<script type="text/javascript" src="//use.typekit.net/rau5wab.js"></script>
	<script type="text/javascript">
		try{Typekit.load({});}catch(e){}

		<?php if(ctype_digit($pid) && $pid == 0){ ?>$(document).ready(newPost);<?php } ?>
	</script>
</head>
<body>
	<div class="head">
		<div class="bar">
			<div class="title">
				<img class="icon" src="http://www.gravatar.com/avatar/d2b4354e4e40f0b5dc30e5c3b2ccf7c5.png" />
				<span class="label">Aditya Mukherjee</span>
			</div>
			<div class="nav">
			<?php if($LOGGED): ?>
				<a class="edit-post" data-pid="<?php echo $post['pid']?>">Edit</a>

				<a class="cancel-post">Cancel</a>
				<a class="save-post" data-pid="<?php echo $post['pid']?>">Save</a>
			<? endif; ?>
				<a href="/">Home</a>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="body">
		<div class="post" id="<?php echo $post['pid'] ?>">
			<?php
				$pdate = ($post['created']) ? strtotime($post['created']) : time();
			?>
			<div class="post-title-wrap">
				<div class="post-title"><?php echo $post['title']; ?></div>
			</div>
			<div class="post-meta-wrap">
				<div class="post-meta">
					<a href="#<?php echo $post['pid'] ?>"><?php echo strftime("%e %B, &rsquo;%y", $pdate) ?></a>
					<span class="sep">+</span> 
					<span class="ttr">
						<span class="icon-time"></span>
						<?php
							$rawtext = count(explode(" ", $post['body']));

							// 1 min reading time for every 200 words
							// round up to nearest .5 before taking floor
							echo round($rawtext/200, 0) . " min";
						?>
					</span>
				</div>
			</div>
			<div class="post-body">
				<div class="text">
					<?php 
						echo SmartyPants(Markdown($post['body']));
					?>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
		<div class="sep">
			<span class="outer-circle">
				<span class="inner-circle"></span>
			</span>
		</div>
	</div>
	<div class="feet">
		<div class="left">
			<span class="cpy-label">Copyright &copy; 2013</span> <a href="/about/">Aditya Mukherjee<a/> <span>+</span> <a href="mailto:hi@adityamukherjee.com">Say Hi!</a> <span>+</span> <a href="http://adityamukherjee.com/rss/longform/" class="icon-rss"></a>
		</div>
		<div class="cent">
			<a href="http://twitter.com/aditya" class="icon-twitter" target="_blank"></a>
			<span>+</span>
			<a href="http://github.com/adityavm" class="icon-github" target="_blank"></a>
			<span>+</span>
			<a href="http://instagram.com/aditya_" class="icon-instagram" target="_blank"></a>
		</div>
		<div class="right">
			Made with <span>&hearts;</span> in New Delhi
		</div>
		<div class="clear"></div>
	</div>
</body>
</html>
