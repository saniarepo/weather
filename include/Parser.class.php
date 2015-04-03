<?php

class Parser implements IParser
{	
	private $storage;
	
	public function __construct(IStorage $storage)
	{
		$this->storage = $storage;
	}
	
	/**
	* ������� ���� ������ � �������� � ��������� �����������
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
	* ������� ����� isd-history.cvs � ��������� ������ �� ��������
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