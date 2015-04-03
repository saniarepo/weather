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
		foreach($files as $file)
		{
			$rows = Fs::getRows($dir . $file);
			foreach($rows as $num => $row)
			{
				if($num == 0) continue;
				$items = Fs::getItems($row, ' ');
				$this->storage->saveMeteoData($items);
			}
		}
	}
	
	/**
	* парсинг файла isd-history.cvs и получение данных по станциям
	**/
	public function parseStationsDataFile($file)
	{
		$rows = Fs::getRows($file);
		foreach($rows as $num => $row)
		{
			if ( $num == 0 ) continue;
			$items = Fs::getItems($row, ',');
			$this->storage->saveStationData($items);
		}
	}

}