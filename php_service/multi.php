<?php
		
	/**
	* прототип сервиса погодных данных (по набору точек)
	* принимает запрос вида http://site1.loc:8080/weather/multi.php?date=20140116&dots=56.12,47.67|58.17,49.11|54.13,48.32
	**/
	require_once('include/common.inc.php');

	$date = isset( $_GET['date'] )? $_GET['date'] : "20140101";
	$dots = isset( $_GET['dots'] )? $_GET['dots'] : "";
	
	$points = array();
	$count = 0;
	foreach(explode('|', $dots) as $dot)
	{
		$coords = explode(',', $dot);
		$points[$count]['lat'] = $coords[0];
		$points[$count]['lng'] = $coords[1];
		settype($points[$count]['lat'], 'float');
		settype($points[$count]['lng'], 'float');
		$count++;
	}
	
	$year = substr($date, 0, 4);
	settype($date, 'int');
	
	$db = new SQLite3(DB_DIR .'stations.sqlite');
	
	$stnArr = array();
	$wbanArr = array();
	$minRast = array();
	$foundLat = array();
	$foundLng = array();
	$sql = 'SELECT * FROM station';
	$result = $db->query($sql);
	$stnCoord = array();
	unset($db);
	//var_dump($result->fetchArray(1));
	
	while($station = $result->fetchArray(1))
	{		
		
		$datebegin = $station['datebegin'];
		$dateend = $station['dateend'];
		settype($datebegin, 'int');
		settype($dateend, 'int');		
		if ( $date < $datebegin || $date > $dateend ) continue;
		$currLat = $station['lat'];
		$currLng = $station['lng'];
		settype($currLat, 'float');
		settype($currLng, 'float');
		$stn = $station['stn'];
		$wban = $station['wban'];
		$stnCoord[$stn.'-'.$wban]['lat'] = $currLat;
		$stnCoord[$stn.'-'.$wban]['lng'] = $currLng;
		$count = 0;
		$rast = array();
		foreach($points as $point)
		{
			$rast[$count] = getRast($point['lat'], $point['lng'], $currLat, $currLng);
			if ( !isset($minRast[$count]) || $rast[$count] < $minRast[$count] )
			{
				$minRast[$count] = $rast[$count];
				$stnArr[$count] = $station['stn'];
				$wbanArr[$count] = $station['wban'];
				$foundLat[$count] = $currLat;
				$foundLng[$count] = $currLng;
			}
			$count++;
		}
		unset($rast);
	}
	unset($result);
	//echo "Nearest Station: rast=$minRast m; stn=$stn; wban=$wban; lat=$foundLat; lng=$foundLng<br><br>"; 
	
	for($i = 0; $i < $count; $i++)
	{
		if(!isset($foundLat[$i]) || !isset($foundLng[$i]))
		{
			$response = '{result:fail}';
			echo $response;
			exit();
		}		
	}
	
	$db = new SQLite3(DB_DIR .'weather.'.$year.'.sqlite');
	$sql = "SELECT * FROM meteo WHERE thedate=$date AND (";
	for($i = 0; $i < $count; $i++)
	{
		$sql .= "(wban=$wbanArr[$i] AND stn=$stnArr[$i])";
		if ($i < $count -1) $sql .= " OR ";
	}
	$sql .= ")";
	echo $sql."<br/><hr/>";//exit();
	$result = $db->query($sql);		
	$response = '{';
	$response .= 'result:ok,data:[';
	while($row = $result->fetchArray(1)	)
	{
		$temperature = F2C($row['temperature']);
		$pressure = mb2atm($row['pressure']);
		$wind = node2ms($row['wind']);
		$stn = $row['stn'];
		$wban = $row['wban'];
		$lat = $stnCoord[$stn.'-'.$wban]['lat'];
		$lng = $stnCoord[$stn.'-'.$wban]['lng'];
		$response .= '{temperature:' . $temperature . ',';
		$response .= 'pressure:' . $pressure . ',';
		$response .= 'wind:' . $wind .',';
		$response .= 'stn:' .$stn . ',';
		$response .= 'wban:' .$wban . ',';
		$response .= 'found_lat:' .$lat . ',';
		$response .= 'found_lng:' .$lng . '},';
	}
	$response{strlen($response)-1} = ']';
	$response .='}';
	unset($db);
	unset($result);
	echo $response;
	
	