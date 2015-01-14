<?php
$ds=DIRECTORY_SEPARATOR;
$base_path=dirname(__FILE__);
$base_path=$base_path. $ds . "..". $ds . ".." . $ds;
require_once($base_path.'/modules/modules.php');
require_once($base_path.'/checkLogin.php');

// permitions
$permitions = array(5,2);
$auth = Auth::isLogin();
$user = $auth->getUser();
$userId = $user->getID();
$userName = $user->getData('userName');
$userKind = $user->getData('userUserKindID');
$loginUserRow = $user->getUserRow();

// get page vars
$p = isset($_GET['p']) ? dbInterface::real_escape_string($_GET['p']) : 0;
$i = isset($_GET['i']) ? dbInterface::real_escape_string($_GET['i']) : 0;

if(isset($_GET['s'])) 
    $s = dbInterface::real_escape_string($_GET['s']);
else 
    $s='';
// build query and print the rows

if ($user->getData('userOrganizationIdSelect')=='0'){
	$orgId = $user->getData('userOrganizationID');
}else{
	$orgId = $user->getData('userOrganizationIdSelect');
}


if(!isset($_POST['op']))
    die();
$op=$_POST['op'];

$REQ=array();
foreach($_POST as $key=>$value):
    $REQ[mysql_real_escape_string($key)]=mysql_real_escape_string($value);
endforeach;
unset($_POST);

$op($REQ);

//header('Content-Type:application/json');

function stripData($data) {
    $strippedData=str_replace ("___amp___","&",$data);
    return $strippedData;
}

function getApp($REQ) {
    global $dbop;
    $appID=$REQ['appID'];
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    
    $res=array();
    $res['appHttpLogin']=$app['appHttpLogin'];
    echo json_encode($res);
}

function getAppInfo($REQ) {
    global $dbop;
    $appID=$REQ['appID'];
    $appInfo=$dbop->selectAssocRow("appinfo","WHERE `appID`='{$appID}'");
    echo json_encode($appInfo);
}

function getCopyInfo($REQ) {
    global $dbop;
    $copyID=$REQ['copyID'];
    $appInfo=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$copyID}'");
    echo json_encode($appInfo);
}


function getEditData($REQ) {
    
    global $dbop;
    $appID=$REQ['appID'];
    $copyID=$REQ['copyID'];
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    $appEditService=$app['appEditService'];
    $res=null;

    if($appEditService!=""):
        $parameters['appID']=$copyID;
        $url="http://".$app['appAddress'].$appEditService;
        
        foreach($parameters as $key=>$value):             
            $url=str_replace("[".$key."]", $value,$url);      
        endforeach;
        
        $postArray['edit_op']="get";
        $postArray['copyID']=$REQ['copyID'];
        $res=doRequest($url,$postArray);
    endif;
    
    echo $res;
}


function setEditData($REQ) {
    global $dbop;
    $appID=$REQ['appID'];
    $copyID=$REQ['copyID'];
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    $appEditService=$app['appEditService'];
    $res=null;
    if($appEditService!=""):
        $parameters['appID']=$copyID;
        $url="http://".$app['appAddress'].$appEditService;
        
        foreach($parameters as $key=>$value):             
            $url=str_replace("[".$key."]", $value,$url);      
        endforeach;
        
        $postArray['edit_op']="set";
        $postArray['data']=$REQ['data'];
        $postArray['copyID']=$REQ['copyID'];
        $res=doRequest($url,$postArray);
    endif;
    
    echo $res;
}

function getNewCopyID($REQ) {
    
    global $dbop;
    global $userId;
    global $orgId;
    
    $appID=$REQ['appID'];
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    
    $appCopyCopy=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$app['original_copy']}'");

    
    
    $fields=array();
    $fields['appCopyID']=null;
    $fields['appCopyAppID']=$appID;
    $fields['appCopyUserID']=$userId;
    $fields['appCopyOriginalID']=0;
    $fields['appCopyOrganizationID']=$orgId;
    $fields['appCopyTerminate']=1;
    $fields['appCopyTimestamp']=time();
    $fields['appCopyInactive']=0;
    $fields['appCopyMultiple']=1;
    $fields['appCopyPrivate']=1;
    $fields['appCopyName']="My copy of ".$app['appName'];
    $fields['appCopyDescription']="";
    $fields['appCopyIsOnMarket']=0;
    $fields['appCopyAutoEmail']=$appCopyCopy['appCopyAutoEmail'];
    $fields['appCopyAfterSet']=0;
    $fields['appCopyLocked']=0;

    //var_dump($fields);
    $insert=$dbop->insertDB("appCopies",$fields,false);
    
    // duplicate app ::: //

    $appAdress=$app['appAddress'];
    if($app['appDuplicate']!='') {
        $url = 'http://'.$appAdress.$app['appDuplicate'];
        $url=str_replace("[old]",$app['original_copy'],$url);
        $url=str_replace("[new]",$insert,$url);
    }
    else {
        $url = 'http://'.$appAdress."duplicate.php?oldID=".$app['original_copy']."&newID=".$insert;
    }



    $url=str_replace(".com",".".AvbDevPlatform::getServerName(),$url);


    $check=file_get_contents($url);
    
    echo json_encode(array("copyID"=>$insert));
    
    
    
    
    
}



function getAppReport($REQ) {
    global $dbop;
    $appID=$REQ['appID'];
    $copyID=$REQ['copyID'];
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    $appReportService=$app['appReport'];

    $res=null;
    if($appReportService!=""):
        $parameters['appID']=$copyID;
        $url="http://".$app['appAddress'].$appReportService;
        
        foreach($parameters as $key=>$value):             
            $url=str_replace("[".$key."]", $value,$url);      
        endforeach;

        $postArray['copyID']=$REQ['copyID'];
        $res=doRequest($url,$postArray);
    endif;
    
    echo $res;
}


function getTeamsList($REQ) {
    set_time_limit (60*5);
    global $dbop;
    global $orgId;
//    $teams=array();
//    
//    $ans=$dbop->selectDB("teams","WHERE `teamOrganizationID`='{$orgId}' ORDER BY `teamName` ASC");
//    for($i=0;$i<$ans['n'];$i++) {
//        $row=mysql_fetch_assoc($ans['p']);
//        
//        $ans2=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='{$row['teamID']}'");
//        //$ans2['n']=1;
//        $teams[$i]=$row;
//        $teams[$i]['teamID']=$teams[$i]['teamID']."_".$ans2['n'];
//        $teams[$i]['teamName']=$teams[$i]['teamName']." (".$ans2['n']." users)";
//    }
    
    $organization=$dbop->selectAssocRow("organizations","WHERE `organizationID`='{$orgId}'");
    
    $teams=$organization['teamsList']!=""?json_decode($organization['teamsList'],true):array();
    
    header('Content-Type:application/json');
    echo json_encode($teams);
}

