<?
require_once('modules/modules.php');

$dec=AvbDevPlatform::decrypt_Nmb($_GET['userID']);
if(!$dec)
    die();

$userID=$dec;

$user=$dbop->selectAssocRow("users","WHERE `userID`='{$userID}'");
if(!$user)
    die();


$notify=false;
if(isset($_GET['no_image'])) {
    $notify=true;
}


//print_r($user);

$photo=$user['userPhotoID'];

$note="";
if(!empty($_FILES)) {
    
    if ($_FILES["userFile"]["error"] > 0) {
        
    }
    else {
        set_time_limit(200);
        include 'vendor/cloudinary/Cloudinary.php';
        include 'vendor/cloudinary/Uploader.php';
        include 'vendor/cloudinary/Api.php';


        \Cloudinary::config(array( 
          "cloud_name" => "wheeldo", 
          "api_key" => "767556142719463", 
          "api_secret" => "JZkUVUZsUaNOS_YKzeXkOjQaXbE" 
        ));

        $res= \Cloudinary\Uploader::upload($_FILES["userFile"]["tmp_name"],
        array(
           "public_id" => $userID,
           "crop" => "limit", "width" => "200", "height" => "200",
           "eager" => array(
             array( "width" => 100, "height" => 100, 
                    "crop" => "thumb", "gravity" => "face",
                    "radius" => 0, "effect" => "sepia" )
           )
        ));
        
        
        if(isset($res['url'])) {
            
            $fields=array();
            $fields['userPhotoID']=$res['url'];
            $dbop->updateDB("users",$fields,$userID,"userID");
            $photo=$res['url'];
            $note="Image uploaded successfully!";
        } 
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style>
            
            @font-face {
                font-family: 'Open Sans';
                font-style: normal;
                font-weight: 400;
                src: local('Open Sans'), local('OpenSans'), url(/css/fonts/OpenSans/OpenSans.woff) format('woff');
              }
            
            body {
                background-color:#EDEDED;
                color:#333333;
                font-family: 'Open Sans';
                font-weight:normal;
            }
            h3 {
                font-size:12px;
                margin:0px;
                margin-bottom:3px;
            }
          
            
            .wait {
                width:360px;
                height:150px;
                background-color:black;
                opacity:0.4;
                filter:alpha(opacity=40); 
                position:fixed;
                top:0px;
                left:0px;
                background-image:url(/img/wait_ge.gif);
                background-repeat:no-repeat;
                background-position:center center;
                display:none;
            }
            
            
        </style>
        <script type="text/javascript">
            function fileUploaded() {
                document.getElementById("wait").style.display="block";
            }
        </script>
    </head>
    <body>
        <div class="wait" id="wait">
            
        </div>
        <div style="height:120px;width:100px;float:left;">
            <div style="padding:1px;border:2px dashed #999999;height:auto;<?if($photo==""){?>height:90px;<?}?>">
                <?if($photo!=""){?><img style="height:90px;max-width:90px;margin:2px" src="<?=$photo?>" />
                    <?}else{?>
                        <img style="height:90px;max-width:90px;margin:2px" src="http://my.wheeldo.com/uimg_uid-<?=$userID?>___effect-c_fit,h_120,w_120.png" />
                    <?}?>
            </div>
        </div>
        <div style="height:120px;width:220px;float:left;margin-left:6px;overflow:hidden;">
            <? if($note!="") { ?>
                <?=$note?>
            <?}else{?>
                <h3>Hello <?=ucfirst($user['userName'])?>,</h3>
                <?if($photo!=""){?>
                    <h3>Replace profile image:</h3>
                <?}else{?>
                    <h3>Upload your picture so we can get to know you better:</h3>
                <?}?>
                    <form enctype="multipart/form-data" action="" method="post" onsubmit="fileUploaded();">
                        <input type="file" name="userFile" />
                        <br />
                        <input type="submit" value="Upload" />
                    </form>
            <?}?>
        </div>
    </body>
</html>
