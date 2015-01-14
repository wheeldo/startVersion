<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');
$user = $auth->getUser();
$user_data=$user->getUserRow();
$userID=$user_data['userID'];
$userName=$user_data['userName'];


$orgSelection = (int) $user->getData('userOrganizationIdSelect');

// if not admin get orgId from user
if ($orgSelection=='0'){
	$orgSelection = (int) $user->getData('userOrganizationID');
}

$con = db::getDefaultAdapter();
$selectOrg = $con->select()->from('organizations')->where('organizationID = ? ', array($orgSelection));
$resultOrg = $con->query($selectOrg);
$rowOrg = $resultOrg->fetch_array();
$orgName=$rowOrg['organizationName'];


$dbop_logs=new dbop();
$dbop_logs->connect(USER,PASSWORD,"wheeldo_logs",DB_HOST);

$fields=array();
$fields['userID']=$userID;
$fields['userName']=$userName;
$fields['userOrg']=$orgName;
$fields['time']=time();
$fields['ip']=$_SERVER['REMOTE_ADDR'];
$fields['type']=$_POST['type'];
$fields['more']=isset($_POST['more'])?$_POST['more']:"";
$dbop_logs->insertDB("logs",$fields);