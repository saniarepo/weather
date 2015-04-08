<?php
	require_once('include/common.inc.php');
				
	const START_YEAR = 2004;
	const END_YEAR = 2014;
	
	for ( $year = START_YEAR; $year <= END_YEAR; $year++ )
	{
		$storage = new SqliteStorage('weather.'.$year.'.sqlite');
		$parser = new GsodParser($storage);
		
		echo "\nParse meteostations data for ".$year.": ";
		$parser->parseStationsDataFile(STATIONS_FILE);
		
		echo "\nParse meteo data for ".$year.": ";
		$parser->parseMeteoDataFiles(DATA_DIR . $year . '/');
		
		unset($storage);
		unset($parser);
	}
	echo "\nDone";
	
	

	
	
	
	
	
	