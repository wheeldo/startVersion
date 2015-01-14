<?php

$name=$_GET['name'];
$ex=explode(".",$name);
unset($ex[count($ex)-1]);
$strippedName=implode(".",$ex);

$slide=$_GET['slide'];
if((int)$slide<10)
    $slide="0".(string)$slide;

$ds=DIRECTORY_SEPARATOR;
$root_path= __DIR__ .$ds."..".$ds."..".$ds;
$root_path_for_output=$root_path."uploads".$ds."presentations".$ds;

require __DIR__.$ds."Aspose".$ds."Cloud".$ds."Exception".$ds."AsposeCloudException.php";
require __DIR__.$ds."Aspose".$ds."Cloud".$ds."Common".$ds."AsposeApp.php";

require __DIR__.$ds."Aspose".$ds."Cloud".$ds."Common".$ds."Utils.php";
require __DIR__.$ds."Aspose".$ds."Cloud".$ds."Common".$ds."Product.php";
require __DIR__.$ds."Aspose".$ds."Cloud".$ds."Storage".$ds."Folder.php";
require __DIR__.$ds."Aspose".$ds."Cloud".$ds."Slides".$ds."Converter.php";


use \Aspose\Cloud\Exception\AsposeCloudException as Exception;
use \Aspose\Cloud\Common\AsposeApp;
use Aspose\Cloud\Common\Utils;
use Aspose\Cloud\Common\Product;
use Aspose\Cloud\Storage\Folder;
use Aspose\Cloud\Slides\Converter;

$folder=$root_path_for_output."Output".$ds.$strippedName;


if (!file_exists($folder)) {
    mkdir($folder, 0777);
   // echo "The directory $folder was successfully created.";
   // exit;
} else {
   
}

//sepcify App SID
AsposeApp::$appSID = "c0069cfd-e16a-4f6d-a841-7d810ed0508d";
//sepcify App Key
AsposeApp::$appKey = "cc3824109fc395af37055ab659faf520";

Product::$baseProductUri = "http://api.aspose.com/v1.1";

AsposeApp::$outPutLocation = $root_path_for_output . "Output".$ds.$strippedName.$ds.$slide."_";



//echo getcwd() . "/Output/";

//create Converter object
$doc = new Converter($name);
$slideNumber = $slide;
$imageFormat = "jpg";

echo 1;
//var_dump($doc);
//save in the required format
$doc->convertToImage($slideNumber, $imageFormat);


//$imginfo = getimagesize($photo);
//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//header("Content-type: {$imginfo['mime']}");
//readfile($photo); 