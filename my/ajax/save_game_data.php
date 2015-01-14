<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');


$auth = Auth::isLogin();
$user = $auth->getUser();
$userId = $user->getID();

$orgSelection = (int) $user->getData('userOrganizationIdSelect');

// if not admin get orgId from user
if ($orgSelection=='0'){
	$orgSelection = (int) $user->getData('userOrganizationID');
}

$copyID=$_POST['copyID'];
$dataArray=$_POST['dataArray'];
$email_lang=isset($_POST['email_lang'])?$_POST['email_lang']:"en";


$exp1=explode("$",$dataArray);

foreach($exp1 as $parameter):
    $exp2=explode("|=|",$parameter);
    $key=$exp2[0];
    $value=$exp2[1];
    $postData[$key]=$value;
endforeach;


if(isset($postData['new_players'])) {
   $exp3=explode('___',$postData['new_players']);
   unset($postData['new_players']);
   $c=0;
   foreach($exp3 as $user):
       $exp4=explode("|",$user);
       $postData['new_players'][$c]['name']=$exp4[0];
       $postData['new_players'][$c]['email']=$exp4[1];
       $postData['new_players'][$c]['empID']=$exp4[2];
   
       $c++;
   endforeach;
}



function setUpdateOrg($orgID) {
    global $dbop;
    $fields=array();
    $fields['teamsList_uptodate']=0;
    $dbop->updateDB('organizations',$fields,$orgID,"organizationID");
}


// create team (if needed) //
if($postData['recipients_type']=="new" || $postData['recipients_type']=="file") {
    $fields=array();
    $fields['teamID']=null;
    $fields['teamName']=$postData['team_name'];
    $fields['teamDescription']="";
    $fields['teamOrganizationID']=$orgSelection;
    $fields['teamUserID']=$userId;
    $teamID=$dbop->insertDB("teams",$fields,false);
    
    setUpdateOrg($orgSelection);
    
}
///////////////////////////

// insert user to db and register to the team (if needed) //
if($postData['recipients_type']=="new" || $postData['recipients_type']=="file") {
    foreach($postData['new_players'] as $user):
        
        $fields=array();
        $fields['userID']=null;
        $fields['userName']=$user['name'];
        $fields['userEmail']=$user['email'];
        $fields['userOrganizationID']=$orgSelection;
        $fields['userUserKindID']=1;
        $fields['is_manger']=0;
        $fields['userInactive']=0;
        $fields['userEmpID']=$user['empID'];
        $fields['userRegTime']=time();
        $insertID=$dbop->insertDB("users",$fields,false);


        $fields=array();
        $fields['teamUserID']=null;
        $fields['teamUserUserID']=$insertID;
        $fields['teamUserTeamID']=$teamID;
        $insert=$dbop->insertDB("teamsUsers",$fields,false);
    endforeach;
    
    setUpdateOrg($orgSelection);
    
}
//////////////////////////////////////////////////////////////

if($postData['recipients_type']=="exist") {
    $teamID=$postData['team_id'];
}





// update copy data //
$copyFields=array();
$copyFields['app_email_title']=$postData['email_subject'];
$copyFields['app_email_content']=json_decode($postData['email_content'],true);
$copyFields['appCopyName']=$postData['game_name'];
$copyFields['appCopyTeam']=$teamID;
$copyFields['appCopyTerminate']=0;
$dbop->updateDB("appCopies",$copyFields,$copyID,"appCopyID");
//////////////////////


// create program //
$arr = array(
    'programDescription'		=> 'Auto generated program.',
    'programUserID'			=> $userId,
    'programOrganizationID'         => $orgSelection,
    'programName'			=> 'Instant program for - '.$postData['game_name'],
    'programOriginalID'		=> -1,
    'programPrivate'		=> 0,
    'programInactive'		=> 0,
    'programCategoryID'		=> 1
);
//Initializes appCopy object same thing for public and private
$program = new program($arr); 
if(Auth::canAccess($auth, 'createProgram'))
{
        //creates new app copy in db
        $program->store();
}

$program->addAppCopy($copyID,'0 0',$order = 1);
////////////////////

$appCopyId=$copyID;

// run the program //
$fullTime='1970-01-01 00:00:00';
$program->registerTeam($teamID, $fullTime,1);
      
$programId=$program->getTeamProgramTeams($teamID);
include "../runProgram.php";
/////////////////////