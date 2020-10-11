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

	public static function get_turnaround_in_rows($trackid)
	{   
		global $hesk_settings, $hesklang, $hesk_db_link;	
		$html_row="";
		$total_dff=0;
		$query1="
			select acct_ticket_trail.id,action,data_item,time_stamp ,timestamp_in_seconds,acct_users.name as actor 
			from acct_ticket_trail inner join acct_users on acct_users.id = acct_ticket_trail.actor_id
		where trackid = '".$trackid."' order by id asc";	
		$res = hesk_dbQuery($query1);
		$x=0;
		$previous_time  = time();
		while ($row = hesk_dbFetchAssoc($res))
		{

			$x > 0 ? $cal_period = 1 : $cal_period = 0;
			if($cal_period)
			{
				$today = strtotime($row['time_stamp']);
				//place into method-start				
				//$dff = ($today - $previous_time); //WORKING AND CURRENTLY IN USE	
				$dff = intval($row['timestamp_in_seconds']);
											
				//$nonWorkingHrs = $this->returnNonWorkHours($previous_time,$today);							
				//$dff = subtractNonWorkingHoursFromTimestamp($nonWorkingHrs,$dff1);							
				$period = self::get_day_hour_minute_and_second($dff);
				
				//place into method-end					
				$to = " to ";		
				$total_dff = $dff + $total_dff;
			}else{
				$period = "";
				$to = "";						
			}

			$action_item = $row['data_item'];			
			if($action_item)
			{
				$to = " to ";						
			}			
			
			switch ($row['action']) 
			{
				case "PENDED-ON":
				case  "REQUEST-TYPE":				
				case "REQUEST-TYPE-DETAILS":
					$to=" set as ";										
					break;

				case "CHANGED-LOCATION":
					$to=" ";										
					break;
				
				default:
					$to = $to;
					break;
			}
			
			
			$date_object = date_create($row['time_stamp']);
			$reformated_date = date_format($date_object, "Y-m-d H:i:s");				
			$html_row =  $html_row."<li class='smaller'>".$reformated_date." | ".$period." ".$row['action']."$to".$action_item." by ".strtoupper($row['actor']) ."</li>";				
			$x++;
			$previous_time = strtotime($row['time_stamp']);
		}
		 $total_period = self::get_day_hour_minute_and_second($total_dff);
		
		 $html_total_row = "<li class='smaller'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>TOTAL</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;| ".$total_period." </li>";
		 
		 echo $html_row.$html_total_row;		 
	}
	
	
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



