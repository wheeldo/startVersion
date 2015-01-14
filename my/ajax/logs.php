<div id="scroller">
<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');

$user = $auth->getUser();
$userKind = $user->getData('userUserKindID');
$orgSelection = (int) $user->getData('userOrganizationIdSelect');

// if not admin get orgId from user
if ($orgSelection=='0'){
	$orgSelection = (int) $user->getData('userOrganizationID');
}

$extra_search='';
if($_POST['search']!="" && $_POST['search']!="Type to search") {
    $search=$_POST['search'];
    $extra_search=" AND (`appCopyName` LIKE '%{$search}%')";
}


$counter=0;
$ans=$dbop->selectDB("appCopies","WHERE `appCopyInactive`=0 AND `appCopyOrganizationID`='{$orgSelection}' $extra_search ORDER BY `appCopyID` DESC");
for($i=0;$i<$ans['n'];$i++) {
   $appCopy=mysql_fetch_assoc($ans['p']);
   $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appCopy['appCopyAppID']}'");
   $programAppCopies=$dbop->selectAssocRow("programAppCopies","WHERE `programAppCopyAppCopyID`='{$appCopy['appCopyID']}'");
  // echo "WHERE `programAppCopyAppCopyID`='{$appCopy['appCopyID']}'";
   //var_dump($programAppCopies);
   $appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$appCopy['appCopyAppID']}'");
   
   $gameID=$appInfo['appID'];
   if((int)$_POST['game']!=0 && (int)$_POST['game']!=$gameID) {
        continue;
   }

   $teamName="";
   $team=$dbop->selectAssocRow("teams","WHERE `teamID`='{$appCopy['appCopyTeam']}'");

   if($team) {
       $c=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='{$appCopy['appCopyTeam']}'");
       $teamName=$team['teamName']." (".$c['n'].")";
   }
   
   
   $counter++;
   if($counter==20) 
       break;
?>
<!--log-->                            
<div class="log">
    <h3><?=$appCopy['appCopyName']?></h3>
    <div class="app_name">(<?=$appCopy['appCopyID']?>) Copy of <?=$app['appName']?></div>
    <div class="copy_data">
        <div class="icon_wrap active">
            <?if($appCopy['appCopyTerminate']=="0"){?><img class="active_app" src="img/log_active_icon.png" /><?}?>
            <img class="app_icon" alt="app icon" src="<?=$appInfo['icon']?>" />
        </div>
        <div class="data_wrap">
            <?if((int)$appCopy['appCopyTimestamp']>0){?>
                <h5>Started On</h5>
                <?=date("m/d/y l",$appCopy['appCopyTimestamp'])?>
            <?}?>
            <?if($team){?>
                <h5 class="top10">Playing Team</h5>
                <?=$teamName?>
            <?}?>
        </div>
        <br class="clr" />
    </div>
    <div class="buttons">
        <a href="javascript:void(0)" class="button_blue button_app edit_game" copyID="<?=$appCopy['appCopyID']?>" appID="<?=$app['appID']?>" as_service="<?=$appInfo['edit_in_service']?>">Edit Game</a>
        <?if($appCopy['appCopyTerminate']=="0"){?><a href="javascript:void(0)" class="button_blue button_app center_button app_report" copyID="<?=$appCopy['appCopyID']?>" appID="<?=$app['appID']?>" as_service="<?=$appInfo['report_in_service']?>">Report</a><?}?>
        <a href="javascript:void(0)" class="button_red button_app right terminate" copyID="<?=$appCopy['appCopyID']?>">Remove</a>
        <br class="clr" />
    </div>
</div>
<!--log end-->
<? }?>
</div>