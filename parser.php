<?php
	require_once('include/common.inc.php');
				
	const START_YEAR = 1980;
	const END_YEAR = 2014;
	
	for ( $year = START_YEAR; $year <= END_YEAR; $year++ )
	{
		$parser = new GsodParser(new SqliteStorage('weather.'.$year.'.sqlite'));
		
		echo "\nParse meteostations data for ".$year.": ";
		$parser->parseStationsDataFile(STATIONS_FILE);
		
		echo "\nParse meteo data for ".$year.": ";
		$parser->parseMeteoDataFiles(DATA_DIR . $year . '/');
	}
	echo "\nDone";
	
	

	
	
	
	
	
	