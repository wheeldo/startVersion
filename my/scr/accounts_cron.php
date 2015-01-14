<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');
set_time_limit(60*5);


$ans=$dbop->selectDB("organizations");
for($i=0;$i<$ans['n'];$i++) {
    $row=mysql_fetch_assoc($ans['p']);
    $orgID=$row['organizationID'];
    $account=$dbop->selectAssocRow("accounts","WHERE `orgID`='{$orgID}'");
    
    if(!$account) {
        // get the older user
        $olderUser=$dbop->selectAssocRow("users","WHERE `userUserKindID`>1 AND `userOrganizationID`='{$orgID}' ORDER BY `userRegTime` ASC");
        
        
        $regDate=time(); 
        if($olderUser && $olderUser['userRegTime']!="0") {
            $regDate=(int)$olderUser['userRegTime']; 
        }
        
        $fields=array();
        $fields['orgID']=$orgID;
        $fields['regDate']=$regDate;
        $fields['pricingPackage']=0;
        $fields['validUntil']=$regDate+3600*24*7;
        $dbop->insertDB('accounts',$fields);
    }
}
