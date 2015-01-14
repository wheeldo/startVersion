<?
require_once('../modules/modules.php');
require_once('../checkLogin.php');

$teamID=$_POST['teamID'];


$users=array();
$ans=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='{$teamID}'");
for($i=0;$i<$ans['n'];$i++) {
        $row=mysql_fetch_assoc($ans['p']);
        $userID=$row['teamUserUserID'];
        $user=$dbop->selectAssocRow("users","WHERE `userID`='{$userID}'");
        $users[$i][0]=$user['userName'];
        $users[$i][1]=$user['userEmail'];
}

?>
new_players=<?=json_encode($users)?>;
setTeamHTML(false);
new_players=[];

