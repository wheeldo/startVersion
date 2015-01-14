<?php
require_once('modules/modules.php');

$email=$_POST['email'];

$user=$dbop->selectAssocRow("users","WHERE `userEmail`='{$email}' AND `userUserKindID`>1");
if($user) {
    
    
    
    $newPass = substr( sha1( time() ), 0, 8 );
    $hashedPass = hash( 'SHA256', $newPass, false );
    
    $dbop->updateDB("users",array("userPassword"=>$hashedPass),$user['userID'],"userID");
    
    
    
    $Body=  file_get_contents(dirname(__FILE__)."/Emails/recover_password.html");
    $parameters=array();  
    $parameters['org_logo']="img/logo.png";
    $parameters['link']="http://my.wheeldo.com";
    $parameters['password']=$newPass;
    $parameters['name']=ucfirst($user['userName']);
    foreach($parameters as $key=>$value):             
            $Body=str_replace("[".$key."]", $value,$Body);      
    endforeach;
    ob_start();
        email::semailFrom("Wheeldo system", $email, "Password recovery", $Body);
    ob_end_clean();
    
    
    echo '$("#email_recovery").val("");';
    echo '$("#email").val("'.$email.'");';
    echo 'login();';
    echo '$(".tootltip_email").hide();';
    echo '$("#email").addClass("ok");';
    echo 'alert("New password sent to your email address.");';
}
else {
    echo 'alert("Unknown Email!");';
}