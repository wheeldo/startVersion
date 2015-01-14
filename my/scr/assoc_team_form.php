<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');


if(isset($_POST['op'])) {
    switch($_POST['op']):
        case "getTeams":
            $orgID=$_POST['orgID'];
            echo '<option value="0">-------------</option>';
            $ans=$dbop->selectDB("teams","WHERE `teamOrganizationID`='{$orgID}' ORDER BY `teamName` ASC");
            for($i=0;$i<$ans['n'];$i++) {
                    $row=mysql_fetch_assoc($ans['p']);
                    echo '<option value="'.$row['teamID'].'">'.$row['teamName'].'</option>';
            }
        break;
        case "getUser":
            $userID=$_POST['userID'];
            $orgID=$_POST['orgID'];
            $user=$dbop->selectAssocRow("users","WHERE `userID`='{$userID}' AND `userOrganizationID`='{$orgID}'");
            
            echo "<pre>";
            echo print_r($user);
            echo "</pre>";
        break;
    endswitch;
    
    die();
}


if(!empty($_POST)) {
    $orgID=$_POST['orgID'];
    $teamID=$_POST['teamID'];
    $fromID=$_POST['fromID'];
    $endID=$_POST['endID'];

    $ans=$dbop->selectDB("users","WHERE `userOrganizationID`='$orgID' AND `userID`>=$fromID AND `userID`<=$endID");
    for($i=0;$i<$ans['n'];$i++) {
        $row=mysql_fetch_assoc($ans['p']);
         echo $i.")".$row['userName']."<br>";   

         $fields=array();
         $fields['teamUserUserID']=$row['userID'];
         $fields['teamUserTeamID']=$teamID;
         $dbop->insertDB("teamsUsers",$fields,false);
    }
    
    
    echo ($i)." rows affected!";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
        <script>
            function loadTeams(value) {
                $.ajax({
                    type: "post",
                    url: "/scr/assoc_team_form.php",
                    data:{
                        op:"getTeams",
                        orgID:value
                    },
                    success: function(data, textStatus, jqXHR) {
                        $("#teams").html(data);
                    }
                }); 
            }
            
            function getUser(f_id,r_id) {
                var userID=$("#"+f_id).val();
                $.ajax({
                    type: "post",
                    url: "/scr/assoc_team_form.php",
                    data:{
                        op:"getUser",
                        userID:userID,
                        orgID:$("#orgID").val()
                    },
                    success: function(data, textStatus, jqXHR) {
                        $("#"+r_id).html(data);
                    }
                });
            }
            
            function checkForm() {
                var ok=true;
                if($("#orgID").val()=="0") {
                    $("#orgID").addClass("broken");
                    ok=false;
                }
                
                if($("#teams").val()=="0") {
                    $("#teams").addClass("broken");
                    ok=false;
                }
                
                if($("#fromID").val()=="") {
                    $("#fromID").addClass("broken");
                    ok=false;
                }
                
                if($("#endID").val()=="") {
                    $("#endID").addClass("broken");
                    ok=false;
                }
                
                
                return ok;
                
            }
        </script>
        <style>
            th {
                vertical-align:top;
            }
            
            .broken {
                background-color:red;
            }
        </style>
    </head>
    <body>
        <div>
            <form action="" method="post" onsubmit="return checkForm()">
                <table>
                    <tr>
                        <th>Org</th>
                        <td>
                            <select name="orgID" id="orgID" onchange="loadTeams(this.value)">
                                <option value="0">-------------</option>
                                <?
                                $ans=$dbop->selectDB("organizations"," ORDER BY `organizationName` ASC");
                                for($i=0;$i<$ans['n'];$i++) {
                                        $row=mysql_fetch_assoc($ans['p']);
                                ?>
                                <option value="<?=$row['organizationID']?>"><?=$row['organizationName']?> (<?=$row['organizationID']?>)</option>
                                <?}?>
                            </select>                            
                        </td>
                    </tr>
                    <tr>
                        <th>Team</th>
                        <td>
                            <select name="teamID" id="teams">
                                <option value="0">-------------</option>
                            </select>                            
                        </td>
                    </tr>
                    <tr>
                        <th>Start From (userID)</th>
                        <td>
                            <input type="text" name="fromID" id="fromID" />  
                            <button type="button" onclick="getUser('fromID','fromIDRes')">GET USER</button>
                            <span id="fromIDRes"></span>
                        </td>
                    </tr>
                    
                    <tr>
                        <th>End (userID)</th>
                        <td>
                            <input type="text" name="endID" id="endID" />  
                            <button type="button" onclick="getUser('endID','endIDRes')">GET USER</button>
                            <span id="endIDRes"></span>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="submit" />  
                            
                        </td>
                    </tr>
                </table>        
            </form>   
        </div>
        
        
    </body>
</html>
