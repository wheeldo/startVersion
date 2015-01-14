<?

//echo hash('SHA256', "values123",false);
require_once('modules/modules.php');

//$auth = Auth::isLogin();
//if ($auth){
//    unset($_SESSION['auth']);
//    $auth->logout();
//}

if(isset($_GET['hash'])) {
    $ex=explode("___",$_GET['hash']);
    $auth = Auth::authFromLogin($ex[0], $ex[1],true);
    $_SESSION['auth']=$auth;
    $_SESSION['playIntro']=1;
    header("location:/");
    die();
//    var_dump($auth);
//    echo $ex[0]."<br>";
//    echo $ex[1]."<br>";
}

//echo hash( 'SHA256', "wheeldo123", false );

// login from cookie:
$autoLogin=false;
if(isset($_COOKIE['login_wheeldo'])) {
    $userID=$_COOKIE['login_wheeldo'];
    $auth = Auth::authFromUserID($userID);
    if($auth)
        $autoLogin=true;
}

$address="http://".$_SERVER['HTTP_HOST']."/";
?>
<!doctype html>
<html ng-app="loginApp">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="icon" href="img/favicon.ico" type="image/x-icon" />
<title>Wheeldo | Login</title>
<meta name="description" content="">
<meta name="keywords" content="">
<link href="css/wheeldoAdmin.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="wheeldoAdmin_ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!-- jquery from google -->
<script type="text/javascript" src="js/jquery1.9.1.js"></script>
 
<script src='vendor/angularjs-1.0.7/angular.min.js'></script>
<script src='vendor/angularjs-1.0.7/angular-resource.min.js'></script>
<!-- form clientSide validation -->
<script type="text/javascript" src="js/login.js?t=<?=time()?>"></script> 
<script type="text/javascript">
            var address='<?=$address?>';
            <?if($autoLogin){?>
                moveToSystem();
            <?}?>

</script>
<link rel="stylesheet" href="vendor/bootstrap-2.3.1/css/bootstrap.css" media="screen" />
<link rel="stylesheet" href="vendor/bootstrap-2.3.1/css/bootstrap-responsive.min.css" media="screen" />
<script src="vendor/bootstrap-2.3.1/js/bootstrap.min.js"></script>
<style type="text/css">
    div.row {
        float:left;
        margin-left:168.5px;
        width:360px;
        margin-bottom:10px;
        height:40px;
    }
    
    div.row.checkbox {
        height:20px;
        margin-bottom:2px;
    }
    
    div.row input[type="text"],
    div.row input[type="email"],
    div.row input[type="password"] {
       width:350px;
       padding:7px 5px; 
    }
    
    .systemInput {
        position:absolute;
        border: 1px dashed #ABABAB;
        color: #8D8D8D;
        min-height: 26px;
        padding-left:5px !important;
        z-index:2;
        background-color:transparent;
    }
    
    
    .systemInput.ok {
        border: 1px solid #000000;
        color: #000000;
    }
    img {
        border:0;
    }
    
    .notes,
    .notes_recovery{
        color:red;
    }
    
    .notes div,
    .notes_recovery div{
        border:1px solid red;
        padding:3px;
        background-color:#FFE06A;
        font-size:12px;
        margin-bottom:5px;
        opacity:0.7;
        filter:alpha(opacity=70);
    }
    
    .forgot_password {
        text-align:right;
        height:14px !important;
    }
    
    .forgot_password a{
        color: #29ADE3;
        font-family: 'l';
        font-size: 14px;
        text-decoration:none;
    }
    
    #loginForm,
    #password_recovery{
        margin-top:50px;
    }
    
    #loginForm h1,
    #password_recovery h1{
        margin-bottom:20px;
    }
    
    a.actionButton {
        display:block;
        width:100%;
        height:40px;
        line-height:40px;
        background-color:#29ADE3;
        color:#ffffff;
        text-align:center;
        text-decoration:none;
        -webkit-border-radius: 5px;
        border-radius: 5px; 
    }
    
    div.wait {
        color:#29ADE3;
        text-align:center;
        font-size:18px;
        display:none;
    }
    
    #password_recovery {

    }
