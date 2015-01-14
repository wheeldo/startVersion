<?php
require_once('modules/modules.php');

if(isset($_POST['request']) && isset($_POST['key']) && isset( $_POST['login']))
{
    
    $req=$_POST['request'];
    $api = new APIFunctionsAD();
    echo $api->$req($_POST['function_data']);

    
    
}
else
{
	var_dump($_POST);
}