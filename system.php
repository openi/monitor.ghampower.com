<?php
	$table=$_GET['site'];
	date_default_timezone_set('UTC');
	include("connection.php");
	$query=mysql_query("SELECT site_name FROM site_info WHERE link='$table'") or die(mysql_error());
	while($rows=mysql_fetch_array($query)){
		$site_name=$rows["site_name"];
	}
	$query=mysql_query("SHOW COLUMNS FROM $table") or die(mysql_error());
	while($rows=mysql_fetch_array($query)){
		$columns[]=$rows["Field"];
	}
	$battery_voltage=0;
	$solar_current=0;
	$inverter_current=0;
	$solar_energy_total=0;
	$load_energy_total=0;
	$grid_energy_total=0;
	$datetime;
	$SoC=0;
	$query=mysql_query("SELECT * FROM $table ORDER BY TIMESTAMP(`Date`,`Time`)") or die(mysql_error());
	while($rows=mysql_fetch_array($query)){
		$datetime[]=date("U",strtotime($rows["Date"]." ".$rows["Time"]));
		$battery_voltage=$rows[$columns[3]];
		$solar_current=$rows[$columns[4]];
		$inverter_current=$rows[$columns[5]];
		$SoC=$rows[$columns[6]];
		for ($i=3; $i<count($columns);$i++) {
			if ($i==7) $solar_energy_total+=$rows[$columns[$i]];
			if ($i==8) $grid_energy_total+=$rows[$columns[$i]];
			if ($i==9) $load_energy_total+=$rows[$columns[$i]];
			$data[$i-3].=",[".(date("U",strtotime($rows["Date"]." ".$rows["Time"]))*1000).",".$rows[$columns[$i]]."]";
		}
	}
	for ($i=3; $i<count($columns);$i++) {
		$data[$i-3][0]="[";
		$data[$i-3][strlen($data[$i-3])]="]";
	}
	$json_weather=file_get_contents("http://api.openweathermap.org/data/2.5/weather?id=1283240");
	$weather=json_decode($json_weather,true);
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	<title>Gham Power Nepal | Monitor</title>
    	<link href="css/style.css" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>
        <script src="js/highstock.js"></script>
		<script src="js/modules/exporting.js"></script>
		<script type="text/javascript">
			$(function () {
				$('#chart').highcharts('StockChart', {
					chart: {
						type: 'spline',
						zoomType:'x'
					},
					legend: {
						enabled:true,
						align: 'left',
						layout: 'vertical',
						verticalAlign: 'middle',
						itemMarginBottom: 20,
						itemMarginTop: 20
					},
					rangeSelector : {
						selected : 0,
						inputEnabled: true,
						buttons: [{
							type: 'day',
							count: 1,
							text: '1d'
						}, {
							type: 'week',
							count: 1,
							text: '1w'
						}, {
							type: 'month',
							count: 1,
							text: '1m'
						}, {
							type: 'month',
							count: 6,
							text: '6m'
						}, {
							type: 'year',
							count: 1,
							text: '1y'
						}, {
							type: 'all',
							text: 'All'
						}]
					},
					xAxis: {
						ordinal: false
					},
					yAxis: [{
						title: {
							text: 'kWh'
						},
						opposite: false
					},
					{
						title: {
							text: '%'
						},
						min:0,
						max:100,
						opposite: true
					}],
					series : [{
						type:'column',
						name : '<?php echo $columns[7]; ?>',
						data : <?php echo $data[4]; ?>,
						yAxis:0,
						dataGrouping: {
							approximation: "sum",
							enabled: true,
							forced: true,
							groupPixelWidth: 30,
							units: [['minute',[10,30]],['hour',[1,12]],['day',[1,15]]]
				
						},
						tooltip: {
							valueDecimals: 2
						}
					},{
						type:'column',
						name: '<?php echo $columns[8]; ?>',
						data: <?php echo $data[5]; ?>,
						yAxis:0,
						dataGrouping: {
							approximation: "sum",
							enabled: true,
							forced: true,
							groupPixelWidth: 30,
							units: [['minute',[10,30]],['hour',[1,12]],['day',[1,15]]]
				
						},
						tooltip: {
							valueDecimals: 2
						}
					},{
						type:'column',
						name : '<?php echo $columns[9]; ?>',
						data : <?php echo $data[6]; ?>,
						yAxis:0,
						dataGrouping: {
							approximation: "sum",
							enabled: true,
							forced: true,
							groupPixelWidth: 30,
							units: [['minute',[10,30]],['hour',[1,12]],['day',[1,15]]]
				
						},
						tooltip: {
							valueDecimals: 2
						}
					},{
						name : '<?php echo $columns[6]; ?>',
						data : <?php echo $data[3]; ?>,
						yAxis:1,
						dataGrouping: {
							approximation: "average",
							enabled: true,
							forced: true,
							groupPixelWidth: 30,
							units: [['minute',[10,30]],['hour',[1,12]],['day',[1,15]]]
				
						},
						tooltip: {
							valueDecimals: 2
						}
					}
					]
				});
			});
		</script>
	</head>
	<body>
    	<nav class="menu">
            <a class="left back_but" href="<?php echo $base_url.'/'; ?>">&lArr; MAP</a>
            <div class="right logo">
            </div>
            <div class="clear"></div>
        </nav>
        <div id="middle">
        	<div class="content">
                <div class="topmiddlecontent">
                    <div class="side first left">
                    	<p>SITE:</p>
                        <h2><?php  echo $site_name; ?></h2>
                        <p>
                        <select id="dataselect">
                        	<option value="0">ALL</option>
                            <option value="1">TODAY</option>
                            <option value="2">YESTERDAY</option>
                            <option value="3">WEEK</option>
                            <option value="4">MONTH</option>
                            <option value="5">YEAR</option>
                        </select>
                        </p>
                    </div>
                    <div class="side left">
                    	<p>TOTAL SOLAR ENERGY</p>
                        <h1 id="solar"><?php echo round($solar_energy_total,3); ?> kWh</h1>
                        <span style="border-bottom:3px dotted #999999;"><img src="images/fuel.png" /> <span id="solar_fuel"><?php echo round($solar_energy_total*0.2953,3); ?></span> liters of fuel saved</span>
                        <br />
                        <span style="border-bottom:3px dotted #999999; margin-top:2px;"><img src="images/eco.png" /> <span id="solar_co2"><?php echo round($solar_energy_total*0.6985,3); ?></span> kilograms of CO<sub>2</sub> saved</span>
                    </div>
                    <div class="side left">
                    	<p>TOTAL ENERGY CONSUMED </p>
                        <h1 id="load"><?php echo round($load_energy_total,3); ?> kWh</h1>
                        <p>TOTAL ENERGY FROM GRID </p>
                        <h1 id="grid"><?php echo round($grid_energy_total,3); ?> kWh</h1>
                    </div>
                  	<div class="side left" style="width:10px;">
                    	
                    </div>
                    <div class="side last right">
                    	<p align="center">WEATHER</p>
                        <p align="center"><img src="http://openweathermap.org/img/w/<?php echo $weather['weather'][0]['icon']; ?>.png" />
                        <p align="center"><?php echo $weather['weather'][0]['main']; ?></p>
                    	<p align="center"><?php echo $weather['main']['temp']-273; ?> &deg;C</p>
                        <p align="center"><?php date_default_timezone_set('Asia/Kathmandu'); echo date("h:i A"); date_default_timezone_set('UTC'); ?></p>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="clear borderbottom"></div>
                <div id="chart"></div>
        	</div>
        </div>
		<div class="footer">
        </div>
        <script type="text/javascript">
			$(document).ready(function() {
				$('#dataselect').change(function() {
					var str=$(this).val();
					var xmlhttp = new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
							var restxt = xmlhttp.responseText;
							var energy =restxt.split(",");
							$('#solar').html(Math.round(energy[0]*1000)/1000+ " kWh");
							$('#solar_fuel').html(Math.round(energy[0]*0.2953*1000)/1000);
							$('#solar_co2').html(Math.round(energy[0]*0.6985*1000)/1000);
							$('#grid').html(Math.round(energy[1]*1000)/1000+ " kWh");
							$('#load').html(Math.round(energy[2]*1000)/1000+ " kWh");
						}
					}
					xmlhttp.open("GET", "getd.php?q=" + str +"&site=<?php echo $table; ?>", true);
					xmlhttp.send();
				});
            });
		</script>
	</body>
</html>
