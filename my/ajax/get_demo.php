<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');

$user = $auth->getUser();
$userKind = $user->getData('userUserKindID');
$userID=$user->getData('userID');
$orgSelection = (int) $user->getData('userOrganizationIdSelect');

// if not admin get orgId from user
if ($orgSelection=='0'){
	$orgSelection = (int) $user->getData('userOrganizationID');
}


$appID=$_POST['appID'];

$app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
$appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$appID}'");


//appRemoveUser;
//appAddUser;




//echo $appInfo['demoCopy'];


$appAdress=$app['appAddress'];
//if(AvbDevPlatform::isLocalMachine()) {
//    $appAdress=  str_replace(".com",".com.loc",$appAdress);
//}

if(AvbDevPlatform::isLocalMachine())
    $appAdress=str_replace(".com",".".AvbDevPlatform::getServerName(),$appAdress);



if($app['appRemoveUser']!='') {
    $url_remove_user = 'http://'.$appAdress.$app['appRemoveUser'];
    $url_remove_user=str_replace("[appID]",$appInfo['demoCopy'],$url_remove_user);
    $url_remove_user=str_replace("[userID]",$userID,$url_remove_user);
}
else {
    die("app unable to proccess the remove request!!!!");
}



//echo $url_remove_user;
//
//var_dump($app);

$remove_res=file_get_contents($url_remove_user);


if($app['appAddUser']!='') {
    $url_add_user = 'http://'.$appAdress.$app['appAddUser'];
    $url_add_user=str_replace("[appID]",$appInfo['demoCopy'],$url_add_user);
    $url_add_user=str_replace("[userID]",$userID,$url_add_user);
}
else {
    die("app unable to proccess the add request!!!!");
}
//echo $url_add_user;
$add_res=file_get_contents($url_add_user);

//echo $add_res;




$response=json_decode($add_res,true);


//echo($add_res);

if($response['status']=="ok") {
    echo 'http://'.$appAdress.$response['link'];
}