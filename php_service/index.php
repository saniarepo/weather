<?php
		
	/**
	* прототип сервиса погодных данных (по одной точке)
	* принимает запрос вида http://site1.loc:8080/weather/?date=20140116&lat=56.4&lng=48.16
	**/
	require_once('include/common.inc.php');

	$date = isset( $_GET['date'] )? $_GET['date'] : "20140101";
	$lat = isset( $_GET['lat'] )? $_GET['lat'] : "0.0";
	$lng = isset( $_GET['lng'] )? $_GET['lng'] : "0.0";
	
	$year = substr($date, 0, 4);
	settype($lat,'float');
	settype($lng,'float');
	settype($date, 'int');
	
	$db = new SQLite3(DB_DIR .'stations.sqlite');
	
	$stn = null;
	$wban = null;
	$minRast = BIG_NUM;
	$foundLat = null;
	$foundLng = null;
	$sql = 'SELECT * FROM station';
	$result = $db->query($sql);
	
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
		$rast = getRast($lat, $lng, $currLat, $currLng);
		
		if ( $rast < $minRast )
		{
			$minRast = $rast;
			$stn = $station['stn'];
			$wban = $station['wban'];
			$foundLat = $currLat;
			$foundLng = $currLng;
		}
	}
	unset($result);
	//echo "Nearest Station: rast=$minRast m; stn=$stn; wban=$wban; lat=$foundLat; lng=$foundLng<br><br>"; 
	
	if ( $foundLat == null || $foundLng == null )
	{
		$response = '{result:fail}';
		echo $response;
		exit();
	}
	
	$db = new SQLite3(DB_DIR .'weather.'.$year.'.sqlite');
	$sql = "SELECT * FROM meteo WHERE wban=$wban AND stn=$stn AND thedate=$date";
	$result = $db->query($sql);
		
	$row = $result->fetchArray(1);	
	$temperature = F2C($row['temperature']);
	$pressure = mb2atm($row['pressure']);
	$wind = node2ms($row['wind']);
	unset($db);
	unset($result);
	
	$response = '{';
	$response .= 'result:ok,';
	$response .= 'temperature:' . $temperature . ',';
	$response .= 'pressure:' . $pressure . ',';
	$response .= 'wind:' . $wind .',';
	$response .= 'rast:' . $minRast . ',';
	$response .= 'stn:' .$stn . ',';
	$response .= 'wban:' .$wban . ',';
	$response .= 'found_lat:' .$foundLat . ',';
	$response .= 'found_lng:' .$foundLng . '}';
	
	echo $response;