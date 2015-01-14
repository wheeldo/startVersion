<?php
require_once('modules/modules.php');

$userID=1234567890;
$enc=AvbDevPlatform::encrypt_Nmb($userID);
$dec=AvbDevPlatform::decrypt_Nmb($enc);
?>

<hr>
decoded: <?=$dec?> <br /><br /> 