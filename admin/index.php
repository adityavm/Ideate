<?php

require_once "../include/crud/db.php";
$db = new DB();

if($_POST['p']):
	# set a cookie for a week
	$auth = $db->_query("SELECT * FROM auth LIMIT 1");
	if($_POST['p'] == $auth['pass']){ 
		setcookie($auth['cookie'], "1", mktime()+60*60*24*7, "/");
	} else
		# wrong password logs me out
		setcookie($auth['cookie'], "0", mktime()-3600, "/");

	header("Location: /");
else:

?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<title>Log In + Aditya Mukherjee</title>
	<link rel="stylesheet" href="admin.css" type="text/css" />

	<script type="text/javascript" src="//use.typekit.net/rau5wab.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
</head>
<body>
	<div class="head">
		<div class="back"><a href="../all">Back</a></div>
		<div class="title">Aditya Mukherjee</div>
	</div>
	<div class="body">
		<form method="POST" action=".">
			<!-- <input type="text" name="u" /> -->
			<input type="password" name="p" />
			<input type="submit" />
		</form>
	</div>
	<div class="feet">
	</div>
</body>
</html>

<?php 
	
endif; 

?>
