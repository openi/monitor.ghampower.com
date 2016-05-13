<?php
include('../connection.php');
if( $_COOKIE['user_id']) {
	header('Location: index.php');
}
if($_POST) {
	$email = $_POST['email'];
	$pass = $_POST['pwd'];
	$result = mysql_query("SELECT `sn`,`name`,`email`,`password`,`level` FROM users WHERE email='$email'") or die (mysql_error()); 
	$num = mysql_num_rows($result);
	list($sn,$name,$emaildb,$pwd,$level) = mysql_fetch_row($result);
	if ($num) {
		if ($pwd ==$pass) { 
			$stamp = time();
			mysql_query("UPDATE users SET `ctime`='$stamp' WHERE sn='".$sn."'") or die(mysql_error());
			setcookie("user_id", $sn, time()+60*60*24*5, "/");
			setcookie("user_email",$emaildb, time()+60*60*24*5, "/");
			setcookie("user_name",$name, time()+60*60*24*5, "/");
			header("Location: index.php");
		}
		else {
			$err='Invalid Password. Please try again with correct password.';
		}
	}
	else {
		$err='Invalid Login. Please try again with correct user name and password.';
	}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gham Power Nepal | Monitor</title>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <link href="../css/style.css" rel="stylesheet">
  </head>
  <body>
  	<nav class="menu">
    	<div class="right logo">
        </div>
        <div class="clear"></div>
    </nav>
<div id="container">
	<div style="width:60%; margin:0 auto; margin-top:100px;">
	    <div class="paddingall">
            <h1>Login</h1>
            <hr width="100%" size="1" style="color:#AAAAAA"/>
            <br />
            <?php
            if(!empty($err))  {
                echo "<div class=\"error\">";
                echo $err;
                echo "</div>";	
            }
            ?>
            <form action="login.php" method="post" name="logform">
            <table class="login" width="100%">
                <tr><td align="right">Email:</td><td><input type="text" name="email" value="<?php echo (($email)?$email:''); ?>" /></td></tr>
                <tr><td align="right">Password:</td><td><div class="inputbox"><div class="inputboxside"></div><input type="password" name="pwd" /><div class="inputboxside"></div></div></td></tr>
            </table>
            <div style="padding:10px 0; text-align:right;"><input type="submit" class="button" value="Login" />&nbsp;&nbsp;&nbsp;<a href='<?php echo $base_url;?>'>Cancel</a></div>
            </form>
        </div>
    </div>
</div>
<div class="footer">
</div>
<script type="text/javascript" src="../js/jquery-2.1.0.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#container').height(window.innerHeight-190);
	});
	$(window).resize(function() {
		$('#container').height(window.innerHeight-190);
	});
</script>