<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');

if(!empty($_POST)) {   
    //die();
    $from=$_POST['from'];
    $to=$_POST['to'];
    $ans=$dbop->selectDB("users","WHERE `userOrganizationID`='172' AND `userID`>='{$from}' AND `userID`<='{$to}'");
    for($i=0;$i<$ans['n'];$i++) {
        $row=mysql_fetch_assoc($ans['p']);
         echo $i.")".$row['userName']."<br>";   

         $fields=array();
         $fields['teamUserUserID']=$row['userID'];
         $fields['teamUserTeamID']=1372;
         $dbop->insertDB("teamsUsers",$fields,false);
    }
}
?>
<form action="" method="post">
    <input type="text" name="from" placeholder="from" />
    <input type="text" name="to" placeholder="to" />
    <input type="submit" value="submit" />
    
</form>