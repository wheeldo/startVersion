<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');

$table=$_POST['table'];
$id=$_POST['id'];
$altIDKey=isset($_POST['altIDKey'])?$_POST['altIDKey']:null;
$key=$_POST['key'];
$value=$_POST['value'];

$fields=array();
$fields[$key]=$value;
$dbop->updateDB($table,$fields,$id,$altIDKey);