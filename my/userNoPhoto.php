<?
require_once('modules/modules.php');

$dec=AvbDevPlatform::decrypt_Nmb($_GET['userID']);
if(!$dec)
    die();

$userID=$dec;

$user=$dbop->selectAssocRow("users","WHERE `userID`='{$userID}'");
if(!$user)
    die();


$pos=strpos($user['userPhotoID'], "cloudinary");

if($pos===false){?>
userNoPhoto=true;
if(userNoPhoto && !getCookie("close_image_uploader_"+USER.ID)) {
    createPopUp(true);
}
<?}?>
