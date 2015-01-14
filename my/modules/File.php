<?php
require_once(ROOT .DS . 'modules' . DS . 'db.php');
require_once(ROOT .DS . 'modules' . DS . 'errorLogger.php');
class File {
	
	/**
	 * Stores a file in the system
	 * @param string $fileName the file name of the file to store. Should be stored in temp folder
	 * @param number $ownerID the ID of the user who uploaded the file
	 * @return number|boolean returnt the ID of the file or false if operaion failed;
	 */
	public static function storeFile($fileName,$ownerID)
	{
		$filePath = "./temp/".$fileName;
		$ext = pathinfo($fileName, PATHINFO_EXTENSION);
		$user = new User(null,$ownerID);
		if(file_exists($filePath))
		{
			$arr = array(
					'fileName'  		 => $fileName,
					'fileType'  		 => self::getMimeType($filePath),
					'fileUserID' 		 => $ownerID,
					'filePermissionID'   => 1,
					'fileExtension'		 => $ext,
					'fileOrganizationID' => $user->getData('userOrganizationID'),
					);
			
			$con = db::getDefaultAdapter();
			$con->insert('files',$arr);
			$id = $con->getInsertedID();
			
			$newPath = "./files/".$id.'.file';
			if(!rename($filePath,$newPath))
			{
				ErrorLogger::logOperationError('FileStoreFile', 'IOOperationFailed', 'File '.$fileName." could not be moved");
				$delete = $con->delete()->from('Files')->where('fileID = ?', $id);
			}
			else
				return $id;
		}
		else
			ErrorLogger::logOperationError('FileStoreFile', 'invalidArgumentSupplied', 'File '.$fileName." does not exist");
		return false;
	}
	
	/**
	 * deletes a file from the system
	 * @param number $fileID the ID of the file to delete
	 * @return boolean return true on success false otherwise
	 */
	public static function deleteFile($fileID)
	{
		if(file_exists("./files/".$fileID.'.file'))
		{
			if(unlink("./files/".$fileID.'.file'))
			{
				$con = db::getDefaultAdapter();
				$delete = $con->delete()->from('Files')->where('fileID = ?', $fileID);
				$con->query($delete);
				return true;
			}
			else
			{
				ErrorLogger::logOperationError('FileStoreFile', 'IOOperationFailed', 'File '."./files/".$fileID.'.file'." could not be deleted");
			}
		}
		else
			ErrorLogger::logOperationError('FileStoreFile', 'invalidArgumentSupplied', 'File '."./files/".$fileID.'.file'." does not exist");
		return false;
	}
	
	/**
	 * checks if a user has permission to access file
	 * @param number $fileID the ID of the file to access
	 * @param Auth $auth The authentication token of the user
	 * @return boolean return true if has permission, false otherwise
	 */
	public static function hasPermission($fileID, Auth $auth)
	{
		$user = $auth->getUser();
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('files')->where('fileID = ?',$fileID);
		$result = $con->query($select);
		if($result->num_rows > 0)
		{
			$fileInfo = $result->fetch_array();
			if($fileInfo['fileOrganizationID'] == $user->getData('userOrganizationID') || $fileInfo['fileUserID'] == $user->getID())
				return true;
		}
		return false;
	}
	
	/**
	 * Returns the row from the database concerning the file 
	 * @param number $fileID the ID of the file to get data for
	 * @return Array|NULL returns an Array of data if file found NULL otherwise
	 */
	public static function fileInfo($fileID)
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('files')->where('fileID = ?',$fileID);
		$result = $con->query($select);
		if($result->num_rows > 0)
			return $result->fetch_array();
		ErrorLogger::logOperationError('FileStoreFile', 'invalidArgumentSupplied', 'File of ID '.$fileID." does not exist");
		return null;
	}
	
	/**
	 * Returns the MIME type of a file
	 * @param  $file_path the path to the file
	 * @return string MIME type of file
	 */
	private static function getMimeType($file_path)
	{
		$size = getimagesize($file_path);
		if($size)
		{
			return $size['mime'];
		}
		$mtype = '';
		if (function_exists('mime_content_type')){
			$mtype = mime_content_type($file_path);
		}
		else if (function_exists('finfo_file')){
			$finfo = finfo_open(FILEINFO_MIME);
			$mtype = finfo_file($finfo, $file_path);
			finfo_close($finfo);
		}
		if ($mtype == ''){
			$mtype = "application/force-download";
		}
		return $mtype;
	}
}

?>