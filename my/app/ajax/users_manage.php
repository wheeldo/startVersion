<?php

$ds=DIRECTORY_SEPARATOR;
$base_path=dirname(__FILE__);
$cache_path=$base_path. $ds . ".." . $ds . "cache" . $ds;
$base_path=$base_path. $ds . "..". $ds . ".." . $ds;
require_once($base_path.'/modules/modules.php');
require_once($base_path.'/checkLogin.php');


$sql="SELECT
organizations.organizationName,
users.userID,
users.userName,
users.userDepartment,
users.userPosition,
users.userLevel,
users.userPassword,
users.userEmail,
users.userEmpID,
users.userOrganizationID,
users.userOrganizationIdSelect,
users.userUserKindID,
users.is_manger,
users.userPhotoID,
users.userRetainerPaidDate,
users.userPaypalToken,
users.userPayPalAccount,
users.userPhone,
users.userInactive
FROM
users
INNER JOIN organizations ON users.userOrganizationID = organizations.organizationID
WHERE
users.userUserKindID > 1
ORDER BY
organizations.organizationName ASC";
$p=mysql_query($sql);
$n=mysql_num_rows($p);

$users=array();
for($i=0;$i<$n;$i++):
    $user=mysql_fetch_assoc($p);
    $users[]=$user;
endfor;


$return=json_encode($users);

header('Content-type: application/json');
echo $return;
