<?php

class UPLOADER_AVB {
    
    
    public function __construct() {
        ;
    }
    
    public function uploadImage($file,$target,$size=null) {
        $allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
        $max_file='2';
        $image_ext = "";

        $userfile_name = $file['name'];
        $userfile_tmp =  $file['tmp_name'];
        $userfile_size = $file['size'];
        $userfile_type = $file['type'];

        $filename = basename($file['name']);
        $file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

        $move_to=$target.".".$file_ext;

        if((!empty($file)) && ($file['error'] == 0)) {

            foreach ($allowed_image_types as $mime_type => $ext) {
                    //loop through the specified image types and if they match the extension then break out
                    //everything is ok so go and check file size
                    if($file_ext==$ext && $userfile_type==$mime_type){
                            $error = "";
                            break;
                    }else{
                            $error = "Only <strong>".$image_ext."</strong> images accepted for upload<br />";
                    }
            }
            //check if the file size is above the allowed limit
            if ($userfile_size > ($max_file*1048576)) {
                    $error.= "Images must be under ".$max_file."MB in size";
            }

        }else{
                $error= "Please select an image for upload";
        }
        
        
        $uploaded=false;
        if (move_uploaded_file($file['tmp_name'], $move_to)) {
            $uploaded=true;
            $data = array('filename' => $filename);
        } else {
            $data = array('error' => 'Failed to save');
        }
        
        
        if($uploaded) {
            chmod($move_to, 0777);
            
            
            if($size!=null) {

                $width = $this->getWidth($move_to);
                $height = $this->getHeight($move_to);
                
                
                /////// check what to scale //////////

                $scale = 1;
                
                if ($width > $size['width']){
                     $scale = $size['width']/$width;
                     $heightAfterScale=$height*$scale;
                     if($heightAfterScale>$size['height'])
                         $scale = $size['height']/$height;
                     else 
                         $scale = $size['width']/$width;
                }
                //////////////////////////////////////
                
                
                if (true){
                        $uploaded = $this->resizeImage($move_to,$width,$height,$scale);
                }
                
                
                //Delete the thumbnail file so the user can create a new one
                if (file_exists($move_to)) {
                        //unlink($move_to);
                }
                
                
            }
            
            return $move_to;
            
        }

        //header('Content-type: text/html');
        //echo json_encode($data);



        return $error;


    }
    
    
    
    public function createThumbnail($origFile,$newFile,$size=null) {
        copy($origFile, $newFile);
        if($size!=null) {
                
                $width = $this->getWidth($newFile);
                $height = $this->getHeight($newFile);
                
                
                /////// check what to scale //////////

                $scale = 1;
                
                if ($width > $size['width']){
                     $scale = $size['width']/$width;
                     $heightAfterScale=$height*$scale;
                     if($heightAfterScale>$size['height'])
                         $scale = $size['height']/$height;
                     else 
                         $scale = $size['width']/$width;
                }
                //////////////////////////////////////
                
                
                if (true){
                        $uploaded = $this->resizeImage($newFile,$width,$height,$scale);
                }
                
            }
        
    }
    
    
    protected function resizeImage($image,$width,$height,$scale) {
            $image_data = getimagesize($image);
            $imageType = image_type_to_mime_type($image_data[2]);
            $newImageWidth = ceil($width * $scale);
            $newImageHeight = ceil($height * $scale);
            $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
            switch($imageType) {
                    case "image/gif":
                            $source=imagecreatefromgif($image); 
                            break;
                case "image/pjpeg":
                    case "image/jpeg":
                    case "image/jpg":
                            $source=imagecreatefromjpeg($image); 
                            break;
                case "image/png":
                    case "image/x-png":
                            $source=imagecreatefrompng($image); 
                            break;
            }
            imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);

            switch($imageType) {
                    case "image/gif":
                            imagegif($newImage,$image); 
                            break;
            case "image/pjpeg":
                    case "image/jpeg":
                    case "image/jpg":
                            imagejpeg($newImage,$image,90); 
                            break;
                    case "image/png":
                    case "image/x-png":
                            imagepng($newImage,$image);  
                            break;
        }

            chmod($image, 0777);
            return $image;
    }
    
    
    protected function getHeight($image) {
            $size = getimagesize($image);
            $height = $size[1];
            return $height;
    }
    //You do not need to alter these functions
    protected function getWidth($image) {
            $size = getimagesize($image);
            $width = $size[0];
            return $width;
    }
    
    
    
    
    
    
    
}


$uploader= new UPLOADER_AVB();