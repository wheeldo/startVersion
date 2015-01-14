<?php
require_once(ROOT .DS . 'modules' . DS . 'modules.php');

class APIFunctions {
	
	private $token;
	private $appCopyID;
	private $accessType;
	private $score;
	private $userID;
	private $content;
	private $subject;
	
	const ACCESS_EDIT = 1;
	const ACCESS_VIEW = 2;
	
	/**
	 * creates a new APIFunctions session 
	 * @param string $token token string for the user
	 * @param int $appCopyID ID of the app used
	 * @param int $accessType type of access user wants
	 */
	public function __construct($token,$appCopyID,$accessType = null,$score = null,$userID = null,$content = null,$subject = null)
	{
		$this->token = $token;
		$this->appCopyID = $appCopyID;
		$this->accessType = $accessType;
		if($score > -1)
			$this->score = $score;
		else
			$this->score = -1;
		$this->userID = $userID;
		$this->content = $content;
		$this->subject = $subject;
	}
	
	
	/**
	 * returns whether a session token exists for token value
	 * @return int 1 if exists and valid 0 otherwise
	 */
	public function getSession()
	{
		$auth = Auth::authFromToken($this->token);
		
		if($auth === 0)
			return json_encode(array('authorized' => 0));
		return json_encode(array('authorized' => 1));
	}
	
	/**
	 * returns data on user
	 * @return string json encoded array of user daya
	 */
	public function getUser()
	{
                //echo $this->getTeamID();
		$auth = Auth::authFromToken($this->token,$this->appCopyID);
		$user = $auth->getUser();
                
                
                
                
                $con = db::getDefaultAdapter();
		$select = $con->select()->from('organizations')->where('organizationID = ?',$user->getData('userOrganizationID'));
		$result = $con->query($select);
		$row = $result->fetch_array();
                
                
		$userArray= array('name'    => $user->getData('userName'),
				   		  'photo'	=> $user->getData('userPhotoID'),
						  'ID'		=> $user->getID(),
                                                  'teamID'	=> $this->getTeamID(),
						  'email'	=> $user->getData('userEmail'),
                                                  'organizationName' => $row['organizationName'],
                                                  'organizationLogo' => $row['organizationImg'],
                    );
		return json_encode($userArray);
	}
        
        /**
	 * returns data on tean
	 * @return string json encoded array of user daya
	 */
	public function getTeamID()
	{
		$auth = Auth::authFromToken($this->token);
		
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('teamsUsers')->where('teamUserUserID = ?',$auth->getUserID());
		$result = $con->query($select);
		$row = $result->fetch_array();
		
		$teamID = $row['teamUserTeamID'];
		return $teamID;
	}
	
	/**
	 * returns data on tean
	 * @return string json encoded array of user daya
	 */
	public function getTeam()
	{
		$auth = Auth::authFromToken($this->token);
		
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('teamsUsers')->where('teamUserUserID = ?',$auth->getUserID());
		$result = $con->query($select);
		$row = $result->fetch_array();
		
		$teamID = $row['teamUserTeamID'];
		$select = $con->select()->from('users')->join('teamsUsers', 'teamUserUserID = userID')->where('teamUserTeamID = ?',$teamID);
		$result = $con->query($select);
		$userArray= array();
		while($row = $result->fetch_array())
		{
			$userArray[]= array('name'  => $row['userName'],
								'photo'	=> $row['userPhotoID'],
								'ID'	=> $row['userID']);
		}
		
		return json_encode($userArray);
	}
	
