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
    $orgID=$user['userOrganizationID'];
    $org=$dbop->selectAssocRow("organizations","WHERE `organizationID`='{$orgID}'");
    
    $photo=$org['organizationImg'];
}


$defLogo="http://res.cloudinary.com/wheeldo/image/upload/v1385371658/org_def_usufua.png";

if($photo==""){
    $photo=$defLogo;
}

if(isset($query['effect'])) {
    $expp=explode("upload/",$photo);
    $expp[1]=$query['effect']."/".$expp[1];
    $photo=implode("upload/",$expp);
}




if(!$imginfo = @getimagesize($photo)) {
    $photo=$defLogo;
    $imginfo = @getimagesize($photo);
}

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Content-type: {$imginfo['mime']}");
readfile($photo); 