function getTeamsListNoC($REQ) {
    global $dbop;
    global $orgId;
    $teams=array();
    
    $ans=$dbop->selectDB("teams","WHERE `teamOrganizationID`='{$orgId}' ORDER BY `teamName` ASC");
    for($i=0;$i<$ans['n'];$i++) {
        $row=mysql_fetch_assoc($ans['p']);
        $teams[$i]=$row;
        $teams[$i]['check_status']=0;
        $teams[$i]['teamID']=$teams[$i]['teamID'];
        $teams[$i]['teamName']=$teams[$i]['teamName'];
    }

    echo json_encode($teams);
}





function getPreviousGames($REQ) {
    global $dbop;
    global $orgId;
    $appID=$REQ['appID'];
    $copyID=$REQ['copyID'];
    
    $copies=array();
    $ans=$dbop->selectDB("appCopies","WHERE `appCopyAppID`='$appID' AND `appCopyOrganizationID`='$orgId'");
    $c=0;
    for($i=0;$i<$ans['n'];$i++) {
        $row=mysql_fetch_assoc($ans['p']);
        if($row['appCopyID']==$copyID)
            continue;
        $copies[$c]['id']=$row['appCopyID'];
        $copies[$c]['name']=$row['appCopyName'];
        $c++;
    }
    echo json_encode($copies);
}

function loadPrevEditData($REQ) {
    global $dbop;
    global $orgId;
    $appID=$REQ['appID'];
    $copyID=$REQ['copyID'];
    $loadGameID=$REQ['loadGameID'];
    
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    
    // duplicate:
    $appDuplicate=$app['appDuplicate'];
    
    
    if($app['appDuplicate']!="") {
        $url="http://".$app['appAddress'].$appDuplicate;
        $url=str_replace("[old]",$loadGameID,$url);
        $url=str_replace("[new]",$copyID,$url);
        
    }
    else {
        $url = 'http://'.$app['appAddress']."duplicate.php?oldID=".$loadGameID."&newID=".$copyID;
    }
    
    
    
    
    
    $url=str_replace(".com",".".AvbDevPlatform::getServerName(),$url);
    
    $check=file_get_contents($url);
    
    ////////////
    
    
    
    
    $appEditService=$app['appEditService'];
    $res=null;
    if($appEditService!=""):
        $parameters['appID']=$copyID;
        $url="http://".$app['appAddress'].$appEditService;
        
        foreach($parameters as $key=>$value):             
            $url=str_replace("[".$key."]", $value,$url);      
        endforeach;
        
        $postArray['edit_op']="get";
        $postArray['copyID']=$REQ['copyID'];
        $res=doRequest($url,$postArray);
    endif;
    
    echo $res;
}


function setUpdateOrg($orgID) {
    global $dbop;
    $fields=array();
    $fields['teamsList_uptodate']=0;
    $dbop->updateDB('organizations',$fields,$orgID,"organizationID");
}


function addPlayerToCopyID($REQ) {
    $res=array();
    global $dbop;
    global $orgId;
    global $loginUserRow;
    $appID=$REQ['appID'];
    $copyID=$REQ['copyID'];
    $name=$REQ['name'];
    $email=$REQ['email'];
    $empID=$REQ['empID'];
    
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    $appCopy=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$copyID}'");
    $teamID=$appCopy['appCopyTeam'];
    
    
    // add user to users:
    $fields=array();
    $fields['userID']=null;
    $fields['userName']=$name;
    $fields['userEmail']=$email;
    $fields['userOrganizationID']=$orgId;
    $fields['userUserKindID']=1;
    $fields['is_manger']=0;
    $fields['userInactive']=0;
    $fields['userEmpID']=$empID;
    $insertID=$dbop->insertDB("users",$fields,false);
    
    
    // add user to team:
    $fields=array();
    $fields['teamUserID']=null;
    $fields['teamUserUserID']=$insertID;
    $fields['teamUserTeamID']=$teamID;
    $insert=$dbop->insertDB("teamsUsers",$fields,false);
    
    // insert user to app:
    $appAddUser=$app['appAddUser'];
    if($appAddUser!="") {
        $url="http://".$app['appAddress'].$appAddUser;
        $url=str_replace("[appID]",$copyID,$url);
        $url=str_replace("[userID]",$insertID,$url);
        

        $url=str_replace(".com",".".AvbDevPlatform::getServerName(),$url);
        $check=file_get_contents($url);
        $res=json_decode($check,true);
        
        
        // take 1 token //
        Accounts::insertTokenHistoryRow(array('orgID'=>$appCopy['appCopyOrganizationID'],'time'=>time(),'userID'=>$insertID,'userEmail'=>$email,'copyID'=>$appCopy['appCopyID'],'copyName'=>$appCopy['appCopyName']));
        Accounts::useToken($appCopy['appCopyOrganizationID']);
        //////////////////
        
        // check app auto email:
        $autoEmail=$appCopy['appCopyAutoEmail'];
        //if((int)$autoEmail==1) {
        if(false){
            $link="http://".$app['appAddress'].$res['link'];
            
            $user=$dbop->selectAssocRow("users","WHERE `userID`='{$insertID}'");
            $organization=$dbop->selectAssocRow("organizations","WHERE `organizationID`='{$orgId}'");


            $templateName="programInvite";
            $fileLocation=dirname(__FILE__)."/../../Emails/{$templateName}.html";
            $check=file_exists ($fileLocation);
                if($check) {

                    $BodyOrig=  file_get_contents($fileLocation);
                    $Body=$BodyOrig;
                    $parameters=array();  
                    $parameters['org_logo']=$organization['organizationImg'];
                    $parameters['email_content']=$appCopy['app_email_content'];
                    $parameters['link']=$link;
                    $parameters['name']=ucfirst($user['userName']);
                    foreach($parameters as $key=>$value):             
                            $Body=str_replace("[".$key."]", $value,$Body);      
                    endforeach;
                    $c=email::semailFrom(ucfirst($loginUserRow['userName']), $user['userEmail'], $appCopy['app_email_title'], $Body);
                    $res['email']=$c;
                }
            }
            /////////////////////////////////////////   
    }
    
    
    
    setUpdateOrg($orgId);
    
    echo json_encode($res);
}





function checkPassword($REQ) {
    $res=array();
    global $dbop;
    global $userId;
    $password=$REQ['password'];
    $hashedPass = hash('SHA256', $password,false);
    $check=$dbop->selectAssocRow("users","WHERE `userID`='{$userId}' AND `userPassword`='{$hashedPass}'");
    if($check)
        $res['status']="ok";
    else
        $res['status']="faild";
    echo json_encode($res);
}

function resetPassword($REQ) {
    $res=array();
    global $dbop;
    global $userId;
    $password=$REQ['password'];
    $hashedPass = hash('SHA256', $password,false);
    $fields=array();
    $fields['userPassword']=$hashedPass;
    $dbop->updateDB("users",$fields,$userId,"userID");
    $res['status']="ok";
    echo json_encode($res);
}


function getOrgInfo($REQ) {
    $res=array();
    global $dbop;
    global $orgId;
    $orgRow=$dbop->selectAssocRow("organizations","WHERE `organizationID`='{$orgId}'");
    
    $res['organizationName']=$orgRow['organizationName'];
    $res['organizationImg']=$orgRow['organizationImg'];
    
    
    echo json_encode($res);
}

