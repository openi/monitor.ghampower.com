<?php
	include("connection.php");
	date_default_timezone_set('Asia/Kathmandu');
	$site=$_GET['site'];
	$val=$_GET['q'];
	$today=date("Y-m-d",strtotime("today"));
	$yesterday=date("Y-m-d",strtotime("yesterday"));
	$week=date("Y-m-d",strtotime("last sunday"));
	$month=date("Y-m-d",strtotime("first day of this month"));
	$year=date("Y")."-01-01";
	if ($val==0) {
		$query=mysql_query("SELECT sum(`kWh from Solar`) AS sum_solar, sum(`kWh from Grid`) AS sum_grid, sum(`kWh to Load`) AS sum_load FROM $site") or die (mysql_error());
		while($rows=mysql_fetch_array($query)){
			echo $rows["sum_solar"].",";
			echo $rows["sum_grid"].",";
			echo $rows["sum_load"];
		}
	}
	if ($val==1) {
		$query=mysql_query("SELECT sum(`kWh from Solar`) AS sum_solar, sum(`kWh from Grid`) AS sum_grid, sum(`kWh to Load`) AS sum_load FROM $site WHERE `Date`='$today'") or die (mysql_error());
		while($rows=mysql_fetch_array($query)){
			echo $rows["sum_solar"].",";
			echo $rows["sum_grid"].",";
			echo $rows["sum_load"];
		}
	}
	if ($val==2) {
		$query=mysql_query("SELECT sum(`kWh from Solar`) AS sum_solar, sum(`kWh from Grid`) AS sum_grid, sum(`kWh to Load`) AS sum_load FROM $site WHERE `Date`='$yesterday'") or die (mysql_error());
		while($rows=mysql_fetch_array($query)){
			echo $rows["sum_solar"].",";
			echo $rows["sum_grid"].",";
			echo $rows["sum_load"];
		}
	}
	if ($val==3) {
		$query=mysql_query("SELECT sum(`kWh from Solar`) AS sum_solar, sum(`kWh from Grid`) AS sum_grid, sum(`kWh to Load`) AS sum_load FROM $site WHERE `Date`>='$week'") or die (mysql_error());
		while($rows=mysql_fetch_array($query)){
			echo $rows["sum_solar"].",";
			echo $rows["sum_grid"].",";
			echo $rows["sum_load"];
		}
	}
	if ($val==4) {
		$query=mysql_query("SELECT sum(`kWh from Solar`) AS sum_solar, sum(`kWh from Grid`) AS sum_grid, sum(`kWh to Load`) AS sum_load FROM $site WHERE `Date`>='$month'") or die (mysql_error());
		while($rows=mysql_fetch_array($query)){
			echo $rows["sum_solar"].",";
			echo $rows["sum_grid"].",";
			echo $rows["sum_load"];
		}
	}
	if ($val==5) {
		$query=mysql_query("SELECT sum(`kWh from Solar`) AS sum_solar, sum(`kWh from Grid`) AS sum_grid, sum(`kWh to Load`) AS sum_load FROM $site WHERE `Date`>='$year'") or die (mysql_error());
		while($rows=mysql_fetch_array($query)){
			echo $rows["sum_solar"].",";
			echo $rows["sum_grid"].",";
			echo $rows["sum_load"];
		}
	}
?>
