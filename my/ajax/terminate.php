<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');


$copyID=$_POST['copyID'];


$dbop->updateDB("appCopies",array("appCopyInactive"=>1),$copyID,"appCopyID");