function getUserInfo($REQ) {
    $res=array();
    global $dbop;
    global $userId;
    $userRow=$dbop->selectAssocRow("users","WHERE `userID`='{$userId}'");
    
    $res['userName']=$userRow['userName'];
    $res['userEmail']=$userRow['userEmail'];
    
    
    echo json_encode($res);
}


function saveMysettings($REQ) {
    $res=array();
    global $dbop;
    global $userId;
    global $orgId;
    
    
    
    $data=json_decode(stripslashes(stripData($REQ['userData'])),true);
    
    $dbop->updateDB("organizations",$data['organization'],$orgId,"organizationID");
    $dbop->updateDB("users",$data['user'],$userId,"userID");
    $res['status']="ok";
    echo json_encode($res);
}



function getHash($REQ) {   
    $res=array();
    $res['hash']=sha1(time());
    echo json_encode($res);
}


function getCsvFile($REQ) {
    global $base_path;
    global $userId;
    global $orgId;
    global $ds;
    $res=array();
    $f_name=$REQ['name'];
    $data=json_decode(stripslashes(stripData($REQ['data'])),true);
    
    $download_path=$f_name."_".$userId."_".$orgId."_".time().".csv";
    $file_name=$base_path."reports_csv".$ds.$download_path;
    
    
    $headline=array();
    
    $c=0;
    foreach($data['cols'] as $col):
        $headline[]=$col['label'];
    endforeach;
    
    $list[$c]=$headline;
    $c++;
    foreach($data['rows'] as $row):
        $rowDataToCsv=array();
        foreach($row['c'] as $rowData):
            $rowDataToCsv[]=$rowData['v'];
        endforeach;
        
        $list[$c]=$rowDataToCsv;
        $c++;
    endforeach;

    
    
    $fp = fopen($file_name, 'w');
    
    foreach ($list as $fields) {
        fputcsv($fp, $fields);
    }

    fclose($fp);
    
    
    
    $res['link']="getCsv/".$download_path;
    echo json_encode($res);
}

function getGuessWotFullReport($REQ) {
    $res=array();
    global $dbop;
    global $base_path;
    global $userId;
    global $orgId;
    global $ds;
    $appID=$REQ['appID'];
    $copyID=$REQ['copyID'];
    
    $download_path="GuessWotFull_".$copyID.".csv";
    $file_name=$base_path."reports_csv".$ds.$download_path;
    if(file_exists($file_name)) {
        unlink($file_name);
    }
    
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    $appReportService=$app['appReport'];


    if($appReportService!=""):
        $parameters['appID']=$copyID;
        $url="http://".$app['appAddress'].$appReportService;
        
        foreach($parameters as $key=>$value):             
            $url=str_replace("[".$key."]", $value,$url);      
        endforeach;

        $postArray['copyID']=$REQ['copyID'];
        $data_j=doRequest($url,$postArray);
        $data=json_decode($data_j,true);
    endif;
    
    
    $list=array();
    

    
    
    $cols=array();
    
    $cols[0][0]="Employee number";
    $cols[0][1]="";
    
    $cols[1][0]="Name";
    $cols[1][1]="";
    
    
    $usersC=0;
    $c=2;
    $usersArr=$data['users'];
    foreach($usersArr as $user):
        $row=array();
        $cols[0][$c]=$user['empID'];
        $cols[1][$c]=$user['user_name'];
        $c++;
        $usersC++;
    endforeach;
    
    
    
    $colC=count($cols);

    function setNewQToCols(&$usersArr,&$cols,&$colC,$q){

        $type=$q['q']['type'];
        $q_data=json_decode($q['q']['text'],true);
        switch((int)$type):
            case 1:
                $cols[$colC][0]=$q_data['text'];
                $cols[$colC][1]="";
                $usersData=array();
                foreach($q['user_data'] as $user_data):
                    if($user_data['p']=="1")
                        continue;
                    $data=json_decode(stripslashes($user_data['data']),true);
                    $usersData[$user_data['userID']]=$data['value'];
                endforeach;

                $rowsC=2;
                foreach($usersArr as $user):
                    $userID=$user['user_id'];
                    if(isset($usersData[$userID])) {
                        $cols[$colC][$rowsC]=$usersData[$userID];
                    }
                    else {
                        $cols[$colC][$rowsC]="";
                    }
                    $rowsC++;
                endforeach;
                $colC++;
            break;
            case 2:
                
                $initial_col=$colC;
                $cols[$colC][0]=$q_data['text'];
                
                $usersData=array();
                foreach($q['user_data'] as $user_data):
                    if($user_data['p']=="1")
                        continue;
                    $data=json_decode(stripslashes($user_data['data']),true);
                    $usersData[$user_data['userID']]=$data['answers'];
                endforeach;

                
                $k=0;
                $valuesC=0;
                foreach($q_data['values'] as $value):
                    if($k!=0)
                        $cols[$colC][0]="";
                    
                    $cols[$colC][1]=$value['text'];
                    $colC++;
                    $k++;
                    $valuesC++;
                endforeach;
                

                $rowsC=2;
                foreach($usersArr as $user):
                    $colC=$initial_col;
                    $userID=$user['user_id'];
                    
                    
                    if(isset($usersData[$userID])) {
                        //$cols[$colC][$rowsC]=$usersData[$userID];
                        for($i=0;$i<$valuesC;$i++) {
                            $cols[$colC][$rowsC]=$usersData[$userID][$i]!="0"?$usersData[$userID][$i]:"";
                            $colC++;
                        }
                    }
                    else {
                        
                        for($i=0;$i<$valuesC;$i++) {
                            $cols[$colC][$rowsC]="";
                            $colC++;
                        }
                    }
                    $rowsC++;
                endforeach;
                
            break;
            case 3:
                $initial_col=$colC;
                $cols[$colC][0]=$q_data['text'];
                
                $usersData=array();
                foreach($q['user_data'] as $user_data):
                    if($user_data['p']=="1")
                        continue;
                    $data=json_decode(stripslashes($user_data['data']),true);
                    $usersData[$user_data['userID']]=$data['options'];
                endforeach;

                
                $k=0;
                $valuesC=0;
                foreach($q_data['options'] as $option):
                    if($k!=0)
                        $cols[$colC][0]="";
                    
                    $cols[$colC][1]=$option['text'];
                    $colC++;
                    $k++;
                    $valuesC++;
                endforeach;
                

                $rowsC=2;
                foreach($usersArr as $user):
                    $colC=$initial_col;
                    $userID=$user['user_id'];
                    
                    
                    if(isset($usersData[$userID])) {
                        //$cols[$colC][$rowsC]=$usersData[$userID];
                        for($i=0;$i<$valuesC;$i++) {
                            $cols[$colC][$rowsC]=in_array($i,$usersData[$userID])?"Y":"N";
                            $colC++;
                        }
                    }
                    else {
                        
                        for($i=0;$i<$valuesC;$i++) {
                            $cols[$colC][$rowsC]="";
                            $colC++;
                        }
                    }
                    $rowsC++;
                endforeach;
            break; 
            
            case 5:
                $cols[$colC][0]=$q_data['text'];
                $cols[$colC][1]="";
                $usersData=array();
                foreach($q['user_data'] as $user_data):
                    if($user_data['p']=="1")
                        continue;
                    $data=json_decode(stripslashes($user_data['data']),true);
                    $usersData[$user_data['userID']]=$data['value'];
                endforeach;
                
                

                $rowsC=2;
                foreach($usersArr as $user):
                    $userID=$user['user_id'];
                    if(isset($usersData[$userID])) {
                        $cols[$colC][$rowsC]=$usersData[$userID];
                    }
                    else {
                        $cols[$colC][$rowsC]="";
                    }
                    $rowsC++;
                endforeach;
                $colC++;
                
                // create new q from cond:
                
                
                // true:
                $new_q=array();
                $new_q['q']['type']=3;
                $new_q_text=array();
                $new_q_text['type']=3;
                $new_q_text['text']=$q_data['conditionMultiSelect']['instruction_true'];
                $new_q_text['options']=$q_data['conditionMultiSelect']['options_true'];
                $new_q['q']['text']=json_encode($new_q_text);
                
                
                $new_q['user_data']=array();
                
                $cc=0;
                foreach($q['user_data'] as $user_data):
                    if($user_data['p']=="1")
                        continue;
                    $data=json_decode(stripslashes($user_data['data']),true);
                    if($data['condition_result']=="1") {
                        unset($user_data['data']);
                        $user_data['data']=json_encode(array("options"=>$data['if_true_options']));
                        $new_q['user_data'][$cc]=$user_data;
                        $cc++;
                    }
                endforeach;
                setNewQToCols($usersArr,$cols,$colC,$new_q);
                
                
                // false:
                $new_q=array();
                $new_q['q']['type']=3;
                $new_q_text=array();
                $new_q_text['type']=3;
                $new_q_text['text']=$q_data['conditionMultiSelect']['instruction_false'];
                $new_q_text['options']=$q_data['conditionMultiSelect']['options_false'];
                $new_q['q']['text']=json_encode($new_q_text);
                
                
                $new_q['user_data']=array();
                
                $cc=0;
                foreach($q['user_data'] as $user_data):
                    if($user_data['p']=="1")
                        continue;
                    $data=json_decode(stripslashes($user_data['data']),true);
                    if($data['condition_result']=="0") {
                        unset($user_data['data']);
                        $user_data['data']=json_encode(array("options"=>$data['if_false_options']));
                        $new_q['user_data'][$cc]=$user_data;
                        $cc++;
                    }
                endforeach;
                
                //var_dump($new_q);
                setNewQToCols($usersArr,$cols,$colC,$new_q);
                //////////////////////////
                
            break;
        endswitch;
        
        
    }

    foreach($data['q_types_q'] as $q):
        setNewQToCols($usersArr,$cols,$colC,$q);
    endforeach;


    
    
    
    
    
    
    $rows=array();

    $colC=0;
    foreach($cols as $col):
        $row_no=0;
        foreach($col as $cell):
            $rows[$row_no][$colC]=$cell;
            $row_no++;
        endforeach;
        $colC++;
    endforeach;
    
    
    $fp = fopen($file_name, 'w');
    
    foreach ($rows as $fields) {
        fputcsv($fp, $fields);
    }

    fclose($fp);
    
    
    
    $res['link']="getCsv/".$download_path;
    //header('Content-Type:application/json');
    echo json_encode($res);
}

