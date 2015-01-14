<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');


// permitions
$permitions = array(5,2);
$auth = Auth::isLogin();
$user = $auth->getUser();
$userId = $user->getID();
$userKind = $user->getData('userUserKindID');
if (!(in_array($userKind,$permitions))){
	die('no permission');
}

$appCopyId = dbInterface::real_escape_string($_POST['copyID']);
$appCopy = new appCopy(null,$appCopyId);
$app = new App(null,$appCopy->getData('appCopyAppID'));

$appAdress=$app->getData('appAddress');
if(AvbDevPlatform::isLocalMachine()) {
    //$appAdress="localhost.".$appAdress;
    $appAdress=str_replace("com","localhost",$appAdress);
}

$appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$app->getData('appID')}'");

$appEditAdress=$app->getData('appEdit');
if($appEditAdress!='') {
    $url = 'http://'.$appAdress.$appEditAdress;
    $url=str_replace("[appID]",$appCopyId,$url);
}
else {
    $url="http://".$appAdress."edit.php?token=".$auth->getTokenVal()."&configID=".$appCopyId;
}

?>
<div class="wheeldoSlider" id="slider_app_<?=$_POST['copyID']?>">
    <div class="slide_screen create_game_stages create_game_stage1">
        <div class="top">
            <h1>
                <?=ucfirst($appInfo['name']);?>
            </h1>
        </div>
        <div class="middle">
            <table class="app_edit">
                <tr>
                    <td style="width:75px;"><img class="app_icon" src="<?=$appInfo['icon']?>" alt="app name" /></td>
                    <td style="width:675px;padding-left:10px;">
                        <div class="app_name">
                            <input type="text" class="app_name_input autoTxT" id="game_name" title="Enter a name for your game" value="<?=$appCopy->getData('appCopyName'); ?>" />
                        </div>                    
                        <div class="edit_iframe"><iframe src="<?=$url?>" id="editFrame_<?=$appCopyId?>"></iframe></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="bottom">         
            <a href="javascript:void(0)" class="cancel hide">Cancel</a>
            <a href="javascript:void(0)" class="ready_to_publish save_edit" copyID="<?=$appCopyId?>">Save</a>
        </div>
    </div>
</div>
