<?php
require_once(ROOT .DS . 'modules' . DS . 'modules.php');
class APIFunctionsAD {
	
    
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
        $con = db::getDefaultAdapter();
        $select = $con->select()->from('appCopies')->where('appCopyID = ?',$data['appID']);
        $result = $con->query($select);
        $appCopy = $result->fetch_array();
        
        
        $teamID=$appCopy['appCopyTeam'];
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
		if(email::semail($row['userEmail'],$subject, $content))
			$success = 1;
		else
			$success = 0;
		return json_encode(array('success' => $success));
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
                
                
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('users')->where('userID = ? ', array($userID));
		$result = $con->query($select);
		
		$row = $result->fetch_array();
                $toEmail=$row['userEmail'];

                
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
        
        public function sendMailFullFlex($data)
	{
                $userFromAddress=$data['userFromAddress'];
                $userFromName=$data['userFromName'];
                $userToAddress=$data['userToAddress'];
                $subject=$data['subject'];
                $content=$data['content'];
                


                
                $from=array();
                $from['address']=$userFromAddress;
                $from['name']=$userFromName;
                
                //echo $row['userEmail'];
		if(email::semailFrom($from,$userToAddress,$subject, $content))
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
		return json_encode(array('code' => $token));
	}
	
	
}

?>
