<?
require_once('../modules/modules.php');
if(isset($_POST['email']) && isset($_POST['password']))
{
	$auth = Auth::authFromLogin($_POST['email'], $_POST['password']);
        
}else{
        
	$auth = Auth::isLogin();
}


if($auth) {
    $_SESSION['auth']=$auth;
}
//unset($_SESSION['auth']);


if (!($_SESSION['auth'] && Auth::canAccess($auth, 'checkLogin')))
{
    echo 0;
}
else {
    echo 1;
}



?>