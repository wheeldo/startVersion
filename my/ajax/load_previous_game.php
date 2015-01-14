<?
require_once('../modules/modules.php');
require_once('../checkLogin.php');


$load_game_id=$_POST['load_game_id'];
$curr_game_id=$_POST['curr_game_id'];
$appID=$_POST['appID'];


$appArray = App::appArray('appID = ? ',array($appID));
$appAdress=$appArray[0]['appAddress'];
if(AvbDevPlatform::isLocalMachine()) {
    $appAdress="localhost.".$appAdress;
}

if($appArray[0]['appDuplicate']!='') {
    $url = 'http://'.$appAdress.$appArray[0]['appDuplicate'];
    $url=str_replace("[old]",$load_game_id,$url);
    $url=str_replace("[new]",$curr_game_id,$url);
}
else {
    $url = 'http://'.$appAdress."duplicate.php?oldID=".$load_game_id."&newID=".$curr_game_id;
}
//echo $url;
$c=file_get_contents($url);

echo $c;