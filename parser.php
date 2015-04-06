<?php
	require_once('include/common.inc.php');
				
	$year = 2014;
	$parser = new GsodParser(new SqliteStorage('weather.'.$year.'.sqlite'));
	
	echo "\nParse meteostations data: ";
	$parser->parseStationsDataFile(STATIONS_FILE);
	
	echo "\nParse meteodata: ";
	$parser->parseMeteoDataFiles(DATA_DIR . $year . '/');
	
	echo "\nDone";
	
	

	
	
	
	
	
	