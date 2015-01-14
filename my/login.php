<?
require_once('modules/modules.php');
if(isset($_POST['email']) && isset($_POST['password']))
{
	$auth = Auth::authFromLogin($_POST['email'], $_POST['password']);
        
}else{
        
	$auth = Auth::isLogin();
}



if($auth) {
    $_SESSION['auth']=$auth;
    $userID=$auth->getUserID();
    // set cookie:
    if($_POST['remember_me']=="true") {
        $hashedPass = hash('SHA256', $_POST['password'],false);
        setcookie("login_wheeldo", $userID, time()+3600*24*14);
    }
    else {
        setcookie("login_wheeldo", "", time()-3600);
    }
    //////////////
    
    die("moveToSystem();");
}
else {
    die("alert('Login error!')");
}