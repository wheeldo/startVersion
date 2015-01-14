<div class="userLogsrap" ng-init="init();">
<div class="search_bar">
    <div>
        <select id="org_filter" onchange="filter_logs()">
            <?
            $s="SELECT
            DISTINCT(`logs`.userOrg)
            FROM
            `logs` ORDER BY `logs`.userOrg ASC";
            $p=mysql_query($s);
            $n=mysql_num_rows($p);
            for($i=0;$i<$n;$i++):
                $r=mysql_fetch_assoc($p);
            ?>
                <option value="<?=$r['userOrg']?>"><?=ucfirst($r['userOrg'])?></option>
            <? endfor; ?>
        </select>
        
    </div>
    
    
    
</div>
<table class="users_logs sortable">
    <thead>
    <tr>
        <th>Org</th>
        <th>User</th>
        <th>Time</th>
        <th>Action</th>
        <th>More</th>
    </tr>
    </thead>
    <tbody>
<?
$ans=$dbop->selectDB("logs","ORDER BY `time` DESC LIMIT 0,100");
for($i=0;$i<$ans['n'];$i++) {
     $row=mysql_fetch_assoc($ans['p']);
     
     
//     if($row['userName']=="") {
//         $userID=$row['userID'];
//         $dbop->connect(USER,PASSWORD,"wheeldo_db",DB_HOST);
//         $user=$dbop->selectAssocRow("users","WHERE `userID`='{$userID}'");
//         $orgSelection = (int) $user['userOrganizationIdSelect'];
//
//        // if not admin get orgId from user
//        if ($orgSelection=='0'){
//                $orgSelection = (int) $user['userOrganizationID'];
//        }
//
//        $con = db::getDefaultAdapter();
//        $selectOrg = $con->select()->from('organizations')->where('organizationID = ? ', array($orgSelection));
//        $resultOrg = $con->query($selectOrg);
//        $rowOrg = $resultOrg->fetch_array();
//        $orgName=$rowOrg['organizationName'];
//        $id=$row['id'];
//
//         $dbop_logs->connect(USER,PASSWORD,"wheeldo_logs",DB_HOST);
//         $dbop->updateDB("logs",array("userName"=>$user['userName'],"userOrg"=>$orgName),$id);
//     }
     
     
?>
   <tr>
       <td><?=$row['userOrg']?></td>
       <td><?=$row['userName']?></td>
       <td><?=date("d/m/Y H:i",$row['time'])?></td>
       <td><?=$row['type']?></td>
       <td><a href="javascript:void(0)" title="<?=$row['more']?>">More...</a></td>
   </tr>
<?}?>
   </tbody>
</table>
    
</div>