function getGuessWotFilteredData($REQ) {
    $res=array();
    global $dbop;
    $appID=$REQ['appID'];
    $copyID=$REQ['copyID'];
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    $appReportFilteredService="api/reportFilteredWS/[appID]";
    
    $res=null;
    if($appReportFilteredService!=""):
        $parameters['appID']=$copyID;
        $url="http://".$app['appAddress'].$appReportFilteredService;
        
        foreach($parameters as $key=>$value):             
            $url=str_replace("[".$key."]", $value,$url);      
        endforeach;

        $postArray['copyID']=$REQ['copyID'];
        $postArray['data']=$REQ['data'];
        $postArray['op']='reportFilteredWS';
        
        $res=doRequest($url,$postArray);
    endif;

    
    
    //$data=json_decode(stripslashes(stripData($REQ['data'])),true);
    
    header('Content-Type:application/json');
    echo $res;
}




function getAccounts(){
    $res=array();
    global $dbop;
    $ans=$dbop->selectDB("accounts","WHERE `active`=1 ORDER BY `validUntil` ASC"); 
    for($i=0;$i<$ans['n'];$i++) {
        
        $row=mysql_fetch_assoc($ans['p']);
        //var_dump($row);
        $res[$i]['account']=$row;
        // days:
        $diff=time()-$row['validUntil'];
        $measureDays=-floor($diff/(3600*24))-1;
        
        $status_class="";
        if($measureDays>=0) {
            if($measureDays==0)
                $status_class="left0";
            if($measureDays>0&&$measureDays<3)
                $status_class="left12";
            
            $valid="$measureDays days";
        }
        else {
            $status_class="blocked";
            $measureDays=-$measureDays;
            $valid="Minus $measureDays days";
        }
        
        $res[$i]['status_class']=$status_class;
        ///////////////
        $res[$i]['account']['regDate']=date("m/d/Y", $row['regDate']);
        $res[$i]['account']['validUntil']=date("m/d/Y", $row['validUntil']);
        $res[$i]['account']['validUntilLeft']=$valid;
        $res[$i]['account']['bu_pricingPackage']=$row['pricingPackage'];
        $res[$i]['account']['tokens_c']=$row['tokens_c'];
        $res[$i]['account']['tokens_limit']=$row['tokens_limit'];
        $org=$dbop->selectAssocRow("organizations","WHERE `organizationID`='{$row['orgID']}'");
        $res[$i]['org']=$org;
        
    }
    header('Content-Type:application/json');
    echo json_encode($res);
}

function updateAccountData($REQ) {
    $res=array();
    global $dbop;
    $account_id=$REQ['account_id'];
    
    $fields=array();
    $fields[$REQ['key']]=$REQ['value'];
    $dbop->updateDB("accounts",$fields,$account_id);
    
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
}

function setAccountInactive($REQ) {
    $res=array();
    global $dbop;
    $account_id=$REQ['account_id'];
    $fields=array();
    $fields['active']=0;
    $dbop->updateDB("accounts",$fields,$account_id);
    
    
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
    
}

function setPackageAccount($REQ) {
    $res=array();
    global $dbop;
    $account_id=$REQ['account_id'];
    $pack=$REQ['pack'];
    $fields=array();
    $fields['pricingPackage']=$pack;
    $dbop->updateDB("accounts",$fields,$account_id);
    
    
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
    
}


function setExpiryDate($REQ) {
    $res=array();
    global $dbop;
    
    $account_id=$REQ['account_id'];
    $validUntil=(int)$REQ['validUntil']+24*3600;
    
    
    
    $dbop->updateDB("accounts",array("validUntil"=>$validUntil),$account_id);
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
}



