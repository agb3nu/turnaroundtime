<?php
namespace TurnAroundTime;

class SameDayHours
{
		
	public function returnTotalHoursWhenOnSameDaysOnTheWeekday(
		$actual_date_start_hr,$createddate,
		$actual_date_end_hr, 
		$resolutiondate,$total_hours
	)
	{
		//die(var_dump($createddate));
		
		$createddate_hr = intval(date('H', $createddate));
		$resolutiondate_hr = intval(date('H', $resolutiondate));

		$computingBtnTwoPeriods = new ComputingBetweenTwoPeriods();
		
		//die("actual: ".$actual_date_start_hr." created ".$createddate_hr." actual end hr: ".$actual_date_end_hr." resolution date ".$resolutiondate_hr);
				
		if( $actual_date_start_hr > $createddate_hr and  $actual_date_start_hr > $resolutiondate_hr )
		{
			//die("aaaaa");															
			$hours11 = $computingBtnTwoPeriods->computeTotalHours($resolutiondate_hr, $createddate_hr );	
			$total_hours = $total_hours;			
		}

	
		if(($actual_date_start_hr > $createddate_hr ) and  ($actual_date_start_hr < $resolutiondate_hr ) and ($actual_date_end_hr > $resolutiondate_hr )) 
		{
			//die("tttt");												
			$hours11 = $computingBtnTwoPeriods->computeTotalHours( $resolutiondate_hr , $actual_date_start_hr );
			$total_hours =  $total_hours - $hours11;				
		}

	
	
		if(($actual_date_start_hr > $createddate_hr ) and  ($actual_date_start_hr < $resolutiondate_hr) and ( $resolutiondate_hr > $actual_date_end_hr )) 
		{
			//die("qqqqq");									
			$hours11 = $computingBtnTwoPeriods->computeTotalHours( $resolutiondate_hr , $actual_date_end_hr );			
			$hours22 = $computingBtnTwoPeriods->computeTotalHours( $actual_date_start_hr , $createddate_hr );			
			$total_hours = $hours22 + $hours11;				
		}
	
	
		if(($actual_date_start_hr < $createddate_hr ) and  ($actual_date_start_hr < $resolutiondate_hr) and ( $resolutiondate_hr > $actual_date_end_hr )) 
		{											
			$total_hours = $computingBtnTwoPeriods->computeTotalHours( $resolutiondate_hr , $actual_date_end_hr );								
		}

	
		if(($actual_date_end_hr < $createddate_hr  ) and  ($actual_date_end_hr < $resolutiondate_hr )) 
		{
			$total_hours = $computingBtnTwoPeriods->computeTotalHours( $resolutiondate_hr , $actual_date_end_hr );				
		}	
	
		if(($actual_date_start_hr <= $createddate_hr ) and  ($actual_date_end_hr >= $resolutiondate_hr )) 
		{
			$hours11 = $computingBtnTwoPeriods->computeTotalHours( $createddate_hr ,  $resolutiondate_hr );										
			//$hours11 = $computingBtnTwoPeriods->computeTotalHours( $actual_date_start_hr ,  $createddate_hr );													
			$total_hours = $total_hours - $hours11;				
			//die("zzzz".$total_hours."  xxx ".$hours11);			
		}

		//die("zzzz".$total_hours."  ");			

		return $total_hours;
	}

	
	
}

?>