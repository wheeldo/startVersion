<?php
set_time_limit(200);
include 'vendor/cloudinary/Cloudinary.php';
include 'vendor/cloudinary/Uploader.php';
include 'vendor/cloudinary/Api.php';

$name=$_POST['file_name'];

file_put_contents ( "uploads".DIRECTORY_SEPARATOR.$name , $_POST['file']);

\Cloudinary::config(array( 
  "cloud_name" => "wheeldo", 
  "api_key" => "767556142719463", 
  "api_secret" => "JZkUVUZsUaNOS_YKzeXkOjQaXbE" 
));

$img_url=dirname(__FILE__).DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.$name;


$res= \Cloudinary\Uploader::upload($img_url,
array(
   "public_id" => md5(time())
//           "crop" => "limit", "width" => "200", "height" => "200",
//           "eager" => array(
//             array( "width" => 100, "height" => 100, 
//                    "crop" => "thumb", "gravity" => "face",
//                    "radius" => 0, "effect" => "sepia" )
//           )
));

if(isset($res['url'])) {
    //unlink($img_url);
    $url=$res['url'];
    if($_POST['img_effect']!="") {
        $ex=explode("upload/",$url);
        $url=implode("upload/".$_POST['img_effect']."/",$ex);
    }
    echo $url;
}