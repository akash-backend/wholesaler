<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function dateDiff($date)
{
   $mydate= date("Y-m-d H:i:s");
   $theDiff="";
   //echo $mydate;//2014-06-06 21:35:55
   $datetime1 = date_create($date);
   $datetime2 = date_create($mydate);
   $interval = date_diff($datetime1, $datetime2);
   //echo $interval->format('%s Seconds %i Minutes %h Hours %d days %m Months %y Year    Ago')."<br>";
   $min=$interval->format('%i');
   $sec=$interval->format('%s');
   $hour=$interval->format('%h');
   $mon=$interval->format('%m');
   $day=$interval->format('%d');
   $year=$interval->format('%y');
   if($interval->format('%i%h%d%m%y')=="00000")
   {
    //echo $interval->format('%i%h%d%m%y')."<br>";
      return $sec." Sec ago";
   } 

   else if($interval->format('%h%d%m%y')=="0000"){
      return $min." Min ago";
   }
   else if($interval->format('%d%m%y')=="000"){
      return $hour." Hours ago";
   }
   else if($interval->format('%m%y')=="00"){
      return $day." Days ago";
   }
   else if($interval->format('%y')=="0"){
      return $mon." Months ago";
   }
   else{
      return $year." Years ago";
   }
}

if(!function_exists('multid'))
{
   function multid_sort($arr, $index) {
      $b = array();
      $c = array();
      foreach ($arr as $key => $value) {
         $b[$key] = $value[$index];
      }
      asort($b);
      foreach ($b as $key => $value) {
         $c[] = $arr[$key];
      }

      return $c;
   }
}

function distance($lat1, $lon1, $lat2, $lon2 ) {

   $theta = $lon1 - $lon2;
   $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
   $dist = acos($dist);
   $dist = rad2deg($dist);
   $miles = $dist * 60 * 1.1515 * 1.609344;
   return $miles;
  
}
