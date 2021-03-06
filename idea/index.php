<?php

error_reporting(E_PARSE);// in-case of direct access

require_once $BASE_URL ."/include/crud/db.php";
require_once $BASE_URL ."/include/functions.php";
require_once $BASE_URL ."/include/md/markdown.php";
require_once $BASE_URL ."/include/md/smartypants.php";

# initialise db
$db = new DB();
$auth = $db->_query("SELECT * FROM auth LIMIT 1");

if($_COOKIE[$auth['cookie']])
	$LOGGED = true;

# get idea ID from URL
if($POST_IDENT){
	$iid = $POST_IDENT;
	if(ctype_digit($iid)){
		$idea = 	$db->_query("SELECT * FROM idea WHERE `iid`=$iid LIMIT 1");
		$posts = 	$db->query("SELECT * FROM post WHERE `iid`=$iid ORDER BY `pid`");
	} else if (ctype_print($iid)){
		$idea = 	$db->_query("SELECT * FROM idea WHERE `slug` LIKE '$iid' LIMIT 1");
		$posts = 	$db->query("SELECT * FROM post WHERE `iid`={$idea['iid']} ORDER BY `pid`");
	}
} else
	header("Location: /404");

if(!$idea)
	header("Location: /404");

if($LOGGED){
	echo "<!--\n";
	echo '$idea:',  "\n", var_export($idea), "\n";
	echo '$posts:',  "\n", var_export($posts), "\n";
	echo '$_GET:', "\n", var_export($_GET), "\n";
	echo getcwd() . "\n";
	echo "-->";
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $idea['title'] ?> + Aditya Mukherjee</title>

	<meta charset='utf-8'> 

	<link rel="stylesheet" type="text/css" href="/tb/idea/style/idea.less" />
	<?php if($LOGGED){ ?>
		<link rel="stylesheet" href="/tb/include/cm/codemirror.css" />
		<link rel="stylesheet" href="/tb/include/cm/elegant.css" />
		<link rel="stylesheet" type="text/css" href="/tb/idea/style/idea-admin.less" />
	<? } ?>
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

	<style type="text/css">
		@-webkit-keyframes fadeOutBg {
			0% { background-color: #fff; border-color: #aaa; -webkit-animation-timing-function: ease-out; }
			100% { background-color: #f5f5f5; border-color: #f5f5f5; -webkit-animation-timing-function: ease-out; }
		}

		::selection {
			background-color: <?php echo $idea['bg_color'] ?>;
			color: #fff;
		}

		.banner-custom {
			background-image:url('<?php echo $idea['bg_img'] ?>');
		}

		.banner-custom,
		.end-circle {
			background-color: <?php echo $idea['bg_color'] ?>;
		}

		.body a,
		.body a:visited {
			color: <?php echo $idea['bg_color'] ?>;
		}
	</style>

	<script type="text/javascript" charset="utf-8">
		var idea = {
			id:		<?php echo $idea['iid'] ?>,
			title: 	"<?php echo $idea['title'] ?>"
		}
	</script>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

	<?php if($LOGGED){ ?>
		<script src="/tb/include/cm/codemirror.js"></script>
		<script src="/tb/include/cm/markdown.js"></script>
		<script src="/tb/idea/idea-admin.js" type="text/javascript"></script>
	<? } ?>

	<script src="/tb/idea/idea.js" type="text/javascript"></script>

	<script type="text/javascript" src="//use.typekit.net/uit4mor.js"></script>
	<script type="text/javascript">try{Typekit.load({});}catch(e){}</script>
</head>
<body>
	<div class="head">
		<div class="title">
			<img class="icon" src="http://www.gravatar.com/avatar/d2b4354e4e40f0b5dc30e5c3b2ccf7c5.png" />
			<span class="label">Aditya Mukherjee</span>
		</div>
		<div class="nav">
			<a href="/">Home</a>
			<!-- <a class="disabled" href="./about">About</a> -->
		</div>
		<div class="clear"></div>
	</div>
	<div class="banner banner-custom">
		<?php
			if ($idea['title']):
		?>
			<div class="title-wrap">
				<div class="title"><? echo $idea['title'] ?></div><br/>
				<?php if($idea['desc']){ ?><div class="desc"><? echo $idea['desc'] ?></div><?php } ?>
				<div class="clear"></div>
		<?php
			$related = array();
			$rel = $db->query("SELECT `rel_to` FROM rels WHERE `iid`={$idea['iid']}");
			while($rid = $rel->fetch_assoc()):
				$re = $db->_query("SELECT `iid`,`title`,`bg_img`,`bg_img_small`,`bg_color` FROM idea WHERE `iid`={$rid['rel_to']}");
				$related[] = $re;
			endwhile;
			if(count($related)):
				for($i=0;$i<count($related);$i++):
		?>
			<a href="./<?php echo $related[$i]['iid']?>" title="<?php echo $related[$i]['title']?>"><div class="related-circle" style="background-color:<?php echo $related[$i]['bg_color']?>;background-image:url('<?php echo $related[$i]['bg_img_small']?>');"></div></a>
		<?php 
				endfor;
			endif;
		?>
		</div><!-- title-wrap -->
		<?php
			endif;
		?>
	</div><!-- banner -->
	<div class="sep">
		<span class="sep-line"></span>
		<span class="sep-circ"></span>
	</div>
	<div class="body">
		<?php
			if($posts):
				while ($post = $posts->fetch_assoc()):
					$pdate = strtotime($post['created']);
		?>
					<div class="post" id="<?php echo $post['pid'] ?>">
						<div class="fns"></div>
						<div class="related-to">
						<?php
							if($post['link']): ?>
								<a class="fr" href="<?php echo $post['link'] ?>" target="_blank">related reading</a>
							<? endif; ?>
							<?php
								$related = array();
								$rel = $db->query("SELECT `rel_to` FROM rels WHERE `pid`={$post['pid']}");
								while($rid = $rel->fetch_assoc()):
									$re = $db->_query("SELECT `iid`,`title`,`bg_img`,`bg_img_small`,`bg_color` FROM idea WHERE `iid`={$rid['rel_to']}");
									$related[] = $re;
								endwhile;
								if(count($related)):
							?>
									<div class="label">also related</div>
							<?php
									for($i=0;$i<count($related);$i++):
							?>
								<a href="./<?php echo $related[$i]['iid']?>"><div class="idea-circle" style="background-color:<?php echo $related[$i]['bg_color']?>;background-image:url('<?php echo $related[$i]['bg_img_small']?>');"></div></a>
							<?php
									endfor;
								endif;
							?>
						</div>
						<?php if($LOGGED): ?>
							<div class="edit-btns">
								<a class="edit-post icon-pencil" data-pid="<?php echo $post['pid'] ?>"><span>Edit post</span></a>
							</div>
						<?php endif; ?>
							<div class="post-body">
								<a class="upd" href="#<?php echo $post['pid'] ?>"><?php echo strftime("<span class='day'>%d</span><span class='mon'>%b</span><span class='yea'>%Y</span>", $pdate) ?></a>
								<div class="text">
									<?php 
										echo SmartyPants(Markdown($post['body']));
									?>
								</div>
							</div>
					</div>
		<?php
				endwhile;
			endif;

		# show ending separator if this idea is closed
			if($idea['closed']):
		?>
			<div class="sep sep-end">
				<span class="sep-circ"></span>
				<span class="sep-line"></span>
				<span class="end-circle">fin.</span>
			</div>
		<?php
			else:
		?>
			<p class="sep">
			<?php
				if($LOGGED):
			?>
				<a class="new-post">
			<?php
				endif;
			?>
				+
			<?php
				if($LOGGED):
			?>
				</a>
			<?php
				endif;
			?>
			</p>
		<?php
			endif;
		?>
	</div>
	<div class="feet">
		<div class="left">
			<span class="cpy-label">Copyright &copy; 2013</span> <a href="/about/">Aditya Mukherjee</a> <span>+</span> <a href="mailto:hi@adityamukherjee.com">Say Hi!</a> <span>+</span> <a href="/rss/<?php echo $idea['iid']; ?>/" class="icon-rss"></a>
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
