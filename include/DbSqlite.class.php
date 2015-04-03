<?php

class DbSqlite implements IStorage
{
	private $db;
	private $file;
	private $meteoRecord = array(
		'stn'  => 0,
		'wban' => 1,
		'date' => 2,
		'temperature' => 3,
		'pressure' => 9,
		'wind' => 13
	
	);
	
	private $stationRecord = array(
		'stn'  => 0,
		'wban' => 1,
		'lat' => 6,
		'lng' => 7,
		'elevation' => 8,
		'begin' => 9,
		'end' => 10	
	);
	
	public function __construct($file)
	{
		$this->file = $file;
		$this->db = new SQLite3($file);
		$sql = 'CREATE TABLE IF NOT EXISTS meteo(';
		$count = 0; $len = count($this->meteoRecord);
		foreach( $this->meteoRecord as $key=>$value)
		{
			$sql .= $key;
			$count++;
			if ($count < $len) $sql .= ',';
		}
		$this->db->exec($sql);
		
		$sql = 'CREATE TABLE IF NOT EXISTS stations(';
		$count = 0; $len = count($this->stationRecord);
		foreach( $this->stationRecord as $key=>$value)
		{
			$sql .= $key;
			$count++;
			if ($count < $len) $sql .= ',';
		}		
		$sql .= ')';
		$this->db->exec($sql);
	}	
	
	public function saveMeteoData(array $record)
	{
		print_r($record);
	}
	public function saveStationData(array $record)
	{
		
	}

}