function loadSlidesPics($REQ) {
    $res=array();
    global $dbop;
    $copyID=$REQ['copyID'];
    
    $ds=DIRECTORY_SEPARATOR;
    $root_path= __DIR__ .$ds."..".$ds."..".$ds;
    $root_path_for_output=$root_path."uploads".$ds."presentations".$ds."Output".$ds.$copyID.$ds;
    
    $base=str_replace(".com",".".AvbDevPlatform::getServerName(true),"http://my.wheeldo.com/uploads/presentations/Output/$copyID/");
    
    if(file_exists($root_path_for_output)) {
        $files=array();
        $res['status']="ok";
        if ($handle = opendir($root_path_for_output)) { 
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $files[] = $base.$file;
                }
            }
            closedir($handle); 
        }
        sort($files);
        $res['files']=$files;
        
    }
    else {
        $res['status']="not ok";
    }
    
    
    //header('Content-Type:application/json');
    echo json_encode($res);
}


function getAppTeam($REQ) {
    
    $res=array();
    global $dbop;
    $copyID=$REQ['copyID'];
    $copyRow=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$copyID}'");
    $teamID=$copyRow['appCopyTeam'];
    
    
    $res['team']=$dbop->selectAssocRow("teams","WHERE `teamID`='{$teamID}'");
    
    $res['users']=array();
    $ans=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='{$teamID}'");
    for($i=0;$i<$ans['n'];$i++) {
        set_time_limit(2);
        $row=mysql_fetch_assoc($ans['p']);
        $userID=$row['teamUserUserID'];
        $user=$dbop->selectAssocRow("users","WHERE `userID`='{$userID}'");
        $res['users'][]=$user;
    }
    
    header('Content-Type:application/json');
    echo json_encode($res);
}

function getUserLink($REQ) {
    $res=array();
    global $dbop;
    $copyID=$REQ['copyID'];
    $userID=$REQ['userID'];
    
    $copyRow=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$copyID}'");
    $user=$dbop->selectAssocRow("users","WHERE `userID`='{$userID}'");
    $programAppCopies=$dbop->selectAssocRow("programAppCopies","WHERE `programAppCopyAppCopyID`='{$copyID}'");
    $programID=$programAppCopies['programAppCopyProgramID'];
    $token = Auth::generateAuthFromURL($userID, $user['userUserKindID'], $copyID, $programID);
    
    $appID=$copyRow['appCopyAppID'];
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    
    //var_dump($app);
    $url="http://".$app['appAddress'].$app['appIndex'];
    $url=str_replace("[appID]",$copyID,$url);
    $url=str_replace("[token]",$token,$url);
    $url=str_replace(".com",".".AvbDevPlatform::getServerName(true),$url);
    $res['link']=$url;
    header('Content-Type:application/json');
    echo json_encode($res);
}


function sendInvitation($REQ) {
    $res=array();
    global $dbop;
    $copyID=$REQ['copyID'];
    $userID=$REQ['userID'];
    
    $copyRow=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$copyID}'");
    $user=$dbop->selectAssocRow("users","WHERE `userID`='{$userID}'");
    $user_sender=$dbop->selectAssocRow("users","WHERE `userID`='{$copyRow['appCopyUserID']}'");
    $programAppCopies=$dbop->selectAssocRow("programAppCopies","WHERE `programAppCopyAppCopyID`='{$copyID}'");
    $programID=$programAppCopies['programAppCopyProgramID'];
    $token = Auth::generateAuthFromURL($userID, $user['userUserKindID'], $copyID, $programID);
    
    $appID=$copyRow['appCopyAppID'];
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    
    $url="http://".$app['appAddress'].$app['appIndex'];
    $url=str_replace("[appID]",$copyID,$url);
    $url=str_replace("[token]",$token,$url);
    $url=str_replace(".com",".".AvbDevPlatform::getServerName(true),$url);
    $res['link']=$url;
    
    
    $organization=$dbop->selectAssocRow("organizations","WHERE `organizationID`='{$user['userOrganizationID']}'");
    
    
    // send email:
    $templateName="programInvite";
    $ds=DIRECTORY_SEPARATOR;
    $Body=file_get_contents(dirname(__FILE__).$ds."..".$ds."..".$ds."Emails".$ds.$templateName.".html");
    $parameters=array();  
    $parameters['org_logo']=$organization['organizationImg'];
    $parameters['email_content']=$copyRow['app_email_content'];
    $parameters['link']=$url;
    $parameters['name']=ucfirst($user['userName']);
    foreach($parameters as $key=>$value):             
            $Body=str_replace("[".$key."]", $value,$Body);      
    endforeach;
    email::semailFrom(ucfirst($user_sender['userName']), $user['userEmail'], $copyRow['app_email_title'], $Body);
    //////////////
    
    
    $res['status']="Invitation sent";
    
    
    header('Content-Type:application/json');
    echo json_encode($res);
}

function updateEmailContent($REQ) {
    $res=array();
    global $dbop;
    $copyID=$REQ['copyID'];
    $fields=array();
    $fields['app_email_title']=$REQ['app_email_title'];
    $fields['app_email_content']=str_replace('\\n', "<br>", $REQ['app_email_content']);
    $appInfo=$dbop->updateDB("appCopies",$fields,$copyID,"appCopyID");
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
}


function getAdmins($REQ) {
    $res=array();
    global $dbop;
    global $userId;
    global $orgId;
    
    $res['admins']=array();
    
    $ans=$dbop->selectDB("users","WHERE `userOrganizationID`='{$orgId}' AND `userUserKindID`>1 AND `userID`!='{$userId}'");
    for($i=0;$i<$ans['n'];$i++) {
        $row=mysql_fetch_assoc($ans['p']);
        $user=array();
        $user['id']=$row['userID'];
        $user['name']=$row['userName'];
        $user['email']=$row['userEmail'];
        $user['bu_name']=$row['userName'];
        $user['bu_email']=$row['userEmail'];
        $user['new_password']="";
        $res['admins'][]=$user;
    }
    
    header('Content-Type:application/json');
    echo json_encode($res);
}

function saveAdmin($REQ) {
    $res=array();
    global $dbop;
    global $orgId;
    global $userName;
    
    
    
    $fields=array();
    $fields['userName']=$REQ['name'];
    $fields['userEmail']=$REQ['email'];
    
    $fields['userOrganizationID']=$orgId;
    $fields['userOrganizationID']=$orgId;
    $fields['userUserKindID']=2;
    $fields['userRegTime']=time();
    
    if($REQ['id']=="0") {
        $newPass = substr( sha1( time() ), 0, 8 );
        $hashedPass = hash( 'SHA256', $newPass, false );
        $fields['userPassword']=$hashedPass;
        $res['id']=$dbop->insertDB('users',$fields,false);
        
        
        // invite admin email //
        $templateName="admin_invite";
        $check=file_exists (dirname(__FILE__)."/../../Emails/{$templateName}.html");
        if($check) {
            $ds=DIRECTORY_SEPARATOR;
            $Body=  file_get_contents(dirname(__FILE__)."/../../Emails/{$templateName}.html");
            $parameters=array();  
            $parameters['name']=ucfirst($REQ['name']);
            $parameters['super_admin_name']=ucfirst($userName);
            $parameters['user_name']=$REQ['email'];
            $parameters['password']=$newPass;
            $parameters['link']="http://my.wheeldo.com";
            
            $parameters['path']=dirname(__FILE__).$ds."..".$ds."..".$ds;
            
            foreach($parameters as $key=>$value):             
                    $Body=str_replace("[".$key."]", $value,$Body);      
            endforeach;
            email::semailFrom(ucfirst($userName), $REQ['email'], "Welcome to Wheeldo", $Body);
        }
        //////////////////////// 
        
    }
    else {
        $dbop->updateDB('users',$fields,$REQ['id'],"userID");
    }
    
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
}



