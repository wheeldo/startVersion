<?php
set_time_limit(60);
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

function isLocalMachine() {
    if(!isset($_SERVER['SERVER_ADDR'])) {
        return false;
    }

    $server0=explode(".",$_SERVER['SERVER_ADDR']);
    $serverStart=$server0[0];
    if($serverStart=="10" || $serverStart=="127") return true;
    else return false;

} 


function doRequest($url,$postArray) {
    
    
    if(isLocalMachine()) {
       // $url=str_replace(".com", ".com.loc", $url);
    }
    
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postArray);
    $response = curl_exec($ch); 
    if($response === false)
        return 'Curl error: ' . curl_error($ch);
    curl_close($ch);
    return $response;
}




$sendAlertEmail=false;
$withTokenChecks=true;
$loginDet=array();
$loginDet['login']='y';
$loginDet['key']='x';
$testResults=array();

/* 
 * alive test:
 * expect to get: {"login":"y","key":"x"}
*/

$testResults['alive']['status']="ok";

$expect='{"login":"y","key":"x"}';

$url="http://api.wheeldo.com/API.php";
$response=doRequest($url,$loginDet);
if($response!=$expect) {
    $testResults['alive']['status']="faild";
    $testResults['alive']['address']=$url;
    $testResults['alive']['response']=$response;
    $sendAlertEmail=true;
}


/* 
 * getCode:
 * expect to get: {"code":"$some_hash_code"}
*/


$testResults['getCode']['status']="ok";
$postArray=array();
$postArray['request']='getCode';
$postArray['function_data[appID]']=1; 
$postArray['function_data[userID]']=71;
$postArray = array_merge($postArray,$loginDet);
$url="http://api.wheeldo.com/APIAD.php";

$response=doRequest($url,$postArray);
$response_as_array=json_decode($response,true);
if(!isset($response_as_array['code']) || strlen($response_as_array['code'])<10) {
    $testResults['getCode']['status']="faild";
    $testResults['getCode']['address']=$url;
    $testResults['getCode']['response']=$response;
   $sendAlertEmail=true;
   $withTokenChecks=false;
}



////////////////////////////////////////////////////////////////////////////////////////////////////////
if($withTokenChecks):
// if false skip all the checks from here:
    
    
$appID=1;
$token=$response_as_array['code'];
$sessionO=WheelDoSession::createSession($token,$appID);
$session=$sessionO->getSessionData();



/* 
 * canAccess:
 * expect to get: {"canAccess":"1"}
*/
$testResults['canAccess']['status']="ok";
$postArray=array();
$postArray['request']='canAccess';
$postArray['accessType']=2; // 1 for edit, 2 for view only

$postArray = array_merge($postArray,$loginDet);
$postArray = array_merge($postArray,$session);

$expect='{"canAccess":1}';
$url="http://api.wheeldo.com/API.php";
$response=doRequest($url,$postArray);
if($response!=$expect) {
    $testResults['canAccess']['status']="faild";
    $testResults['canAccess']['address']=$url;
    $testResults['canAccess']['response']=$response;
    $sendAlertEmail=true;
}


/* 
 * getUser:
 * expect to get: {"name":"cto","photo":"http:\/\/api.wheeldo.com\/userImages\/user_71.jpg","ID":71,"teamID":68,"email":"cto@wheeldo.com"}
*/
$testResults['getUser']['status']="ok";
$postArray=array();
$postArray['request']='getUser';
$postArray['accessType']=2; // 1 for edit, 2 for view only

$postArray = array_merge($postArray,$loginDet);
$postArray = array_merge($postArray,$session);

$url="http://api.wheeldo.com/API.php";
$response=doRequest($url,$postArray);

$response_as_array=json_decode($response,true);
if(!isset($response_as_array['name'])) {
    $testResults['getUser']['status']="faild";
    $testResults['getUser']['address']=$url;
    $testResults['getUser']['response']=$response;
    $sendAlertEmail=true;
}


/* 
 * getTeam:
 * expect to get: [{"name":"aviadblu","photo":"http:\/\/api.wheeldo.com\/userImages\/user_71.jpg","ID":71},{"name":"yaron","photo":"","ID":106},{"name":"Aviad blu","photo":"","ID":105},{"name":"Reut Yehodai","photo":"","ID":104},{"name":"Behind Methods","photo":"","ID":66}]
*/
$testResults['getTeam']['status']="ok";
$postArray=array();
$postArray['request']='getTeam';

