<?php

class AvbDevPlatform {
    private $local=false;
    public static function isLocalMachine() {
        
        if(!isset($_SERVER['SERVER_ADDR'])) {
            return false;
        }
        
        $server0=explode(".",$_SERVER['SERVER_ADDR']);
        $serverStart=$server0[0];
        if($serverStart=="10" || $serverStart=="127") return true;
        else return false;

    } 
    
    
    public static function getServerName($inner_call=false) {
        $host=$_SERVER['HTTP_HOST'];
        $ex=explode(".",$host);
        $server=$ex[count($ex)-1];
        
        if($inner_call) {
            if($server=="staging") {
                $server="com";
            } 
        }
        
        return $server;
    } 
    
    public static function encrypt_Nmb($nmb) {
        $unrecstr=AvbDevPlatform::safe_b64encode($nmb);
        $encoded_length=strlen($unrecstr);
        $rand=rand(0,8);
        $hystek_part1=substr(sha1(time()),0,$rand);
        $hystek_part2=substr(md5(time()),0,10);
        $hystek_part3=substr(md5(time()),10,2);
        $encrypted=$hystek_part1.$unrecstr.$hystek_part2.$rand.$hystek_part3."_".$encoded_length;
        return $encrypted;
    }
    
    public static function decrypt_Nmb($encrypted) {
        $ex=explode("_",$encrypted);
        $encoded_length=$ex[1];
        $enc=$ex[0];
        $steps=substr($enc, -3, 1);
        $unrecstr=substr($enc,$steps,$encoded_length);
        $nmb=AvbDevPlatform::safe_b64decode($unrecstr);
        $nmb=rtrim($nmb);
        
        if(strlen($nmb)!=strlen((int)$nmb)) {
            echo "error";
            return false;
        }
        return $nmb;
    }
        
    public static function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
    
    public static function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
            
            
}