</style>
</head>
<body style="background-color:#f0f0f0;">

	<div id="loginContainer" ng-controller="loginController">
            <div id="secureWrap">
                <!-- START MCAFEESECURE CODE -->
                 <a target="_blank" href="https://www.mcafeesecure.com/RatingVerify?ref=my.wheeldo.com"><img width="115" height="32" border="0" src="//images.mcafeesecure.com/meter/my.wheeldo.com/12.gif" alt="McAfee SECURE sites help keep you safe from identity theft, credit card fraud, spyware, spam, viruses and online scams" oncontextmenu="alert('Copying Prohibited by Law - McAfee SECURE is a Trademark of McAfee, Inc.'); return false;"></a>
                 <!-- END MCAFEESECURE CODE --> 
             </div>
		<img src="images/logo.png" id="logo"/>
		
<!--		<form id="loginForm" name="loginForm" method="post" action="/cl">-->
                <div id="loginForm" ng-init="initLogin()">
                    <h1>
                            Sign in to Wheeldo
                    </h1>
                    <div class="row">
                        <input class="inputMark" type="email" placeholder="Email" name="email" ng-model="email" id="email" ng-minlength=3 ng-maxlength=120 maxlength="120" required ng-focus ng-init="setSaveCopyName()" />
                    </div>
		
                    <div class="row">
                        <input class="inputMark" type="password" placeholder="Password" name="password" ng-model="password" id="password" ng-minlength=3 ng-maxlength=30 maxlength="30" required ng-focus ng-init="setSaveCopyName()" />
                    </div>
                    <div class="row checkbox">
                        <label>
                            <input type="checkbox" id="remember_me" checked="" />
                            Remember me (14 days)
                        </label>
                    </div>
                    <div class="row forgot_password">
                        <a href="javascript:password_recovery()">Forgot your password?</a>
                        <br class="clr" />
                    </div>
                
                
                    <div class="row"><a class="actionButton" id="login" href="javascript:loginCheck()">Login</a></div>
                    
                    <div class="row notes"></div>
                </div>

                <div id="password_recovery">
                    <h1>
                            Password Recovery
                    </h1>
                    <div class="row">
                        <input class="inputMark" type="email" placeholder="Email" name="email_recovery" ng-model="email_recovery" id="email_recovery" ng-minlength=3 ng-maxlength=30 maxlength="30" required ng-focus ng-init="setSaveCopyName()" />
                    </div>
                    <div class="row hideRecover_button"><a class="actionButton" href="javascript:recoveryCheck()">Send me an new password</a></div>
                    <div class="row wait">Please wait...</div>
                    <div class="row forgot_password"><a href="javascript:login()">Back</a></div>
                    <div class="row notes_recovery"></div>
                </div>
<!--		</form>-->
                <img class="wheeldo_group_login" src="img/wheeldo_group.png" />
	</div>
        <?if($_SERVER['REMOTE_ADDR']=="127.0.0.1" || ($_SERVER['REMOTE_ADDR']=="188.120.158.254"&&isset($_GET['ql']))) {?>
            <script>
//                $("#email").val("aviadblu@gmail.com");
//                $("#password").val("wheeldo123");
//                loginCheck();
                
            </script>
        <?}?>
        
        
        <script>
            var app = angular.module('loginApp', []);
            app.controller('loginController', function ($scope, $templateCache, $http, $location) {
                
                
                $scope.initLogin = function() {
                  
                };
                
            });
        </script>
</body>
</html>
<?
$allowKeys = array(1,2);
$msg = Array();
$msg[1] = 'Email must be under 30 letters';
$msg[2] = 'Password must be under 20 letters';
if ((isset($_GET['msg']))&&(in_array($_GET['msg'],$allowKeys))){
	echo '<script>alert("'.$msg[$_GET['msg']].'");</script>';
}
?>