function deleteAdmin($REQ) {
    $res=array();
    global $dbop;
    global $orgId;
    $deleteUserID=$REQ['admin_id'];
    $dbop->deleteDB("users",$deleteUserID,"userID");
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
}

function resetAdminPassword($REQ) {
    $res=array();
    global $dbop;
    $userID=$REQ['id'];

    $newPass = $REQ['new_password'];
    $hashedPass = hash( 'SHA256', $newPass, false );
    
    $fields=array();
    $fields['userPassword']=$hashedPass;
    
    $dbop->updateDB('users',$fields,$REQ['id'],"userID");
    
    
    
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
}


function getUserFullDetails($REQ) {
    $res=array();
    global $dbop;
    global $orgId;
    global $userId;
    
    $user=$dbop->selectAssocRow("users","WHERE `userID`='{$userId}'");
    $org=$dbop->selectAssocRow("organizations","WHERE `organizationID`='{$orgId}'");
    
    
    $uex=explode(" ",$user['userName']);
    
    $res['firstName']=$uex[0];
    $res['lastName']=isset($uex[1])?$uex[1]:"";
    $res['companyName']=$org['organizationName'];
    $res['orgID']=$org['organizationID'];
    $res['email']=$user['userEmail'];

    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
}

function getPricingPackages() {
    $res=array();
    global $dbop;
    
    $res['packages']=array();
    $ans=$dbop->selectDB("pricing_packages");
    for($i=0;$i<$ans['n'];$i++) {
        $row=mysql_fetch_assoc($ans['p']);
        $res['packages'][]=$row;
    }
    
    
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
};


function getTokensLeft() {
    global $orgId;
    $res=array();
    $res['status']="ok";
    $res['tokens']=Accounts::getTokensLeft($orgId);
    header('Content-Type:application/json');
    echo json_encode($res);
}

function setBid($REQ) {
    $res=array();
    global $dbop;
    global $orgId;
    global $userId;
    global $loginUserRow;
    
    $q=$REQ['am'];
    
    $res['price']=0;
    
    if($q<30) {
        //alert("as")
        $res['price']=round($q*7);
    }

    if($q==30) {
        $res['price']=199;
    }

    if($q>30) {
        $res['price']=199 + round(($q-30)*6.6);
    }

    if($q==50) {
        $res['price']=299;
    }

    if($q>50) {
        $res['price']=299 + round(($q-50)*6);
    }

    if($q==100) {
        $res['price']=499;
    }

    if($q>100) {
        $res['price']=499 + round(($q-100)*(5-1*($q/600)));
    }

    if($q==500) {
        $res['price']=1999;
    }

    if($q>500) {
        $res['price']=1999 + round(($q-500)*(4.1-1*($q/10000)));
    }

    if($q>10000) {
        $res['price']=round($q*3);
    }
    
    
    
    // check if shopper exists:
    $url='http://billing.wheeldo.com/index.php/bluesnap/api_func';
    $postArray['func']='checkIfShopperExists';
    $postArray['userID']=$userId;
    $response=doRequest($url,$postArray,true);
    $reArr=json_decode($response,true);
    if($reArr['status']=="ok"){
        if((int)$reArr['shopper_exists']>0) {
            $res['shopper']=$reArr['shopper'];
        }
        else {
            $ex_name=explode(" ",$loginUserRow['userName']);
            
            $res['shopper']=array();
            $res['shopper']['firstName']=$ex_name[0];
            $res['shopper']['lastName']=isset($ex_name[1])?$ex_name[1]:"";
            $res['shopper']['address1']="";
            $res['shopper']['city']="";
            $res['shopper']['state']="";
            $res['shopper']['country']="";
            $res['shopper']['zipCode']="";
            $res['shopper']['phone']=$loginUserRow['userPhone'];
        }
        
        $res['shopper_status']=$reArr['shopper_exists'];
    }
    else {
        $res['shopper_status']=-1;
    }
    
    ///////////////////////////
    
    $_SESSION['bid']['bid_q']=$q;
    $_SESSION['bid']['bid_price']=$res['price'];
    $_SESSION['bid']['bid_hash']=md5($q.$res['price']);
    $res['status']="ok";
    
    
    header('Content-Type:application/json');
    echo json_encode($res);
}


function getCountries() {
    global $base_path;
    $res=array();
    $xml = new DOMDocument();
    $xml->load( $base_path.'/lib/data_files/countries.xml' );
    $countries = $xml->getElementsByTagName("country");
    
    $res['countries']=array();
    $c=0;
    foreach( $countries as $country ):
        
        $names=$country->getElementsByTagName("name");
        $res['countries'][$c]['name']=$names->item(0)->nodeValue;
        $codes=$country->getElementsByTagName("code");
        $res['countries'][$c]['code']=$codes->item(0)->nodeValue;
        $c++;
    endforeach;
    
    
    $xml = new DOMDocument();
    $xml->load( $base_path.'/lib/data_files/states_ca.xml' );
    $states_ca = $xml->getElementsByTagName("state");
    
    $res['states_ca']=array();
    $c=0;
    foreach( $states_ca as $state ):
        
        $names=$state->getElementsByTagName("name");
        $res['states_ca'][$c]['name']=$names->item(0)->nodeValue;
        $codes=$state->getElementsByTagName("code");
        $res['states_ca'][$c]['code']=$codes->item(0)->nodeValue;
        $c++;
    endforeach;
    
    
    $xml = new DOMDocument();
    $xml->load( $base_path.'/lib/data_files/states_us.xml' );
    $states_us = $xml->getElementsByTagName("state");
    
    $res['states_us']=array();
    $c=0;
    foreach( $states_us as $state ):
        $names=$state->getElementsByTagName("name");
        $res['states_us'][$c]['name']=$names->item(0)->nodeValue;
        $codes=$state->getElementsByTagName("code");
        $res['states_us'][$c]['code']=$codes->item(0)->nodeValue;
        $c++;
    endforeach;

											  
    
    header('Content-Type:application/json');
    echo json_encode($res);
    
}


