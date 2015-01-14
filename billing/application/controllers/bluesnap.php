<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bluesnap extends CI_Controller {
    
        private $ip;
        private $remote_host;
        private $user_agent;
        private $accept_language;
        private $baseServiceUrl;
        private $api_user='API_1387895633049988238276';
        private $api_pass='BlueSnap1wh';
        private $credentials;
        private $shopper_id;
    

	function __construct()
        {
            // Call the Model constructor
            parent::__construct();
            $this->load->helper('url');	
            $this->load->library('session');
            
            
            
            $this->ip=$_SERVER['REMOTE_ADDR'];
            $this->remote_host=isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:"";
            $this->user_agent=isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"";
            $this->accept_language=isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])?$_SERVER['HTTP_ACCEPT_LANGUAGE']:"";
            $this->baseServiceUrl='https://sandbox.plimus.com/';
            $this->credentials = $this->api_user.':'.$this->api_pass;
            //$service = 'https://sandbox.plimus.com/services/2/shoppers';
        }
        
        
	public function index()
	{
            $session_data=$this->session->all_userdata();

            if(!isset($session_data['login_user_id'])) {
                //$this->login();
                return;
            }
            
            $this->load->model('users_model');
            $data['users']=$this->users_model->get_users_kind2();
            
            $this->load->model('billing_model');
            $data['shoppers']=$this->billing_model->get_all_shppers();
            //var_dump($data['shoppers']);
            
            $data['contracts']=$this->getContracts();
            //var_dump($data['contracts']);   
            
            
            $this->load->view('test_functions',$data);
	}
        
        public function payment_ipn() {
            $post = $this->input->post();
            
            if($post) {           
                $body="<html><body><pre>";
                ob_start();
                print_r($post);
                $body.=ob_get_clean();
                $body.="</pre></body></html>";

                $userID='';
                $shopperId='';
                $type=$post['transactionType'];
                $response=json_encode($post);



                $this->load->model('billing_model');
                $this->billing_model->insert_history($userID,$shopperId,$type,$response);
            }
            else {
                $body="direct call test";
            }
            
            $this->SendMailToTeam("sendMail",1,1,71,"IPN test - ".$_SERVER['REMOTE_ADDR'],$body);
            
        }
        
        public function test_func() {
            $res=array();
            $post = $this->input->post();
            $func=$post['func'];
            unset($post['func']);
            
            $this->$func($post);
            
            //var_dump($post);
        }
        
        public function api_func() {
            $ip=$_SERVER['REMOTE_ADDR'];
            $check=$this->checkIP($ip);
            if(!$check)
                die($ip." Blocked");
            $res=array();
            $post = $this->input->post();
            $func=$post['func'];
            unset($post['func']);
            
            $this->$func($post);
            
            //var_dump($post);
        }
        
        private function checkIP($ip) {
            return true;
        }
        
        
        public function getContracts() {
            $res=array();
            //$contracts=array("Silver - 25 tokens/month"=>2146090,"Gold - 50 tokens/month"=>2146108,"Platinum - 100 tokens/month"=>2146110);
            $this->load->model('billing_model');
            $contracts=$this->billing_model->get_contracts();

            $c=0;
            foreach($contracts as $contract):
                $res[$c]['name']=$contract->name;
                $res[$c]['id']=$contract->contract_id;
                $res[$c]['price']=$this->get_sku_price($contract->contract_id);
                $c++;
            endforeach;
           
            return $res;
            
        }
        
        private function checkIfShopperExists($function_data=false) {
            $post = $this->input->post();
            header('Content-Type:application/json');
            $res=array();
            
            $service=$this->baseServiceUrl."services/2/shoppers";
            
            if($function_data) {
                $userID=$function_data['userID'];
            }
            elseif($post) {
                $function_data=$post;
            }
            else {
                return;
            }
            
            
            $this->load->model('users_model');
            $user_data=$this->users_model->get_user($userID);
            if($user_data) {
                $this->load->model('billing_model');
                $shopper_exists=$this->billing_model->shopper_exists($user_data->userID);
                if($shopper_exists) {
                    $res['status']="ok";
                    $res['shopper']=$this->billing_model->get_shopper($user_data->userID);
                    $res['shopper_exists']="1";
                    echo json_encode($res);
                }
                else {
                    $res['status']="ok";
                    $res['shopper_exists']="0";
                    echo json_encode($res);
                }
            }
            else {
                $res['status']="faild";
                $res['error']="userID missed!";
                echo json_encode($res);
            }
            
            
        }
        
        
        public function create_shopper($function_data=false) {
            $post = $this->input->post();
            header('Content-Type:application/json');
            $res=array();
            
            $service=$this->baseServiceUrl."services/2/shoppers";
            
            if($function_data) {
                $userID=$function_data['userID'];
            }
            elseif($post) {
                $function_data=$post;
            }
            else {
                return;
            }
            

            
            $this->load->model('users_model');
            $user_data=$this->users_model->get_user($userID);
            if($user_data) {
                $this->load->model('billing_model');
                $shopper_exists=$this->billing_model->shopper_exists($user_data->userID);
                
                if(!$shopper_exists) {
                    // blue snap create shopper://
                    $name_ex=explode(" ",$user_data->userName);
                    
                    
                    $firstName = $function_data['firstName'];  		 
                    $lastName = $function_data['lastName'];     		 
                    $email = $user_data->userEmail;      
                    $address1 = $function_data['address1'];
                    $address2 = "";
                    $city = $function_data['city'];
                    $state = $function_data['state'];
                    $country = $function_data['country'];
                    $zipCode = $function_data['zipCode'];
                    $phone = $function_data['phone'];
                    
                    
                    
//                    $firstName = 'Bob';   		 
//                    $lastName = 'Smith';     		 
//                    $email = "bob.smith@plimus.com";      
//                    $address1 = "123 Main Street";
//                    $address2 = "Apt K-9";
//                    $city = "Parkville";
//                    $state = "TN";
//                    $country = "us";
//                    $zipCode = "37027";
//                    $phone = "411-555-1212";
                    
                    $xmlToSend = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>
                    <shopper xmlns=\"http://ws.plimus.com\">
                          <shopper-info>
                                <shopper-contact-info>
                                      <first-name>". $firstName ."</first-name>
                                      <last-name>". $lastName ."</last-name>
                                      <email>". $email ."</email>
                                      <address1>". $address1 ."</address1>
                                      <city>". $city ."</city>
                                      <zip>". $zipCode ."</zip>
                                      <country>". $country ."</country>
                                      <state>". $state ."</state>
                                      <phone>". $phone ."</phone>
                                </shopper-contact-info>
                                <locale>en</locale>
                          </shopper-info>          
                          <web-info> 
                                <ip>$this->ip</ip>
                                <remote-host>$this->remote_host</remote-host>
                                <user-agent>$this->user_agent</user-agent>
                                <accept-language>$this->accept_language</accept-language>
                          </web-info>
                    </shopper>";

                    
                    $contentType = array('Content-type: application/xml');
                    // Initialize handle and set options
                    $ch = curl_init();
                    // more info about setopt options can be found here: http://www.php.net/manual/en/function.curl-setopt.php
                    curl_setopt($ch, CURLOPT_URL, $service); 
                    curl_setopt($ch, CURLOPT_USERPWD, $this->credentials); // authentication (credentials) string encoded in base-64 

                    curl_setopt($ch, CURLOPT_HEADER, true);          // include the headers in the output
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // don't output the response to screen (default behavior)
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlToSend);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $contentType);    
                    curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'get_shopper_from_header'));
                    
                    $response = curl_exec($ch);
                    
