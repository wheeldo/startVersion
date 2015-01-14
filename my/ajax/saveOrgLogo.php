<?php
require_once('../modules/modules.php');
// permitions
$permitions = array(5,2);
$auth = Auth::isLogin();
$user = $auth->getUser();
$userId = $user->getID();
$userKind = $user->getData('userUserKindID');

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




$allowed_ext=array();
$allowed_ext[]="png";
$allowed_ext[]="jpg";
$allowed_ext[]="gif";

if ($_FILES["image"]["error"] > 0){
    echo "Return Code: " . $_FILES["image"]["error"] . "<br>";
}
else {
    $ex=explode(".",$_FILES["image"]["name"]);
    $ext=strtolower($ex[(count($ex)-1)]);
    /////////// security checks: ///////////////////
    // check no 1:
    if(!in_array($ext, $allowed_ext))
        die("http://my.wheeldo.com/img/app_icon.png");
    // check no 2:
    $file_type=$_FILES["image"]["type"];
    $ex1=explode("/",$file_type);
    if($ex1[0]!="image")
        die("http://my.wheeldo.com/img/app_icon.png");
    /////////////////////////////
    
    
    $file_name="org_".$orgId."_".time().".".$ext;
    move_uploaded_file($_FILES["image"]["tmp_name"],"../uploads/organizations_logos/" . $file_name);
    
    if(AvbDevPlatform::isLocalMachine()) {
        echo "http://localhost.my.wheeldo.com/uploads/organizations_logos/".$file_name;
    }
    else {
        echo "http://my.wheeldo.com/uploads/organizations_logos/".$file_name;
    }
}