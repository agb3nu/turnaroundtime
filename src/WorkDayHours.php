<?php
namespace TurnAroundTime;

class WorkDayHours
{
	
	public function getWorkDayBusinessHoursTotal($day_index,$total_hours,$day_date)
	{
		if ($day_index==7 or $day_index==6)
		{
			$total_hours = $total_hours;			
		}
		else
		{
			$fileHandle = fopen('../holidays.txt',"r");//change to readt from db
			$found=FALSE;
			while (($fields = fgetcsv($fileHandle, 0, ",")) !== FALSE)
			{
				//$row++;
			 	$holiday_date = $fields[0];	

				if ($day_date == $holiday_date)
				{
					$found=True;
					$day_hours = 0;
				}
				
				if(!$found){
					$day_hours = 9;					
				}

				$total_hours = $total_hours - $day_hours;																
			}				
			

		}		

	
		return $total_hours;
		
	}
	
	
}


?>