	/**
	 * checks whether user can access a certain function or not
	 * @return int 1 if can access 0 otherwise
	 */
	public function canAccess()
	{          
                $auth = Auth::authFromToken($this->token);
                //// check if admin or developer /////
                //echo "heer";
                 $kind=$auth->getUserKindID();
  
                if($kind==4 || $kind==5) {
                    return json_encode(array('canAccess' => 1));
                }
                //////////////////////////////////////
                //echo "here";
		if($this->accessType == self::ACCESS_EDIT)
		{
			$action = 'appCopyEdit';
		}
		elseif($this->accessType == self::ACCESS_VIEW)
		{
			$action = 'appCopyView';
		}
		else
		{
			return json_encode(array('canAccess' => 0));
		}
		
		$con = db::getDefaultAdapter();
		//var_dump($this->token);
		
                //var_dump($auth);
		$select = $con->select()->from('appCopies')->where('appCopyID = ?', $this->appCopyID);
		$result = $con->query($select);
		$row = $result->fetch_array();
		if($action === 'appCopyView')
		{
			$multiple = $row['appCopyMultiple'];
			if($multiple == 0)
			{
				$select = $con->select()->from('userAppCopies')->where('userAppCopyUserID = ?', $auth->getUserID())->where('userAppCopyAppCopyID = ?', $this->appCopyID);
				$result = $con->query($select);
				if($result->num_rows > 0)
				{
					$row = $result->fetch_array();
					if($row['userAppCopyScore'] != -1)
					{
						return json_encode(array('canAccess' => 0));
					}
				}
				else
				{
					return json_encode(array('canAccess' => 0));
					
				}
			}
		}
		$canAccess = Auth::canAccess($auth, $action,array('AppCopy',$this->appCopyID));
                //var_dump($auth);
		$arr = array('canAccess' => $canAccess);
		$encoded = json_encode($arr);
		return  $encoded;
	}
	
	
	public function userFinished()
	{

		$auth = Auth::authFromToken($this->token);
		$con = db::getDefaultAdapter();
		$arr = array('userAppCopyScore' 	 => $this->score,
					 'userAppCopyFinishDate' => date("Y-m-d H:i:s"));

		$update = $con->update()->table('userAppCopies')->set($arr)->where('userAppCopyUserID = ? AND userAppCopyProgramID = ? AND userAppCopyAppCopyID = ?', array($auth->getUserID(),$auth->getProgramID(),$this->appCopyID));

		$con->query($update);
	}
	
	public function getCode()
	{
		$user = new user(null,$this->userID);
                
                //var_dump($user);
                
                
		$auth = Auth::authFromToken($this->token);
                
                //var_dump($auth);
                
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('userAppCopies')->where('userAppCopyAppCopyID = ? AND userAppCopyUserID = ?', array($this->appCopyID,$this->userID))->orderBy("userAppCopyScore");
		$result = $con->query($select);
                
                
		$row = $result->fetch_array();
		$token = Auth::generateAuthFromURL($this->userID, $user->getData("userUserKindID"), $this->appCopyID, $row['userAppCopyProgramID']);
		return json_encode(array('code' => $token));
	}


	public function sendMail()
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('userAppCopies')->where('userAppCopyAppCopyID = ? AND userAppCopyUserID = ?', array($this->appCopyID,$this->userID))
		->join('users', 'userAppCopyUserID = userID');
		$result = $con->query($select);
		
		$row = $result->fetch_array();
                //var_dump($row);
                //echo "user ID:".$this->userID."<br>";
                //echo "user email:".$row['userEmail']."<br>";
		if(email::semail($row['userEmail'],$this->subject, $this->content))
			$success = 1;
		else
			$success = 0;
		return json_encode(array('success' => $success));
	}
        
        public function getAllTeams() {
            $user = new user(null,$this->userID);
            $auth = Auth::authFromToken($this->token);
            $appID=$this->appCopyID;
            global $dbop;
            $appRow=$dbop->selectAssocRow("appCopies","WHERE `appCopyID`='$appID'");
            $orgID=$appRow['appCopyOrganizationID'];
            
            $res['teams']=array();
            $ans=$dbop->selectDB("teams","WHERE `teamOrganizationID`='$orgID' ORDER BY `teamName`");
            for($i=0;$i<$ans['n'];$i++) {
                    $res['teams'][$i]=mysql_fetch_assoc($ans['p']);
                    $Nousers=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='{$res['teams'][$i]['teamID']}'");
                    $res['teams'][$i]['users']=$Nousers['n'];
                    
            }
            
            
            $res=json_encode($res);
            return $res;
        }
	
}

?>