//                    echo "<pre>";
//                    var_dump($xmlToSend);
//                    echo "<hr>";

                    
                    // Check for errors
                    if ( curl_errno($ch) ) {
                        $res['status']="faild";
                        $res['error']='HTTP error code: ' . curl_errno($ch) . '<br>error-message: "' . curl_error($ch) . '"';
                        echo json_encode($res);
                        return;
                    } 

                    // SUCCESS
                    if (is_numeric($this->shopper_id)) {
                        // userID,shopper_id,firstName,lastName,email,address1,city,state,country,zipCode,phone,insert_time
                        $shopper_data=array();
                        $shopper_data['userID']=$userID;
                        $shopper_data['organizationID']=$user_data->userOrganizationID;
                        $shopper_data['shopper_id']=$this->shopper_id;
                        $shopper_data['firstName']=$firstName;
                        $shopper_data['lastName']=$lastName;
                        $shopper_data['email']=$email;
                        $shopper_data['address1']=$address1;
                        $shopper_data['city']=$city;
                        $shopper_data['state']=$state;
                        $shopper_data['country']=$country;
                        $shopper_data['zipCode']=$zipCode;
                        $shopper_data['phone']=$phone;
                        $shopper_data['insert_time']=time();

                        $this->load->model('billing_model');
                        $this->billing_model->saveShopper($shopper_data);
                        
                        
                        
                        
                        $res['status']="ok";
                        $res['shopper_id']=$this->shopper_id;
                        echo json_encode($res);
                        return;
                    }
                    // FAIL
                    else {
                        $res['status']="faild";
                        $res['error']=$response;
                        echo json_encode($res);
                        return;
                    }
                    //////////////////////////////
                }
                else {
                    $res['status']="faild";
                    $res['error']='Shopper already exists!';
                    echo json_encode($res);
                    return;
                }
            }

        }
        
        public function createChargeOnDemand($function_data=false) {
            
            $function_data['description']="OnDemand test";
            $function_data['price']="199.00";
            
            $post = $this->input->post();
            //header('Content-Type:application/json');
            $res=array();
            
            
            
            $service=$this->baseServiceUrl."services/2/subscriptions/0/subscription-charges";
            
            if($function_data) {
                //$userID=$function_data['userID'];
            }
            elseif($post) {
                $function_data=$post;
            }
            else {
                return;
            }
            
            
            $xmlToSend = "<subscription-charge xmlns=\"http://ws.plimus.com\">
                             <charge-info>
                                <charge-description>".$function_data['description']."</charge-description>
                             </charge-info>
                             <sku-charge-price>
                                <amount>".$function_data['price']."</amount>
                                <currency>USD</currency>
                             </sku-charge-price>
                             <expected-total-price>
                                <amount>".$function_data['price']."</amount>
                                <currency>USD</currency>
                             </expected-total-price>
                         </subscription-charge>";
            
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $service); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/xml"));
            curl_setopt($ch, CURLOPT_USERPWD, $this->credentials);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlToSend);	
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);

            /**
             * Execute Curl call and extract header and body contant
            */
            $result = curl_exec($ch);
            $info = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            curl_close($ch);
            $header = substr($result, 0, $info);
            $body = substr($result, $info);

            //echo format_header($header, $body); 
            
            echo $result;
            
        }
        
        
        public function get_token($function_data=false) {

            
            $shopperId = $function_data['shopperId'];
            $expiration = 5; // the token will remain valid for number 'expiration' number of minutes 
            $username   = $this->api_user;   // vendor API username - not to confuse with the control-panel credentials
            $password   = $this->api_pass;;   // vendor API password
            $contractId = $function_data['contractId']; // contract to buy
            $target     = "step2";     // target - the Plimus page to redirect the customer: cp,step1,step2,paypal
            

            
            $URL=$this->baseServiceUrl."services/2/tools/auth-token?shopperId=$shopperId&expirationInMinutes=$expiration";

            // use base64 to encode the credentials
            $authorization = base64_encode($username.':'.$password); 

            $ch = curl_init();
            // set URL
            curl_setopt_array($ch, array(CURLOPT_URL => $URL));
            // set headers
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Basic $authorization", "Content-type: application/xml")); // This line is mandatory for every API call!
            // set  HTTP request to GET
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); // This service (get token) is implement via RESTful GET, other services might use POST and PUT
            // stop output of curl_exec to standard output (don't send output to screen)
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            // make HTTP call and read the response into an XML object
            $xml = new SimpleXMLElement(curl_exec($ch));
            
            $plimus = $this->baseServiceUrl."jsp/entrance.jsp?contractId=$contractId&target=$target&token={$xml->token}";
            
            
            
            
            
            //echo $plimus;
            
            header('Content-Type:application/json');
            $res=array();
            
            $res['status']="ok";
            $res['res']='<a target="_blank" href="'.$plimus.'">'.$plimus.'</a>';
            echo json_encode($res);
        }
        
        public function get_shopper_data($function_data=false) {
            $post = $this->input->post();
            header('Content-Type:application/json');
            $res=array();
            
            $service=$this->baseServiceUrl."services/2/shoppers";
            
            if($function_data) {
                $shopperId=$function_data['shopperId'];
            }
            elseif($post) {
                $function_data=$post;
            }
            else {
                return;
            }
            $shopper_data=$this->get_bluesnap_shopper_data($shopperId);
            //var_dump($shopper_data); 
            
            ob_start();
            print_r($shopper_data);
            $data=ob_get_clean();
            
            $res['status']="ok";
            $res['res']='<pre>'
                  . $data .
                  '</pre>';
            echo json_encode($res);
        }
        
        
        public function get_bluesnap_shopper_data($shopperId) {

            //header('Content-Type:application/json');


            
            $url = $this->baseServiceUrl."services/2/shoppers/".$shopperId;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_USERPWD, $this->credentials); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

            /**
             * Execute Curl call and display XML response
            */
            $result = curl_exec($ch);
            curl_close($ch);
            
            $xml_object = simplexml_load_string($result);
            $xml_array=$this->object2array($xml_object);


            return $xml_array['shopper-info'];
        }
        
        private function check_if_shopper_have_cc_data($shopperId) {
            $shopper_data=$this->get_bluesnap_shopper_data($shopperId);
            return isset($shopper_data['payment-info']['credit-cards-info']['credit-card-info']['credit-card']['card-last-four-digits']);
        }
        
        
        
        public function get_sku_price($skuID) {
            //$skuId='2146060';
            
            $url = $this->baseServiceUrl.'services/2/skus/'.$skuID.'/price/USD';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_USERPWD, $this->credentials);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            /**
             * Execute Curl call and display XML response
            */
            $result = curl_exec($ch);
            curl_close($ch);
            
            //var_dump($result);

            //echo $result;
            
