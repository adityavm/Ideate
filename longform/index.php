<?php
require "../crud/db.php";
require_once "../idea/md/markdown.php";
require_once "../idea/md/smartypants.php";

# initialise db
$db = new DB();
$auth = $db->_query("SELECT * FROM auth LIMIT 1");

if($_COOKIE[$auth['cookie']])
	$LOGGED = true;

# get post ID from URL
$pid = $_GET['id'];
if($pid == 0 || ctype_digit($pid)){
	$post = $db->_query("SELECT * FROM longform WHERE `pid`={$pid}");
} else
	header("Location: /");

if($LOGGED){
	echo "<!--\n";
	echo '$post:',  "\n", var_export($posts), "\n";
	echo '$_GET:', "\n", var_export($_GET), "\n";
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
		<link rel="stylesheet" href="/tb/idea/cm/codemirror.css" />
		<link rel="stylesheet" href="/tb/idea/cm/elegant.css" />
		<link rel="stylesheet" type="text/css" href="/tb/longform/style/longform-admin.less" />
	<? } ?>
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

	<style type="text/css">
		@-webkit-keyframes fadeOutBg {
			0% { background-color: #fff; border-color: #aaa; -webkit-animation-timing-function: ease-out; }
			100% { background-color: #f5f5f5; border-color: #f5f5f5; -webkit-animation-timing-function: ease-out; }
		}
	</style>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

	<?php if($LOGGED){ ?>
		<script src="/tb/idea/cm/codemirror.js"></script>
		<script src="/tb/idea/cm/markdown.js"></script>
		<script src="/tb/longform/sd/showdown.js"></script>
		<script src="/tb/longform/longform-admin.js" type="text/javascript"></script>
	<? } ?>

	<script src="/tb/longform/longform.js" type="text/javascript"></script>

	<script type="text/javascript" src="//use.typekit.net/rau5wab.js"></script>
	<script type="text/javascript">
		try{
			Typekit.load({
				"active": function(){ 
					$('p a, p:not(:has(a))')
						.ligature('ffi', '&#xfb03;')
						.ligature('ffl', '&#xfb04;')
						.ligature('ff', '&#xfb00;')
						.ligature('fi', '&#xfb01;')
						.ligature('fl', '&#xfb02;');
					}
			});
		}catch(e){}

		<?php if($pid == 0){ ?>$(document).ready(newPost);<?php } ?>
	</script>

	<script src="/tb/woopra.js" type="text/javascript"></script>
</head>
<body>
	<div class="head">
		<div class="bar">
			<div class="title">
				<img class="icon" src="https://si0.twimg.com/profile_images/2745465978/5999ce441d4251bcdd79159b5d75f359.png" />
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
			<div class="post-meta">
				<a href="#<?php echo $post['pid'] ?>"><?php echo strftime("%d %B, &rsquo;%y", $pdate) ?></a>
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
			<div class="post-title-wrap <?php if($post['cover']){ ?>post-cover<?php } ?>" data-cover="<?php echo $post['cover'] ?>" style="background-image:url('<?php echo $post['cover']?>');">
				<?php if($LOGGED): ?>
					<span class="edit-cover-btn icon-picture"></span>
				<?php endif; ?>
				<div class="post-title"><?php echo $post['title']; ?></div>
				<div class="post-cover-text <?php echo ($post['cover_text']) ? "" : "hide" ?>"><?php echo SmartyPants(Markdown($post['cover_text'])); ?></div>
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
		<div class="copy">
			<span class="cpy-label">Copyright &copy; 2013</span> <a href="/about/">Aditya Mukherjee<a/> <span>+</span> <a href="mailto:hi@adityamukherjee.com">Say Hi!</a> <span>+</span> <a href="http://adityamukherjee.com/rss/longform/" class="icon-rss"></a> <span>+</span>
<a href="http://twitter.com/aditya" class="icon-twitter" target="_blank"></a> <span>+</span> <a href="http://github.com/adityavm" class="icon-github" target="_blank"></a>
		</div>
		<div class="right">
			Designed with <span>&hearts;</span> in New Delhi
		</div>
		<div class="clear"></div>
	</div>
</body>
</html>
