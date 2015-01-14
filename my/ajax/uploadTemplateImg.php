<?php

$img_id=$_POST['id'];

if(!empty($_FILES)) {
    
    if ($_FILES["img"]["error"] > 0) {
        
    }
    else {
        set_time_limit(200);
        include '../vendor/cloudinary/Cloudinary.php';
        include '../vendor/cloudinary/Uploader.php';
        include '../vendor/cloudinary/Api.php';


        \Cloudinary::config(array( 
          "cloud_name" => "wheeldo", 
          "api_key" => "767556142719463", 
          "api_secret" => "JZkUVUZsUaNOS_YKzeXkOjQaXbE" 
        ));

        $res= \Cloudinary\Uploader::upload($_FILES["img"]["tmp_name"],
        array(
           "public_id" => $img_id
//           "crop" => "limit", "width" => "200", "height" => "200",
//           "eager" => array(
//             array( "width" => 100, "height" => 100, 
//                    "crop" => "thumb", "gravity" => "face",
//                    "radius" => 0, "effect" => "sepia" )
//           )
        ));
        
        
        if(isset($res['url'])) {
            $ds=DIRECTORY_SEPARATOR;
            $base_path=dirname(__FILE__);
            $file_name=md5($img_id);
            $file_location=$base_path.$ds."..".$ds."uploads".$ds."tempFiles".$ds.$file_name.".txt";
            unlink($file_location);
            file_put_contents($file_location,json_encode($res)); 
            $res['status']="ok";
            $res['data']=$result;
            echo json_encode($res);
            die();
        } 
    }
}
