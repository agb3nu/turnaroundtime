<?php
namespace TurnAroundTime;

use TurnAroundTime\RetrieveTimes;
use TurnAroundTime\ComputingBetweenTwoPeriods;


class FirstDayHours
{

	public function returnTotalHoursForFirstDay($createddate,$total_hours,$start_weekday)
	{		

		if ($start_weekday >= 1 and $start_weekday <= 5){			
			$total_hours = $this->returnTotalHoursWhenFirstDayIsAWeekDay($createddate,$total_hours);				

		}
		return $total_hours;		
	}

	public function  returnTotalHoursWhenFirstDayIsAWeekDay($createddate,$total_hours)
	{
		$retrieveTimes = new RetrieveTimes();
		$cBetweenTwoPeriod = new ComputingBetweenTwoPeriods();
		
		$actual_date_start_hr = $retrieveTimes->returnStartTime($createddate);
		$actual_date_end_hr = $retrieveTimes->returnEndTime($createddate); 
		$actual_date_midnight = $retrieveTimes->returnMidNightTime($createddate);
		$createddate_hr = date('H', $createddate);

		$createddate_actual_date = date("Y-m-d", $createddate);


		$fileHandle = fopen('../holidays.txt',"r");		

		

		$found=FALSE;

		
		while (($fields = fgetcsv($fileHandle, 0, ",")) !== FALSE)
		{
		 	$holiday_date = $fields[0];	

			if ($createddate_actual_date == $holiday_date)
			{				
				$found=True;
			}


			$total_hours = $total_hours;																
		}	
		
	

		if(!$found)
		{


			if (($actual_date_start_hr < $createddate_hr ) and ( $actual_date_end_hr >  $createddate_hr ))
			{

				//$cBetweenTwoPeriod = new ComputingBetweenTwoPeriods();			
				$hours11 = $cBetweenTwoPeriod->computeTotalHours( $actual_date_end_hr ,  $createddate_hr );						
				$total_hours = $total_hours - $hours11;
			}else if ($actual_date_start_hr > $createddate_hr ){

				$hours11 = $cBetweenTwoPeriod->computeTotalHours( $actual_date_end_hr ,  $actual_date_start_hr );					
				$total_hours = $total_hours - $hours11;
			}else if ($actual_date_end_hr > $createddate_hr )
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