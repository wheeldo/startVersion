<?php
class Accounts extends dbop{
    /*
     * Accounts::setTokensLimit($orgSelection,25);
     * Accounts::useToken($orgSelection);
     * $account=Accounts::getOrgAccount($orgSelection);
     * $tokens_left=Accounts::getTokensLeft($orgSelection);
     * Accounts::insertTokenHistoryRow(array('orgID'=>1,'time'=>time(),'userID'=>71,'userEmail=>'cto@wheeldo.com','copyID'=>7821,'copyName'=>'Test copy'));
     */
    

    public static function getOrgAccount($orgID) {
        global $dbop;
        $account=$dbop->selectAssocRow("accounts","WHERE `orgID`='{$orgID}'");
        if(!$account) {
            $fields=array();
            $fields['orgID']=$orgID;
            $fields['regDate']=time();
            $fields['tokens_c']=0;
            $fields['tokens_limit']=20;
            $fields['active']=1;
            $dbop->insertDB("accounts",$fields);
        }
        return $dbop->selectAssocRow("accounts","WHERE `orgID`='{$orgID}'");
    }
    
    public static function getTokensLeft($orgID) {
        $account=Accounts::getOrgAccount($orgID);
        return (int)$account['tokens_limit']-(int)$account['tokens_c'];
    }
    
    public static function setTokensLimit($orgID,$limit) {
        $account=Accounts::getOrgAccount($orgID);
        global $dbop;
        $fields=array();
        $fields['tokens_limit']=$limit;
        $dbop->updateDB("accounts",$fields,$account['id']);
    }
    
    public static function useToken($orgID) {
        $account=Accounts::getOrgAccount($orgID);
        global $dbop;
        $fields=array();
        $fields['tokens_c']=(int)$account['tokens_c']+1;
        $dbop->updateDB("accounts",$fields,$account['id']);
    }
    
    public static function insertTokenHistoryRow($row) {
        global $dbop;
        $dbop->insertDB("accounts_tokens_history",$row);
        echo mysql_error();
    }
    
    
  
}
