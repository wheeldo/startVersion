<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');


$copyID=$_POST['copyID'];
$name=$_POST['name'];


$dbop->updateDB("appCopies",array("appCopyName"=>$name),$copyID,"appCopyID");