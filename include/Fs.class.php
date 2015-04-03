<?php

class Fs
{
	/**
	* �������� ������ ������ � ��������
	* @param string $dir ��� ��������
	* @return array ������ ������
	**/
	public static function getFiles($dir)
	{
		$result = array();
		$files = scandir($dir);
		//print_r($files);
		foreach( $files as $file )
		{
			if ($file == '.' || $file == '..') continue;
			$result[] = $file;
		}
		return $result;
	}
	
	/**
	* �������� ������ �� �����
	* @param string $filename ��� �����
	* @return array ������ ����� � ����� 
	**/
	public static function getRows($filename)
	{
		$rows = file($filename);
		$result = array();
		foreach($rows as $row)
		{
			if ($row == '') continue;
			$result[] = $row;
		}
		return $result;
	}
	
	/**
	* �������� �������� ������ � ���� �������
	* @param string $row ������
	* @param string $delimiter ����������� ����� � ������
	* @return array ������ ��������� � ������ 
	**/
	public static function getItems($row, $delimiter)
	{
		$result = array();
		$items = explode($delimiter, $row);
		foreach($items as $item)
		{
			if ($item == ' ' || $item == '') continue;
			$result[] = $item;
		}
		return $result;
	}


}