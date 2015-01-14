<?php
$path = 'lib';
set_include_path(ROOT .DS  . $path);

//echo get_include_path() . PATH_SEPARATOR . $path;

require_once(ROOT .DS . 'lib/services/AdaptivePayments/AdaptivePaymentsService.php');
require_once(ROOT .DS . 'lib/PPLoggingManager.php');


class Billing {
	/**
	 * @var double how much HR user pays for each active user per month
	 */
	const HRUserFee = 2;
	/**
	 * 
	 * @var double how much Proffesional user pays for each user he "owns"
	 */
	const ProffesionalUserFee = 0.5;
	/**
	 * @var double how much a proffesional makes for each use of his programs
	 */
	const ProffesionalPayPerUse = 0.25;
	/**
	 * @var string email of the company's PayPal account
	 */
	const companyPayPal = 'yotam._1348477149_biz@gmail.com';
	
	
	/**
	 * Calculates how much a user owes for usage this month.
	 * checks how many days since user last payed, divides by 30 and multiplies by constant to get amount
	 * works for both proffesionals and HR
	 * @param int $userID id of user to charge
	 */
	public static function billUserForUsers($userID)
	{
		$con = db::getDefaultAdapter();
		//$select = $con->select()->from('userKinds')->where('userKindName = ?', 'HR');
		//$res = $con->query($select);
		$user = new User(null,$userID);
		if(strcasecmp($user->getData('userUserKindID'),'3') == 0   || strcasecmp($user->getData('userUserKindID'),'2') == 0)
		{
			$vals = array('ownerID' => $userID);
			$report = new Report('userPayment',$vals);
			$result = $report->getResult();
			$total = 0;
			$select = $con->select()->from('payments')->where('paymentUserID = ?', $userID)->orderBy('paymentDate DESC');
			$res = $con->query($select);
			$row = $res->fetch_array();
			if(strcasecmp($row['paymentDenied'],'0') == 0 || $res->num_rows ==0)
			{
				$now = time(); // or your date as well
				foreach($result as $res)
				{
					$stamp = strtotime($res['userRetainerPaidDate']);
					$datediff = $now - $stamp;
				    $total += floor($datediff/(60*60*24));
				}
			}
			else
			{
				$row = $res->fetch_array();
				$now = strtotime($row['paymentDate']);
				foreach($result as $res)
				{
					$stamp = strtotime($res['userRetainerPaidDate']);
					$datediff = $now - $stamp;
				    $total += floor($datediff/(60*60*24));
				}
			}
			if(strcasecmp($user->getData('userUserKindID'),'3') == 0)
			{
				$paymentAmount = round(self::ProffesionalUserFee*($total/30),2);
			}
			else
			{
				$paymentAmount = round(self::HRUserFee*($total/30),2);
			}
			
			if($token = self::billUser($paymentAmount,$userID,'lalallala'))
				
			{
				$userIDs = array();
				$arr = array('userRetainerPaidDate'   => date("Y-m-d H:i:s"));
				$where = 'userID IN(?';
				foreach($result as $res)
				{
					$where = $where.',?';
					$userIDs[] = $res['userID'];
				}
				$where .= ')';
				$userIDs[] = $userID;
				$update = $con->update()->table('users')->set($arr)->where($where, $userIDs);
				$con->query($update);
				
				$arr = array(
						'paymentUserID' => $userID,
						'paymentDate'   => date("Y-m-d H:i:s"),
						'paymentAmount' => $paymentAmount,
						'paymentDenied' => 0,
						'paymentToken'  => $token,
						'paymentReport' => serialize($report),
				);
				
				$con->insert('payments',$arr);
				logger::logOperation('billingBillUserForUsers', 'billed user of ID '.$userID.' for '.$paymentAmount.' USD');
			}
			else
			{
				$arr = array(
						'paymentUserID' => $userID,
						'paymentDate'   => date("Y-m-d H:i:s"),
						'paymentAmount' => $paymentAmount,
						'paymentDenied' => 1,
						'paymentReport' => serialize($report),
						'paymentToken'  => ''
						);

				$con->insert('payments',$arr);
				logger::logOperation('billingBillUserForUsers', 'failed to complete transaction for user of ID '.$userID.' for '.$paymentAmount.' USD');
			}
		}
		else
		{
			errorLogger::logOperationError('billingBillUserForUsers', 'invalidArgumentSupplied', 'user is not HR or proffesional');
		}
	}
	
