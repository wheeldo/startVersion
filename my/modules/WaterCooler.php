<?php
require_once('db.php');
date_default_timezone_set('America/New_York'); 

/**
 * @author Kenneth
 *
 */
class WaterCooler {
	protected $posts;
	protected $id;
	
	/**
	 * Initializes a new waterCooler
	 * @param number $programID the ID of the program it belongs to
	 * @param number $teamID	the ID of the team it belongs to
	 * @param number $waterCoolerID	the ID of the water cooler. Optional if given will ignore $teamID and $programID
	 */
	public function __construct($programID,$teamID,$waterCoolerID = null)
	{
		if(isset($waterCoolerID))
			$this->id = $waterCoolerID;
		else
		{
			$con = db::getDefaultAdapter();
			$select = $con->select()->cols(array('waterCoolerID'))->from('waterCoolers')->where('waterCoolerProgramID = ?',$programID)->where('waterCoolerTeamID = ?',$teamID);
			$res = $con->query($select);
			$row = $res->fetch_array();
                        
                        
                        // if no watercooler exists //
                        if($row==null) {
                            $con->insert('waterCoolers',array('waterCoolerProgramID'=>$programID,'waterCoolerTeamID'=>$teamID));
                            
                            $select = $con->select()->cols(array('waterCoolerID'))->from('waterCoolers')->where('waterCoolerProgramID = ?',$programID)->where('waterCoolerTeamID = ?',$teamID);
                            $res = $con->query($select);
                            $row = $res->fetch_array();
                        }
                        //////////////////////////////
                        
			$this->id = $row['waterCoolerID'];
		}
		
	}
	
	/**
	 * @return the $posts
	 */
	public function getPosts() {
		if(!isset($this->posts))
			$this->loadData();
		return $this->posts;
	}

	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Loads the data relevant for the waterCooler from the db.
	 * i.e. posts,comments,likes
	 */
	public function loadData()
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('waterCoolerPosts')->where('waterCoolerPostWaterCoolerID = ? ',$this->id);
		$res = $con->query($select);
		$posts = array();
		while($row = $res->fetch_array())
		{
			
			$posts [] = new Post($row['waterCoolerPostID'],$row);
		}
		$this->posts = $posts;
	}
	/**
	 * Adds a new post to the water cooler
	 * @param string $content the content of the post to add
	 * @param number $userID the ID of the user who posted 
	 */
	public function addPost($content,$userID)
	{
		$con = db::getDefaultAdapter();
		$arr = array(
				'waterCoolerPostWaterCoolerID' => $this->id,
				'waterCoolerPostUserID'		   => $con->real_escape_string($userID),
				'waterCoolerPostContent'	   => $con->real_escape_string($content),
                                'waterCoolerPostTime'	   => time()
				);
		$con->insert('waterCoolerPosts',$arr);
	}
	
	/**
	 * removes a post from the Water Cooler
	 * @param number $postID the ID of the post remove
	 */
	public function removePost($postID)
	{
		$con = db::getDefaultAdapter();
		
		$delete = $con->delete()->from('waterCoolerPosts')->where('waterCoolerPostID = ?',$postID)->where('waterCoolerPostWaterCoolerID = ?',$this->id);
		$con->query($delete);
	}
	/**
	 * Adds a like to a new post by a user
	 * @param number $postID the ID of the post to add a like to
	 * @param number $userID the ID of the user who liked
	 * @return boolean true if successful (i.e. user didn't like before) false otherwise
	 */
	public function addLikePost($postID,$userID)
	{
		
		$con = db::getDefaultAdapter();
		
		$select = $con->select()->from('waterCoolerPostsLikes')->where('waterCoolerPostLikePostID =?',$postID)->where('waterCoolerPostLikeUserID = ?',$userID);
		$res = $con->query($select);
		if($res->num_rows > 0)
		{
			return false;
		}
		$arr = array(
				'waterCoolerPostLikePostID' => $postID,
				'waterCoolerPostLikeUserID' => $userID,
		);
		$con->insert('waterCoolerPostsLikes',$arr);
		return true;
	}
	
	/**
	 * removes a like from a post if the user liked it previously. Doesn't check to see if user previously liked
	 * @param number $postID the ID of the post to remove a like from
	 * @param number $userID the ID of the user who wants to remove
	 */
	public function removeLikePost($postID,$userID)
	{
		$con = db::getDefaultAdapter();
		$del = $con->delete()->from('waterCoolerPostsLikes')->where('waterCoolerPostLikePostID = ?', $postID)->where('waterCoolerPostLikeUserID = ?',$userID);
		$con->query($del);
	}
	
	/**
	 * Adds a comment to a post in the waterCooler
	 * @param  $postID
	 * @param unknown_type $content
	 * @param unknown_type $userID
	 */
	public function addComment($postID,$content,$userID)
	{
		$con = db::getDefaultAdapter();
		$arr = array(
				'waterCoolerCommentWaterCoolerID' => $this->id,
				'waterCoolerCommentUserID'		  => $con->real_escape_string($userID),
				'waterCoolerCommentContent'	      => $con->real_escape_string($content),
				'waterCoolerCommentPostID'		  => $con->real_escape_string($postID),
                                'waterCoolerCommentTime'		  => time()
		);
		$con->insert('waterCoolerComments',$arr);
	}
	
	public function removeComment($commentID)
	{
		$con = db::getDefaultAdapter();
	
		$delete = $con->delete()->from('waterCoolerComments')->where('waterCoolerCommentID = ?',$commentID)->where('waterCoolerCommentWaterCoolerID = ?',$this->id);
		$con->query($delete);
	}
	
	public function addLikeComment($commentID,$userID)
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('waterCoolerCommentsLikes')->where('waterCoolerCommentLikeCommentID =?',$commentID)->where('waterCoolercommentLikeUserID = ?',$userID);
		$res = $con->query($select);
		if($res->num_rows > 0)
		{
			return false;
		}
		
		$arr = array(
				'waterCoolerCommentLikeCommentID' => $commentID,
				'waterCoolercommentLikeUserID' 	  => $userID,
		);
		$con->insert('waterCoolerCommentsLikes',$arr);
		return true;
	}
	
	public function removeLikeComment($commentID,$userID)
	{
		$con = db::getDefaultAdapter();
		$del = $con->delete()->from('waterCoolerCommentsLikes')->where('waterCoolerCommentLikeCommentID = ?', $commentID)->where('waterCoolerCommentLikeUserID = ?',$userID);
		$con->query($del);
	}
	
	
	public function str()
	{
		foreach($this->posts as $post)
			echo $post->str();
	}
}

