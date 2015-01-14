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

$ds=DIRECTORY_SEPARATOR;

$parameters=array();
$parameters['user_name']=$user_data['userName'];;
$parameters['user_email']=$user_data['userEmail'];;
$parameters['feedback']=isset($_POST['feedback'])?$_POST['feedback']:"";    
$parameters['logo_img']="../Emails/images/banner.png";
$parameters['cto_img']="../Emails/images/CTO.jpg";       
        
$Body = file_get_contents(dirname(__FILE__)."{$ds}..{$ds}Emails{$ds}feedback.html");
foreach($parameters as $key=>$value):             
    $Body=str_replace("[".$key."]", $value,$Body);      
endforeach;


echo $Body;
$subject="Feddback from system";

$feedbackRec=array();
$feedbackRec[]="aviadblu@gmail.com";
$feedbackRec[]="irad@wheeldo.com";
email::semailFrom("Wheeldo system", $feedbackRec, $subject, $Body);