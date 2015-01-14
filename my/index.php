<?
require_once('modules/modules.php');
require_once('checkLogin.php');



$user = $auth->getUser();
$user_data=$user->getUserRow();

$userKind = $user->getData('userUserKindID');
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
$campaign=false;
if($rowOrg['organizationArea']!="") {
    $campaign=$rowOrg['organizationArea'];
}

$userKind = (int) $user->getData('userUserKindID');

if(isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME']=="https") {
    $DATA_PATH="https://".$_SERVER['HTTP_HOST']."/";
}
else {
    $DATA_PATH="http://".$_SERVER['HTTP_HOST']."/";
}

$DATA_PATH="http://".$_SERVER['HTTP_HOST']."/";
$DATA_PATH_SSL="https://".$_SERVER['HTTP_HOST']."/";

$token=$dbop->selectAssocRow("tokens","WHERE `tokenUserID`='{$user->getData('userID')}'");




// trial check //
$trial=false;
$expired=0;
//$account=$dbop->selectAssocRow("accounts","WHERE `orgID`='{$orgSelection}'");

//Accounts::setTokensLimit($orgSelection,60);
//Accounts::useToken($orgSelection);

//Accounts::insertTokenHistoryRow(array('orgID'=>$orgSelection,'time'=>time(),'userID'=>$user_data['userID'],'userEmail'=>$user_data['userEmail'],'copyID'=>7777,'copyName'=>'name'));

$account=Accounts::getOrgAccount($orgSelection);
$tokensMode=false;
switch((int)$account['pricingPackage']):
    case 0:
        $tokensMode=true;
    break;
endswitch;
//$tokens_left=Accounts::getTokensLeft($orgSelection);


//var_dump($account);

if($account['pricingPackage']=="0") {
    $trial=true;
    $left=$account['validUntil']-time();
    if($left<0) {
        $expired=1;
    } 
    else {
        $leftDays=floor($left/3600/24);
    }
}
$trial=false;
$expired=0;


?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="icon" href="img/favicon.ico" type="image/x-icon" />
        <!--[if lt IE 9]>
                <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
        <![endif]-->
        
        <title>Wheeldo</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">        
        <!-- Le styles -->
        
        <link rel="stylesheet" href="vendor/bootstrap-2.3.1/css/bootstrap-select.min.css" media="screen" />
        <link rel="stylesheet" href="vendor/bootstrap-2.3.1/css/bootstrap.css" media="screen" />
        <link rel="stylesheet" href="vendor/bootstrap-2.3.1/css/bootstrap-responsive.min.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/wheeldo_slide.css">
        
        <link type="text/css" href="vendor/jscrollpane/style/jquery.jscrollpane.css" rel="stylesheet" media="all" />
        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script type="text/javascript" src="js/jquery-1.8.3.js"></script>
        <script src="js/wheeldo_slide.js"></script>
        <script src="js/wheeldo_autosave.js"></script>
<!--        <script type="text/javascript" src="js/jquery1.9.1.js"></script>-->
<!--        <script type="text/javascript" src="js/jquery-2.0.3.min.js"></script>-->
        <script type="text/javascript">
            var hateIe=false;
            var token='<?=$token['tokenVal']?>';
            var userID=<?=$user->getData('userID')?>;
            var DATA_PATH="<?=$DATA_PATH?>";
            var DATA_PATH_SSL="<?=$DATA_PATH_SSL?>";
            
            var userID=<?=$user_data['userID']?>;
            var trial_expired=false;
            $(document).ready(function() {
                    logoutListen();     
            });
            
            var tm=false;
            var devMode=false;
            <?if($tokensMode){?>
                devMode=true;
                tm=true;
            <?}?>
                
            var uk=<?=$userKind?>;
            
        </script>
        <!--[if lte IE 9]>
        <script type="text/javascript">
            hateIe=true;
        </script>
        <![endif]-->
       <!--[if lte IE 8]>
        <script type="text/javascript">
        $(document).ready(function() {
            //popText('You are using an outdated version of Internet Explorer.<br /> To better serve you we recommend that you will use one of the following browsers: <ul><ol>Mozila FireFox.</ol><ol>Google Chrome.</ol><ol>Safari.</ol><ol>Internet Explorer 9/10.</ol></ul>');
        });
        </script>
       <![endif]-->
        
        <!--iscroll-->
            
    <!-- end iscroll-->
        <script type="text/javascript" src="js/custom.js?t=<?=time()?>"></script>
        <script type="text/javascript" src="js/ajax.js?t=<?=time()?>"></script>
        
