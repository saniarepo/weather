<?php

class GsodParser implements IParser
{	
	private $storage;
	
	private $model = array(
		'meteo' => array(
							'stn'  => 0,
							'wban' => 1,
							'thedate' => 2,
							'temperature' => 3,
							'pressure' => 9,
							'wind' => 13
							),
		'station' => array(
							'stn'  => 0,
							'wban' => 1,
							'lat' => 6,
							'lng' => 7,
							'elevation' => 8,
							'datebegin' => 9,
							'dateend' => 10	
							)
	);
	
	public function __construct(IStorage $storage)
	{
		$this->storage = $storage;
		$this->storage->setModel($this->model);
	}
	
	/**
	* парсинг всех файлов в каталоге и получение метеоданных
	**/
	public function parseMeteoDataFiles($dir)
	{
		$files = Fs::getFiles($dir);
		$full = count($files);
		$count  = 0;
		$logger = new Logger();
		foreach($files as $file)
		{
			$rows = Fs::getRows($dir . $file);
			foreach($rows as $num => $row)
			{
				if($num == 0) continue;
				$items = Fs::getItems($row, ' ');
				$this->storage->save($items, 'meteo');
			}
			$this->storage->flush('meteo');
			$count++;
			if ( $count % 10 == 0 )
				$logger->progress($count, $full);
		}
	}
	
	/**
	* парсинг файла isd-history.cvs и получение данных по станциям
	**/
	public function parseStationsDataFile($file)
	{
		$rows = Fs::getRows($file);
		$full = count($rows);
		$count  = 0;
		$logger = new Logger();
		foreach($rows as $num => $row)
		{
			if ( $num == 0 ) continue;
			$items = Fs::getItems($row, ',');
			if ($this->storage->save($items, 'station'))
			{
				$count++;
				if ( $count % 100 == 0 )
					$logger->progress($count, $full);
			}
		}
		$this->storage->flush('station');
	}

}