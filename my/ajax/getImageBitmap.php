<?


//$file_to_read=$_FILES['image']['tmp_name'];
//
//function encode64($file){
//    $extension = explode(".", $file);
//    $extension = end($extension);
//
//    $binary = fread(fopen($file, "r"), filesize($file));
//
//    return 'data:image/'.$extension.';base64,'.base64_encode($binary);
//}

//$result=array();
//
//
//
//$bitmap="";
//$file_handle = fopen($file_to_read, "r");
//while (!feof($file_handle)) {
//   $line = fgets($file_handle);
//   $bitmap.=$line;
//}
//fclose($file_handle);
//
//




if ($_FILES["image"]["error"] > 0){
    echo "Return Code: " . $_FILES["image"]["error"] . "<br>";
}
else {
    $ex=explode(".",$_FILES["image"]["name"]);
    $ext=$ex[(count($ex)-1)];
    $file_name="app_".time().".".$ext;
    move_uploaded_file($_FILES["image"]["tmp_name"],"../uploads/appsImages/" . $file_name);
    echo "http://my.wheeldo.com/uploads/appsImages/".$file_name;
}






//echo encode64($file_to_read);


//$result['img']=$bitmap;
//
//header('Content-Type:application/json');
//echo json_encode($result);
