<?php
require_once('modules/modules.php');
if(isset($_POST['request']) && isset($_POST['key']) && isset( $_POST['login']))
{
//        echo "<pre>";
//        print_r($_POST);
	$requestType = $_POST['request'];
	$requestKey  = $_POST['key'];
	$requestLogin   = $_POST['login'];
        $appID=$_POST['appConfig'];
        $appID=isset($_POST['appID']) ? $_POST['appID']: $appID;

        
        $dev=false;
        $tokens=$dbop->selectAssocRow("tokens","WHERE `tokenVal`='{$_POST['token']}'");
        if($tokens) {
            $dev=true;
            $userID=$tokens['tokenUserID'];
        }
        else {
            $urlTokens=$dbop->selectAssocRow("urlTokens","WHERE `urlTokenVal`='{$_POST['token']}'");
            $userID=$urlTokens['urlTokenUserID'];
        }
        
        
        switch($requestType):
            case "getUserData":
                $return_data=array();
                $appCopies=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='$appID'");
                ///// get user data /////
                $users=$dbop->selectAssocRow("users","WHERE `userID`='{$userID}'");

                $orgID=($dev)?$users['userOrganizationIdSelect']:$users['userOrganizationID'];
                $organizations=$dbop->selectAssocRow("organizations","WHERE `organizationID`='$orgID'");


                $return_data['appName']=$appCopies['appCopyName'];
                $return_data['userID']=$userID;
                $return_data['userName']=$users['userName'];
                $return_data['userPhoto']=$users['userPhotoID'];
                $return_data['userDepartment']=$users['userDepartment'];
                $return_data['userPosition']=$users['userPosition'];
                $return_data['userLevel']=$users['userLevel'];
                $return_data['userOrganizationID']=$orgID;
                $return_data['organizationName']=$organizations['organizationName'];
                $return_data['organizationLogo']=$organizations['organizationImg'];

                echo json_encode($return_data);        
                //////////////////////////////
                break;
                
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
                
                
            case "getTeamData":
                $return_data=array();
                $programAppCopies=$dbop->selectAssocRow("programAppCopies","WHERE `programAppCopyAppCopyID`='{$appID}'");
                $programID=$programAppCopies['programAppCopyProgramID'];
                $teams=array();
                $ans=$dbop->selectDB("programTeams","WHERE `programTeamProgramID`='$programID'");
                for($i=0;$i<$ans['n'];$i++) {
                        $row=mysql_fetch_assoc($ans['p']);
                        $teamID=$row['programTeamTeamID'];
                        
                        $teamsUsers=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='$teamID'");
                        if($teamsUsers) {
                            break;
                        }
                        
                }
                
                $team=$dbop->selectAssocRow("teams","WHERE `teamID`='$teamID'");
                
                if(!$team) {
                    echo json_encode(array("error"=>"no team"));
                    return;
                }
                
                /// get team users ///
                $teamUsers=array();
                $usersC=0;
                $ans=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='$teamID'");
                for($i=0;$i<$ans['n'];$i++) {
                        $row=mysql_fetch_assoc($ans['p']);
                        $teamUserID=$row['teamUserUserID'];
                        $users=$dbop->selectAssocRow("users","WHERE `userID`='{$teamUserID}'");
                        if($users){
                            
                            $teamUsers[$usersC]['userID']=$users['userID'];
                            $teamUsers[$usersC]['userName']=$users['userName'];
                            $teamUsers[$usersC]['userPhoto']=$users['userPhotoID'];
                            $usersC++;
                        }


                        
                }
                
                
                //////////////////////
                
                
                
                $return_data['teamID']=$teamID;
                $return_data['teamName']=$team['teamName'];
                $return_data['teamUsers']=$teamUsers;

                
                
                
                //var_dump($return_data);
                echo json_encode($return_data);
                    
                break;
                
                
                case "getTeamID":
                    $return_data=array();
                    $programAppCopies=$dbop->selectAssocRow("programAppCopies","WHERE `programAppCopyAppCopyID`='{$appID}'");
                    $programID=$programAppCopies['programAppCopyProgramID'];
                    $teams=array();
                    $ans=$dbop->selectDB("programTeams","WHERE `programTeamProgramID`='$programID'");
                    for($i=0;$i<$ans['n'];$i++) {
                            $row=mysql_fetch_assoc($ans['p']);
                            $teamID=$row['programTeamTeamID'];

                            $teamsUsers=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='$teamID'");
                            if($teamsUsers) {
                                break;
                            }

                    }
                    
                    $return_data['teamID']=$teamID;
                    echo json_encode($return_data);
                break;
                
                case "getTeamDataByTeamID":
                    
                    $teamID=$_POST['teamID'];
                    $team=$dbop->selectAssocRow("teams","WHERE `teamID`='$teamID'");
                
                    if(!$team) {
                        echo json_encode(array("error"=>"no team"));
                        return;
                    }

                    /// get team users ///
                    $teamUsers=array();
                    $usersC=0;
                    $ans=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='$teamID'");
                    for($i=0;$i<$ans['n'];$i++) {
                            $row=mysql_fetch_assoc($ans['p']);
                            $teamUserID=$row['teamUserUserID'];
                            $users=$dbop->selectAssocRow("users","WHERE `userID`='{$teamUserID}'");
                            if($users){

                                $teamUsers[$usersC]['userID']=$users['userID'];
                                $teamUsers[$usersC]['hash']=sha1(strtolower ($users['userEmail'].$users['userLevel']));
                                $teamUsers[$usersC]['empID']=strtolower($users['userEmpID']);
                                $teamUsers[$usersC]['hashedEmail']=sha1(strtolower($users['userEmail']));
                                $teamUsers[$usersC]['userName']=$users['userName'];
                                $teamUsers[$usersC]['userPhoto']=$users['userPhotoID'];
                                $teamUsers[$usersC]['userDepartment']=$users['userDepartment'];
                                $teamUsers[$usersC]['userPosition']=$users['userPosition'];
                                $usersC++;
                            }



                    }


                    //////////////////////


                    $return_data['teamID']=$teamID;
                    $return_data['teamName']=$team['teamName'];
                    $return_data['teamUsers']=$teamUsers;




                    //var_dump($return_data);
                    echo json_encode($return_data);
                break;
                
                case "getUserDataByID":
                    $userID=$_POST['userID'];
                    ///// get user data /////
                    $users=$dbop->selectAssocRow("users","WHERE `userID`='{$userID}'");

                    $orgID=($dev)?$users['userOrganizationIdSelect']:$users['userOrganizationID'];
                    $organizations=$dbop->selectAssocRow("organizations","WHERE `organizationID`='$orgID'");


                    $return_data['userID']=$userID;
                    $return_data['userName']=$users['userName'];
                    $return_data['userPhoto']=$users['userPhotoID'];
                    $return_data['userDepartment']=$users['userDepartment'];
                    $return_data['userPosition']=$users['userPosition'];
                    $return_data['userLevel']=$users['userLevel'];
                    $return_data['userOrganizationID']=$orgID;
                    $return_data['organizationName']=$organizations['organizationName'];
                    $return_data['organizationLogo']=$organizations['organizationImg'];
                    $return_data['empID']=$users['userEmpID'];
                    $return_data['hashedEmail']=sha1(strtolower($users['userEmail']));
                    echo json_encode($return_data);        
                    //////////////////////////////
                break;
            
                case "loginUser":
                    $userEmail=$_POST['userEmail'];
                    $userEmployeeID=$_POST['userEmployeeID'];
                    
                    $users=$dbop->selectDB("users","WHERE `userEmail`='{$userEmail}' AND `userLevel`='{$userEmployeeID}'");
                    
                    $req['canLogin']=0;
                    $ans=$dbop->selectDB("users","WHERE `userEmail`='{$userEmail}' AND `userLevel`='{$userEmployeeID}'");
                    for($i=0;$i<$ans['n'];$i++) {
                            $row=mysql_fetch_assoc($ans['p']);
                            $userIDToCheck=$row['userID'];
                            // check for specific app //
                            $ans=$dbop->selectAssocRow("teamsUsers","WHERE `teamUserUserID`='$teamID'");
                            
                            
                            
                            
                            
                    }
                    
                    
                    
                    echo json_encode($req);
                break;
        endswitch;
        
        
       
}