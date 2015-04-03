<?php
	require_once('include/common.inc.php');
				
	$parser = new Parser(new DbSqlite('weather.sqlite'));
	
	//echo "\nParse meteostations data: ";
	//$parser->parseStationsDataFile(STATIONS_FILE);
	
	echo "\nParse meteodata: ";
	$parser->parseMeteoDataFiles(DATA_DIR);
	
	echo "\nDone";
	
	

	
	
	
	
	
	