	/**
	 * Pays proffessional for usage of his programs since last payment
	 * Calculates how many programs were started since last days and pays for each user who started
	 * @param int $userID the ID of the proffessional
	 */
	public static function payProffesional($userID)
	{
		$user = new User(null,$userID);
		if(strcasecmp($user->getData('userUserKindID'),'3') == 0)
		{
			$con = db::getDefaultAdapter();
			
			$select = $con->select()->from('payments')->where('paymentUserID = ?', $userID)->orderBy('paymentDate DESC');
			$res = $con->query($select);
			$startDate = null;
			while($row = $res->fetch_array())
			{
				if(!isset($startDate))
					$startDate = $row['paymentDate'];
				if(strcasecmp($row['paymentDenied'],'0') == 0)
					break;
				$startDate = $row['paymentDate'];
			}
			
			
			$select = $con->select()->from('programs')->where('programUserID = ? AND programOriginalID = programID',$userID);
			$res = $con->query($select);
			$total = 0.0;
			
			$vals = array('startDate' => $startDate,'userID' =>$userID);
			$report = new Report('programUsers',$vals);
			$result = $report->getResult();
			$total += count($result);
			
			
			
			$paymentTotal = round($total*self::ProffesionalPayPerUse, 2);

			if($token = self::creditUser($paymentTotal,$userID,'some text'))
			{
				$arr = array(
						'paymentUserID' => $userID,
						'paymentDate'   => date("Y-m-d H:i:s"),
						'paymentAmount' => (-1.0*$paymentTotal),
						'paymentDenied' => 0,
						'paymentToken'  => $token,
						'paymentReport' => serialize($report),
				);
				$con->insert('payments',$arr);
				logger::logOperation('billingPayProffesional', 'payed proffesional of ID '.$userID.' for '.$paymentTotal.' USD');
			}
			else
			{
				$arr = array(
						'paymentUserID' => $userID,
						'paymentDate'   => date("Y-m-d H:i:s"),
						'paymentAmount' => (-1.0*$paymentTotal),
						'paymentDenied' => 1,
						'paymentReport' => serialize($report),
						'paymentToken'  => ''
						
				);
					
				$con->insert('payments',$arr);
				logger::logOperation('billingPayProffesional', 'failed to pay proffesional of ID '.$userID.' for '.$paymentTotal.' USD');
			}
			
		}
		else
		{
			errorLogger::logOperationError('billingBillUserForUsers', 'invalidArgumentSupplied', 'user is not HR');
		}
	}
	
	/**
	 * Returns a URL for a link to get user approval for preapproved payments
	 * non mandatory options -
	 * 
	 * dateOfMonth - date of month to charge on
	 * endingDate - last date on which token is valid. No more than a year into future
	 * maxNumberOfPayments - maximum number of payments token is valid for
	 * maxTotalAmountOfAllPayments - charging ceiling for token
	 * senderEmail - paypal account email of user
	 * @param array $ops options to user
	 * @param string $returnURL the url to return to if user accepts
	 * @param string $cancelURL the url to return to if user declines
	 * @param string $memo description for the token
	 * @return NULL|string return null of failure, Token to get approval on success
	 */
	public static function getApprovalToken(array $options,$returnURL,$cancelURL,$memo)
	{		
		$logger = new PPLoggingManager('PreApproval');
		
		// create request
		$requestEnvelope = new RequestEnvelope("en_US");
		$preapprovalRequest = new PreapprovalRequest($requestEnvelope, $cancelURL,
				'USD', $returnURL, date("Y-m-d"));
		
		if(isset($options['dateOfMonth'])) {
			$preapprovalRequest->dateOfMonth = $options['dateOfMonth'];
		}
		if(isset($options['endingDate'])) {
			$preapprovalRequest->endingDate = $options['endingDate'];
		}
		
		if(isset($options['maxNumberOfPayments'])) {
			$preapprovalRequest->maxNumberOfPayments = $options['maxNumberOfPayments'];
		}
		
		if(isset($options['maxNumberOfPaymentsPerPeriod'])) {
			$preapprovalRequest->maxNumberOfPaymentsPerPeriod = $options['maxNumberOfPaymentsPerPeriod'];
		}
		if(isset($options['maxTotalAmountOfAllPayments'])) {
			$preapprovalRequest->maxTotalAmountOfAllPayments = $options['maxTotalAmountOfAllPayments'];
		}
		if(isset($memo)) {
			$preapprovalRequest->memo = $memo;
		}
		if(isset($options['senderEmail'] )) {
			$preapprovalRequest->senderEmail = $options['senderEmail'];
		}
		
		$preapprovalRequest->feesPayer = 'EACHRECEIVER';
		
		if(isset($options['maxTotalAmountOfAllPayments'])) {
			$preapprovalRequest->displayMaxTotalAmount = 1;
		}
		
		$logger->log("Created PreApprovalRequest Object");
		$service = new AdaptivePaymentsService();
		try {
			$response = $service->Preapproval($preapprovalRequest);
			$logger->error("Received PreApprovalResponse:");
			$ack = strtoupper($response->responseEnvelope->ack);
		} catch(Exception $ex) {
			return null;
		}
		
		if($ack != "SUCCESS"){
			return null;
		}
		
		$token = $response->preapprovalKey;
		
		return $token;		
	}
	