$postArray = array_merge($postArray,$loginDet);
$postArray = array_merge($postArray,$session);

$url="http://api.wheeldo.com/API.php";
$response=doRequest($url,$postArray);
$response_as_array=json_decode($response,true);
if(!isset($response_as_array[0])) {
    $testResults['getTeam']['status']="faild";
    $testResults['getTeam']['address']=$url;
    $testResults['getTeam']['response']=$response;
    $sendAlertEmail=true;
}



/* 
 * teamByID:
 * expect to get: [{"name":"aviadblu","photo":"http:\/\/api.wheeldo.com\/userImages\/user_71.jpg","ID":71},{"name":"yaron","photo":"","ID":106},{"name":"Aviad blu","photo":"","ID":105},{"name":"Reut Yehodai","photo":"","ID":104},{"name":"Behind Methods","photo":"","ID":66}]
*/
$testResults['teamByID']['status']="ok";
$postArray=array();
$postArray['request']='teamByID';
$postArray['function_data[teamID]']=68;

$postArray = array_merge($postArray,$loginDet);
$postArray = array_merge($postArray,$session);

$url="http://api.wheeldo.com/APIAD.php";
$response=doRequest($url,$postArray);
$response_as_array=json_decode($response,true);

if(!isset($response_as_array[0])) {
    $testResults['teamByID']['status']="faild";
    $testResults['teamByID']['address']=$url;
    $testResults['teamByID']['response']=$response;
    $sendAlertEmail=true;
}



/* 
 * teamByAppID:
 * expect to get: [{"name":"aviadblu","photo":"http:\/\/api.wheeldo.com\/userImages\/user_71.jpg","ID":71},{"name":"yaron","photo":"","ID":106},{"name":"Aviad blu","photo":"","ID":105},{"name":"Reut Yehodai","photo":"","ID":104},{"name":"Behind Methods","photo":"","ID":66}]
*/

$testResults['teamByAppID']['status']="ok";
$postArray=array();
$postArray['request']='teamByAppID';
$postArray['function_data[appID]']=1161;

$postArray = array_merge($postArray,$loginDet);
$postArray = array_merge($postArray,$session);

$url="http://api.wheeldo.com/APIAD.php";
$response=doRequest($url,$postArray);
$response_as_array=json_decode($response,true);
//echo $response;
//echo "<pre>";
//print_r($response_as_array);

if(!isset($response_as_array[0])) {
    $testResults['teamByAppID']['status']="faild";
    $testResults['teamByAppID']['address']=$url;
    $testResults['teamByAppID']['response']=$response;
    $sendAlertEmail=true;
}



/* 
 * getUserOrgLogo:
 * expect to get: http://my.wheeldo.com/uploads/organizations_logos/default.png || http://my.wheeldo.com/uploads/organizations_logos/Wheeldo.jpg
*/

$testResults['getUserOrgLogo']['status']="ok";
$postArray=array();
$postArray['request']='getUserOrgLogo';
$postArray['function_data[userID]']=71;

$postArray = array_merge($postArray,$loginDet);
$postArray = array_merge($postArray,$session);

$url="http://api.wheeldo.com/APIAD.php";
$response=doRequest($url,$postArray);

if($response!="http://my.wheeldo.com/uploads/organizations_logos/default.png" && $response!="http://my.wheeldo.com/uploads/organizations_logos/Wheeldo.jpg") {
    $testResults['getUserOrgLogo']['status']="faild";
    $testResults['getUserOrgLogo']['address']=$url;
    $testResults['getUserOrgLogo']['response']=$response;
    $sendAlertEmail=true;
}


/* 
 * sendMail:
 * expect to get: {"success":1}
*/
$testResults['sendMail']['status']="ok";
$postArray=array();
$postArray['request']='sendMail';
$postArray['function_data[appID]']=1;
$postArray['function_data[userID]']=1;
$postArray['function_data[subject]']="Unit test";
$postArray['function_data[content]']="Unit test";

$postArray = array_merge($postArray,$loginDet);
$postArray = array_merge($postArray,$session);

