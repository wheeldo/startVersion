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

// get page vars
$p = isset($_GET['p']) ? dbInterface::real_escape_string($_GET['p']) : 0;
$i = isset($_GET['i']) ? dbInterface::real_escape_string($_GET['i']) : 0;

if(isset($_GET['s'])) 
    $s = dbInterface::real_escape_string($_GET['s']);
else 
    $s='';
// build query and print the rows

if ($user->getData('userOrganizationIdSelect')=='0'){
	$orgId = $user->getData('userOrganizationID');
}else{
	$orgId = $user->getData('userOrganizationIdSelect');
}


$appArray = App::appArray('appID = ? ',array($_POST['appID']));


$appCopyCopy=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$appArray[0]['original_copy']}'");

$fields=array();
$fields['appCopyID']=null;
$fields['appCopyAppID']=$_POST['appID'];
$fields['appCopyUserID']=$userId;
$fields['appCopyOriginalID']=0;
$fields['appCopyOrganizationID']=$orgId;
$fields['appCopyTerminate']=1;
$fields['appCopyTimestamp']=time();
$fields['appCopyInactive']=0;
$fields['appCopyMultiple']=1;
$fields['appCopyPrivate']=1;
$fields['appCopyName']="My copy of ".$appArray[0]['appName'];
$fields['appCopyDescription']="";
$fields['appCopyIsOnMarket']=0;
$fields['appCopyAutoEmail']=$appCopyCopy['appCopyAutoEmail'];
$fields['appCopyAfterSet']=0;
$fields['appCopyLocked']=0;

//var_dump($fields);
$insert=$dbop->insertDB("appCopies",$fields,false);
//

// duplicate app ::: //


//if(AvbDevPlatform::isLocalMachine()) {
//    $appAdress=  str_replace(".com",".com.loc",$appAdress);
//}

$appAdress=$appArray[0]['appAddress'];
if(AvbDevPlatform::isLocalMachine()) {
    //$appAdress="localhost.".$appAdress;
    $appAdress=str_replace("com","localhost",$appAdress);
}

if($appArray[0]['appDuplicate']!='') {
    $url = 'http://'.$appAdress.$appArray[0]['appDuplicate'];
    $url=str_replace("[old]",$appArray[0]['original_copy'],$url);
    $url=str_replace("[new]",$insert,$url);
}
else {
    $url = 'http://'.$appAdress."duplicate.php?oldID=".$appArray[0]['original_copy']."&newID=".$insert;
}

$check=file_get_contents($url);



$appCopyId = $insert;
$appCopy = new appCopy(null,$appCopyId);
$app = new App(null,$appCopy->getData('appCopyAppID'));
$appEditAdress=$app->getData('appEdit');


if($appEditAdress!='') {
    $url = 'http://'.$appAdress.$appEditAdress;
    $url=str_replace("[appID]",$appCopyId,$url);
}
else {
    $url="http://".$appAdress."edit.php?token=".$auth->getTokenVal()."&configID=".$appCopyId;
}



$appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$_POST['appID']}'");



$appID=$_POST['appID'];

$orgSelection = (int) $user->getData('userOrganizationIdSelect');

// if not admin get orgId from user
if ($orgSelection=='0'){
	$orgSelection = (int) $user->getData('userOrganizationID');
}


$teamsArray = team::teamArray("teamName LIKE ? AND teamOrganizationID ='$orgId' ORDER BY teamName",'%'.$s.'%');



$appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$_POST['appID']}'");


$appCheck=false;
if($appArray[0]['appEditCheck']!="") {
    $appCheckUrl = 'http://'.$appAdress.$appArray[0]['appEditCheck'];
    $appCheckUrl=str_replace("[appID]",$appCopyId,$appCheckUrl);
    $appCheckUrl=str_replace("[app]",$_POST['appID'],$appCheckUrl);
    $appCheck=true;
}

?>
<script type="text/javascript">
    function checkAppEdit() {
        <?
        if($appCheck) {
        ?>
        var check_url="<?=$appCheckUrl?>"; 
        $.ajax({
                url:check_url,
                dataType: 'jsonp', // Notice! JSONP <-- P (lowercase)
                complete:function(json){

                }

            });
            return false;
        <?}else{?>
        return true;
        <?}?>
    }
