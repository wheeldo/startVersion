<?
$q=$_GET['q'];
$exp1=explode("___",$q);
$query=array();
foreach($exp1 as $r):
    $exp2=explode("-",$r);
    $query[$exp2[0]]=isset($exp2[1])?$exp2[1]:"";
endforeach;

$photo="";
if(isset($query['uid'])) {
    require_once('modules/modules.php');
    $userID=$query['uid'];
    $user=$dbop->selectAssocRow("users","WHERE `userID`='{$userID}'");
    $photo=$user['userPhotoID'];
    $pos1 = strpos($photo, "cloudinary");
    $pos2 = strpos($photo, "facebook");
    if ($pos1 === false && $pos2 === false) 
       $photo="";
}


if($photo==""){
    $photo="http://res.cloudinary.com/wheeldo/image/upload/v1385030174/User_default_qgwkye.jpg";
}

if(isset($query['effect'])) {
    $expp=explode("upload/",$photo);
    $expp[1]=$query['effect']."/".$expp[1];
    $photo=implode("upload/",$expp);
}

$imginfo = getimagesize($photo);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Content-type: {$imginfo['mime']}");
readfile($photo); 