//$expect='{"success":1}';
//$url="http://api.wheeldo.com/APIAD.php";
//$response=doRequest($url,$postArray);
//if($response!=$expect) {
//    $testResults['sendMail']['status']="faild";
//    $testResults['sendMail']['address']=$url;
//    $testResults['sendMail']['response']=$response;
//    $sendAlertEmail=true;
//}



/* 
 * sendMailFromUser:
 * expect to get: {"success":1}
*/
$testResults['sendMailFromUser']['status']="ok";
$postArray=array();
$postArray['request']='sendMailFromUser';
$postArray['function_data[appID]']=1;
$postArray['function_data[userFromID]']=71;
$postArray['function_data[userID]']=1;
$postArray['function_data[subject]']="Unit test";
$postArray['function_data[content]']="Unit test";

$postArray = array_merge($postArray,$loginDet);
$postArray = array_merge($postArray,$session);

$expect='{"success":1}';
$url="http://api.wheeldo.com/APIAD.php";
/*
$response=doRequest($url,$postArray);
if($response!=$expect) {
    $testResults['sendMailFromUser']['status']="faild";
    $testResults['sendMailFromUser']['address']=$url;
    $testResults['sendMailFromUser']['response']=$response;
    $sendAlertEmail=true;
}
*/


/* 
 * sendMailFromName:
 * expect to get: {"success":1}
*/
$testResults['sendMailFromName']['status']="ok";
$postArray=array();
$postArray['request']='sendMailFromName';
$postArray['function_data[appID]']=1;
$postArray['function_data[fromName]']='Wow name';
$postArray['function_data[userID]']=1;
$postArray['function_data[subject]']="Unit test";
$postArray['function_data[content]']="Unit test";

$postArray = array_merge($postArray,$loginDet);
$postArray = array_merge($postArray,$session);

$expect='{"success":1}';
$url="http://api.wheeldo.com/APIAD.php";
/*
$response=doRequest($url,$postArray);

if($response!=$expect) {
    $testResults['sendMailFromName']['status']="faild";
    $testResults['sendMailFromName']['address']=$url;
    $testResults['sendMailFromName']['response']=$response;
    $sendAlertEmail=true;
}
*/

/* 
 * sendMailAddress:
 * expect to get: {"success":1}
*/
$testResults['sendMailAddress']['status']="ok";
$postArray=array();
$postArray['request']='sendMailAddress';
$postArray['function_data[appID]']=1;
$postArray['function_data[email]']='cto@wheeldo.com';
$postArray['function_data[userID]']=1;
$postArray['function_data[subject]']="Unit test";
$postArray['function_data[content]']="Unit test";

$postArray = array_merge($postArray,$loginDet);
$postArray = array_merge($postArray,$session);

$expect='{"success":1}';
$url="http://api.wheeldo.com/APIAD.php";
/*
$response=doRequest($url,$postArray);

if($response!=$expect) {
    $testResults['sendMailAddress']['status']="faild";
    $testResults['sendMailAddress']['address']=$url;
    $testResults['sendMailAddress']['response']=$response;
    $sendAlertEmail=true;
}
*/

//sendMailToAddress


//sendMailFromAddress


//getCode




endif; // if there is a token
////////////////////////////////////////////////////////////////////////////////////////////////////////
echo "<pre>";
print_r($testResults);




$content='<table border="1">';
$content.="<tr>";
   $content.="<th>Request</th>";         
   $content.="<th>Address</th>"; 
   $content.="<th>Response</th>"; 
$content.="</tr>";

if($sendAlertEmail) {
    foreach($testResults as $request=>$res):
        if($res['status']!="ok"):
            $content.="<tr>";
                $content.="<td>{$request}</td>";
                $content.="<td>{$res['address']}</td>";
                $content.="<td>{$res['response']}</td>";
            $content.="</tr>";
        endif;
    endforeach;

    $content.="</table>";
    $parameters=array();
    $parameters['content']=$content;
    
    $body=  file_get_contents("Emails/unitTest.html");
    foreach($parameters as $key=>$value):             
        $body=str_replace("[".$key."]", $value,$body);      
    endforeach;
    
    $subject="API Unit test error";
    $to="aviadblu@gmail.com";
    
    
    // To send HTML mail, the Content-type header must be set
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

    // Additional headers
    $headers .= "To: $to <$to>" . "\r\n";
    $headers .= 'From: Wheeldo System <cto@wheeldo.com>' . "\r\n";

    // Mail it
    //mail($to, $subject, $body, $headers);

}