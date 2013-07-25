<?php

require_once "./tb/crud/db.php";

$db = new DB();
$auth = $db->_query("SELECT * FROM auth LIMIT 1");
$ideas = $db->query("SELECT `iid`,`bg_color`,`bg_img`,`bg_img_small`,`title` FROM idea WHERE `closed`=0 ORDER BY `updated` DESC");

if($_COOKIE[$auth['cookie']])
	$LOGGED = true;
?>
<!DOCTYPE html>
<html>
<head>
	<title>Aditya Mukherjee</title>

	<link rel="stylesheet" type="text/css" href="./tb/all/style/ideas.less" />
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

	<?php if($LOGGED): ?>
		<link rel="stylesheet" type="text/css" href="./tb/all/style/ideas-admin.less" />
	<?php endif; ?>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="./tb/all/raphael.js" type="text/javascript"></script>
	<script src="./tb/all/ideas.js" type="text/javascript"></script>

	<?php if($LOGGED){ ?><script src="./tb/all/ideas-admin.js" type="text/javascript"></script><?php } ?>

	<script type="text/javascript" src="//use.typekit.net/rau5wab.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

	<script type="text/javascript" charset="utf-8">
		var ideas = <?php
			$out = array();
			while($idea = $ideas->fetch_assoc())
				$out[] = $idea;

			echo json_encode($out);
		?>;
		var links = <?
			$rel_out = array();
			for($i=0;$i<count($out);$i++):
				$rels = $db->query("SELECT * FROM rels WHERE `iid`={$out[$i]['iid']}");
				while($rel = $rels->fetch_assoc()):
					// { iid : { related_to : weight, ... } }
					$rel_out[$out[$i]['iid']][$rel['rel_to']]++;// = $rel['rel_to'];
				endwhile;
			endfor;

			echo json_encode($rel_out);
		?>;
	</script>

	<?php if(!$LOGGED){ ?>
		<script src="/tb/woopra.js" type="text/javascript"></script>
	<?php } ?>
</head>
<body>
	<div class="head">
		<div class="title">
			<img class="icon" src="https://si0.twimg.com/profile_images/2745465978/5999ce441d4251bcdd79159b5d75f359.png" />
			<span class="label">Aditya Mukherjee</span>
		</div>
		<div class="nav">
			<?php if($LOGGED): ?>
				<a href="/longform/0">New Longform</a>
				<a href="#" class="new-idea">New Idea</a>
			<?php endif; ?>
			<a href="/" class="active">Home</a>
			<!-- <a class="disabled" href="./about">About</a> -->
		</div>
		<div class="clear"></div>
	</div>
	<div class="body">
		<div class="woi-label">Web<span>of</span>Ideas</div>
		<div class="longform">
			<div class="trans-arrow-back"></div>
			<div class="trans-arrow"></div>
			<hr class="stitch" />
			<h3>Longform</h3>
			<div class="longform-list">
			<?php
				$lf = $db->query("SELECT * FROM longform ORDER BY `pid` DESC");
				while($l = $lf->fetch_assoc()):
			?>
				<div class="lf-post">
					<div class="lf-post-title"><a href="http://adityamukherjee.com/longform/<?php echo $l['pid'] ?>"><?php echo htmlentities($l['title']) ?></a></div>
					<div class="lf-post-meta">
					<a href="http://adityamukherjee.com/longform/<?php echo $l['pid'] ?>"><?php echo strftime("%d %B, &rsquo;%y", strtotime($l['created'])) ?></a>
						<span class="sep">+</span>
						<span class="ttr">
							<span class="icon-time"></span>
							<?
								$rawtext = count(explode(" ", $l['body']));
								echo round($rawtext/200, 0) . " min";
							?>
						</span>
					</div>
				</div>
			<?php
				endwhile;
			?>
			</div>
		</div>

	<div class="old-ideas hide">
	<?php
		$i = 0;
		$ideas = $db->query("SELECT `iid`,`bg_color`,`bg_img`,`title` FROM idea WHERE `closed`=0 ORDER BY `updated` DESC");
		while($idea = $ideas->fetch_assoc()):
			$posts = $db->_query("SELECT count(`pid`) FROM post WHERE `iid`={$idea['iid']}");
	?>
		<div class="idea square <?php echo ($i == 0) ? "idea-row-s" : "" ?> <?php echo ($i==3) ? "idea-row-e" : "" ?> <?php echo (!$idea['closed']) ? "open" : "" ?>" id="<?php echo $idea['iid'] ?>">
			<a href="../idea/<?php echo $idea['iid'] ?>">
				<div class="idea-circle" style="background-color:<?php echo $idea['bg_color'] ?>;<?php echo ($idea['bg_img']) ? "background-image:url('{$idea['bg_img']}')" : "" ?>"></div>
				<div class="idea-title"><?php echo $idea['title'] ?></div>
				<?php
					$count = $posts['count(`pid`)'];
					if($count):
				?>
						<div class="pcou">(<?php echo ($count == 1) ? "1 update" : "$count updates" ?>)</div>
				<?php
					endif;
				?>
			</a>
		</div>
	<?php
			$i=++$i%4;
		endwhile;
		if($LOGGED):
	?>
		<div class="idea square new-idea">
			<div class="idea-circle icon-plus-sign"></div>
			<div class="idea-title">New Idea</div>
		</div>
	<?php endif; ?>
	<div class="clear"></div>
	</div>

	</div>
	<div class="feet">
		<div class="copy">
			<span class="cpy-label">Copyright &copy; 2013</span> <a href="/about/">Aditya Mukherjee</a> <span>+</span> <a href="mailto:hi@adityamukherjee.com">Say Hi!</a> <span>+</span> <a href="/rss/<?php echo $idea['iid']; ?>" class="icon-rss"></a> <span>+</span>
<a href="http://twitter.com/aditya" class="icon-twitter" target="_blank"></a> <span>+</span> <a href="http://github.com/adityavm" class="icon-github" target="_blank"></a>
		</div>
		<div class="right">
			Made with <span>&hearts;</span> in New Delhi
		</div>
		<div class="clear"></div>
	</div>
</body>
</html>
