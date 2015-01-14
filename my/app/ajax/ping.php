<?php
$ds=DIRECTORY_SEPARATOR;
$base_path=dirname(__FILE__);
$base_path=$base_path. $ds . "..". $ds . ".." . $ds;
require_once($base_path.'/modules/modules.php');
require_once($base_path.'/checkLogin.php');



$apps=array();
$appsC=0;

$user = $auth->getUser();
$userKind = $user->getData('userUserKindID');
$orgSelection = (int) $user->getData('userOrganizationIdSelect');

// if not admin get orgId from user
if ($orgSelection=='0'){
	$orgSelection = (int) $user->getData('userOrganizationID');
}

$ans=$dbop->selectDB("users","WHERE `userOrganizationID`='$orgSelection' LIMIT 0,1");


$usersData=array();
$usersDataC=0;

for($i=0;$i<$ans['n'];$i++):
    $user_row=mysql_fetch_assoc($ans['p']);
    

    $teams_array=array();
    $sql="SELECT
            teams.teamName, teams.teamID
            FROM
            teamsUsers
            INNER JOIN teams ON teams.teamID = teamsUsers.teamUserTeamID
            WHERE
            teamsUsers.teamUserUserID = {$user_row['userID']}";
    $p=mysql_query($sql);        
    $n=mysql_num_rows($p);  
    
    //echo $sql;
    
    for($j=0;$j<$n;$j++):
        $team_row=mysql_fetch_assoc($p);
        $teams_array[$j]['id']=$team_row['teamID'];
        $teams_array[$j]['name']=$team_row['teamName'];
    endfor;
    
    
    $sql="SELECT userAppCopies.userAppCopyUserID FROM userAppCopies WHERE userAppCopyUserID='{$user_row['userID']}'";
    $p=mysql_query($sql);
    $gamePlayed=mysql_num_rows($p);

    $usersData[$usersDataC]['id']=$user_row['userID'];
    $usersData[$usersDataC]['name']=$user_row['userName'];
    $usersData[$usersDataC]['email']=$user_row['userEmail'];
    $usersData[$usersDataC]['addData']="";
    $usersData[$usersDataC]['teams']=$teams_array;
    $usersData[$usersDataC]['gamePlayed']=$gamePlayed;
    $usersData[$usersDataC]['admins']=array(7,8);
    
    
    
    
    $usersDataC++;
endfor;


header('Content-type: application/json');
echo json_encode($usersData);