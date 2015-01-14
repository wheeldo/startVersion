<?php

class WheelDoSession {
    private $userToken;
    private $configID;
    private function __construct($userToken,$configID) {
        $this->userToken  = $userToken;
        $this->configID   = $configID;
    }
    public static function createSession($token,$configID){
        return new self($token,$configID);
    }
    public function getSessionData(){
	return $data = array(
            'token'=> $this->userToken,
            'appConfig'	=> $this->configID,
         );
    }
}


function doRequest($url,$postArray) {
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postArray);
    $response = curl_exec($ch); 
    curl_close($ch);
    return $response;
}



$configID=1935;
$token='73bdc7c1160a011aa69dd50f4cf9a9314ad48528afdfb61e9f79ae94eb75baf2';
$session = WheelDoSession::createSession($token, $configID);

$url="http://api.wheeldo.com/API.php";
$postArray=array();
$postArray['login']='y';
$postArray['key']='x';


// check for access:
$postArray=array();
$postArray['request']='canAccess';
$postArray['accessType']=2; // 1 for edit, 2 for view only
$postArray = array_merge($postArray,$session->getSessionData());

$response=doRequest($url,$postArray);
$response_as_array=json_decode($response,true);
var_dump($response_as_array);


// get user data:
$postArray=array();
$postArray['request']='getUser';
$postArray = array_merge($postArray,$session->getSessionData());
$response=doRequest($url,$postArray);
$response_as_array=json_decode($response,true);
var_dump($response_as_array);
