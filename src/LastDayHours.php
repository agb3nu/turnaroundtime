<?php
namespace TurnAroundTime;

class LastDayHours
{

	public function returnTotalHoursForLastDay($resolutiondate,$total_hours,$end_weekday)
	{
		$cBetweenTwoPeriod = new ComputingBetweenTwoPeriods();
		$retrieveTimes = new RetrieveTimes();
						
		if( $end_weekday == 7 or $end_weekday == 6 )
		{
			$actual_date_morning = $retrieveTimes->returnMorningTime($resolutiondate);
			$hours11 = $cBetweenTwoPeriod->computeTotalHours( $end_weekday ,  $actual_date_morning );		
		}else
		{
			$total_hours = $this->returnTotalHoursWhenLastDayIsAWeekDay($resolutiondate,$total_hours);		
		} 
		
		return $total_hours;				
	}

	public function returnTotalHoursWhenLastDayIsAWeekDay($resolutiondate,$total_hours)
	{
		$retrieveTimes = new RetrieveTimes();
		$cBetweenTwoPeriod = new ComputingBetweenTwoPeriods();
		$workDayHours = new WorkDayHours();
			
		$actual_date_start_hr =  $retrieveTimes->returnStartTime($resolutiondate);
		$actual_date_end_hr = $retrieveTimes->returnEndTime($resolutiondate);		
		$actual_date_midnight = $retrieveTimes->returnMidNightTime($resolutiondate);
		$resolutiondate_hr = date('H', $resolutiondate);	

		$end_weekday = date('N', $resolutiondate); // 1-7				
		$day_index = $retrieveTimes->getNextDayIndex($end_weekday);

		$resolutiondate_actual_date = date("Y-m-d", $resolutiondate);
				
		$fileHandle = fopen('../holidays.txt',"r");
		$found=FALSE;
		while (($fields = fgetcsv($fileHandle, 0, ",")) !== FALSE)
		{
		 	$holiday_date = $fields[0];	

			if ($resolutiondate_actual_date == $holiday_date)
			{
				$found=True;
			}
			
			$total_hours = $total_hours;																
		}	
			
			if(!$found)
			{

				if(  ($actual_date_start_hr < $resolutiondate_hr ) and ( $actual_date_end_hr >  $resolutiondate_hr ) )
				{
					$hours11 = $cBetweenTwoPeriod->computeTotalHours( $resolutiondate_hr ,  $actual_date_start_hr );					
					$total_hours = $total_hours - $hours11;
					
					// die(var_dump($total_hours));
						 				
				}else if (actual_date_end_hr < $resolutiondate_hr )
				{
					$hours11 = $cBetweenTwoPeriod->computeTotalHours( $actual_date_end_hr ,  $actual_date_start_hr );				
					$total_hours = $total_hours - $hours11;		
				}else if ($actual_date_start_hr > $resolutiondate_hr )
				{
	
					$total_hours = $total_hours;						
				}else{
							
					$total_hours = $total_hours;					
				}			
				
			}
		
		
		return $total_hours;
			
	}


	
}



?>