<!--        fancybox-->
        <link rel="stylesheet" href="plugins/fancybox/jquery.fancybox.css?v=2.1.4" type="text/css" media="screen" />
        <script type="text/javascript" src="plugins/fancybox/jquery.fancybox.pack.js?v=2.1.4"></script>
<!--        end fancybox--> 

        <link rel="stylesheet" href="css/wheeldoSlider.css" type="text/css" media="screen" />
        <script type="text/javascript" src="js/wheeldoSlider.js"></script>
        
<!--        ui-->

       <link rel="stylesheet" href="css/jquery-ui.css" />
       <script src="js/jquery-ui.js"></script>

<!--       end ui  -->

        <link href="css/style.css?t=<?=time()?>" media="screen" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="js/customfile.js"></script>
        
        <script src="vendor/ckeditor/ckeditor.js"></script>
        <script src="vendor/ckeditor/adapters/jquery.js"></script>
        
        
        <?if(!AvbDevPlatform::isLocalMachine()) {?>
        <!-- Start of Woopra Code -->
<!--        <script>
        (function(){
        var t,i,e,n=window,o=document,a=arguments,s="script",r=["config","track","identify","visit","push","call"],c=function(){var t,i=this;for(i._e=[],t=0;r.length>t;t++)(function(t){i[t]=function(){return i._e.push([t].concat(Array.prototype.slice.call(arguments,0))),i}})(r[t])};for(n._w=n._w||{},t=0;a.length>t;t++)n._w[a[t]]=n[a[t]]=n[a[t]]||new c;i=o.createElement(s),i.async=1,i.src="//static.woopra.com/js/w.js",e=o.getElementsByTagName(s)[0],e.parentNode.insertBefore(i,e)
        })("woopra");

        woopra.config({
            domain: 'my.wheeldo.com',
            idle_timeout: 1800000
        });
        // Make sure you identify the visitor before the track() function.
        woopra.identify({
           name: '<?=$user->getData('userName'); ?>',
           email: '<?=$user->getData('userEmail'); ?>',
           company: '<?=$orgName?>'
        });
        woopra.track();
        </script>-->
        <!-- End of Woopra Code -->
        <?}?>
    </head>
    <body ng-app="wheeldoApp">
        <div style="font-size:6px;position:absolute;left:0;"><?=$user->getData('userID')?></div>
<!--        <div id="feedback_legs"></div>-->
        <div id="feedback">
            <div class="feedbackWrap">

                <div class="content">
                    <img class="title" src="img/feedback.png" />
                    <div class="form">
                        <textarea id="feedback_post" class="autoTxT" title="Write your feedback here."></textarea>
                        <a href="javascript:void(0)" class="post">Send</a>
                        <a href="javascript:void(0)" class="close">X</a> 
                    </div>
                </div>
            </div>
       </div>
        <div id="wrapper">
            <?if($userKind>=4) { ?>
            <div id="menager_panel">
                <div class="content">
                        <select id="org_select">
                            <?
                            $s="SELECT
                            organizations.organizationID,
                            organizations.organizationName,
                            organizations.organizationImg
                            FROM
                            organizations ORDER BY organizations.organizationName ASC";
                            $p=mysql_query($s);
                            $n=mysql_num_rows($p);
                            for($i=0;$i<$n;$i++):
                                $r=mysql_fetch_assoc($p);
                            ?>
                                <option value="<?=$r['organizationID']?>" <?=($orgSelection==$r['organizationID'])?'selected="selected"':''?>><?=ucfirst($r['organizationName'])?> (<?=$r['organizationID']?>)</option>
                            <? endfor; ?>
                        </select>
                    
                        <?if($user_data['is_manger']==1){?>
                            <a class="main_link users_logs" href="#/users_logs">Users logs</a>
                            <a class="main_link users_logs" href="#/users_manage">Manage Users</a>
                            <a class="main_link users_logs" href="#/accounts">Accounts</a>
                        <?}?>
                    
                </div>
                <div class="open_panel"><a href="javascript:void(0)"><img src="img/menagersOpen.png" class="icon_open" /><img src="img/menagersClose.png" class="icon_close" /></a></div>
            </div>
            <?}?>
            <div id="header" ng-controller="headerController" ng-init="header_init()">
                <div class="logo">
                    <a href="/"><img src="img/logo.png" alt="logo" /></a>
                    <?=$orgName?>
                </div>
                <div class="top_right" >
                    <div class="topMenu">
                        <a href="#/gameboard" class="link" ng-class="{'active':isActive('/')}">
                            Gameboard
                        </a>
