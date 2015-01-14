<?php
$ds=DIRECTORY_SEPARATOR;
$base_path=dirname(__FILE__);
$base_path=$base_path. $ds . "..". $ds . ".." . $ds;
require_once($base_path.'/modules/modules.php');
require_once($base_path.'/checkLogin.php');

$dbop_logs=new dbop();
$dbop_logs->connect(USER,PASSWORD,"wheeldo_logs",DB_HOST);


$orgs=array();

$s="SELECT
    DISTINCT(`logs`.userOrg)
    FROM
    `logs` ORDER BY `logs`.userOrg ASC";
$p=mysql_query($s);
$n=mysql_num_rows($p);
for($i=0;$i<$n;$i++):
    $r=mysql_fetch_assoc($p);
    $orgs[]=$r;
endfor;

header('Content-type: application/json');
echo json_encode($orgs);