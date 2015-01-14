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

$ans=$dbop->selectDB("users","WHERE `userOrganizationID`='$orgSelection'");
echo $ans['n'];