<!--                        <a href="#/teams" class="link" ng-class="{'active':isActive('/teams')}">
                            Teams
                        </a>  -->
                        <? if($user->getData('ls_is_super_admin')=="1"){?>
                        <a href="#/admins" class="link" ng-class="{'active':isActive('/admins')}">
                            Admins
                        </a>  
                        <?}?>
                    </div>
                    <div class="user ddMenuTrigger" ng-init="setDD()">
                            <a class="trigger" href="javascript:void(0)" ></a>
                            <?=$user->getData('userEmail'); ?>
                            <img src="img/drop_down_icon.png" />
                        <div class="ddMenuCont">
                            <a href="javascript:void(0)" class="dd_link" ng-click="openChangePassword()">Change password{{test}}</a>
                            <a href="javascript:void(0)" class="dd_link" ng-click="openMySettings()">My settings</a>
                            <a href="javascript:logOut()" class="dd_link">Sign out</a>
                        </div>
                    </div>
<!--                    <div class="saparetor"></div>
                    <div>
                        <a href="javascript:void(0)"><img src="img/settings_icon.png" />&nbsp;<img src="img/drop_down_icon.png" /></a>
                    </div>-->
                    <? if($campaign){?>
                    <div class="saparetor"></div>
                    <div>
                        <a href="javascript:void(0)" id="help_icon"><img style="height:20px" src="img/<?=$campaign?>.png" /></a>
                    </div> 
                    <?}?>
                    <div class="saparetor"></div>
                    <div>
                        <a href="javascript:void(0)" id="help_icon"><img src="img/help_icon.png" /></a>
                    </div>
                </div>
                
                <?if($tokensMode){?>
                <div class="tokens">
                    You have <span class="token_c"></span> <span class="only_one">token</span><span class="more_then_one">tokens</span> left <a href="#/purchase" class="buy_now">Buy Now</a>
                </div>
                <?}?>
            </div>

            <div ng-view></div>

        </div>
        <div id="fadeBg">
            <div class="popupWrap">
                <div class="close"></div>
                <div id="popUpData">
                    test
                </div>
            </div>
        </div>
        <div class="fadeBg">
            
        </div>
        <div class="saving">
        </div>
        <div class="wait">
            <img src="img/ajax-loader-large.gif" />
        </div>
        <script src='vendor/angularjs-1.0.7/angular.min.js'></script>
        <script src='vendor/angularjs-1.0.7/angular-resource.min.js'></script>
        <script src='vendor/angularjs-1.0.7/angular-strap.js'></script>
        <script src="vendor/bootstrap-2.3.1/js/bootstrap-select.min.js"></script>
        <script src="vendor/ui.bootstrap/ui-bootstrap-tpls-0.6.0.min.js"></script>
        <script src="vendor/angular-google-chart-gh-pages/ng-google-chart.js"></script>
        <!-- App libs -->
        <script src="vendor/spectrum/spectrum.js"></script>
        <link rel="stylesheet" href="vendor/spectrum/spectrum.css" />
        <script src="vendor/jscrollpane/script/jquery.mousewheel.js"></script>
        <script src="vendor/jscrollpane/script/jquery.jscrollpane.min.js"></script>
        <script src="app/app.js"></script>
        <script src="app/controllers/controllers.js?t=<?=time();?>"></script>
        <script src="app/controllers/myGamesController.js?t=<?=time();?>"></script>
        <script src="app/services/WheeldoService.js?t=<?=time();?>"></script>
        <script src="app/filters/paginator.js"></script>
        <script src="app/directives/formValidator.js"></script>
        <script src="vendor/angular-file-upload-master/angular-file-upload.js"></script>
    </body>
</html>