<?php
	include("connection.php");
	$ftp_files="/home/em-ftp-1/";
	if ($handle = opendir($ftp_files)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				$ext=split('[.]',$file);
				if (($ext[1]='txt' || $ext[1]='TXT') && count($ext)<=2) {
					$table=split('_',$ext[0]);
					$filecont=file_get_contents($ftp_files.$file) or exit("Unable to open file!");
					$content=split("[\n]",$filecont);
					$count=count($content);
					//echo $count;
					for ($j=0;$j<$count;$j++) {
						$rows=split("[,]",$content[$j]);
						$cont="' ','".$rows[0]."'";
						for ($k = 1; $k < count($rows); $k++) {
							$cont.=", '".$rows[$k]."'";
						}
						mysql_query("INSERT INTO $table[0] VALUES (".$cont.")");
						echo "<br />";
					}
				unlink($ftp_files.$file);
				}
			}
		}
	}
?>
