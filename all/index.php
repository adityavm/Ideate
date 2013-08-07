<?php

require_once $BASE_URL . "/include/crud/db.php";

$db = new DB();
$auth = $db->_query("SELECT * FROM auth LIMIT 1");
$ideas = $db->query("SELECT `iid`,`bg_color`,`bg_img`,`bg_img_small`,`title`,`slug` FROM idea WHERE `closed`=0 ORDER BY `updated` DESC");

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
<div class="all-content">
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
		<div class="longform section">
			<hr class="stitch" />
			<h3>Longform</h3>
			<div class="longform-list">
			<?php
				$lf = $db->query("SELECT * FROM longform ORDER BY `pid` DESC");
				while($l = $lf->fetch_assoc()):
			?>
				<div class="lf-post">
					<div class="lf-post-title"><a href="http://adityamukherjee.com/longform/<?php echo $l['slug'] ?>"><?php echo htmlentities($l['title']) ?></a></div>
					<div class="lf-post-meta">
					<a href="http://adityamukherjee.com/longform/<?php echo $l['slug'] ?>"><?php echo strftime("%d %B, &rsquo;%y", strtotime($l['created'])) ?></a>
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
		<!--<div class="instagram section">
			<hr class="stitch" />
			<h3>Instagram</h3>
			<div class="ig-photos">
				<div class="ig-photos-row">
				<?php
					$ig = file_get_contents("https://api.instagram.com/v1/users/318139/media/recent/?access_token=318139.b35cfe4.2be2668bf5bf4c159e5065ed327e612b&count=8");
					$ig = json_decode($ig, true);
					for($i=0;$i<count($ig['data']);$i++):
						echo ($i > 0 && $i%4 == 0) ? "</div><div class='ig-photos-row'>" : "";

						$p = $ig['data'][$i];
						echo "<a href='{$p['link']}' target='_blank'><img src='{$p['images']['thumbnail']['url']}' /></a>";
					endfor;
				?>
				</div>
			</div>
		</div>-->
	</div>

	</div>
	<div class="feet">
		<div class="left">
			<span class="cpy-label">Copyright &copy; 2013</span> <a href="/about/">Aditya Mukherjee</a> <span>+</span> <a href="mailto:hi@adityamukherjee.com">Say Hi!</a> <span>+</span> <a href="/rss/<?php echo $idea['iid']; ?>" class="icon-rss"></a>
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
</div>
</body>
</html>
