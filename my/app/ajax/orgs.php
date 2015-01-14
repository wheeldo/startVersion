<?php
$ds=DIRECTORY_SEPARATOR;
$base_path=dirname(__FILE__);
$base_path=$base_path. $ds . "..". $ds . ".." . $ds;
require_once($base_path.'/modules/modules.php');
require_once($base_path.'/checkLogin.php'); 


$orgs=array();

$s="SELECT
organizations.organizationID,
organizations.organizationName,
organizations.organizationImg
FROM
organizations ORDER BY organizations.organizationName ASC";
$p=mysql_query($s);
$n=mysql_num_rows($p);
for($i=0;$i<$n;$i++):
    $r=mysql_fetch_assoc($p);
    $orgs[]=$r;
endfor;

header('Content-type: application/json');
echo json_encode($orgs);