<?

$ds=DIRECTORY_SEPARATOR;
$base_path=dirname(__FILE__);
$base_path=$base_path. $ds . "..". $ds . ".." . $ds;
$cache_path=dirname(__FILE__) . $ds . ".." .$ds . "cache" . $ds;
require_once($base_path.'/modules/modules.php');
require_once($base_path.'/checkLogin.php');

$apps=array();
$appsC=0;

$user = $auth->getUser();
$userId = $user->getID();
$userKind = $user->getData('userUserKindID');
$orgSelection = (int) $user->getData('userOrganizationIdSelect');

// if not admin get orgId from user
if ($orgSelection=='0'){
	$orgSelection = (int) $user->getData('userOrganizationID');
}

function stripData($data) {
    $strippedData=str_replace ("___amp___","&",$data);
    return $strippedData;
}

$cache_file_name=$cache_path."tesms_$orgSelection.ca";

$usersData=array();

if(file_exists($cache_file_name)) {
    $cache_content= file_get_contents($cache_file_name);
    $cache_Ex=explode("___&___",$cache_content);
    $fromID=$cache_Ex[0];
    $usersData=json_decode($cache_Ex[1],true);
}
$res=array();
$op=$_POST['op'];
$data=json_decode(stripslashes(stripData($_POST['data'])),true);
switch($op):
    case "remove":
        
        // remove from cache:
        $c=0;
        foreach($usersData as $user):
            if(in_array($user['id'], $data)) {
                unset($usersData[$c]);
            }
            $c++;    
        endforeach;
        
        if($c>0) {
            if(file_exists($cache_file_name)) unlink ($cache_file_name);
            //file_put_contents($cache_file_name,$user['id']."___&___".json_encode($usersData));  
        }
        /////////////////////
        foreach($data as $userID):
            $dbop->deleteDB("users",$userID,"userID");
        endforeach;
        $res['status']="ok";
    break;
    
    case "assignTeams":
        $deleteFile=false;
        foreach($data['users'] as $userID):
            $ans=$dbop->selectDB("teamsUsers","WHERE `teamUserUserID`='$userID'");
            for($i=0;$i<$ans['n'];$i++) {
                 $row=mysql_fetch_assoc($ans['p']);
                 $teamUserID=$row['teamUserID'];
                 if(!in_array($row['teamUserTeamID'],$data['keep'])) {
                     $dbop->deleteDB("teamsUsers",$teamUserID,"teamUserID");
                     $deleteFile=true;
                 }
            }

            foreach($data['set'] as $teamID):
                $check=$dbop->selectAssocRow("teamsUsers","WHERE `teamUserUserID`='$userID' AND `teamUserTeamID`='{$teamID}'");
                if(!$check) {
                    $fields=array();
                    $fields['teamUserUserID']=$userID;
                    $fields['teamUserTeamID']=$teamID;
                    $dbop->insertDB("teamsUsers",$fields,false);
                    $deleteFile=true;
                }
            endforeach;
        endforeach;
        
        if($deleteFile && file_exists($cache_file_name)) unlink ($cache_file_name);
        $res['deleteFile']=$deleteFile;
        $res['status']="ok";
    break;
    
    
    case "deleteTeam":
        $deleteFile=false;
        
        $teamID=$data['teamID'];
        $dbop->deleteDB("teams",$teamID,"teamID");
        
        $check=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='$teamID'");
        if($check['n']>0) {
            mysql_query("DELETE FROM `teamsUsers` WHERE `teamUserTeamID`='{$teamID}'");
            $deleteFile=true;
        }
        
        if($deleteFile && file_exists($cache_file_name)) unlink ($cache_file_name);
        $res['deleteFile']=$deleteFile;
        $res['status']="ok";
    break;
    
    case "newTeam":
        $teamName=$data['teamName'];
        $fields=array();
        $fields['teamName']=$teamName;
        $fields['teamOrganizationID']=$orgSelection;
        $fields['teamUserID']=$userId;
        $dbop->insertDB("teams",$fields,false);
        $res['status']="ok";
    break;

    case "insertUsers":
        $users=$data['users'];
        $teamID=$data['teamID'];
        foreach($users as $user):
            $user['userOrganizationID']=$orgSelection;
            $user['userOrganizationIdSelect']=$orgSelection;
            $user['userUserKindID']=1;
            $user['is_manger']=0;
            $user['userRegTime']=time();
            $user['ls_admin']=0;
            $user['ls_is_super_admin']=0;
            $userID=$dbop->insertDB("users",$user,false);
            
            // assign to team:
            $fields=array();
            $fields['teamUserUserID']=$userID;
            $fields['teamUserTeamID']=$teamID;
            $dbop->insertDB("teamsUsers",$fields,false);
        endforeach;
        if(file_exists($cache_file_name)) unlink ($cache_file_name);
        $res['status']="ok";
    break;
    
    
    
endswitch;


header('Content-Type:application/json');
echo json_encode($res);