class Post{
	private $likes;
	private $comments;
	private $id;
	private $data;
	public function __construct($id,$data = null)
	{
		$con = db::getDefaultAdapter();
		$this->id = $id;
		if(!isset($data)){
			
			$select = $con->select()->from('waterCoolerPosts')->where('waterCoolerPostWaterCoolerID = ?',$this->id);
			$res = $con->query($select);
			$this->data = $res->fetch_array();
		}	
		else
			$this->data = $data;
		$select = $con->select()->from('waterCoolerPostsLikes')->where('waterCoolerPostLikePostID = ?',$id);
		$res = $con->query($select);
		$this->likes = array();
		while($row2 = $res->fetch_array())
		{
			$this->likes [] = $row2['waterCoolerPostLikeUserID'];
		}
			
		$this->comments = array();
		$select = $con->select()->from('waterCoolerComments')->where('waterCoolerCommentPostID = ?',$id);
		$res = $con->query($select);
		while($row2 = $res->fetch_array())
		{
			$this->comments [] = new Comment($row2['waterCoolerCommentID'],$row2);
		}
		
	}
	
	
	/**
	 * @return the $likes
	 */
	public function getLikes() {
		return $this->likes;
	}

	/**
	 * @return the $comments
	 */
	public function getComments() {
		return $this->comments;
	}

	/**
	 * @return the $data
	 */
	public function getData() {
		return $this->data;
	}

	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	public function str()
	{
		echo 'post of id '.$this->id;
		var_dump($this->data);
		echo ' likes of post of id '.$this->id;
		var_dump($this->likes);
		foreach($this->comments as $post)
			echo $post->str();
	}


	
}

class Comment
{
	private $data;
	private $likes;
	private $id;
	
	public function __construct($id,$data = null)
	{
		$con = db::getDefaultAdapter();
		$this->id = $id;
		if(!isset($data)){
			$select = $con->select()->from('waterCoolerComments')->where('waterCoolerCommentWaterCoolerID = ?',$this->id);
			$res = $con->query($select);
			$this->data = $res->fetch_array();
		}
		else
			$this->data = $data;
		$select = $con->select()->from('waterCoolerCommentsLikes')->where('waterCoolerCommentLikeCommentID = ?',$id);
		$res = $con->query($select);
		$this->likes = array();
		while($row2 = $res->fetch_array())
		{
			$this->likes [] = $row2['waterCoolerCommentLikeUserID'];
		}
		
	}
	
	
	/**
	 * @return the $data
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @return the $likes
	 */
	public function getLikes() {
		return $this->likes;
	}

	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	public function str()
	{
		echo 'comment of id '.$this->id;
		var_dump($this->data);
		var_dump($this->id);
		echo ' likes of comment of id '.$this->id;
		
		var_dump($this->likes);
	}
}

?>