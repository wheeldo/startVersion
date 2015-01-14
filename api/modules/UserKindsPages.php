<?php
require_once ('db.php');
class UserKindsPages {
	
	protected $pages;
	
	private function __construct($pages)
	{
		$this->pages = $pages;
	}
	/**
	 * returns a userKindPages object which contains info on which pages a user can access
	 * @param Auth $auth an auth token of the user 
	 * @return UserKindsPages an object with which to check specific pages
	 */
	public static function getPermissions(Auth $auth)
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('userKindPages');
		$res = $con->query($select);
		$pages = array();
		$userKind = $auth->getUserKindID();
		while($row  = $res->fetch_array())
		{
			if(isset($pages[$row['userKindPagePageID']]))
				if($pages[$row['userKindPagePageID']])
					continue;
			$pages[$row['userKindPagePageID']] = (($row['userKindPageUserKindID'] == $userKind) ? true : false);
			
		}
		
		$instance = new self($pages);
		return $instance;
	}
	/**
	 * returns whether user has permission to access a single page
	 * @param Auth $auth auth token of user
	 * @param int $pageID ID of page to check
	 * @return boolean
	 */
	
	public static function getSinglePagePermissions(Auth $auth,$pageID)
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('userKindPages')->where('userKindPagePageID = ?',$pageID);
		$res = $con->query($select);
		$authorized = false;
		$userKind = $auth->getUserKindID();
		while($row  = $res->fetch_array())
		{
			
			if($row['userKindPageUserKindID'] == $userKind)
				return true;
				
		}
		return false;
	}
	
	/**
	 * checks whether a user can access a page
	 * @param int $pageID ID of page to check
	 * @return boolean
	 */
	public function hasPermission($pageID)
	{
		if(isset($this->pages[$pageID]))
			return $this->pages[$pageID];
		return false;
	}
}


?>