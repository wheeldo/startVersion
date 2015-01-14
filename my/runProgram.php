<?php

// $programId shuld be initilized


echo "run p";

if(!isset($programId))
    $programId=$_GET['pid'];


$debug=false;

$date = getdate();
$con = db::getDefaultAdapter();
$now = new DateTime($date['mday'].'-'.$date['mon'].'-'.$date['year'].' '.$date['hours'].':00:00');

$selectListPrograms = $con->select()->from('programTeams')->where('programTeamProgramID = ? ', array($programId));
$resultListPrograms = $con->query($selectListPrograms);
$rowListPrograms = $resultListPrograms->fetch_array();
if($debug) {
    print "<hr />";
    print "Program: ".$rowListPrograms['programTeamProgramID'];
    print "<br />";
}


    $stampProgram = strtotime($rowListPrograms['programTeamstartDate']);
    
    /*
    $date = new DateTime();
    $date->setTimestamp($stamp);
    $diff = $date->diff($now);
    */

    $selectListOfApps = $con->select()->from('programAppCopies')->where('programAppCopyProgramID = ?', array($rowListPrograms['programTeamProgramID']));
    $resultListOfApps = $con->query($selectListOfApps);
    
    
    
    while($rowListOfApps = $resultListOfApps->fetch_array()) {
        $delayArr=explode(" ",$rowListOfApps['programAppCopyDelay']);
        $h=3600;
        $d=3600*24;
        $stampApp=$stampProgram+$delayArr[0]*$d+$delayArr[1]*$h;
        if($debug) {
            print "App: ".$rowListOfApps['programAppCopyAppCopyID'];
            print "<br />";
            //print "Should start at: ".date("d/m/Y H:i",$stampApp);
            print "<br />";
        }
        

        
        //var_dump($rowListOfApps);
        
        $selectAppCopy=$con->select()->from('appCopies')->where('appCopyID = ?', array($rowListOfApps['programAppCopyAppCopyID']));
        $resultAppCopy = $con->query($selectAppCopy);
        $rowAppCopy = $resultAppCopy->fetch_array();
        
        
        $selectApp=$con->select()->from('apps')->where('appID = ?', array($rowAppCopy['appCopyAppID']));
        $resultApp = $con->query($selectApp);
        $rowApp = $resultApp->fetch_array();
        
        
        $baseAddressApp=$rowApp['appAddress'];       
        
        $baseAddressApp=str_replace(".com",".".AvbDevPlatform::getServerName(),$baseAddressApp);
        
         /// create userAppCopies premission //
        $programID=$rowListPrograms['programTeamProgramID'];
        $appCopyID=$rowListOfApps['programAppCopyAppCopyID'];
        
        $select = $con->select()->from('programTeams')->join('teamsUsers', 'teamUserTeamID = programTeamTeamID')->join('users', 'teamUserUserID = userID')->where('programTeamID = ?', $rowListPrograms['programTeamID']);
        $result = $con->query($select);

        while($userInfo = $result->fetch_array())
        {
            $userID=$userInfo['userID'];
            
            $userAppCopiesInsertArray=array();
            $userAppCopiesInsertArray['userAppCopyProgramID']=$programID;
            $userAppCopiesInsertArray['userAppCopyAppCopyID']=$appCopyID;
            $userAppCopiesInsertArray['userAppCopyUserID']=$userID;
            
            $con = db::getDefaultAdapter();
            $con->insert('userAppCopies',$userAppCopiesInsertArray);
            
            
            // set token taken: //
            Accounts::insertTokenHistoryRow(array('orgID'=>$rowAppCopy['appCopyOrganizationID'],'time'=>time(),'userID'=>$userInfo['userID'],'userEmail'=>$userInfo['userEmail'],'copyID'=>$rowAppCopy['appCopyID'],'copyName'=>$rowAppCopy['appCopyName']));
            Accounts::useToken($rowAppCopy['appCopyOrganizationID']);
            //////////////////////
            
            
        }
        
        //////////////////////////////////////

        /// preparing to send //
        // Checks if it is time to send the app
        //
        ////////////////////////
        
	$date = new DateTime();
	$date->setTimestamp($stampApp);
	$diff = $date->diff($now);
       // if($diff->h ==0 && $diff->d ==0 && $diff->m ==0 && $diff->y ==0 || $debug) 
        
        
        
        $appAdress=$rowApp['appAddress'];
//        if(AvbDevPlatform::isLocalMachine()) {
//            $appAdress="localhost.".$appAdress;
//        }
        

        
        $appAdress=str_replace(".com",".".AvbDevPlatform::getServerName(true),$appAdress);

        
        
        
        
        
        // check on start conf //
        if($rowApp['appOnStart']!="") {
                set_time_limit (60*5);
                
                $getSettingsUrl="http://".$appAdress.$rowApp['appOnStart'];
                $getSettingsUrl=  str_replace("[appID]", $appCopyId, $getSettingsUrl);
                echo "<hr>";
                echo $getSettingsUrl;
                $res=file_get_contents($getSettingsUrl);
                //echo "<hr>";
                //var_dump($res);
                //echo "<hr>";
                //echo "in";
            } 
            
            
            echo "should pass";
            
            
        /////////////////////////
        
        
        
        
        if($rowAppCopy['appCopyAutoEmail']=="0") {
            
            $appCopyId=$rowAppCopy['appCopyID'];
            $appCronURL="http://{$appAdress}cron.php?appID={$appCopyId}";
            $res=  file_get_contents($appCronURL);
            echo $res;
        }
        else {
            /// send the app to the right team //
            $select = $con->select()->from('programTeams')->join('teamsUsers', 'teamUserTeamID = programTeamTeamID')->join('users', 'teamUserUserID = userID')->where('programTeamID = ?', $rowListPrograms['programTeamID']);
            $result = $con->query($select);
            $addressArray = array();
            $contentArray = array();
            $userDataArray = array();
            $c=0;
            while($userInfo = $result->fetch_array())
            {
                    $token = Auth::generateAuthFromURL($userInfo['userID'], $userInfo['userUserKindID'], $rowListOfApps['programAppCopyAppCopyID'], $rowListPrograms['programTeamProgramID']);
                    //var_dump($userInfo);
                    
                    $tempArr=array();
                    $tempArr['userName']=$userInfo['userName'];
                    $tempArr['userEmail']=$userInfo['userEmail'];
                    $tempArr['userID']=$userInfo['userID'];
                    $tempArr['userUserKindID']=$userInfo['userUserKindID'];
                    $usersDebug[]=$tempArr;
                    
                    
                    $addressArray [$tempArr['userName']] = $userInfo['userEmail'];
                    

                    $userAppCopy = array(
                                    'userAppCopyUserID'		=>	$userInfo['userID'],
                                    'userAppCopyAppCopyID'	=> $rowListOfApps['programAppCopyAppCopyID'],
                                    'userAppCopyProgramID'	=> $rowListPrograms['programTeamProgramID'],

                    );
                    
                    //var_dump($userAppCopy);
                    
                    
                    if($rowAppCopy['app_email_title']!="") {
                        $subject=$rowAppCopy['app_email_title'];
                    }
                    else {
                        $subject='Welcome to the program';
                    }
                    
                    if($rowApp['appIndex']!='') {
                        $url='http://'.$baseAddressApp.$rowApp['appIndex'];
                        $url=str_replace("[appID]",$userAppCopy['userAppCopyAppCopyID'],$url);
                        $url=str_replace("[token]",$token,$url);
                    }
                    else {
                        $url='http://'.$baseAddressApp.'index.php?configID='.$userAppCopy['userAppCopyAppCopyID'].'&token='.$token;
                    }
                    
                    
                    $userDataArray[$c]['name']=$userInfo['userName'];
                    $userDataArray[$c]['email']=$userInfo['userEmail'];
                    $userDataArray[$c]['link']=$url;
                    
                    $array = array('subject' => $subject, '[%CODE%]'	=> '' , '[%BASE%]' => $url);
                    $contentArray [] = $array;
                    
                    
                    $con->insert('userAppCopies',$userAppCopy);
                    $c++;
                    
                    
                    
            }
            //var_dump($contentArray);

            if($debug) {
                echo "user:";
                var_dump($usersDebug);
            }
            else {
//            $email = new Email('programInvite',$rowAppCopy['app_email_content']);
//	    $email->sendEmail($addressArray, $contentArray);
                
            $user=$dbop->selectAssocRow("users","WHERE `userID`='{$rowAppCopy['appCopyUserID']}'");
            $organization=$dbop->selectAssocRow("organizations","WHERE `organizationID`='{$rowAppCopy['appCopyOrganizationID']}'");
            
            
            $templateName="programInvite";
            
            if($email_lang!=="en") {
                $templateName="programInvite_".$email_lang;
            }
            
            echo $templateName;
            
            $check=file_exists (dirname(__FILE__)."/Emails/{$templateName}.html");
                if($check) {

                    $BodyOrig=  file_get_contents(dirname(__FILE__)."/Emails/{$templateName}.html");
                    
                    
                    foreach($userDataArray as $userData):
                        $Body=$BodyOrig;
                        $parameters=array();  
                        $parameters['org_logo']=$organization['organizationImg'];
                        $parameters['email_content']=$rowAppCopy['app_email_content'];
                        $parameters['link']=$userData['link'];
                        $parameters['name']=ucfirst($userData['name']);
                        foreach($parameters as $key=>$value):             
                                $Body=str_replace("[".$key."]", $value,$Body);      
                        endforeach;
                        email::semailFrom(ucfirst($user['userName']), $userData['email'], $rowAppCopy['app_email_title'], $Body);
                    endforeach;
                    
                }
            }
            /////////////////////////////////////////
        }
        
        
        
        
    }
