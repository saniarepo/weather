<?php
	require_once('include/common.inc.php');
		
	
		
	//parseMeteoData();
	$parser = new Parser(new DbSqlite('weather.sqlite'));
	$parser->parseStationsDataFile(STATIONS_FILE);
	
	

	
	
	
	
	
	