	/**
	 * Cancels the payment permission for a user
	 * @param int $userID the ID of the user to cancel for
 	 * @return boolean true on success false otherwise
	 */
	public static function cancelUserPayments($userID)
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('users')->where('userID = ?', $userID);
		$res = $con->query($select);
		$row = $res->fetch_array();
		
		$key = $row['userPayPalToken'];
		return self::cancelkey($key);
	}
	
	/**
	 * Cancels a payment token
	 * @param string $key the payment token to cancel
	 * @return boolean true on success false otherwise
	 */
	
	private static function cancelkey($key)
	{
		$logger = new PPLoggingManager('CancelPreapproval');
		
		// create request
		$requestEnvelope = new RequestEnvelope("en_US");
		$cancelPreapprovalReq = new CancelPreapprovalRequest($requestEnvelope, $key);
		$logger->log("Created CancelPreapprovalRequest Object");
		
		
		$service = new AdaptivePaymentsService();
		try {
			$response = $service->CancelPreapproval($cancelPreapprovalReq);
			$logger->error("Received CancelPreapprovalResponse:");
			$ack = strtoupper($response->responseEnvelope->ack);
		}
		catch(Exception $ex) {
			return false;
		}
		if($ack != "SUCCESS"){
			return false;
		}
		
		return true;
	}
	
	/**
	 * Bills user account for a specified sum
	 * @param double $amount how much money to charge the account for 
	 * @param int $userID the ID of the user to bill
	 * @param string $memo a string describing the transaction
	 * @return boolean|string return payment token on success false on failure
	 */
	private static function billUser($amount,$userID,$memo)
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('users')->where('userID = ?',$userID);
		$res = $con->query($select);
		$row = $res->fetch_array();
		$logger = new PPLoggingManager('Pay');
		$receiver = array();
		
		/*
		 * information on who will recieve the money
		*/
		$receiver[0] = new Receiver();
		$receiver[0]->email = self::companyPayPal;
		$receiver[0]->amount = $amount;
		$receiver[0]->primary = 'false';
		
		/*if($_POST['invoiceId'][0] != "") {
			$receiver[0]->invoiceId = $_POST['invoiceId'][0];
		}*/
		
		/*
		 * the type of service the user is paying for can be
		 * GOODS,DIGITALGOODS,SERVICE 
		 */
		$receiver[0]->paymentType = 'DIGITALGOODS';
		
		/*if($_POST['paymentSubType'][0] != "") {
			$receiver[0]->paymentSubType = $_POST['paymentSubType'][0];
		}
		if($_POST['phoneCountry'][0] != "" && $_POST['phoneNumber'][0]) {
			$receiver[0]->phone = new PhoneNumberType($_POST['phoneCountry'][0], $_POST['phoneNumber'][0]);
			if($_POST['phoneExtn'][0] != "") {
				$receiver[0]->phone->extension = $_POST['phoneExtn'][0];
			}
		}*/
		$receiverList = new ReceiverList($receiver);
		
		
		$requestEnvelope = new RequestEnvelope("en_US");
		
		$payRequest = new PayRequest($requestEnvelope, 'PAY', 'http://127.0.0.1', 'USD', $receiverList,'http://127.0.0.1');
		// Add optional params
		
		$payRequest->feesPayer = 'EACHRECEIVER';
		
		$payRequest->preapprovalKey  = $row["userPaypalToken"];
		
		$payRequest->memo = $memo;		
		
		$service  = new AdaptivePaymentsService();
		try {
			$response = $service->Pay($payRequest);
		} catch(Exception $ex) {
			return false;
		}
		$logger->log("Received payResponse:");
		/* Make the call to PayPal to get the Pay token
		 If the API call succeded, then redirect the buyer to PayPal
		to begin to authorize payment.  If an error occured, show the
		resulting errors */
		$ack = strtoupper($response->responseEnvelope->ack);
		if($ack != "SUCCESS") {
			return false;
		} else {
			return  $response->payKey;
			
		}		
	}
	
	/**
	 * credits user account with a specified amount
	 * @param double $amount how much to credit account with
	 * @param int $userID the ID of the user
	 * @param string $memo description of transaction
	 * @return boolean|string return payment token on success false on failure
	 */
	private static function creditUser($amount,$userID,$memo)
	{
		$logger = new PPLoggingManager('Pay');
		$receiver = array();
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('users')->where('userID = ?',$userID);
		$res = $con->query($select);
		$row = $res->fetch_array();
		
		/*
		 * information on who will recieve the money
		 */
		$receiver[0] = new Receiver();
		$receiver[0]->email = $row['userPayPalAccount'];
		$receiver[0]->amount = $amount;
		$receiver[0]->primary = 'false';

				/*if($_POST['invoiceId'][0] != "") {
			$receiver[0]->invoiceId = $_POST['invoiceId'][0];
		}*/
		
		/*
		* the type of service the user is paying for can be
		* GOODS,DIGITALGOODS,SERVICE
		*/
		$receiver[0]->paymentType = 'SERVICE';
		
		/*if($_POST['paymentSubType'][0] != "") {
			$receiver[0]->paymentSubType = $_POST['paymentSubType'][0];
		}
		if($_POST['phoneCountry'][0] != "" && $_POST['phoneNumber'][0]) {
			$receiver[0]->phone = new PhoneNumberType($_POST['phoneCountry'][0], $_POST['phoneNumber'][0]);
			if($_POST['phoneExtn'][0] != "") {
				$receiver[0]->phone->extension = $_POST['phoneExtn'][0];
			}
		}*/
			
		$receiverList = new ReceiverList($receiver);
		$requestEnvelope = new RequestEnvelope("en_US");
		
		$payRequest = new PayRequest($requestEnvelope, 'PAY', 'http://127.0.0.1', 'USD', $receiverList,'http://127.0.0.1');
		// Add optional params
		$payRequest->feesPayer = 'EACHRECEIVER';
				
		$payRequest->senderEmail  = self::companyPayPal;
		
		$payRequest->memo = $memo;
		
		/*if($_POST['fundingConstraint'] != "" && $_POST['fundingConstraint'] != DEFAULT_SELECT) {
			$payRequest->fundingConstraint = new FundingConstraint();
			$payRequest->fundingConstraint->allowedFundingType = new FundingTypeList();
			$payRequest->fundingConstraint->allowedFundingType->fundingTypeInfo = array();
			$payRequest->fundingConstraint->allowedFundingType->fundingTypeInfo[]  = new FundingTypeInfo($_POST["fundingConstraint"]);
		}*/
		/*if($_POST['emailIdentifier'] != "") {
			$payRequest->sender->emailIdentifier  = $_POST["emailIdentifier"];
		}
		if($_POST['phoneIdentifier'] != "") {
			$payRequest->sender->phoneIdentifier  = $_POST["phoneIdentifier"];
		}
		if($_POST['useCredentials'] != "") {
			$payRequest->sender->useCredentials  = $_POST["useCredentials"];
		}*/
		//$payRequest->sender->emailIdentifier  = self::companyPayPal;
		
		
		
		$service  = new AdaptivePaymentsService();
		try {
			$response = $service->Pay($payRequest);
		} catch(Exception $ex) {
			return false;
		}
		$logger->log("Received payResponse:");
		/* Make the call to PayPal to get the Pay token
		 If the API call succeded, then redirect the buyer to PayPal
		to begin to authorize payment.  If an error occured, show the
		resulting errors */
		$ack = strtoupper($response->responseEnvelope->ack);
		if($ack != "SUCCESS") {
			return false;
		} else {
			return $response->payKey;
			
		}
		
	}
}

?>