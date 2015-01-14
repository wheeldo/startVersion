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
    if($campaign!="sales" && $campaign!="training" && $campaign!="support")
        $campaign=false;
}

$extra_search='';
if($_POST['search']!="" && $_POST['search']!="Type to search") {
    $search=$_POST['search'];
    $extra_search=" AND (`appName` LIKE '%{$search}%' OR `appDesc` LIKE '%{$search}%' )";
}

$ans=$dbop->selectDB("apps","WHERE `appInactive`='0' $extra_search ORDER BY `appOrder` ASC");
for($i=0;$i<$ans['n'];$i++) {
   $iconClass="";
   $private=false;
   $app=mysql_fetch_assoc($ans['p']);
   if($app['appPrivate']!="0") {
       $private=true;
       $iconClass.=" private ";
       $checkPrivate=$dbop->selectAssocRow("privateApps","WHERE `appID`='{$app['appID']}' AND `organizationID`='{$orgSelection}'");
       if(!$checkPrivate)
           continue;
   }
   
   
   
   // split by campaign:
   $appInfo=false;
   if($campaign) {
        $extraSearch="AND `campaign`='{$campaign}'";
        $appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$app['appID']}' $extraSearch");
   }
   //////////////////////
   //var_dump($campaign);
   if(!$appInfo)
       $appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$app['appID']}' AND `campaign`='No'");
   
   
   
    $categories=array();
    $sql="SELECT 
                appCategories.* , categories.* 

                FROM 
                      appCategories INNER JOIN categories
                      ON appCategories.categoryID=categories.categoryID 

                WHERE appCategories.appID='{$app['appID']}' LIMIT 3;";

    $p=mysql_query($sql);
    $n=mysql_num_rows($p);
    $categoriesID=array();
    for($j=0;$j<$n;$j++) {
            $r=mysql_fetch_assoc($p);
            $categories[]=$r;
            $categoriesID[]=$r['categoryID'];
    }
    
    
    if((int)$_POST['category']!=0 && !in_array((int)$_POST['category'], $categoriesID)) {
        continue;
    }
    
    
    $comming_soon=false;
    if($appInfo['comming_soon']=="1") {
        $comming_soon=true;
        $iconClass.=" comming_soon ";
    }
   ?>
<!--                    app cont-->
                    <div class="app_cont" id="app_<?=$app['appID']?>">
                        <div class="app_icon_wrap <?=$iconClass?>">
                            <img class="comming_soon_img" src="img/comming_soon.png" />
                            <img class="private_img" src="img/private.png" title="Private apps are not available to all users" />
                            <img class="app_img" src="<?=$appInfo['icon']?>" alt="<?=ucfirst($appInfo['name']);?>" title="<?=ucfirst($appInfo['name']);?>" />
                        </div>
                        <div class="app_info_wrap">
                            <input type="hidden" id="app_loaded_<?=$app['appID']?>" value="0" />
                            <div class="left">
                                <h2><?=ucfirst($appInfo['name']);?></h2>
                                <div class="short_desc">
                                    <?=($appInfo['slogen']!="" ? $appInfo['slogen']: "" );?>
                                </div>
                                <div class="app_actions">
                                    <?if(!$comming_soon || $userKind==5){?><a href="javascript:void(0)" class="button_green button_app create_game event_log" log_type="create_game" log_more="App Name: <?=ucfirst($appInfo['name']);?>" appID="<?=$app['appID']?>" as_service="<?=$appInfo['edit_in_service']?>">Create Game</a><?}?>
                                    <a href="javascript:void(0)" class="app_extra learn_more event_log" app_id="<?=$app['appID']?>" log_type="learn_more" log_more="App Name: <?=ucfirst($appInfo['name']);?>">Learn more</a>
                                    <div class="seperator"></div>
                                    
                                    <?if($appInfo['demoCopy']!="0"){?>                                       
                                        <a class="app_extra play_now event_log" href="javascript:getDemo(<?=$app['appID']?>)" log_type="demo" log_more="App Name: <?=ucfirst($appInfo['name']);?>">Play now</a>
                                    <?}elseif($appInfo['video']!=""){?>
                                        <a class="app_extra run_demo event_log wheeldoPopUp" data-type="player" href="javascript:void(0)" player="<?=$appInfo['video']?>" log_type="demo" log_more="App Name: <?=ucfirst($appInfo['name']);?>" >Demo</a>
                                    <?}else{?>
<!--                                        <a href="javascript:popText('Demo not avalibale yet...')" class="app_extra run_demo">Run demo</a>-->
                                    <?}?>
                                    
                                </div>
                            </div>
                            <div class="right">
                                <h5>Game categories</h5>
                                <?foreach($categories as $category):?>
                                    <div class="category"><?=$category['categoryName']?></div>
                                <?endforeach;?>
<!--                                <div class="more"><a href="javascript:void(0)">+3 more</a></div>-->
                            </div>
                            <br class="clr" />
                        </div>
                        <div class="more_info" id="more_info_<?=$app['appID']?>">
                            <div class="learn_more_arrow"></div>

                        </div>
                        <br class="clr" />
                    </div>
<!--                    app cont end--> 
   <?      
}