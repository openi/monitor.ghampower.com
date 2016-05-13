<?php
include('../connection.php');
include('checkcookies.php');
if($_COOKIE['user_id']) {
	$query=mysql_query("SELECT `level` FROM users WHERE sn='".$_COOKIE['user_id']."'");
	while ($rows=mysql_fetch_array($query)) {
		if ($rows["level"]==1) {
			header("Location: login.php");
		}
	}
}
else {
	header("Location: login.php");
}
if($_POST) {
	$site1=$_POST["site"];
	if($site1=='new') {
		mysql_query("INSERT INTO site_info (`site_name`,`link`,`lat`,`long`,`description`) VALUES('".$_POST["site_name"]."','".$_POST["link"]."',".$_POST["lat"].",".$_POST["long"].",'".$_POST["description"]."')") or die(mysql_error());
		mysql_query("CREATE TABLE IF NOT EXISTS `".$_POST['link']."` (
			`id` int(11) NOT NULL,
			  `Date` date NOT NULL,
			  `Time` time NOT NULL,
			  `Battery Voltage` float(10,2) NOT NULL,
			  `Solar Current` float(10,2) NOT NULL,
			  `Inverter Current` float(10,2) NOT NULL,
			  `State of Charge` float(10,2) NOT NULL,
			  `kWh from Solar` float(10,2) NOT NULL,
			  `kWh from Grid` float(10,2) NOT NULL,
			  `kWh to Load` float(10,2) NOT NULL
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7135 ") or die(mysql_error());
		mysql_query("ALTER TABLE `".$_POST['link']."` ADD PRIMARY KEY (`id`)") or die(mysql_error());
		mysql_query("ALTER TABLE `".$_POST['link']."`
			MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7135") or die(mysql_error());
	}
	else {
		mysql_query("UPDATE site_info SET `site_name`='".$_POST["site_name"]."', `lat`='".$_POST["lat"]."', `long`='".$_POST["long"]."', `description`='".$_POST["description"]."' WHERE `id`='".$_POST["id"]."'") or die(mysql_error());
	}
}
$query=mysql_query("SELECT * FROM site_info") or die(mysql_error());
 while($rows=mysql_fetch_assoc($query)){
	$sites[]=$rows;
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
    	<a href="logout.php" style="margin-top:10px; margin-left:5px; float:left;">Logout</a>
    	<div class="right logo">
        </div>
        <div class="clear"></div>
    </nav>
    <div style="width:100%;">
    <div class="location_list_admin">
    <ul>
    <li><a class="site_list" href="#" data-link="new">Add New</a></li>
    <?php
		for ($i=0; $i<count($sites); $i++) {
			echo '<li><a class="site_list" href="#" data-link="'.$sites[$i]['link'].'">'.$sites[$i]['site_name'].'</a></li>';
		}
	?>
    </ul>
    </div>
    <div id="container" style="float:right; position:relative; overflow:auto; padding:10px">
    	<?php
			$site=$_GET["site"];
			$edit=$_GET["edit"];
			if ($site) {
        $query=mysql_query("SELECT * FROM site_info WHERE link='$site'") or die(mysql_error());
		 while ($row=mysql_fetch_array($query)) {
			$id=$row['id'];
			$site_name=$row["site_name"];
			$link=$row["link"];
			$lat=$row["lat"];
			$long=$row["long"];
			$description=$row["description"];
			$images=$row["images"];
		} ?>
        <form action="" method="post">
        <table class="login" width="100%">
            <tr><td align="right">Site Name:</td><td><input type="text" name="site_name" value="<?php echo (($site_name)?$site_name:''); ?>" /></td></tr>
            <tr><td align="right">Site Id:</td><td><input type="text" disabled name="link1" value="<?php echo (($link)?$link:''); ?>"/></td></tr>
            <tr><td align="right">Latitude:</td><td><input type="text" name="lat" value="<?php echo (($lat)?$lat:''); ?>" /></td></tr>
            <tr><td align="right">Longitude:</td><td><input type="text" name="long" value="<?php echo (($long)?$long:''); ?>" /></td></tr>
            <tr><td align="right">Description:</td><td><textarea name="description" style="width:400px; height:200px;"><?php echo (($description)?$description:''); ?></textarea></td></tr>
        </table>
        <input type="hidden" name="id" value="<?php echo (($id)?$id:''); ?>"/>
        <input type="hidden" name="link" value="<?php echo (($link)?$link:''); ?>"/>
        <input type="hidden" value="<?php echo $site; ?>" name="site" />
        <div style="padding:10px 0; text-align:right; width:50%"><input type="submit" class="button" value="Submit" />&nbsp;&nbsp;&nbsp;<a href='<?php echo $base_url."/admin";?>'>Cancel</a></div>
        </form>
		<?php } ?>
    </div>
    <div class="clear"></div>
    </div>
    <div class="footer">
    </div>
    <script type="text/javascript" src="../js/jquery-2.1.0.min.js"></script>
    <script type="text/javascript">
		$(document).ready(function() {
			$('.location_list_admin').height(window.innerHeight-90);
			$('#container').height(window.innerHeight-110);
			$('#container').width(window.innerWidth-291);
			$('.site_list').click(function() {
				var sitelink=$(this).attr('data-link'); 
				window.location.replace("<?php echo $base_url; ?>/admin/index.php?site="+sitelink);
			});
			$('input[name="site_name"]').change(function() {
				var test= $('input[name="site_name"]').val();
				test = test.toLowerCase().replace(/ /g,'');
				$('input[name="link"]').val(test.substring(0,8));
				$('input[name="link1"]').val(test.substring(0,8));
			});
		});
		$(window).resize(function() {
			$('.location_list_admin').height(window.innerHeight-90);
			$('#container').height(window.innerHeight-110);
			$('#container').width(window.innerWidth-291);
		});
	</script>
  </body>
</html>