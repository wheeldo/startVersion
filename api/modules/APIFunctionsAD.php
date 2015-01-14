<?php
require_once(ROOT .DS . 'modules' . DS . 'modules.php');

class APIFunctionsAD {
    
    private function isMobile($HTTP_USER_AGENT){   
        if(preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|sagem|sharp|sie-|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $HTTP_USER_AGENT))
        return true;
    else
        return false;
    }
    
    public function deviceDadaCollect($data) {
        global $dbop;
        $res=array();
        
        $copyID=$data['copyID'];
        
        $dontCollect=array();
        $dontCollect[]="127.0.0.1";
        $dontCollect[]="188.120.158.254";
        $dontCollect[]="62.219.237.117";
        
        $appCopy=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$copyID}'");
        $appID=$appCopy['appCopyAppID'];
        
        
        $fields=array();
        $fields['appID']=$appID;
        $fields['copyID']=$copyID;
        $fields['userID']=$data['userID'];
        $fields['ip']=$data['ip'];
        $fields['browser']=$data['browser'];
        $fields['mobile']=$this->isMobile($data['browser'])?1:0;
        $fields['time']=time();
        
        if(!in_array($data['ip'],$dontCollect)):
            // dont collect from development sites
            $dbop->insertDB("device_data_collect",$fields);
        endif;
        
        
        var_dump($fields);
    }
    
    public function getMenagerID($data) {
        global $dbop;
        $res=array();
        // function for reg form:
        $copyID=$data['appID'];
        $appCopy=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$copyID}'");
        $user=$dbop->selectAssocRow("users","WHERE `userID`='{$appCopy['appCopyUserID']}'");
           
        $res['menager']=$user;
        $res['copyRow']=$appCopy;
        return json_encode($res);
    }
    
    public function updateUserData($data) {
        global $dbop;
        // function for reg form:
        $copyID=$data['appID'];
        $userID=$data['userID'];
        $name=$data['name'];
        $email=$data['email'];
        $phone=$data['phone'];
        

        $use_token=isset($data['dont_use_token'])?false:true;
        
        $appCopy=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$copyID}'");
        $appID=$appCopy['appCopyAppID'];

        $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
        $teamID=$appCopy['appCopyTeam'];
        
        $orgId=$appCopy['appCopyOrganizationID'];
        
        
        // add user to users:
        $fields=array();

        if($name!='') 
            $fields['userName']=$name;
        if($email!='') 
            $fields['userEmail']=$email;
        if($phone!='') 
            $fields['userPhone']=$phone;
        $c=$dbop->updateDB("users",$fields,$userID,'userID');
        echo mysql_error();
        
        
        // take 1 token //
        if($use_token) {
            Accounts::insertTokenHistoryRow(array('orgID'=>$appCopy['appCopyOrganizationID'],'time'=>time(),'userID'=>$insertID,'userEmail'=>$email,'copyID'=>$appCopy['appCopyID'],'copyName'=>$appCopy['appCopyName']));
            Accounts::useToken($appCopy['appCopyOrganizationID']);
        }
        //////////////////
        
        
        return json_encode(array('status'=>$c));
    }
	
    public function addPlayer($data) {
        global $dbop;
        $copyID=$data['appID'];
        $name=$data['name'];
        $email=$data['email'];
        $phone=$data['phone'];
        $empID=$data['empID'];
        $photo=isset($data['photo'])?$data['photo']:"";
        $use_token=isset($data['dont_use_token'])?false:true;
        
        
        $appCopy=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$copyID}'");
        $appID=$appCopy['appCopyAppID'];

        $app=$dbop->selectAssocRow("apps","WHERE `appID`='{$appID}'");
        $teamID=$appCopy['appCopyTeam'];
        
        $orgId=$appCopy['appCopyOrganizationID'];


        // add user to users:
        $fields=array();
        $fields['userID']=null;
        $fields['userName']=$name;
        $fields['userEmail']=$email;
        $fields['userOrganizationID']=$orgId;
        $fields['userUserKindID']=1;
        $fields['is_manger']=0;
        $fields['userInactive']=0;
        $fields['userPhotoID']=$photo;
        $fields['userEmpID']=$empID;
        $fields['userPhone']=$phone;
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
        
        //echo AvbDevPlatform::getServerName();
        $check=file_get_contents($url);
        $res=json_decode($check,true);
        
        
        
        // take 1 token //
        if($use_token) {
            Accounts::insertTokenHistoryRow(array('orgID'=>$appCopy['appCopyOrganizationID'],'time'=>time(),'userID'=>$insertID,'userEmail'=>$email,'copyID'=>$appCopy['appCopyID'],'copyName'=>$appCopy['appCopyName']));
            Accounts::useToken($appCopy['appCopyOrganizationID']);
        }
        //////////////////
    }
        
        
        
        return json_encode(array('userID'=>$insertID));
        
    }
    
    public function teamByID($data) {
        $con = db::getDefaultAdapter();
        $teamID=$data['teamID'];
        $select = $con->select()->from('users')->join('teamsUsers', 'teamUserUserID = userID')->where('teamUserTeamID = ?',$teamID);
        $result = $con->query($select);
        $userArray= array();
        while($row = $result->fetch_array())
        {
                $userArray[]= array('name'  => $row['userName'],
                                    'userDepartment'  => $row['userDepartment'],
                                    'userPosition'  => $row['userPosition'],
                                    'userLevel'  => $row['userLevel'],
                                    'photo'	=> $row['userPhotoID'],
                                    'ID'	=> $row['userID']);
        }

        return json_encode($userArray);
        
    }
	
    public function teamByAppID($data) {
        global $dbop;
        $copyRow=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='{$data['appID']}'");
        $teamID=$copyRow['appCopyTeam'];

        $sql="SELECT users.* FROM `users` INNER JOIN teamsUsers ON users.userID = teamsUsers.teamUserUserID WHERE teamsUsers.teamUserTeamID = '{$teamID}'";
        $p=mysql_query($sql);
        //$n=mysql_num_rows($p);
        $i=0;
        $userArray= array();
        while($row=mysql_fetch_assoc($p)):
            set_time_limit(2);
            $userArray[]= array('name'  => $row['userName'],
                'userDepartment'  => $row['userDepartment'],
                'userPosition'  => $row['userPosition'],
                'userLevel'  => $row['userLevel'],
                'general_field_1'  => $row['general_field_1'],
                'general_field_2'  => $row['general_field_2'],
                'general_field_3'  => $row['general_field_3'],
                'general_field_4'  => $row['general_field_4'],
                'general_field_5'  => $row['general_field_5'],
                'general_field_6'  => $row['general_field_6'],
                'general_field_7'  => $row['general_field_7'],
                'general_field_8'  => $row['general_field_8'],
                'general_field_9'  => $row['general_field_9'],
                'general_field_10'  => $row['general_field_10'],
                'photo'	=> $row['userPhotoID'],
                'empID'	=> $row['userEmpID'],
                'hashedEmail'	=> sha1(strtolower($row['userEmail'])),
                'ID'	=> $row['userID']);
        $i++;
        endwhile;

        return json_encode($userArray);
    }
    
    
    public function getUserOrgLogo($data) {
        $con = db::getDefaultAdapter();
        global $dbop;
        $userID=$data['userID'];
        $select = $con->select()->from('users')->where('userID = ?',$userID);
        $result = $con->query($select);
        $user = $result->fetch_array();
        
        $orgID=$user['userOrganizationID'];
        
        $select = $con->select()->from('organizations')->where('organizationID = ?',$orgID);
        $result = $con->query($select);
        $organization = $result->fetch_array();
        
        $orgLogo="http://my.wheeldo.com/";
        $orgLogo.=$organization['organizationImg']!="" ? $organization['organizationImg'] : "uploads/organizations_logos/default.png";
        
        return $orgLogo;
    }
    
    
        public function sendMail($data)
	{
                $appID=$data['appID'];
                $userID=$data['userID'];
                $subject=$data['subject'];
                $content=$data['content'];
                
                
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('users')->where('userID = ? ', array($userID));
		$result = $con->query($select);
		
		$row = $result->fetch_array();
                //echo $row['userEmail'];
		if($c=email::semail($row['userEmail'],$subject, $content))
			$success = 1;
		else
			$success = 0;
		return json_encode(array('success' => $success, 'res'=>$row['userEmail']));
	}
        
        
        public function sendMailFromUser($data)
	{
                $appID=$data['appID'];
                $userFromID=$data['userFromID'];
                $userID=$data['userID'];
                $subject=$data['subject'];
                $content=$data['content'];
                
                
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('users')->where('userID = ? ', array($userID));
		$result = $con->query($select);
		
		$row = $result->fetch_array();
                $toEmail=$row['userEmail'];
                
                
                $select = $con->select()->from('users')->where('userID = ? ', array($userFromID));
		$result = $con->query($select);
		
		$row = $result->fetch_array();
                $from=array();
                $from['address']=$row['userEmail'];
                $from['name']=$row['userName'];
                
                //echo $row['userEmail'];
		if(email::semailFrom($from,$toEmail,$subject, $content))
			$success = 1;
		else
			$success = 0;
		return json_encode(array('success' => $success));
	}
        
        public function sendMailFromName($data)
	{
                $appID=$data['appID'];
                $fromName=$data['fromName'];
                $userID=$data['userID'];
                $subject=$data['subject'];
                $content=$data['content'];
                
                
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('users')->where('userID = ? ', array($userID));
		$result = $con->query($select);
		
		$row = $result->fetch_array();
                $toEmail=$row['userEmail'];
                
                

                $from=array();

                
                //echo $row['userEmail'];
		if(email::semailFrom($fromName,$toEmail,$subject, $content))
			$success = 1;
		else
			$success = 0;
		return json_encode(array('success' => $success));
	}
        
        public function sendMailToAddress($data)
	{
                $appID=$data['appID'];
                $fromName=$data['fromName'];
                $fromAddress=$data['fromAddress'];
                $subject=$data['subject'];
                $content=$data['content'];
                

                $toEmail=$fromAddress;
                
                $exEmails=explode(";",$fromAddress);

                $from=array();

                
                //echo $row['userEmail'];
		if(email::semailFrom($fromName,$exEmails,$subject, $content))
			$success = 1;
		else
			$success = 0;
		return json_encode(array('success' => $success));
	}
        
        public function sendMailFromAddress($data)
	{
                $appID=$data['appID'];
                $userFromAddress=$data['userFromAddress'];
                $userFromName=$data['userFromName'];
                $userID=$data['userID'];
                $subject=$data['subject'];
                $content=$data['content'];
                
                $toEmail=isset($data['email'])?$data['email']:false;
                echo $toEmail;
                
                if(!$toEmail) {
                    $con = db::getDefaultAdapter();
                    $select = $con->select()->from('users')->where('userID = ? ', array($userID));
                    $result = $con->query($select);

                    $row = $result->fetch_array();
                    $toEmail=$row['userEmail'];
                }
                
                $from=array();
                $from['address']=$userFromAddress;
                $from['name']=$userFromName;
                
                //echo $row['userEmail'];
		if(email::semailFrom($from,$toEmail,$subject, $content))
			$success = 1;
		else
			$success = 0;
		return json_encode(array('success' => $success));
	}
        
        
        public function sendMailAddress($data)
	{
                $appID=$data['appID'];
                $email=$data['email'];
                $subject=$data['subject'];
                $content=$data['content'];

                
		if(email::semail($email,$subject, $content))
			$success = 1;
		else
			$success = 0;
		return json_encode(array('success' => $success));
	}
        
        
        public function getCode($data)
	{
            
                $userID=$data['userID'];
                $appID=$data['appID'];
                
                
                
		$user = new user(null,$userID);

		$con = db::getDefaultAdapter();
		$select = $con->select()->from('programAppCopies')->where('programAppCopyAppCopyID = ?', array($appID));
		$result = $con->query($select);
		
		$row = $result->fetch_array();
		$token = Auth::generateAuthFromURL($userID, $user->getData("userUserKindID"), $appID, $row['programAppCopyProgramID']);
		return json_encode(array('code' => $token,'userID'=>$userID));
	}
        
        
        public function userByID($data)
	{
                global $dbop;
                $userID=$data['userID'];
                
                $user=$dbop->selectAssocRow("users","WHERE `userID`='{$userID}'");
                
                
                $user['hashedEmail']=sha1($user['userEmail']);
                
                unset($user['userEmail']);
                
		return json_encode($user);
	}
	
	
}

?>
