<?php

class Parser implements IParser
{	
	private $storage;
	
	public function __construct(IStorage $storage)
	{
		$this->storage = $storage;
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
				$this->storage->saveMeteoData($items);
			}
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
			if ($this->storage->saveStationData($items))
			{
				$count++;
				if ( $count % 100 == 0 )
					$logger->progress($count, $full);
			}
		}
	}

}