<?php
	interface IStorage
	{
		public function saveMeteoData(array $record);
		public function saveStationData(array $record);
		
	}