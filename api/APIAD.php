<?php
require_once('modules/modules.php');

if(isset($_POST['request']))
//if(false)
{
    
    $function_data=isset($_POST['function_data'])?$_POST['function_data']:array();
    $req=$_POST['request'];
    $api = new APIFunctionsAD();
    echo $api->$req($function_data);

}
else
{
	var_dump($_POST);
}