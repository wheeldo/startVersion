<?php
$ds=DIRECTORY_SEPARATOR;
$base_path=dirname(__FILE__);
$cache_path=$base_path. $ds . ".." . $ds . "cache" . $ds;
$base_path=$base_path. $ds . "..". $ds . ".." . $ds;
require_once($base_path.'/modules/modules.php');
require_once($base_path.'/checkLogin.php');
date_default_timezone_set('Asia/Jerusalem');

$cached_timestamp=0;
$cahced_array=[];
$cache_file_name="user_logs.ca";
if(file_exists($cache_path.$cache_file_name)) {
    $cache_file=file_get_contents($cache_path.$cache_file_name);
    $ex=explode("___|___",$cache_file);
    $cached_timestamp=$ex[0];
    $cached_json=$ex[1];
    
    $cahced_array=json_decode($cached_json,true);
    if($cahced_array==null)
        $cahced_array=array();
}

$dbop_logs=new dbop();
$dbop_logs->connect(USER,PASSWORD,"wheeldo_logs",DB_HOST);
$logs=array();
$ans=$dbop->selectDB("logs","WHERE `time`>'$cached_timestamp' ORDER BY `time` DESC LIMIT 0,300"); 
for($i=0;$i<$ans['n'];$i++) {
    set_time_limit(6);
     $row=mysql_fetch_assoc($ans['p']);
     
     
     $row['date']=date("d/m/Y H:i",$row['time']);
     if($row['country']=="") {
         if($row['ip']=="" || $row['ip']=="127.0.0.1") {
             $dbop->updateDB("logs",array("country"=>"def"),$row['id']);
         }
         else {
             $geoData=json_decode(file_get_contents("http://freegeoip.net/json/{$row['ip']}"),true);
             $cc=$geoData['country_code']!=""?$geoData['country_code']:"def";
             $dbop->updateDB("logs",array("country"=>$cc),$row['id']);
             $row['country']=$cc;
         }
     }
     $logs[]=$row;
}



$logs=array_merge($logs,$cahced_array);

$return=json_encode($logs);

file_put_contents($cache_path.$cache_file_name, time()."___|___".$return);


header('Content-type: application/json');
echo $return;