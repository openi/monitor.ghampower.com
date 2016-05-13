<?php
	$con=mysql_connect('localhost','root','*OpenI.,#') or die('Couldnot make connection');
	$db = mysql_select_db('monitor', $con) or die("Couldn't select database");
	$base_url="";
?>
