<?php
function checkcookies() {
	if(isset($_COOKIE['user_id'])){
		$rs_ctime = mysql_query("SELECT ctime FROM users WHERE sn =". $_COOKIE['user_id']) or die(mysql_error());
		$ctime = mysql_fetch_row($rs_ctime);
		if( (time() - $ctime[0]) > 60*60*24*5) {
			logout();
		}
	}
	else {
		//header("Location: login.php");
	}
}

function logout() {
	if(isset($_COOKIE['user_id'])) {
		mysql_query("UPDATE users SET ctime='' WHERE sn=".$_COOKIE['user_id']) or die(mysql_error());
	}
	setcookie("user_id", '', time()-60*60*24*5, "/");
	setcookie("user_email", '', time()-60*60*24*5, "/");
	setcookie("user_name", '', time()-60*60*24*5, "/");
	header("Location: ".$base_url."index.php");
}
checkcookies();
?>