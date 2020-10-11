<?php
namespace TurnAroundTime;

class RetrieveTimes
{

	CONST START_HOUR="08";
	CONST END_HOUR="17";
	CONST MID_NIGHT="23";
	CONST MORNING_TIME="0";

	public function  getNextDayIndex($day_index)
	{
		if ($day_index ==7)
		{
			$next_index = 0;		
		}else
		{
			$next_index = $day_index + 1; 		
		}	
	
		return $next_index;		
	}


	public function returnStringsAsTimesAndItsDifference($today_str,$previous_time_str)
	{
		$previous_time  = strtotime($previous_time_str);
		$today = strtotime($today_str);

		$dff1 = $today - $previous_time;

		return [$previous_time,$today,$dff1];
	}

	public function returnNextDayDate($createddate_timestamp)
	{
		$createddate = date("Y-m-d", $createddate_timestamp);
		
		$next_day_timestamp = strtotime('+1 day', strtotime($createddate));
		$next_day = date("Y-m-d", $next_day_timestamp);
		//die($next_day);

		return $next_day;	
	}


	
	public function returnStartTime($createddate)
	{
		$start_hour = intval(RetrieveTimes::START_HOUR);				
		return $start_hour;			
	}

	public function returnEndTime($createddate)
	{
		$end_hour = intval(RetrieveTimes::END_HOUR);				
		return $end_hour;			
	}

	public function returnMidNightTime($createddate)
	{
		$mid_night = RetrieveTimes::MID_NIGHT;				
		return $mid_night;			
	}

	public function returnMorningTime($createddate)
	{
		$morning_time = RetrieveTimes::MORNING_TIME;				
		return $morning_time;			
	}
		
}


?>