function createBSShopper($REQ) {
    $res=array();
    global $dbop;
    global $orgId;
    global $userId;
    global $loginUserRow;
    
    $user_data=json_decode(stripslashes(stripData($REQ['data'])),true);
    
    
    $url='http://billing.wheeldo.com/index.php/bluesnap/api_func';
    $postArray=$user_data;
    $postArray['func']='create_shopper';
    $postArray['userID']=$userId;
    $response=doRequest($url,$postArray,true);
    
    header('Content-Type:application/json');
    echo json_encode($response);
}



function getPublishedApps() {
    $res=array();
    global $dbop;
    global $orgId;
    
    
    $sql="SELECT
        appCopies.appCopyID,
        FROM_UNIXTIME(appCopies.appCopyTimestamp, '%m/%e/%Y') as date,
        FROM_UNIXTIME(appCopies.appCopyTimestamp, '%h:%i %p') as time,
        appCopies.appCopyTeam,
        appCopies.appCopyName,
        apps.appName,
        apps.appID,
        appinfo.edit_in_service,
        appinfo.report_in_service,
        appinfo.icon,
        appinfo.`name`,
        teams.teamName
        FROM
        appCopies
        INNER JOIN apps ON appCopies.appCopyAppID = apps.appID
        INNER JOIN appinfo ON apps.appID = appinfo.appID
        INNER JOIN teams ON appCopies.appCopyTeam = teams.teamID
        WHERE
        appCopies.appCopyInactive = 0 AND
        appCopies.appCopyOrganizationID = $orgId AND
        appCopies.appCopyTerminate = 0
        GROUP BY
        appCopies.appCopyID
        ORDER BY
        appCopies.appCopyTimestamp DESC LIMIT 0,15";
    
    $res['copies']=array();
    $p=mysql_query($sql);
    $c=0;
    while($row=mysql_fetch_assoc($p)):
        $res['copies'][$c]=$row;
        // get engagement: //
        $appID=$row['appID'];
        $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
        // duplicate:
        $appEngagement=$app['appEngagement'];


        if($appEngagement!="") {
            $url="http://".$app['appAddress'].$appEngagement;
            $url=str_replace("[copyID]",$row['appCopyID'],$url);
            $url=str_replace(".com",".".AvbDevPlatform::getServerName(),$url);
            
            //$check=file_get_contents($url);
            //$res['copies'][$c]['app_data']=$url;
            
            $res['copies'][$c]['engagement']= (int) file_get_contents($url);;
        }
        else {
            $res['copies'][$c]['engagement']=rand(0,100);
        }


        
        
        
        
        
        
        /////////////////////
        
        $c++;
    endwhile;
    
    header('Content-Type:application/json');
    echo json_encode($res);
}

function getMarketApps() {
    $res=array();
    global $dbop;
    global $orgId;
    
    $sql="SELECT
    apps.appID,
    apps.appName,
    apps.appOrder,
    apps.appPrivate,
    appinfo.`name`,
    appinfo.slogen,
    appinfo.comming_soon,
    appinfo.icon,
    appinfo.video,
    appinfo.demoCopy,
    appinfo.edit_in_service,
    appinfo.report_in_service
    
    FROM
    apps
    INNER JOIN appinfo ON apps.appID = appinfo.appID
    WHERE
    apps.appInactive = 0
    GROUP BY
    apps.appID ORDER BY appOrder ASC";
    
    $res['apps']=array();
    $p=mysql_query($sql);
    $c=0;
    while($row=mysql_fetch_assoc($p)):
        $categories=array();
        $sql2="SELECT
        categories.categoryID,
        categories.categoryName
        FROM
        appcategories
        INNER JOIN categories ON appcategories.categoryID = categories.categoryID
        WHERE
        appcategories.appID = {$row['appID']}";
        $p2=mysql_query($sql2);
        while($cat=mysql_fetch_assoc($p2)) {
            $categories[]=$cat;
        }
        
        
        if($row['appPrivate']!="0") {
            $check=$dbop->selectAssocRow("privateApps","WHERE `appID`='{$row['appID']}' AND `organizationID`='{$orgId}'");
            if($check) {
                $res['apps'][$c]=$row;
                $res['apps'][$c]['categories']=$categories;
                $c++;
            }
        }
        else {
            
            $res['apps'][$c]=$row;
            $res['apps'][$c]['categories']=$categories;
            $c++;
        }
    endwhile;
    
    
    header('Content-Type:application/json');
    echo json_encode($res);
}


function getGameCategories() {
    $res=array();
    global $dbop;
    global $orgId;
    $res['categories']=array();
    $ans=$dbop->selectDB("categories","ORDER BY `categoryName` ASC");
    for($i=0;$i<$ans['n'];$i++) {
        $row=mysql_fetch_assoc($ans['p']);
        $res['categories'][$i]['name']=$row['categoryName'];
        $res['categories'][$i]['id']=$row['categoryID'];
    }
    
    
    
    header('Content-Type:application/json');
    echo json_encode($res);
}


function sendGameLink2User($REQ) {
    $res=array();
    global $dbop;
    global $orgId;
    global $userId;
    global $loginUserRow;
    
    $copyID=$REQ['copyID'];
    
    
    // create team:
    $fields=array();
    $fields['teamID']=null;
    $fields['teamName']="Auto Team for $copyID";
    $fields['teamDescription']="";
    $fields['teamOrganizationID']=$orgId;
    $fields['teamUserID']=$userId;
    $teamID=$dbop->insertDB("teams",$fields,false);
    ////////////////
    
    // save copy data:
    $fields=array();
    $fields['appCopyTerminate']=0;
    $fields['appCopyTeam']=$teamID;
    $dbop->updateDB("appCopies",$fields,$copyID,"appCopyID");
    ////////////////////
    
    
    // create hash code:
    $hash=sha1(time());
    
    $fields=array();
    $fields['copyID']=$copyID;
    $fields['hash']=$hash;
    $dbop->insertDB("lead_generation",$fields);
    /////////////////////
    
    // send email to user:
    $link="http://lead.wheeldo.com/gen.wspx?hash=$hash";
    
    
    // check if it's self regform app:
    $appCopy=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$copyID}'");
    $appID=$appCopy['appCopyAppID'];
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    
    $appRegFormEdit=$app['appRegFormEdit'];
    
    if($appRegFormEdit!="") {

        
        $url="http://".$app['appAddress'].$appRegFormEdit;
        $url=str_replace("[appID]",$copyID,$url);
        $postArray['edit_op']="link";
 
        //$url=str_replace(".com",".".AvbDevPlatform::getServerName(),$url);
        $res=doRequest($url,$postArray);
        //echo $res;
        $res=json_decode($res,true);
        
        $link=$res['link'];   
    }
    
    //////////////////////////////////
    
    $Body="Hello {$loginUserRow['userName']}, <br> <br>"
            . "Here is your link to the game: <a href='$link'>$link</a>. <br>"
            . "<br>"
            . "Yours <br>"
            . "Wheeldo Team.";
    
    
    
    $c=email::semailFrom("Wheeldo lead generation system", $loginUserRow['userEmail'], "Your link to your new lead generation campaign", $Body);
    //////////////////////
    
    
    
    
    
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
}

