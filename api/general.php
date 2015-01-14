<?php
require_once('modules/modules.php');
$_POST['op']($_POST);


function getUser($data) {
    global $dbop;
    $user=$dbop->selectAssocRow("users","WHERE `userID`='{$data['userID']}'");
    unset($user['userEmail']);
    echo json_encode($user);
}