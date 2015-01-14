 <?php
  
	require_once('src/saaspose.php');
        
        
        function deleteDir($path){
            return !empty($path) && is_file($path) ?
                @unlink($path) :
                (array_reduce(glob($path.'/*'), function ($r, $i) { return $r && deleteDir($i); }, TRUE)) && @rmdir($path);
        }
        
        $ds=DIRECTORY_SEPARATOR;
        $root_path= __DIR__ .$ds."..".$ds."..".$ds;
        $root_path_for_output=$root_path."uploads".$ds."presentations".$ds;
        
        require $root_path."modules".$ds."modules.php";
        
	//App SID
	$AppSID = "c0069cfd-e16a-4f6d-a841-7d810ed0508d";
	//App Key
	$AppKey = "cc3824109fc395af37055ab659faf520";
	//Base Product URI
	$BaseProductUri = "http://api.saaspose.com/v1.0";

	if(isset($_REQUEST['AppSID']) == 1 && isset($_REQUEST['AppKey']) == 1)
	{
		$AppSID = $_REQUEST['AppSID'];
		
		$AppKey = $_REQUEST['AppKey'];
	}
		
	//web server location to save file
	$OutPutLocation = "C:\\TempFiles\\";
	
	//Document output formats
        $SlideSaveFormat = array(
                    "TIFF" => "TIFF",
                    "PDF" => "PDF",
                    "PPTX" => "PPTX",
                    "XPS" => "XPS"
            );
	
	 
	 if(isset($_REQUEST["convert"]) == 1)
	 {
             
                set_time_limit(250);
                $unique=time();
                if(isset($_POST['name'])) {
                    $unique=$_POST['name'];
                }
                
                
             
                $file = $_FILES["file"]["name"];
                
                $ex=explode(".",$file);
                $ext=$ex[count($ex)-1];
                
                $fileName=$unique.".".$ext;

                
                // delete older file:
                if (file_exists($root_path_for_output."Temp" . $ds . $fileName)) {
                    unlink($root_path_for_output."Temp" . $ds . $fileName);
                }
                
                // delete older images:
                $folder=$root_path_for_output."Output".$ds.$unique;
                deleteDir($folder);
                
		if ($_FILES["file"]["error"] > 0)
		{
			echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
		}
		else
		{
     
			move_uploaded_file($_FILES["file"]["tmp_name"],	$root_path_for_output."Temp" . $ds . $fileName);
		} 

                
                //var_dump($fileName);
		
		//Upload file to Sasspose server
		UploadFile( $root_path_for_output."Temp" .$ds. $fileName, "", $BaseProductUri . "/storage/file/");  
                
                
                
                
                
                
                for($i=1;$i<200;$i++) {
                    // create image:
                    
                    $url="http://my.wheeldo.com/vendor/asposeConvertor/slideImage/$fileName/$i";
                    $url=str_replace(".com",".".AvbDevPlatform::getServerName(),$url);
                    $res=file_get_contents($url);
                    //echo "http://my.wheeldo.com/vendor/asposeConvertor/slideImage/$fileName/$i";
                    //var_dump($res);
                    if($res!="1") {
                        break;
                    }
                }
		
//		//build URI
//		$strURI = $BaseProductUri . "/slides/" . $fileName . "?format=" . $_REQUEST["OutPutType"];
//		
//		//sign URI
//		$signedURI = Sign($strURI);
//		
//		$responseStream = processCommand($signedURI, "GET", "", "");
//		
//		$v_output = ValidateOutput($responseStream);
//
//		if ($v_output === "") 
//		{
//			saveFile($responseStream, $OutPutLocation . getFileName($file) . "." . $_REQUEST["OutPutType"]);
//			header('Location:download.php?file='. $OutPutLocation . getFileName($file).".".$_REQUEST["OutPutType"]);
//		} 
//		else 
//			echo $result;
	}

?>

 <form action="index.php?convert=1" method="post" enctype="multipart/form-data">

   <p>Upload File  :
     <input type="file" name="file" id="file" />
<br>
 <br />
 <input type="submit" name="submit" value="Submit" />
      </p>
 </form>

