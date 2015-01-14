<?
require_once('modules/modules.php');
setcookie("login_wheeldo", "", time()-3600);
$auth = Auth::isLogin();
if ($auth){
    unset($_SESSION['auth']);
    $auth->logout();
}
header("location:/login");