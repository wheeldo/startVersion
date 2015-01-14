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

$con = db::getDefaultAdapter();
$selectOrg = $con->select()->from('organizations')->where('organizationID = ? ', array($orgSelection));
$resultOrg = $con->query($selectOrg);
$rowOrg = $resultOrg->fetch_array();
$orgName=$rowOrg['organizationName'];
$campaign=false;
if($rowOrg['organizationArea']!="") {
    $campaign=$rowOrg['organizationArea'];
}


// split by campaign:
$appInfo=false;
if($campaign) {
     $extraSearch="AND `campaign`='{$campaign}'";
     $appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$_POST['appID']}' $extraSearch");
}
//////////////////////
//var_dump($campaign);
if(!$appInfo)
    $appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$_POST['appID']}' AND `campaign`='No'");

?>
<div class="close_more"></div>
<table class="more_table">
    <tr>
        <td colspan="3" style="border-bottom:1px solid #D9D9D9;"><div style="float:right;height:20px;width:20px;"></div><?=$appInfo['description']?></td>
    </tr>
<!--    <tr>
        <td class="screen_shots_wrap">
            <?
            $ans=$dbop->selectDB("appGallery","WHERE `appID`='{$_POST['appID']}' LIMIT 3");
            for($i=0;$i<$ans['n'];$i++) {
                $row=mysql_fetch_assoc($ans['p']);
            ?>
                <a href="javascript:activateExploreApp(<?=$_POST['appID']?>)"><img src="../<?=$row['img']?>" /></a>
            <?}?>

            
            <script type='text/javascript'>

            app_galleries[<?=$_POST['appID']?>]=[<?
            $ans=$dbop->selectDB("appgallery","WHERE `appID`='{$_POST['appID']}'");
            for($i=0;$i<$ans['n'];$i++) {
                $row=mysql_fetch_assoc($ans['p']);
            ?><?if($i!=0){?>,<?}?>
                {
                    href : '../<?=$row['img']?>',
                    title : '<?=$row['description']?>'
                }<?}?>];
            </script>
            
            
        </td>
        <td class="benefits_wrap">
            <?=$appInfo['gain_html']?>
        </td>
        <td class="objectives_wrap">
            <?=$appInfo['objectives_html']?>
        </td>
    </tr>-->
</table>
