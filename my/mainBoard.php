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


$userKind = (int) $user->getData('userUserKindID');
?>
<div id="content" ng-init="init();<?if(isset($_SESSION['playIntro']) && $_SESSION['playIntro']==1) {?>runIntro();<?unset($_SESSION['playIntro']);}?>">
                <div class="right_info">
                    <div class="padding10_8">
                        <div class="expand_view">
                            Expand View
                        </div>
                        
                        <h4>My games</h4>
                        <div id="logs">
                            <input type="text" class="search autoTxT" id="search_logs" title="Type to search" />
                            <select id="logs_game" class="nooutline">
                                <option value="0">All games</option>
                                <?
                                if(!isset($extra_search))
                                    $extra_search="";
                                $ans=$dbop->selectDB("apps","WHERE `appInactive`='0' AND (`appPrivate`='0' OR `appPrivate`='$orgSelection') $extra_search");
                                for($i=0;$i<$ans['n'];$i++) {
                                   $app=mysql_fetch_assoc($ans['p']);
                                   $appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$app['appID']}'");
                                   ?>
                                <option value="<?=$app['appID']?>"><?=ucfirst($appInfo['name']);?></option>
                                <?
                                }
                                ?>
                                
                            </select>
                            <br class="clr" />
                            <div id="logs_ajax">
<!--                                ajax logs-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="data appStore">
				<div class="headline">
                                    <h1>3 easy steps to create your social learning game:</h1>
					Choose a game, enter your content and send it to your team.   <a class="system_demo event_log wheeldoPopUp" data-type="player" player="http://player.vimeo.com/video/74559233?autoplay=1" log_more="" log_type="system_demo" href="javascript:void(0)" >Intro</a>
				</div>
<!--                    search bar -->
                    <div class="search_bar">
                        <input type="text" class="search app_search autoTxT" id="search_app" title="Type to search" />
                        <select id="app_category" class="nooutline">
                            <option value="0">All game categories</option>
                            <?
                            $ans=$dbop->selectDB("categories");
                            for($i=0;$i<$ans['n'];$i++) {
                               $category=mysql_fetch_assoc($ans['p']);
                               ?>
                            <option value="<?=$category['categoryID']?>"><?=ucfirst($category['categoryName']);?></option>
                            <?
                            }
                            ?>
                            
                        </select>
                        <a href="javascript:void(0)" class="re_expand_view"></a>
                    </div>
<!--                    search bar end -->
                    <div id="apps_ajax">
<!--                        ajax apps-->
                    </div>           
                </div>
                <div class="data reportsData">
                    <div class="reportHeader">
                        <img id="reportAppIcon" src="images/MountaineerIcon.png" />
                        <div class="reportAppData">
                            <h3>Report ::: <span id="appReportName">My copy of bla bla bla</span></h3>
                            <div class="info"><label>Started On:</label> <span id="startedOn"></span></div>
                            <div class="info"><label>Playing Team:</label> <span id="playingTeam"></span></div>
                        </div>
                        <br class="clr" />
                    </div>
                    <iframe id="reportFrame" src="http://wheeldo.com.loc/apps/Mountaineer/report/1023"></iframe>
                </div>
                <div class="data users_logs_data">
                </div>
                <br class="clr" />
            </div>
<script type="text/javascript" src="plugins/iscroll-4/src/iscroll.js"></script>


<div class="playNow">
    <iframe id="demoIframe" src=""></iframe>
</div>