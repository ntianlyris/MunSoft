<?php
date_default_timezone_set("Asia/Taipei"); 

function GetDateDiff($curr_date, $given_date){
	$diff = $curr_date->diff($given_date);
	return $diff->y;
}

function OutputDate($input_date){
	if($date = DateTime::createFromFormat("Y-m-d", $input_date)){
		return $date->format("F d, Y");	
	}
	else{
		return false;
	}
}

function OutputShortDate($input_date){
	if($date = DateTime::createFromFormat("Y-m-d", $input_date)){
		return $date->format("m/d/Y");	
	}
	else{
		return false;
	}
}

function MonthOfYear($month, $year){
	$month_of_year = $year."-".$month;
	$lastday = date('d', mktime(0, 0, 0, $month + 1, 0, $year));
	if($date = new DateTime($month_of_year."-".$lastday)){
		return $date->format('Y-m-d');
	}
	else{
		return false;
	}
}

function DateToday(){
	$today = new DateTime();
	return $today->format('Y-m-d');
}

function CurrentYear(){
	$curr_yr = new DateTime();
	return $curr_yr->format('Y');
}

function CurrentMonth(){
	$curr_mm = new DateTime();
	return $curr_mm->format('m');
}

function GetMonth($str_month){
	$month = new DateTime($str_month);
	return $month->format('m');
}

function GetLastMonth($str_curr_month){
	$curr_mo = new DateTime($str_curr_month);

	$curr_mo->modify('last month');
	return $curr_mo->format('m');
}

function monthNumToName($monthNum){
  // Create date object to store the DateTime format
  $dateObj = DateTime::createFromFormat('!m', $monthNum);  
  // Store the month name to variable
  $monthName = $dateObj->format('F');
  return $monthName;
}

function getYearOfDate($pdate) {
    $date = DateTime::createFromFormat("Y-m-d", $pdate);
    return $date->format("Y");
}

function getMonthOfDate($pdate) {
    $date = DateTime::createFromFormat("Y-m-d", $pdate);
    return $date->format("m");
}

function getDayOfDate($pdate) {
    $date = DateTime::createFromFormat("Y-m-d", $pdate);
    return $date->format("d");
}

////----start date is the last date of the previous month----////
function GetSemiMonthlyPeriod($start){
	/* Initial date and duration of processing */
	//$start = '2021-01-01';
	$months = 12;

	/* reduce start date to it's constituent parts */
	$year = date('Y',strtotime($start));
	$month = date('m',strtotime($start));
	$day = date('d',strtotime($start));
	$_1stday = date('d', mktime( 0, 0, 0, $month, 1, $year ));
	$_15thday = date('d', mktime(0, 0, 0, $month, 15, $year));
	$lastday = date('d', mktime(0, 0, 0, $month+1, 0, $year));

	if($day == $lastday){
		//echo 'lastday is: '.$lastday;
		/* store results */
		$output=array();
		
			for( $i=0; $i < $months; $i++ ){
				/* Get the 1st day of the calendar month */
				$output[]=date('Y-m-d', mktime( 0, 0, 0, $month + $i + 1, 1, $year ) );
			    /* Get the 15th of the month */
			    $output[]=date('Y-m-d', mktime( 0, 0, 0, $month + $i + 1, 15, $year ) );
			}

		/* use the results somehow... */
		//printf('<pre>%s</pre>',print_r($output,true));
		return $output;
	}
	elseif($day == $_15thday){
		$output=array();

			for( $i=0; $i < $months; $i++ ){
			    /* Get the 15th of the month */
			    $output[]=date('Y-m-d', mktime( 0, 0, 0, $month + $i, 16, $year ) );
			    /* Get the last day of the calendar month */
			    $output[]=date('Y-m-t', mktime( 0, 0, 0, $month + $i, 1, $year ) );
			}

		/* use the results somehow... */
		//printf('<pre>%s</pre>',print_r($output,true));
		return $output;
	}
	elseif ($day == $_1stday) {
		//echo 'Firsthalf is: '.$day;
		/* store results */
		$output=array();

			for( $i=0; $i < $months; $i++ ){
				/* Get the 1st day of the calendar month */
				$output[]=date('Y-m-d', mktime( 0, 0, 0, $month + $i, 1, $year ) );
			    /* Get the 15th of the month */
			    $output[]=date('Y-m-d', mktime( 0, 0, 0, $month + $i, 15, $year ) );
			}

		/* use the results somehow... */
		//printf('<pre>%s</pre>',print_r($output,true));
		return $output;
	}
	else{return false;}
}



function Day_1_of_CurrYear($year){
	$day1 = $year."-01-01";
	if($date = DateTime::createFromFormat("Y-m-d", $day1)){
		return $date->format("Y-m-d");	
	}
	else{
		return false;
	}
}

function Last_Day_of_Year($year){
	$endOfYear = $year."-12-31";
	if($date = DateTime::createFromFormat("Y-m-d", $endOfYear)){
		return $date->format("Y-m-d");	
	}
	else{
		return false;
	}
}

function OutputMoney($number){
	return number_format($number, 2);
}





?>