function previewGame($REQ) {
    $res=array();
    global $dbop;
    global $orgId;
    global $userId;
    global $loginUserRow;
    
    $appID=$REQ['appID'];
    $copyID=$REQ['copyID'];
    $name="Demo user";
    $email=$loginUserRow['userEmail'];
    $empID=$loginUserRow['userEmpID'];
    
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    $appCopy=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$copyID}'");
    $teamID=$appCopy['appCopyTeam'];

    // add user to users:
    $fields=array();
    $fields['userID']=null;
    $fields['userName']=$name;
    $fields['userEmail']=$email;
    $fields['userPassword']=$appID."_".$copyID;
    $fields['userOrganizationID']=$orgId;
    $fields['userUserKindID']=1;
    $fields['is_manger']=0;
    $fields['userInactive']=0;
    $fields['userEmpID']=$empID;
    $fields['userRegTime']=time();
    $insertID=$dbop->insertDB("users",$fields,false);
    $userID=$insertID;
    
    
    // create team:
    $fields=array();
    $fields['teamID']=null;
    $fields['teamName']="Demo team";
    $fields['teamDescription']="";
    $fields['teamOrganizationID']=$orgId;
    $fields['teamUserID']=$userId;
    $teamID=$dbop->insertDB("teams",$fields,false);
    ////////////////

    
    // add user to team:
    $fields=array();
    $fields['teamUserID']=null;
    $fields['teamUserUserID']=$insertID;
    $fields['teamUserTeamID']=$teamID;
    $insert=$dbop->insertDB("teamsUsers",$fields,false);
    
    
        // insert user to app:
    $appAddUser=$app['appAddUser'];
    if($appAddUser!="") {
        $url="http://".$app['appAddress'].$appAddUser;
        $url=str_replace("[appID]",$copyID,$url);
        $url=str_replace("[userID]",$insertID,$url);
        

        $url=str_replace(".com",".".AvbDevPlatform::getServerName(),$url);
        $check=file_get_contents($url);
        $res=json_decode($check,true);
        
    }
    
    // create program:
    
    // create program //
    $arr = array(
        'programDescription'		=> 'Auto generated program.',
        'programUserID'			=> $userId,
        'programOrganizationID'         => $orgId,
        'programName'			=> 'Demo program for - '.$appCopy['appCopyName'],
        'programOriginalID'		=> -1,
        'programPrivate'		=> 0,
        'programInactive'		=> 0,
        'programCategoryID'		=> 1
    );
    //Initializes appCopy object same thing for public and private
    $program = new program($arr); 
    $program->store();


    $program->addAppCopy($copyID,'0 0',$order = 1);
    ////////////////////

    $appCopyId=$copyID;

    // run the program //
    $fullTime='1970-01-01 00:00:00';
    $program->registerTeam($teamID, $fullTime,1);

    $programId=$program->getTeamProgramTeams($teamID);
    
    //////////////////////////

    $token = Auth::generateAuthFromURL($userID, 1, $copyID, $programId);

    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    
    //var_dump($app);
    $url="http://".$app['appAddress'].$app['appIndex'];
    $url=str_replace("[appID]",$copyID,$url);
    $url=str_replace("[token]",$token,$url);
    $url=str_replace(".com",".".AvbDevPlatform::getServerName(true),$url);
    $res['link']=$url;
    $res['status']="ok";
    //////////////////////
    
//    $r=json_decode(getUserLink(array('copyID'=>$copyID,'userID'=>$insertID)),true);
//    var_dump($r);
    

    
    
    
    
    header('Content-Type:application/json');
    echo json_encode($res);
    
}


function get_templates($REQ) {
    $res=array();
    global $dbop;
    global $orgId;
    global $userId;
    global $loginUserRow;
    
    $appID=$REQ['appID'];
    $copyID=$REQ['copyID'];
    $q=$REQ['q'];
    
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    
    // getTemplates:
    $appTemplates=$app['appTemplates'];
    
    if($appTemplates!="") {
        $url="http://".$app['appAddress'].$appTemplates;
        $url=str_replace("[orgID]",$orgId,$url);
        if($q=="get") {
            $postArray['edit_op']="get";
            
            
        }
        
        if($q=="set") {
            $postArray['edit_op']="set";
            $postArray['data']=$REQ['data'];
            $url=str_replace("[orgID]",$orgId,$url); 
        }
        
        if($q=="new") {
            $postArray['edit_op']="new";
            $postArray['data']=$REQ['data'];
            $url=str_replace("[orgID]",$orgId,$url); 
        }
        
        //$url=str_replace(".com",".".AvbDevPlatform::getServerName(),$url);
        $res=doRequest($url,$postArray);
        //echo $res;
        $res=json_decode($res,true);
    }

    header('Content-Type:application/json');
    echo json_encode($res);
}


function checkIfImageUploaded($REQ) {
    $res=array();
    global $dbop;
    global $orgId;
    global $userId;
    global $loginUserRow;
    
    $form_name=$REQ['form_name'];
    $appID=$REQ['appID'];
    $copyID=$REQ['copyID'];
    
    $img_id=$orgId."__".$copyID."__".$form_name;
    
    $ds=DIRECTORY_SEPARATOR;
    $base_path=dirname(__FILE__);
    $file_name=md5($img_id);
    $file_location=$base_path.$ds."..".$ds."..".$ds."uploads".$ds."tempFiles".$ds.$file_name.".txt";
    
    $file=file_get_contents($file_location);
    
    if($file) {
        $json=json_decode($file,true);
        $res['status']="ok";
        $res['url']=$json['url'];
        $res['form_name']=$form_name;
        unlink($file_location);
    }
    else {
        $res['status']="faild";
        $res['waiting_for']=$file_name;
    }
    
    header('Content-Type:application/json');
    echo json_encode($res);
    
}

function regFormData($REQ) {
    $res=array();
    global $dbop;
    global $orgId;
    global $userId;
    global $loginUserRow;
    
    $appID=$REQ['appID'];
    $copyID=$REQ['copyID'];
    $type=$REQ['type'];
    
    $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
    
    // getTemplates:
    $appRegFormEdit=$app['appRegFormEdit'];
    
    if($appRegFormEdit!="") {
        $url="http://".$app['appAddress'].$appRegFormEdit;
        $url=str_replace("[appID]",$copyID,$url);
        if($type=="get") {
            $postArray['edit_op']="get";
        }
        
        if($type=="set") {
            $postArray['edit_op']="set";
            $postArray['data']=$REQ['data'];
            $url=str_replace("[orgID]",$orgId,$url); 
        }
 
        //$url=str_replace(".com",".".AvbDevPlatform::getServerName(),$url);
        $res=doRequest($url,$postArray);
        //echo $res;
        $res=json_decode($res,true);
    }

    header('Content-Type:application/json');
    echo json_encode($res);
    
}

/////////// general functions: //////////////

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


function doRequest($url,$postArray,$live=false) {

    if(!$live) 
        $url=str_replace(".com",".".AvbDevPlatform::getServerName(),$url);

    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postArray);
    $response = curl_exec($ch); 
    curl_close($ch);
    return $response;
}