<?php
namespace TurnAroundTime;

use Inc\config\Connect;

use TurnAroundTime\ComputingBetweenTwoPeriods;
use TurnAroundTime\FirstDayHours;
use TurnAroundTime\LastDayHours;
use TurnAroundTime\SameDayHours;
use TurnAroundTime\WorkDayHours;


class TurnAroundTime
{
	
	public static function get_day_hour_minute_and_second($dff)
	{
		$dff_sec = intval($dff)%60;
		$dff_sec = $dff_sec <= 9 ? "0".$dff_sec : $dff_sec;
		
		$dff_min = 	intval($dff/60)%60;	
		$dff_min = $dff_min <= 9 ? "0".$dff_min : $dff_min;
				
		if($dff<=3600){
			$dff_hour = 0;
		}else{
			$dff_hour = intval($dff/3600)%24;												
		}		
		$dff_hour = $dff_hour <= 9 ? "0".$dff_hour : $dff_hour;
		
		
		//$period = "$dff_hour HOUR(S) $dff_min MINUTE(S) $dff_sec SECOND(S) ";	
		$period = "$dff_hour : $dff_min  : $dff_sec  | ";			
		return $period;		
	}

	public function subtractNonWorkingHoursFromTimestamp($nonWorkingHours,$difference_timestamp)
	{
		$nonWorkingHours = intval($nonWorkingHours);

		$dff = strtotime('-'.$nonWorkingHours.' hours', $difference_timestamp);
		return $dff;		
	}
	
	public function returnNonWorkHours($previous_time,$today)
	{
		$cBtnTwoPeriod = new ComputingBetweenTwoPeriods();		
		$firstDayHours = new FirstDayHours();		
		$lastDayHours = new LastDayHours();				
		$retrieveTimes = new RetrieveTimes();
		$sameDayHrs = new SameDayHours();
		$workDayHours = new WorkDayHours();
						
		##die(var_dump($previous_time));


		$initial_work_hours = $cBtnTwoPeriod->hoursBetweenTwoTimePeriods($previous_time,$today);
		$total_hours = $initial_work_hours;
					
		$start_weekday = intval(date('N', $previous_time)); // 1-7		
		$end_weekday = intval(date('N', $today)); // 1-7		

		$days_total = $cBtnTwoPeriod->daysBetweenTwoDayPeriods($previous_time,$today);


		##COMPUTING NON WORK WORK HOURS		
		if ($days_total > 1)
		{			

			$total_hours = $firstDayHours->returnTotalHoursForFirstDay($previous_time,$total_hours,$start_weekday);

		

			$total_hours = $lastDayHours->returnTotalHoursForLastDay($today,$total_hours,$end_weekday);				

													
			$days_less_one = $days_total-1;
			$current_day_count = 1;
			$day_index = $retrieveTimes->getNextDayIndex($start_weekday);
			$next_day_date = $retrieveTimes->returnNextDayDate($previous_time);
			

			while ($current_day_count <=  $days_less_one)
			{
				$total_hours = $workDayHours->getWorkDayBusinessHoursTotal($day_index,$total_hours,$next_day_date);
				$day_index = $retrieveTimes->getNextDayIndex($day_index);
				
				$next_day_date = $retrieveTimes->returnNextDayDate(strtotime($next_day_date));			
				$current_day_count=$current_day_count+1;			
			}
														
		}else{

					
			$actual_date_start_hr = $retrieveTimes->returnStartTime($previous_time);
			$actual_date_end_hr = $retrieveTimes->returnEndTime($previous_time);	

			
			if( $start_weekday == $end_weekday and ( $start_weekday >= 1 and $start_weekday <= 5) ) 
			{

				//die(var_dump($end_weekday));
			
				$total_hours = $sameDayHrs->returnTotalHoursWhenOnSameDaysOnTheWeekday(
					$actual_date_start_hr,$previous_time,$actual_date_end_hr, $today,$total_hours
				);

				//die(var_dump($total_hours));
				
			}else if(  ( $start_weekday == $end_weekday ) and ( $start_weekday == 7 or $start_weekday == 6) )
			{
				$total_hours = $total_hours; 			
				
			}else
			{				


				$total_hours = $firstDayHours->returnTotalHoursForFirstDay($previous_time,$total_hours,$start_weekday);		

				$total_hours = $lastDayHours->returnTotalHoursForLastDay($today,$total_hours,$end_weekday);						
			}
			
		}	
		
			
		return $total_hours;

	}
	
	

}



