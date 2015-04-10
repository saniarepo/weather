<?php
	
	/**
	* ������� �� �������� �� ���������� � ������� �� �������
	* @param float $t ����������� � �������� �� ����������
	* @return float ����������� � �������� �� �������
	**/
	function F2C($t)
	{
		return 5*($t - 32)/9;
	}
	
	/**
	* ������� �������� �� ����� � �/�
	* @param $node �������� � �����
	* @return �������� � �/�
	**/
	function node2ms($node)
	{
		return 0.514 * $node;
	}
	
	/**
	* ������� �������� �� �������� � ���������
	* @param $mbar �������� � ����������
	* @return �������� � ����������
	**/
	function mb2atm($mbar)
	{
		return 0.000986923 * $mbar;
	}
	
	/**
	* ���������� ���������� ����� ����� ������� �� �����
	* @param $llat1, $llng1, $llat2, $llng2 �����. ������ � ������� ���� �����
	* @return $dist ���������� �� ���� � ������ 
	**/
	function getRast($llat1, $llng1, $llat2, $llng2)
	{
		/**pi - ����� pi, rad - ������ ����� (�����)**/
		$rad = 6372795;
		$PI = 3.1416;
		/**� ��������**/
		$lat1 = $llat1*$PI/180;
		$lat2 = $llat2*$PI/180;
		$long1 = $llng1*$PI/180;
		$long2 = $llng2*$PI/180;

		/**�������� � ������ ����� � ������� ������**/
		$cl1 = cos($lat1);
		$cl2 = cos($lat2);
		$sl1 = sin($lat1);
		$sl2 = sin($lat2);
		$delta = $long2 - $long1;
		$cdelta = cos($delta);
		$sdelta = sin($delta);

		/**���������� ����� �������� �����**/
		$y = sqrt(($cl2*$sdelta)*($cl2*$sdelta)+($cl1*$sl2-$sl1*$cl2*$cdelta)*($cl1*$sl2-$sl1*$cl2*$cdelta));
		$x = $sl1*$sl2+$cl1*$cl2*$cdelta;
		$ad = atan2($y,$x);
		$dist = $ad*$rad;
		return $dist;
	}
	
	
	
	