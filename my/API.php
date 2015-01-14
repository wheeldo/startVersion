<?php
require_once('modules/modules.php');
if(isset($_POST['request']) && isset($_POST['key']) && isset( $_POST['login']))
{
        
	$requestType = $_POST['request'];
	$requestKey  = $_POST['key'];
	$requestLogin   = $_POST['login'];
	
	$con = db::getDefaultAdapter();
	$select = $con->select()->from('apps')->where('appKey = ? AND appLogin = ?', array($requestKey,$requestLogin));
	$result = $con->query($select);
        
        
        
        
	if($result->num_rows == 1)
	{
		$requestAppCopy   = $_POST['appConfig'];
		if(isset( $_POST['token']))
			$requestUserToken = $_POST['token'];
		else
			$requestUserToken = null;
		if(isset($_POST['accessType']))
			$requestAccessType = $_POST['accessType'];
		else 
			$requestAccessType = null;
		if(isset($_POST['score']))
			$requestScore = $_POST['score'];
		else
			$requestScore = null;
		if(isset($_POST['userID']))
			$requesUserID = $_POST['userID'];
		else
			$requesUserID = null;
		if(isset($_POST['subject']))
			$requestSubject = $_POST['subject'];
		else
			$requestSubject = null;
		if(isset($_POST['content']))
			$requestContent = $_POST['content'];
		else
			$requestContent = null;
                
		$api = new APIFunctions($requestUserToken,$requestAppCopy,$requestAccessType,$requestScore,$requesUserID,$requestContent,$requestSubject);
		if(method_exists($api, $requestType) && (isset($requestUserToken) || $requestType === "sendMail"))
		{ 
			echo  $api->$requestType();
		}
		else
		{
			echo 'error';
		}
	}
}
else
{
	var_dump($_POST);
}
