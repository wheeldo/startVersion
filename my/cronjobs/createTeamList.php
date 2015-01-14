<?php
$ds=DIRECTORY_SEPARATOR;
$base_path=dirname(__FILE__);
$base_path=$base_path. $ds . "..". $ds;
require_once($base_path.'/modules/modules.php');
require_once($base_path.'/checkLogin.php');


function getJson($orgId) {
    global $dbop;
    $teams=array();
    
    $ans=$dbop->selectDB("teams","WHERE `teamOrganizationID`='{$orgId}' ORDER BY `teamName` ASC");
    for($i=0;$i<$ans['n'];$i++) {
        $row=mysql_fetch_assoc($ans['p']);
        
        $ans2=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='{$row['teamID']}'");
        //$ans2['n']=1;
        $teams[$i]=$row;
        $teams[$i]['teamID']=$teams[$i]['teamID']."_".$ans2['n'];
        $teams[$i]['teamName']=$teams[$i]['teamName']." (".$ans2['n']." users)";
    }
    return json_encode($teams);
}

$ans=$dbop->selectDB("organizations","WHERE `teamsList_uptodate`='0'");
for($i=0;$i<$ans['n'];$i++) {
    $row=mysql_fetch_assoc($ans['p']);
    set_time_limit (60*5);
    $json=getJson($row['organizationID']);
    
    $fields=array();
    $fields['teamsList']=$json;
    $fields['teamsList_uptodate']=1;
    $dbop->updateDB('organizations',$fields,$row['organizationID'],"organizationID");
}

    