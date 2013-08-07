<?php

header("Content-type:application/json");
echo getcwd();

require_once "phpFlickr.php";
require_once "../include/crud/db.php";

$db = new DB();
$auth = $db->_query("SELECT * FROM auth LIMIT 1");

if(!$_COOKIE[$auth['cookie']])
	die(json_encode( array("error"=>"auth") ));

function getSize($id){
	global $auth;

	$f = new phpFlickr($auth["flickr_key"], $auth["flickr_secret"]);
	$f->setToken($auth["flickr_token"]);
	$f->auth("read");
	$ret = $f->photos_getSizes($id);

	if(!$ret)
		var_dump($f->getErrorMsg());
	else
		return $ret;
}

if(isset($_GET['o']))
	echo json_encode(getSize($_GET["id"]));

?>
