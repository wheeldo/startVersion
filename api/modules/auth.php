<?php

require_once('user.php');
require_once('db.php');


/// token valid time ////
define("LIMIT_D" , 0); // days
define("LIMIT_H" , 2); // hours
define("LIMIT_I" , 0); // minutes

$difInSec=LIMIT_I*60+LIMIT_H*3600+LIMIT_D*3600*24;
define("DIF_IN_SEC" , $difInSec);
/////////////////////////

class Auth{
	
	private $tokenVal;
	private $userID;
	private $userKindID;
	private $programID;

	/**
	 * @return the $tokenVal
	 */
	public function getTokenVal() {
		return $this->tokenVal;
	}

	/**
	 * creates a new auth object
	 * @param string $tokenVal string value of token with which it's indentified
	 * @param number $userID the id of the user with which it's indentified
	 * @param number $userKindID the id of the userKind with which the user is identified
	 */
	private function __construct($tokenVal ,$userID,$userKindID,$programID = null) {
		if(!session_id() != '')
			session_start();
		$this->tokenVal = $tokenVal;
		$this->userID = $userID;
		$this->userKindID = $userKindID;
		$this->programID = $programID;
		$_SESSION['tokenVal'] = $tokenVal;
	}
	
	/**
	 * creates a new auth object from token
	 * @param string $tokenVal unique session token
	 * @return number|Auth returns 0 if token doesn't exist or Auth corresponding to token
	 */
	public static function authFromToken($tokenVal,$appID=null){
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('tokens')->where('tokenVal = ?',$tokenVal);
		$result = $con->query($select);
                
                
                if($appID!=null) {
                    $selectURLS = $con->select()->from('urlTokens')->where('urlTokenVal = ? AND urlTokenAppCopyID = ? ',array($tokenVal,$appID));
                }
                else {
                    $selectURLS = $con->select()->from('urlTokens')->where('urlTokenVal = ?',$tokenVal);
                }
		$resultURLS = $con->query($selectURLS);
                

		
		if($result->num_rows > 0){
			$row = $result->fetch_array();
			$stamp = strtotime($row['tokenTime']);
			$date = new DateTime();
			$date->setTimestamp($stamp);
			$diff = $date->diff(new DateTime());
			//if((time()-$stamp)>DIF_IN_SEC)
                        if(false)
			{
				$delete = $con->delete()->from('tokens')->where('tokenVal = ?',$tokenVal);
				$con->query($delete);
				return 0;	
			}
			else
			{
				$arr = array('tokenTime' => date("Y-m-d H:i:s"));
				$update = $con->update()->table('tokens')->set($arr)->where('tokenVal = ?',$tokenVal);
				$con->query($update);
				$instance = new self($row['tokenVal'],$row['tokenUserID'],$row['tokenUserKindID'],$row['tokenProgramID']);
				return $instance;
			}
			
		}
                elseif($resultURLS->num_rows > 0) {
                        $row = $resultURLS->fetch_array();
			$stamp = strtotime($row['urlTokenTime']);
			$date = new DateTime();
			$date->setTimestamp($stamp);
			$diff = $date->diff(new DateTime());
			//if((time()-$stamp)>DIF_IN_SEC)
                        if(false)
			{
				$delete = $con->delete()->from('urlTokens')->where('urlTokenVal = ?',$tokenVal);
				$con->query($delete);
				return 0;	
			}
			else
			{
				$arr = array('urlTokenTime' => date("Y-m-d H:i:s"));
				$update = $con->update()->table('urlTokens')->set($arr)->where('urlTokenVal = ?',$tokenVal);
				$con->query($update);
				$instance = new self($row['urlTokenVal'],$row['urlTokenUserID'],$row['urlTokenUserKindID'],$row['urlTokenProgramID']);
                                /*
                                echo "<pre>";
                                var_dump($instance);
                                echo "</pre>";
                                 */
				return $instance;
			}
                }
		else{
			return 0;
		}
		
	}
	/**
	 * returns an auth object corresponding to login 
	 * @param string $userEmail user name
	 * @param string $userPassword unhashed password string
	 * @return Auth|number return auth if login is successful 0 otherwise
	 */
	public static function authFromLogin($userEmail,$userPassword){
		
		$con = db::getDefaultAdapter();		
		$hashedPass = hash('SHA256', $userPassword,false);
		$select = $con->select()->from('users')->where('userEmail = ?',$userEmail)->where('userPassword = ?',$hashedPass);
		$result = $con->query($select);
		if($result->num_rows > 0){
			$r = $result->fetch_assoc();
			$character_set_array = array( );
			$character_set_array[ ] = array( 'count' => 8, 'characters' => 'abcdefghijklmnopqrstuvwxyz' );
			$character_set_array[ ] = array( 'count' => 2, 'characters' => '0123456789' );
			$character_set_array[ ] = array( 'count' => 2, 'characters' => '!@#$+-*&?:' );
			$temp_array = array( );
			foreach ( $character_set_array as $character_set )
			{
				for ( $i = 0; $i < $character_set[ 'count' ]; $i++ )
				{
					$temp_array[ ] = $character_set[ 'characters' ][ rand( 0, strlen( $character_set[ 'characters' ] ) - 1 ) ];
				}
			}
			shuffle( $temp_array );
			$code = implode( '', $temp_array );
			$code .= $r['userID'];
			$code = hash('SHA256', $code,false);
			$delete = $con->delete()->from('tokens')->where('tokenUserID = ?',$r['userID']);
			$con->query($delete);
			$instance = new self($code,$r['userID'],$r['userUserKindID']);
			$instance->store();
			return $instance;
		}
		else
		{
			return 0;
		}
	}
	
	
	public static function getAuthFromUrlToken($urlToken)
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('urlTokens')->where('urlTokenVal = ?',$urlToken);
		$result = $con->query($select);
		if($result->num_rows > 0){
			$r = $result->fetch_assoc();
			$character_set_array = array( );
			$character_set_array[ ] = array( 'count' => 8, 'characters' => 'abcdefghijklmnopqrstuvwxyz' );
			$character_set_array[ ] = array( 'count' => 2, 'characters' => '0123456789' );
			$character_set_array[ ] = array( 'count' => 2, 'characters' => '!@#$+-*&?:' );
			$temp_array = array( );
			foreach ( $character_set_array as $character_set )
			{
				for ( $i = 0; $i < $character_set[ 'count' ]; $i++ )
				{
					$temp_array[ ] = $character_set[ 'characters' ][ rand( 0, strlen( $character_set[ 'characters' ] ) - 1 ) ];
				}
			}
			shuffle( $temp_array );
			$code = implode( '', $temp_array );
			$code .= $r['urlTokenUserID'];
			$code = hash('SHA256', $code,false);
			$delete = $con->delete()->from('tokens')->where('tokenUserID = ?',$r['urlTokenUserID']);
			$con->query($delete);
			$instance = new self($code,$r['urlTokenUserID'],$r['urlTokenUserKindID'],$r['urlTokenProgramID']);
			$instance->store();
			return $instance;
		}
		else
		{
			return 0;
		}
		
	}
	/**
	 * Generates an url access token
	 * @param int $userID user to whom the token belongs
	 * @param int $programID program to whom the token belongs
	 * @return string token value
	 */
	public static function generateAuthFromURL($userID,$userKindID,$appCopyID,$programID)
	{
                $dbop=new dbop();
                $code=$userID.hash("sha256",time());
                $fields = array(
				'urlTokenUserID'	 => $userID,
				'urlTokenUserKindID' => $userKindID,
				'urlTokenAppCopyID'	 => $appCopyID,
				'urlTokenProgramID'	 => $programID,
				'urlTokenVal'		 => $code,
				'urlTokenTime'		 => date("Y-m-d H:i:s"));
                
                $c=$dbop->insertDB("urlTokens",$fields,false);
                return $code;
                
                
                // old code /////////////////////////////////////////////////////////////////////////////////////////
            
		$character_set_array[ ] = array( 'count' => 22, 'characters' => 'abcdefghijklmnopqrstuvwxyz' );
		$character_set_array[ ] = array( 'count' => 21, 'characters' => '0123456789' );
		$character_set_array[ ] = array( 'count' => 21, 'characters' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
		$temp_array = array( );
		foreach ( $character_set_array as $character_set )
		{
			for ( $i = 0; $i < $character_set[ 'count' ]; $i++ )
			{
				$temp_array[ ] = $character_set[ 'characters' ][ rand( 0, strlen( $character_set[ 'characters' ] ) - 1 ) ];
			}
		}
		shuffle( $temp_array );
		$code = implode( '', $temp_array );
		
                
		$con = db::getDefaultAdapter();
		$arr = array(
				'urlTokenUserID'	 => $userID,
				'urlTokenUserKindID' => $userKindID,
				'urlTokenAppCopyID'	 => $appCopyID,
				'urlTokenProgramID'	 => $programID,
				'urlTokenVal'		 => $code,
				'urlTokenTime'		 => date("Y-m-d H:i:s"));
                $insertC=$con->insert('urlTokens',$arr);
		return $code;	
	}
	
	/**
	 * Checks if there is a user currently logged in from the session
	 * @return Auth|number
	 */
	public static function isLogin(){
		if(!session_id() != '')
			session_start();
        if(isset($_SESSION['tokenVal'])){
        	$auth = self::authFromToken($_SESSION['tokenVal']);
        	
			if((is_object($auth))){
				return $auth;
			}
        }
		return 0;
	}
	
	/**
	 * Returns the user to which the Auth object belongs
	 * @return User
	 */
	public function getUser()
	{
		return new User(null,$this->userID);
	}
	
	/**
	 * returns the ID of the user to whom the token belongs
	 * @return number
	 */
	public function getUserID()
	{
		return $this->userID;
	}
	/**
	 * Returns the ID of the user kind to which the Auth object belongs
	 */
	
	public function getUserKindID()
	{
		return $this->userKindID;
	}
	
	public function getProgramID()
	{
		return $this->programID;
	}
	/**
	 * Logs out a user, by destryoing session values and removing token from DB
	 * @return number 1 if success 0 otherwise
	 */
	public function logout(){
		if(!session_id() != '')
			session_start();
		if(isset($_SESSION['tokenVal'])){
			$con = db::getDefaultAdapter();
			$token = $con->real_escape_string($_SESSION['tokenVal']);
			$delete = $con->delete()->from('tokens')->where('tokenVal = ?',$token);
			$con->query($delete);
			unset($_SESSION['tokenVal']);	
            session_destroy();
			return 1;
		}
		return 0;
	}
	
	/**
	 * Stores the auth token in the DB
	 */
	private function store()
	{
		if(isset($this->programID))
			$arr = array(
					'tokenVal' 		  => $this->tokenVal,
					'tokenUserID' 	  => $this->userID,
					'tokenUserKindID' => $this->userKindID,
					'tokenTime'		  => date("Y-m-d H:i:s"),
					'tokenProgramID'  => $this->programID
			);
		else
			$arr = array(
					'tokenVal' 		  => $this->tokenVal,
					'tokenUserID' 	  => $this->userID,
					'tokenUserKindID' => $this->userKindID,
					'tokenTime'		  => date("Y-m-d H:i:s"),
			);
				
		$con = db::getDefaultAdapter();
		$con->insert('tokens',$arr);
	}
	
		
	
	/**
	 * checks whether a user can access a page
	 * @param Auth $auth auth object for user
	 * @param string $page name of page to check for example edit
	 * @param string $type type of object for this page for exaple appCopy
	 * @param int $id id of object to instantiate
	 * @return 1 if can access 0 otherwise
	 */
	public static function canAccess($auth,$page,array $objects = null)
	{
            
		if($auth instanceof Auth)
		{
			$con = db::getDefaultAdapter();
			$select = $con->select()->from('pages')->where('pageName = ?', $page);
			$result = $con->query($select);
			if($result->num_rows > 0)
			{
				$row = $result->fetch_array();
				$user = $auth->getUser();
				if($row['pageOperationUser'] == 1)
				{
                                    
					
					if(!$objects)
						return 0;
					foreach($objects as $object)
					{
						if(is_array($object))
							if(intval($object['id']) == $user->getID())
								return 1;
						if($object instanceof User)
							$userOn = $user;
						elseif(is_array($object))
						{
							$userOn = new User(null,intval($object['id']));
						}
						if( userKindsUserOperations::getSinglePagePermission($auth, $row['pageOperationID'], $userOn->getData('userUserKindID')) && $userOn->isSameOrg($user))
							return 1;
					}
				}
				else
				{
					
					if(userKindsOperations::getSinglePagePermissions($auth,$row['pageOperationID']))
					{
						if($objects)
						{
							foreach($objects as $object)
							{
								if(is_array($object))
									$dataStructure = new $object['type'](null,$object['id']);
								else
									$dataStructure = $object;
								if($dataStructure instanceof DataStructure)
								{
									if(!$dataStructure->isOwner($user) && !$dataStructure->isSameOrg($user))
									{
										return 0;
									}
								}
							}
						}
						
						return 1;
					}
				}
			}
			
			return 0;
		}
		else
		{
			return 0;
		}
	}
	
	
}
?>