</script>
<div class="wheeldoSlider" id="slider_app_<?=$_POST['appID']?>">
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
                            <div>How would you like to name this copy of '<?=ucfirst($appInfo['name']);?>'?</div>
                            <input type="text" class="app_name_input autoTxT" id="game_name" lastKeyUp="0" copyID="<?=$appCopyId?>" title="Enter a name for your game" />
                        </div>     
                       
                        <div class="edit_iframe"><iframe src="<?=$url?>" id="editFrame_<?=$appID?>"></iframe></div>
                         
                    </td>
                </tr>
            </table>
        </div>
        <div class="bottom">
            <div class="load_games">
                <div class="games">
                    <?
                    $ans=$dbop->selectDB("appCopies","WHERE `appCopyAppID`='$appID' AND `appCopyOrganizationID`='$orgSelection'");
                    for($i=0;$i<$ans['n'];$i++) {
                            $row=mysql_fetch_assoc($ans['p']);
                            if($row['appCopyID']==$appCopyId)
                                continue;
                            
                    ?>
                    <a href="javascript:void(0)" class="load_game" appID="<?=$appID?>" load_game_id="<?=$row['appCopyID']?>" curr_game_id="<?=$appCopyId?>">(<?=$row['appCopyID']?>) <?=$row['appCopyName']?></a>
                    <? }?>

                </div>
                <a href="javascript:void(0)" class="load_previous_game">Load from previous Game</a>
            </div>
            
            <a href="javascript:void(0)" class="cancel event_log" log_type="cancel_game" log_more="App Name: <?=ucfirst($appInfo['name']);?>,CopyID: <?=$appCopyId;?>" copyID="<?=$appCopyId;?>">Cancel</a>
            
            <a href="javascript:void(0)" class="ready_to_publish ready_to_publish_check" copyID="<?=$appCopyId;?>" appID="<?=$appID?>">Ready to Publish</a>
            <a href="javascript:void(0)" class="save_and_close hide event_log" log_type="save_and_close_game" log_more="App Name: <?=ucfirst($appInfo['name']);?>,CopyID: <?=$appCopyId;?>">Save & Close</a>
        </div>
    </div>
    <div class="slide_screen create_game_stages">
        <div class="top">
            <h1>
                Publish 'My <?=ucfirst($appInfo['name']);?>'
            </h1>
            <h5>Copy of <?=ucfirst($appInfo['name']);?></h5>
        </div>
        <div class="middle">
            <table class="recipients">
                <tr>
                    <th>Recipients:</th>
                    <td class="recipients_th"><input type="radio" value="new" name="recipients" checked="checked" /> New team</td>
                    <td class="recipients_th"><input type="radio" value="exist" name="recipients" /> Existing team</td>
                    <td class="recipients_th"><input type="radio" value="file" name="recipients" /> Load players from file <span style="font-size:11px;">(<a href="getFile/WheeldoTeamFileExample.csv" class="download_example_file" target="_blank">Download example file</a>)</span></td>
                </tr>
            </table>
            
            <div class="team_wrap">
                <div class="new choose_recipients">
                    <div class="left">
                        <input type="text" class="autoTxT" id="new_team_name" title="Enter a name for your new team" />
                    </div>
                    <div class="right">
                        This team has a total of: <br />
                        <span class="team_num">0</span> Players <a class="show_team_members" screen="new" href="javascript:void(0)">Show</a>
                    </div>
                     <br class="clr" />
                     <div class="team_members">
                     </div>
                    <div>
                        <table style="width:100%;">
                            <tr>
                                <td><input type="text" class="autoTxT" id="new_team_player_name" title="Player's name" /></td>
                                <td><input type="text" class="autoTxT" id="new_team_player_email" title="Player's email" /></td>
                                <td style="padding-left:10px;width:180px;"><a href="javascript:add_new_player()" class="add_player">Add player to team</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="exist choose_recipients">
                    <div class="left">

                            <select id="selected_team">
                                    <option value="0_0">Please select</option>
                                    <?foreach($teamsArray as $team):?>
                                        <option value="<?=$team['teamID']?>"><?=$team['teamName']?></option>
                                    <?endforeach;?>

                            </select>
                    </div>
                    <div class="right">
                        This team has a total of: <br />
                        <span class="team_num">0</span> Players <a class="show_team_members" screen="exist" href="javascript:void(0)">Show</a>
                    </div>
                     <br class="clr" />
                     <div class="team_members">
                         some data
                     </div>
                </div>
                
                <div class="file choose_recipients">
                    <div class="left">
                        <input type="text" class="autoTxT" id="file_team_name" title="Enter a name for your new team" />
                    </div>
                    <div class="right">
                        This team has a total of: <br />
                        <span class="team_num">0</span> Players <a class="show_team_members" screen="file" href="javascript:void(0)">Show</a>
                    </div>
                     <br class="clr" />
                     <div class="team_members">
                     </div>
                    <div>
                        <form id="team_upload_form" method="post" enctype="multipart/form-data" action="ajax/upload_team.php" onsubmit="set_ajax_load()">
                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        <input type="file" name="csv" id="file" class="customfile-input">
                                    </td>
                                    <td style="width:150px;padding-left:10px;">
                                        <input type="submit" value="Upload" class="upload-button" />
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <iframe id="upload_team_target" name="upload_team_target" onload="uploadTeamDone()" src="" style="width:0;height:0;border:0px solid #fff;"></iframe>
                    </div>
                </div>
            </div>
            <div class="email_content">
                <h4>Email Content</h4>
                <input type="text" class="autoTxT" id="email_subject" title="Enter the subject of the mail" />
                <textarea id="email_content" class="autoTxT" title="Enter the content of the mail"></textarea>
            </div>
        </div>
        <div class="bottom">
            <a href="javascript:void(0)" class="back arrow_back prev"><img src="img/back.png" /></a>
<!--            <a href="javascript:void(0)" class="preview_game">Preview</a>-->
            <a href="javascript:void(0)" class="cancel hide event_log" log_type="cancel_game" log_more="App Name: <?=ucfirst($appInfo['name']);?>,CopyID: <?=$appCopyId;?>">Cancel</a>
            <a href="javascript:void(0)" class="ready_to_publish publish event_log" log_type="publish_game" log_more="App Name: <?=ucfirst($appInfo['name']);?>,CopyID: <?=$appCopyId;?>" copyID="<?=$appCopyId?>" appID="<?=$appID?>">Publish</a>
        </div>
    </div>
<!--    <div class="slide_screen create_game_stages">
        <table class="publish">
            <tr>
                <td>
                    <div id="wait_img"></div>
                    <div id="wait_text"></div>
                </td>
            </tr>
        </table>
    </div>-->
</div>
