<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');

$ret=array();
$copyID=$_POST['copyID'];

$copyRow=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='$copyID'");
$appID=$copyRow['appCopyAppID'];
$appRow=$dbop->selectAssocRow("apps","WHERE `appID`='$appID'");
$reportUrl=$appRow['appReport'];
$appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$copyRow['appCopyAppID']}'");
$ret['icon']=$appInfo['icon'];
$ret['name']=$copyRow['appCopyName'];

$teamName="";
$team=$dbop->selectAssocRow("teams","WHERE `teamID`='{$copyRow['appCopyTeam']}'");

if($team) {
    $c=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='{$copyRow['appCopyTeam']}'");
    $teamName=$team['teamName']." (".$c['n'].")";
}

$ret['teamName']=$teamName;

$ret['startDate']=(int)$copyRow['appCopyTimestamp']>0?date("m/d/y l",$copyRow['appCopyTimestamp']):"";

$appAdress=$appRow['appAddress'];
if(AvbDevPlatform::isLocalMachine()) {
    $appAdress=  str_replace(".com",".com.loc",$appAdress);
}


if($reportUrl!='') {
    $url = 'http://'.$appAdress.$reportUrl;
    $url=str_replace("[appID]",$copyID,$url);
}
else {
    $url = 'http://'.$appAdress."report.php?appID=".$copyID;
}
$ret['url']=$url;

header('Content-type: application/json');
echo json_encode($ret);