//            $xml = new SimpleXMLElement($result);
//            $array = $this->XML2Array($xml);
//            $array = array($xml->getName() => $array);
//            
//            var_dump($array);
            
            
            $xml_object = simplexml_load_string($result);
            $xml_array=$this->object2array($xml_object);
//            echo "<pre>";
//            var_dump($xml_array);
//            echo "</pre>";
            
            return $xml_array['total-price']['charge-price'][0]['value'];
            
        }
        
        public function get_sku($skuID) {
            //$skuId='2146060';
            
            $url = $this->baseServiceUrl.'services/2/skus/'.$skuID.'/price/USD';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_USERPWD, $this->credentials);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            /**
             * Execute Curl call and display XML response
            */
            $result = curl_exec($ch);
            curl_close($ch);
            
            //var_dump($result);

            //echo $result;
            
//            $xml = new SimpleXMLElement($result);
//            $array = $this->XML2Array($xml);
//            $array = array($xml->getName() => $array);
//            
//            var_dump($array);
            
            
            $xml_object = simplexml_load_string($result);
            $xml_array=$this->object2array($xml_object);
//            echo "<pre>";
//            var_dump($xml_array);
//            echo "</pre>";
            
            return $xml_array;
            
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        
        private function get_shopper_from_header($ch, $string) {
            //looking for the "Location" header - but since it's case insensitive...
            if(strpos($string, "ocation") > -1){ 
                $tokens = explode("/", $string);
                //the shopper-id will always be the last token
                $this->shopper_id = trim($tokens[count($tokens)-1]); 
            }  
            return strlen($string);
        }
        
        private function XML2Array(SimpleXMLElement $parent){
            $array = array();

            foreach ($parent as $name => $element) {
                ($node = & $array[$name])
                    && (1 === count($node) ? $node = array($node) : 1)
                    && $node = & $node[];

                $node = $element->count() ? XML2Array($element) : trim($element);
            }

            return $array;
        }

        
        private function object2array($object) { return @json_decode(@json_encode($object),1); }
        
        public function SendMailToTeam($request, $appID,$token, $userID, $subject,$content){

                
                if($this->isLocal()) {
                    $url="http://localhost.api.wheeldo.com/APIAD.php";
                }
                else {
                    $url="http://api.wheeldo.com/APIAD.php";
                }

		

		$postArray=array();

		$postArray['request']= $request;/*'sendMail'*/;

		$postArray['function_data[appID]']=$appID;

		$postArray['function_data[userID]']=$userID;

		$postArray['function_data[subject]']=$subject;

		$postArray['function_data[content]']=$content;



		$loginDet=array();

		$loginDet['login']='y';

		$loginDet['key']='x';

		

		//$sessionO=WheelDoSession::createSession($token,$appID);

		//$session=$sessionO->getSessionData();

		$session = array(

				'token'=> $token,

				'appConfig'	=> $appID 

				

			 );

		



		$postArray = array_merge($postArray,$loginDet);

		$postArray = array_merge($postArray,$session);

	

		$response=$this->doRequest($url,$postArray);

	}
        
        function doRequest($url,$postArray) {

		

		$ch = curl_init(); 

		curl_setopt($ch, CURLOPT_URL, $url); 

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

		curl_setopt($ch, CURLOPT_POST, true); 

		curl_setopt($ch, CURLOPT_POSTFIELDS, $postArray);

		$response = curl_exec($ch); 

		curl_close($ch);

		return $response;

	}
        
        private function isLocal() {
            
            if(!isset($_SERVER['SERVER_ADDR'])) {
                return false;
            }

            $server0=explode(".",$_SERVER['SERVER_ADDR']);
            $serverStart=$server0[0];
            if($serverStart=="10" || $serverStart=="127") return true;
            else return false;
            
        }
        
        
}
