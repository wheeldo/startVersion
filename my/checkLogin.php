<?





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

if(isset($_GET['u'])) {
    $_SESSION['user_redirect']=$_GET['u'];
}

if (!($_SESSION['auth'] && Auth::canAccess($auth, 'checkLogin')))
{ 
    header("location:/login");
    //echo '<script>window.location = "loginWindow.php";</script>';
}

?>