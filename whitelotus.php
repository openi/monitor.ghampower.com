<?php
 include("connection.php");
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
    <link href="css/style.css" rel="stylesheet">
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBPyd_X25JrIz8Aqug4OZ6j3wX8GPqFX10">
    </script>
    <script type="text/javascript">
	var map;
	var infowindow=Array();
	var marker=Array();
    function initialize() {
		var mapOptions = {
		  center: { lat: 27.8464426, lng: 85.0483207},
		  zoom: 9,
		  panControl: false,
		  zoomControl: true,
		  streetViewControl: false,
		  zoomControlOptions: {
			style: google.maps.ZoomControlStyle.LARGE,
			position: google.maps.ControlPosition.RIGHT_TOP
		  }
        };
        map = new google.maps.Map(document.getElementById('map'),mapOptions);
		<?php
		for($i=0;$i<count($sites);$i++) {
			$datetime=0;
                	$battery_voltage=0;
                	$solar_current=0;
        	        $inverter_current=0;
	                $SoC=0;
			$query=mysql_query("SELECT * FROM ".$sites[$i]['link']." ORDER BY TIMESTAMP(`Date`,`Time`) DESC LIMIT 1") or die(mysql_error());
			while($rows=mysql_fetch_array($query)){
				$datetime=date("U",strtotime($rows["Date"]." ".$rows["Time"]));
				$battery_voltage=$rows['Battery Voltage'];
				$solar_current=$rows['Solar Current'];
				$inverter_current=$rows['Inverter Current'];
				$SoC=$rows['State of Charge'];
				$grid_current=$rows['Grid Current'];
			}
		?>
			var contentString = '<div id="content">'+
				  '<div id="siteNotice">'+
				  '</div>'+
				  '<h1 class="firstHeading"><?php echo $sites[$i]['site_name']; ?></h1>'+
				  '<div class="bodyContent"><div class="left decs">'+
				  '<p><?php echo $sites[$i]['description']; ?></p>'+
				  '</div><div class="right datagraph">'+
				  '<div class="flowchart_1">'+
				  '<svg width="300" height="200">'+
				  <?php if($solar_current>0) { ?>
				  '<circle r="3" style="stroke: none; fill: #FFFFFF;">'+
				  '<animateMotion path="M46 39 L103 39 L103 128" begin="0s" dur="0.4s" repeatCount="indefinite" rotate="auto" />'+
				  '</circle>'+
				  <?php } ?>
				  '<text x="40" y="100" fill="black" font-size="11"><?php echo $solar_current."A"; ?></text>'+
				  '<text x="40" y="115" fill="black" font-size="11"><?php echo (($battery_voltage*$solar_current)>1000) ? round($battery_voltage*$solar_current/1000,2)."kW" : round($battery_voltage*$solar_current,2)."W"; ?></text>'+
				  <?php if ($sites[$i]['link']=="unicef") { ?>
				  '<text x="185" y="160" fill="black" font-size="11"><?php echo $grid_current."A"; ?></text>'+
				  '<text x="185" y="175" fill="black" font-size="11"><?php echo (($battery_voltage*$grid_current)>1000) ? round($battery_voltage*$grid_current/1000,2)."kW" : round($battery_voltage*$grid_current,2)."W"; ?></text>'+
				  <?php } ?>
				  <?php if($inverter_current<0) { ?>
				  '<circle r="3" style="stroke: none; fill: #FFFFFF;">'+
				  '<animateMotion path="M139 128 L139 80 L180 80" begin="0s" dur="0.4s" repeatCount="indefinite" rotate="auto" />'+
				  '</circle>'+
				  <?php } else { ?>
				  '<circle r="3" style="stroke: none; fill: #FFFFFF;">'+
				  '<animateMotion path="M180 80 L139 80 L139 128" begin="0s" dur="0.4s" repeatCount="indefinite" rotate="auto" />'+
				  '</circle>'+
				  <?php } ?>
				  '<text x="130" y="52" fill="black" font-size="11"><?php echo $inverter_current."A"; ?></text>'+
				  '<text x="130" y="67" fill="black" font-size="11"><?php echo (abs($battery_voltage*$inverter_current)>1000) ? round($battery_voltage*$inverter_current/1000,2)."kW" : round($battery_voltage*$inverter_current,2)."W"; ?></text>'+
				  '<circle r="3" style="stroke: none; fill: #FFFFFF;">'+
				  '<animateMotion path="M193 64 L193 39 L240 39" begin="0s" dur="0.4s" repeatCount="indefinite" rotate="auto" />'+
				  '</circle>'+
				  <?php if($inverter_current>0 || $grid_current>0) { ?>
				  '<circle r="3" style="stroke: none; fill: #FFFFFF;">'+
				  '<animateMotion path="M240 143 L193 143 L193 111" begin="0s" dur="0.4s" repeatCount="indefinite" rotate="auto" />'+
				  '</circle>'+
				  <?php } ?>
				  '<text x="110" y="162" fill="white" font-size="11"><?php echo round($SoC,0)."%"; ?></text>'+
				  '<text x="105" y="195" fill="black" font-size="11"><?php echo $battery_voltage."V"; ?></text>'+
				  '<text x="10" y="10" fill="black" font-size="14" font-weight="bold"><?php echo "Energy Flow"; ?></text>'+
				  '</svg>'+
				  '</div>'+
				  '<p class="data_link"><a class="data_but" href="<?php echo $base_url; ?>/system.php?site=<?php echo $sites[$i]['link']; ?>">'+
				  'More Data</a> </div>'+
				  '<div class="clear"></div>'+
				  '</div>';
			
			infowindow[<?php echo $i; ?>] = new google.maps.InfoWindow({
				  content: contentString,
			  });
			var myLatlng = new google.maps.LatLng(<?php echo $sites[$i]['lat']; ?>,<?php echo $sites[$i]['long']; ?>);
			marker[<?php echo $i; ?>] = new google.maps.Marker({
				position: myLatlng,
				map: map,
				title:'<?php echo $sites[$i]['site_name']; ?>'
			});
			google.maps.event.addListener(marker[<?php echo $i; ?>], 'click', function() {
			<?php for($ii=0;$ii<count($sites);$ii++) { ?>
					infowindow[<?php echo $ii; ?>].close();
			<?php
				}
			?>
				infowindow[<?php echo $i; ?>].open(map,marker[<?php echo $i; ?>]);
			});
		<?php
		}
		?>
		google.maps.event.addListener(map, "click", function(event) {
		<?php
			for($i=0;$i<count($sites);$i++) { ?>
				infowindow[<?php echo $i; ?>].close();
		<?php
			}
		?>
		$('.location_list').animate({'left':-250},500);
		});
    }
	
    google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
  	<nav class="menu">
    	<a class="left main_but" href="#">&nbsp;</a>
        <a class="left info" href="#">&nbsp;</a>
    	<a class="right logo" href="http://monitor.ghampower.com/">&nbsp;</a>
        <div class="clear"></div>
    </nav>
    <div class="location_list">
    <ul>
    <?php
		for ($i=0; $i<count($sites); $i++) {
			echo '<li><a class="site_list" href="#" data-link="'.$sites[$i]['lat'].",".$sites[$i]['long'].','.$i.'">'.$sites[$i]['site_name'].'</a></li>';
		}
		
	?>
		<!-- <li><a class="site_list" href="ratnanagar.php" >Ratnanagar Hospital</a></li> -->
		<!-- <li><a class="site_list" href="whitelotus.php" >White Lotus</a></li> -->
    </ul>
    </div>
      <div id="map1">
		  <iframe id='iframe2' src="https://vrm.victronenergy.com/site/5087?mainTabId=live-feed" frameborder="0" style="overflow: hidden; height: 100%;
		  width: 100%; position: absolute;" height="100%" width="100%"></iframe>
	  </div>
    <div class="footer">
    </div>
    <div id="overlay">
    	<div class="info_content">
        	<div class="left">
            </div>
            <a class="right close" href="#">&nbsp;</a>
            <div class="clear"></div>
        </div>
    </div>
    <script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>
    <script type="text/javascript">
		$(document).ready(function() {
			$('#map').height(window.innerHeight-90);
			$('.location_list').height(window.innerHeight-90);
			$('.main_but').click(function() {
				if($('.location_list').css('left')!='0px') {
					$('.location_list').animate({'left':0},500);
				}
				else {
					$('.location_list').animate({'left':-250},500);
				}
			});
			$('.site_list').click(function() {
				var latlong=$(this).attr('data-link');
				var slatlong =latlong.split(",");
				var laLatLng = new google.maps.LatLng(slatlong[0], slatlong[1]);
        		map.setCenter(laLatLng);
				for (ii=0;ii<infowindow.length;ii++) {
					infowindow[ii].close();
				}
				infowindow[slatlong[2]].open(map,marker[slatlong[2]]);
				map.setZoom(17);
				$('.location_list').animate({'left':-250},500);
			});
			$('.info').click(function() {
				$('#overlay').show();
			});
			$('.close').click(function() {
				$('#overlay').hide();
			});
			$('.info_content').width(window.innerWidth*.7);
			$('.info_content').css("margin-top",(window.innerHeight-400)/2);
			var d = new Date()
			var n = d.getTimezoneOffset();
			var dd=150;
			if (d%10==0) {
				dd=400;
			}
			var seco=(Math.floor(d.getMinutes()%10)*60+d.getSeconds());
			if (seco>dd) dd+=600;
			seco=dd-(Math.floor(d.getMinutes()%10)*60+d.getSeconds());
			setTimeout("location.reload(true);",seco*1000);
		});
		$(window).resize(function() {
			$('#map').height(window.innerHeight-90);
			$('.location_list').height(window.innerHeight-90);
			$('.info_content').width(window.innerWidth*.7);
			$('.info_content').css("margin-top",(window.innerHeight-400)/2);
		});
	</script>
  </body>
</html>
