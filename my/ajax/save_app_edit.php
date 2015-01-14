<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');


$auth = Auth::isLogin();
$user = $auth->getUser();
$userId = $user->getID();

$orgSelection = (int) $user->getData('userOrganizationIdSelect');

// if not admin get orgId from user
if ($orgSelection=='0'){
	$orgSelection = (int) $user->getData('userOrganizationID');
}

$copyID=$_POST['copyID'];
$copyName=$_POST['name'];

// update copy data //
$copyFields=array();
$copyFields['appCopyName']=$copyName;
$dbop->updateDB("appCopies",$copyFields,$copyID,"appCopyID");
//////////////////////