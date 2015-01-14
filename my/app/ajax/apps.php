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

$extra_search='';


$ans=$dbop->selectDB("apps","WHERE `appInactive`='0' $extra_search ORDER BY `appOrder` ASC");
for($i=0;$i<$ans['n'];$i++) {
   $app=mysql_fetch_assoc($ans['p']);
   if($app['appPrivate']!="0") {
       $checkPrivate=$dbop->selectAssocRow("privateApps","WHERE `appID`='{$app['appID']}' AND `organizationID`='{$orgSelection}'");
       if(!$checkPrivate)
           continue;
   }
   
   
   $appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$app['appID']}'");
   
   
   
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
    
    
    $apps[$appsC]['appID']=$app['appID'];
    $apps[$appsC]['icon']=$appInfo['icon'];
    $apps[$appsC]['name']=ucfirst($appInfo['name']);
    $apps[$appsC]['slogen']=$appInfo['slogen'];
    $apps[$appsC]['video']=$appInfo['video'];
    $apps[$appsC]['categories']=$categories;
    $appsC++;
    
}

header('Content-type: application/json');
echo json_encode($apps);