<?php
require_once('modules/modules.php');
$userID=$_POST['userID'];
//$userID=77422;

$baseAddressSystem="http://api.wheeldo.com";
        
if(AvbDevPlatform::isLocalMachine()) {
    $baseAddressSystem=str_replace(".com", ".com.loc", $baseAddressSystem);
}


function replace_local_image($new_url,$ext) {
    global $local_image;
    global $userID;
    global $baseAddressSystem;
    global $dbop;
    if($local_image!="") {
        unlink($local_image);
    }
    
    $fileName="userImages/user_{$userID}.{$ext}";
    
    $check=copy($new_url, $fileName);
    if($check) {
        $imgUrl=$baseAddressSystem."/".$fileName;
        $dbop->updateDB("users",array("userPhotoID"=>$imgUrl),$userID,"userID");
        return $imgUrl;
    }
    else {
        return false;
    }
        
    
}


$availibleImages=array();
$local_image="";
$j=0;
// get local image //
$path=dirname(__FILE__);
$ds= DIRECTORY_SEPARATOR;
$path=$path."{$ds}userImages";
if ($handle = opendir($path)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $ex1=explode(".",$entry);
            $ex2=explode("_",$ex1[0]);
            if(isset($ex2[1]) && $ex2[1]!="" && $ex2[1]==$userID) {
                $last_modified=filemtime($path.$ds.$entry);
                $local_image=$path.$ds.$entry;
                $availibleImages[$last_modified]['local']=true;
                $availibleImages[$last_modified]['url']= $baseAddressSystem."/userImages/".$entry;
                $availibleImages[$last_modified]['last_modified']=$last_modified;
                $availibleImages[$last_modified]['ext']=$ex1[1];

                $j++;
            }
        }
    }
    closedir($handle);
}
/////////////////////

$availibleImages=array();


//echo "<pre>";
/////// get image from apps /////
$ans=$dbop->selectDB("apps","WHERE `appGetUserImage`!=''");
for($i=0;$i<$ans['n'];$i++) {
        $row=mysql_fetch_assoc($ans['p']);
        $baseAddressApp=$row['appAddress'];
        
        if(AvbDevPlatform::isLocalMachine()) {
            $baseAddressApp=str_replace(".com", ".com.loc", $baseAddressApp);
        }
        
        $getImageUrl="http://".$baseAddressApp.$row['appGetUserImage'];
        
        $getImageUrl=  str_replace("[userID]", $userID, $getImageUrl);
        
        $imageUrl=file_get_contents($getImageUrl);
        //var_dump($imageUrl);
        if($imageUrl!="none" && $imageUrl!="") {
            
            $img_data=json_decode($imageUrl,true);
            //var_dump($img_data);
            $fullUrl="http://".$baseAddressApp.$img_data['url'];
            $availibleImages[$img_data['last_modified']]['local']=false;
            $availibleImages[$img_data['last_modified']]['url']=$fullUrl;
            $availibleImages[$img_data['last_modified']]['last_modified']=$img_data['last_modified'];
            $availibleImages[$img_data['last_modified']]['ext']=$img_data['ext'];
            $j++;
            
            //echo $row['appName']."<br />";
            //var_dump($img_data);
        }
}
///////////////////////////////////
//header('Content-type: application/json');
if(count($availibleImages)==0) {
    $res=array();
    $res['status']="no image";
    echo json_encode($res);
}
else {
    //var_dump($availibleImages);
    krsort($availibleImages);
    //var_dump($availibleImages);
    $res=array();
    $res['status']="ok";
    $res['link']=$availibleImages[getFirsKeyAssocArray($availibleImages)]['url'];
    //var_dump($availibleImages[0]);
    if(!$availibleImages[getFirsKeyAssocArray($availibleImages)]['local']) {
        $check=replace_local_image($availibleImages[getFirsKeyAssocArray($availibleImages)]['url'],$availibleImages[getFirsKeyAssocArray($availibleImages)]['ext']);
        if($check)
            $res['link']=$check;
    }
    echo json_encode($res);
}

function getFirsKeyAssocArray($array) {
    $key=null;
    foreach($array as $key=>$value):       
        break;
    endforeach;
    
    return $key;
}