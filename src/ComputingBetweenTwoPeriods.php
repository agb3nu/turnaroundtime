<?php
namespace TurnAroundTime;


class ComputingBetweenTwoPeriods
{

	public function computeTotalHours( $datetime_1, $datetime_2 )
	{
		$hours1 =  intval($datetime_1) - intval($datetime_2);
		//$hours11 = intval($hours1/60/60);		
		return $hours1;		
	}


	
	public function hoursBetweenTwoTimePeriods($datetime_1, $datetime_2)
	{
		$hours1 =  $datetime_1 - $datetime_2;
		//$hours11 = intval($hours1/60/60);
		$hours11 = $hours1/3600;		
		$hours11 = abs($hours11);		
		return $hours11;		
	}


	public function daysBetweenTwoDayPeriods($previous_time,$today)
	{
		$hours1 =  $previous_time - $today;
		$hours11 = intval($hours1/3600/24);
		$hours11 = abs($hours11);		
		return $hours11;
	}

	
}


?>