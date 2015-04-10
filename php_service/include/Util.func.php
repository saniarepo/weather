<?php
	
	/**
	* перевод из градусов по Фаренгейту в градусы по Цельсию
	* @param float $t температура в градусах по Фаренгейту
	* @return float температура в градусах по Цельсию
	**/
	function F2C($t)
	{
		return 5*($t - 32)/9;
	}
	
	/**
	* перевод скорости из узлов в м/с
	* @param $node скорость в узлах
	* @return скорость в м/с
	**/
	function node2ms($node)
	{
		return 0.514 * $node;
	}
	
	/**
	* перевод давления из миллибар в атмосферы
	* @param $mbar давление в миллибарах
	* @return давление в атмосферах
	**/
	function mb2atm($mbar)
	{
		return 0.000986923 * $mbar;
	}
	
	/**
	* вычисление расстояния между двумя точками на сфере
	* @param $llat1, $llng1, $llat2, $llng2 соотв. широта и долгота двух точек
	* @return $dist расстояние по дуге в метрах 
	**/
	function getRast($llat1, $llng1, $llat2, $llng2)
	{
		/**pi - число pi, rad - радиус сферы (Земли)**/
		$rad = 6372795;
		$PI = 3.1416;
		/**в радианах**/
		$lat1 = $llat1*$PI/180;
		$lat2 = $llat2*$PI/180;
		$long1 = $llng1*$PI/180;
		$long2 = $llng2*$PI/180;

		/**косинусы и синусы широт и разницы долгот**/
		$cl1 = cos($lat1);
		$cl2 = cos($lat2);
		$sl1 = sin($lat1);
		$sl2 = sin($lat2);
		$delta = $long2 - $long1;
		$cdelta = cos($delta);
		$sdelta = sin($delta);

		/**вычисления длины большого круга**/
		$y = sqrt(($cl2*$sdelta)*($cl2*$sdelta)+($cl1*$sl2-$sl1*$cl2*$cdelta)*($cl1*$sl2-$sl1*$cl2*$cdelta));
		$x = $sl1*$sl2+$cl1*$cl2*$cdelta;
		$ad = atan2($y,$x);
		$dist = $ad*$rad;
		return $dist;
	}
	
	
	
	