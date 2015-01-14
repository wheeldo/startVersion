<?php
$ds=DIRECTORY_SEPARATOR;
$base_path=dirname(__FILE__);
$base_path=$base_path. $ds . "..". $ds;
require_once($base_path.'/modules/modules.php');
require_once($base_path.'/checkLogin.php');


$ans=$dbop->selectDB("users","WHERE `userName` = 'Demo user'");
for($i=0;$i<$ans['n'];$i++) {
    $row=mysql_fetch_assoc($ans['p']);
    

    // valid for 30 min
    if($row['userRegTime']<time()-0.5*3600) {
    //if(true) {
        $userID=$row['userID'];
        //var_dump($row);
        
        
        $data=explode("_",$row['userPassword']);
        $appID=(int)$data[0];
        $copyID=(int)$data[1];
        
        
        
        $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
        // delete user from game:
        $appRemoveUser=$app['appRemoveUser'];
        if($appRemoveUser!="") {
            $url="http://".$app['appAddress'].$appRemoveUser;
            $url=str_replace("[appID]",$copyID,$url);
            $url=str_replace("[userID]",$userID,$url);


            $url=str_replace(".com",".".AvbDevPlatform::getServerName(),$url);
            $check=file_get_contents($url);
        }
        /////////////////////////
        
        $dbop->deleteDB("users",$userID,"userID");
    }
    
    
}
