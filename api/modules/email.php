<?php
header("Content-type: text/html; charset=utf-8");
mb_internal_encoding('UTF-8'); 
require_once ('db.php');
include "class.smtp.php";
include "class.phpmailer.php";

class Email
{
	
	private $templateData;
        private $prependBody;
        
	public function __construct($templateName,$prependBody="")
	{
		$con = db::getDefaultAdapter();
                // cehck for template file //
                $check=file_exists("../Emails/{$templateName}.html");
                if($check) {
                    
                }
                /////////////////////////////
		$select = $con->select()->from('emailTemplates')->where('emailTemplateName = ?', $templateName);
		$res = $con->query($select);
		$this->templateData = $res->fetch_array();
                $this->prependBody = $prependBody;
                //var_dump($this->templateData);
		
	}
        
        public static function setEmbedImages($body) {
            $r=array();
            $origImageSrc=array();
            preg_match_all('/<img[^>]+>/i',$body, $imgTags);
            for ($i = 0; $i < count($imgTags[0]); $i++) {
              preg_match('/src="([^"]+)/i',$imgTags[0][$i], $imgage);
              $ex=explode("/",$imgage[0]);
              $newName=$ex[count($ex)-1];
              $ex2=explode(".",$newName);
              $newName2=$i.time().".".$ex2[1];
              $pos = strpos(str_ireplace( 'src="', '',  $imgage[0]), "http://");
              if($pos === false) {
                  $origImageSrc[$newName2] = str_ireplace( 'src="', '',  $imgage[0]);
                  $body=str_replace($imgage[0], 'src="cid:'.$newName2 ,$body);  
              }  

            }
            $r['attachments']=array_unique($origImageSrc);
            $r['html']=$body;
            return $r;     
        }
	
	public function sendEmail($emailsArray,$contentArray){ 
		$first = reset($contentArray);
                
		if(is_array($first))
		{
                    
			foreach($emailsArray as $address)
			{
				$current = array_shift($contentArray);
				$subject = array_shift($current);
                                $body = $this->prependBody." <br />";
				$body .= $this->templateData['emailTemplateBody'];
				$keys = array_keys($current);
				
				$body = str_replace($keys, $current, $body);
				self::semail($address, $subject, $body);
			}
		}
		else
		{
			$subject = array_shift($contentArray);
                        $body = $this->prependBody." <br />";
			$body .= $this->templateData['emailTemplateBody'];
			$keys = array_keys($contentArray);
			
			$body = str_replace($keys, $contentArray, $body);
			foreach($emailsArray as $address)
			{
				self::semail($address, $subject, $body);
			}
		}
	}
	
	
	
    public static function semail($To, $Subject, $Body){
        /////// chagne SMTP Server /////////////
	if(AvbDevPlatform::isLocalMachine()) {
            $mail=Email::SMTP_Gmail();
        }
        else {
            $mail=Email::getLiveSMTP_SERVER();
        }
        /////////////////////////////////////////
        $mail->From     = "game@wheeldo.com";
        $mail->FromName = "Wheeldo game";
	$mail->Subject = $Subject;
	$mail->Body = $Body;
	$mail->AddAddress($To);
	
        $mail->WordWrap = 50;				// set word wrap
	$mail->Priority = 1; 
        $mail->IsHTML(true);  
        $mail->Subject  =  $Subject;
	
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	$mail->MsgHTML($Body);
	$mail->CharSet="UTF-8";
        
        //echo $Body."<Br />";
	
	if(!$mail->Send()) {
		$error = 'Mail error: '.$mail->ErrorInfo; 
                echo $error."<br />";
		return false;
	} else {
		$error = 'Message sent!';
		return true;
	}
    }
    
    
    
    public static function semailFrom($FromName, $To, $Subject, $Body){
        $embedImages=true;
        /////// chagne SMTP Server /////////////
	if(AvbDevPlatform::isLocalMachine()) {
            $mail=Email::SMTP_Gmail();
        }
        else {
            $mail=Email::getLiveSMTP_SERVER();
        }
        /////////////////////////////////////////

        if(is_array($FromName)) {
            $mail->From     = $FromName['address'];
            $mail->FromName = $FromName['name'];
        }
        else {
            $mail->From     = "game@wheeldo.com";
            $mail->FromName = $FromName;
        }

        if(is_array($To)) {
            foreach($To as $add):
                if($add != "")
                    $mail->AddAddress($add);

                echo $add;
            endforeach;
        }
        else {
            $mail->AddAddress($To);
        }

        $mail->AddReplyTo($mail->From, $mail->FromName);
        $mail->IsHTML(true);
        $mail->Subject=$Subject;


        if($embedImages) {
            // embed engine
            $embedImages=Email::setEmbedImages($Body);
            foreach($embedImages['attachments'] as $new_name=>$src):
                $mail->AddEmbeddedImage($src, $new_name, $src);
            endforeach;
            $Body=$embedImages['html'];
        }


        $mail->Body     = $Body;
        $mail->WordWrap = 50; 
        $mail->CharSet="UTF-8";

        if(!$mail->Send()) {
                $error = 'Mail error: '.$mail->ErrorInfo; 
                echo $error."<br />";
                return false;
        } else {
                $error = 'Message sent!';
                return true;
        }
    }
    
    
    public static function getLiveSMTP_SERVER() {
        return Email::SMTP_SMTP2GO();
    }
  
    
    public static function SMTP_Gmail() {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = "smtp.gmail.com";;
        $mail->Port = 465; 
        $mail->Username = "noreply@wheeldo.com"; 
        $mail->Password = "team@wheeldo";
        return $mail;
    }
    
    public static function SMTP_TurboSMTP() {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->Host = "pro.turbo-smtp.com"; 
        $mail->Port = "465"; 
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl'; 
        $mail->Username = "irad@wheeldo.com";
        $mail->Password = "rGR9AFMT";
        return $mail;
    }
    
    public static function SMTP_SMTP2GO() {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->Host = "smtpcorp.com"; 
        $mail->Port = "465";
        $mail->SMTPAuth = true;
	$mail->SMTPSecure = 'ssl';
        $mail->Username = "cto@wheeldo.com";
        $mail->Password = "wheeldo123";
        return $mail;
    }
}
?>
