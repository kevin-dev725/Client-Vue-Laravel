<?php

namespace App\SES;

use App\SES\SimpleEmailServiceRequest;

class SimpleEmailService
{
	protected $__accessKey; // AWS Access key
	protected $__secretKey; // AWS Secret key
	protected $__host;

	public function getAccessKey() { return $this->__accessKey; }
	public function getSecretKey() { return $this->__secretKey; }
	public function getHost() { return $this->__host; }

	protected $__verifyHost = 1;
	protected $__verifyPeer = 1;

	// verifyHost and verifyPeer determine whether curl verifies ssl certificates.
	// It may be necessary to disable these checks on certain systems.
	// These only have an effect if SSL is enabled.
	public function verifyHost() { return $this->__verifyHost; }
	public function enableVerifyHost($enable = true) { $this->__verifyHost = $enable; }

	public function verifyPeer() { return $this->__verifyPeer; }
	public function enableVerifyPeer($enable = true) { $this->__verifyPeer = $enable; }

	/**
	* Constructor
	*
	* @param string $accessKey Access key
	* @param string $secretKey Secret key
	* @return void
	*/
	public function __construct($accessKey = null, $secretKey = null, $host = 'email.us-east-1.amazonaws.com') {
		if ($accessKey !== null && $secretKey !== null) {
			$this->setAuth($accessKey, $secretKey);
		}
		$this->__host = $host;
	}

	/**
	* Set AWS access key and secret key
	*
	* @param string $accessKey Access key
	* @param string $secretKey Secret key
	* @return void
	*/
	public function setAuth($accessKey, $secretKey) {
		$this->__accessKey = $accessKey;
		$this->__secretKey = $secretKey;
	}

	/**
	* Lists the email addresses that have been verified and can be used as the 'From' address
	* 
	* @return An array containing two items: a list of verified email addresses, and the request id.
	*/
	public function listVerifiedEmailAddresses() {
		$rest = new SimpleEmailServiceRequest($this, 'GET');
		$rest->setParameter('Action', 'ListVerifiedEmailAddresses');

		$rest = $rest->getResponse();
		if($rest->error === false && $rest->code !== 200) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}
		if($rest->error !== false) {
			$this->__triggerError('listVerifiedEmailAddresses', $rest->error);
			return false;
		}

		$response = array();
		if(!isset($rest->body)) {
			return $response;
		}

		$addresses = array();
		foreach($rest->body->ListVerifiedEmailAddressesResult->VerifiedEmailAddresses->member as $address) {
			$addresses[] = (string)$address;
		}

		$response['Addresses'] = $addresses;
		$response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;

		return $response;
	}

	/**
	* Requests verification of the provided email address, so it can be used
	* as the 'From' address when sending emails through SimpleEmailService.
	*
	* After submitting this request, you should receive a verification email
	* from Amazon at the specified address containing instructions to follow.
	*
	* @param string email The email address to get verified
	* @return The request id for this request.
	*/
	public function verifyEmailAddress($email) {
		$rest = new SimpleEmailServiceRequest($this, 'POST');
		$rest->setParameter('Action', 'VerifyEmailAddress');
		$rest->setParameter('EmailAddress', $email);

		$rest = $rest->getResponse();
		if($rest->error === false && $rest->code !== 200) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}
		if($rest->error !== false) {
			$this->__triggerError('verifyEmailAddress', $rest->error);
			return false;
		}

		$response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;
		return $response;
	}

	/**
	* Removes the specified email address from the list of verified addresses.
	*
	* @param string email The email address to remove
	* @return The request id for this request.
	*/
	public function deleteVerifiedEmailAddress($email) {
		$rest = new SimpleEmailServiceRequest($this, 'DELETE');
		$rest->setParameter('Action', 'DeleteVerifiedEmailAddress');
		$rest->setParameter('EmailAddress', $email);

		$rest = $rest->getResponse();
		if($rest->error === false && $rest->code !== 200) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}
		if($rest->error !== false) {
			$this->__triggerError('deleteVerifiedEmailAddress', $rest->error);
			return false;
		}

		$response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;
		return $response;
	}

	/**
	* Retrieves information on the current activity limits for this account.
	* See http://docs.amazonwebservices.com/ses/latest/APIReference/API_GetSendQuota.html
	*
	* @return An array containing information on this account's activity limits.
	*/
	public function getSendQuota() {
		$rest = new SimpleEmailServiceRequest($this, 'GET');
		$rest->setParameter('Action', 'GetSendQuota');

		$rest = $rest->getResponse();
		if($rest->error === false && $rest->code !== 200) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}
		if($rest->error !== false) {
			$this->__triggerError('getSendQuota', $rest->error);
			return false;
		}

		$response = array();
		if(!isset($rest->body)) {
			return $response;
		}

		$response['Max24HourSend'] = (string)$rest->body->GetSendQuotaResult->Max24HourSend;
		$response['MaxSendRate'] = (string)$rest->body->GetSendQuotaResult->MaxSendRate;
		$response['SentLast24Hours'] = (string)$rest->body->GetSendQuotaResult->SentLast24Hours;
		$response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;

		return $response;
	}

	/**
	* Retrieves statistics for the last two weeks of activity on this account.
	* See http://docs.amazonwebservices.com/ses/latest/APIReference/API_GetSendStatistics.html
	*
	* @return An array of activity statistics.  Each array item covers a 15-minute period.
	*/
	public function getSendStatistics() {
		$rest = new SimpleEmailServiceRequest($this, 'GET');
		$rest->setParameter('Action', 'GetSendStatistics');

		$rest = $rest->getResponse();
		if($rest->error === false && $rest->code !== 200) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}
		if($rest->error !== false) {
			$this->__triggerError('getSendStatistics', $rest->error);
			return false;
		}

		$response = array();
		if(!isset($rest->body)) {
			return $response;
		}

		$datapoints = array();
		foreach($rest->body->GetSendStatisticsResult->SendDataPoints->member as $datapoint) {
			$p = array();
			$p['Bounces'] = (string)$datapoint->Bounces;
			$p['Complaints'] = (string)$datapoint->Complaints;
			$p['DeliveryAttempts'] = (string)$datapoint->DeliveryAttempts;
			$p['Rejects'] = (string)$datapoint->Rejects;
			$p['Timestamp'] = (string)$datapoint->Timestamp;

			$datapoints[] = $p;
		}

		$response['SendDataPoints'] = $datapoints;
		$response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;

		return $response;
	}


	/**
	* Given a SimpleEmailServiceMessage object, submits the message to the service for sending.
	*
	* @return An array containing the unique identifier for this message and a separate request id.
	*         Returns false if the provided message is missing any required fields.
	*/
	public function sendEmail($sesMessage) {
		if(!$sesMessage->validate()) {
			$this->__triggerError('sendEmail', 'Message failed validation.');
			return false;
		}

		$rest = new SimpleEmailServiceRequest($this, 'POST');
		$rest->setParameter('Action', 'SendEmail');

		$i = 1;
		foreach($sesMessage->to as $to) {
			$rest->setParameter('Destination.ToAddresses.member.'.$i, $to);
			$i++;
		}

		if(is_array($sesMessage->cc)) {
			$i = 1;
			foreach($sesMessage->cc as $cc) {
				$rest->setParameter('Destination.CcAddresses.member.'.$i, $cc);
				$i++;
			}
		}

		if(is_array($sesMessage->bcc)) {
			$i = 1;
			foreach($sesMessage->bcc as $bcc) {
				$rest->setParameter('Destination.BccAddresses.member.'.$i, $bcc);
				$i++;
			}
		}

		if(is_array($sesMessage->replyto)) {
			$i = 1;
			foreach($sesMessage->replyto as $replyto) {
				$rest->setParameter('ReplyToAddresses.member.'.$i, $replyto);
				$i++;
			}
		}

		$rest->setParameter('Source', $sesMessage->from);

		if($sesMessage->returnpath != null) {
			$rest->setParameter('ReturnPath', $sesMessage->returnpath);
		}

		if($sesMessage->subject != null && strlen($sesMessage->subject) > 0) {
			$rest->setParameter('Message.Subject.Data', $sesMessage->subject);
			if($sesMessage->subjectCharset != null && strlen($sesMessage->subjectCharset) > 0) {
				$rest->setParameter('Message.Subject.Charset', $sesMessage->subjectCharset);
			}
		}


		if($sesMessage->messagetext != null && strlen($sesMessage->messagetext) > 0) {
			$rest->setParameter('Message.Body.Text.Data', $sesMessage->messagetext);
			if($sesMessage->messageTextCharset != null && strlen($sesMessage->messageTextCharset) > 0) {
				$rest->setParameter('Message.Body.Text.Charset', $sesMessage->messageTextCharset);
			}
		}

		if($sesMessage->messagehtml != null && strlen($sesMessage->messagehtml) > 0) {
			$rest->setParameter('Message.Body.Html.Data', $sesMessage->messagehtml);
			if($sesMessage->messageHtmlCharset != null && strlen($sesMessage->messageHtmlCharset) > 0) {
				$rest->setParameter('Message.Body.Html.Charset', $sesMessage->messageHtmlCharset);
			}
		}

		$rest = $rest->getResponse();
		if($rest->error === false && $rest->code !== 200) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}
		if($rest->error !== false) {
			$this->__triggerError('sendEmail', $rest->error);
			return false;
		}

		$response['MessageId'] = (string)$rest->body->SendEmailResult->MessageId;
		$response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;
		return $response;
	}

	/**
	* Trigger an error message
	*
	* @internal Used by member functions to output errors
	* @param array $error Array containing error information
	* @return string
	*/
	public function __triggerError($functionname, $error)
	{
		if($error == false) {
			trigger_error(sprintf("SimpleEmailService::%s(): Encountered an error, but no description given", $functionname), E_USER_WARNING);
		}
		else if(isset($error['curl']) && $error['curl'])
		{
			trigger_error(sprintf("SimpleEmailService::%s(): %s %s", $functionname, $error['code'], $error['message']), E_USER_WARNING);
		}
		else if(isset($error['Error']))
		{
			$e = $error['Error'];
			$message = sprintf("SimpleEmailService::%s(): %s - %s: %s\nRequest Id: %s\n", $functionname, $e['Type'], $e['Code'], $e['Message'], $error['RequestId']);
			trigger_error($message, E_USER_WARNING);
		}
		else {
			trigger_error(sprintf("SimpleEmailService::%s(): Encountered an error: %s", $functionname, $error), E_USER_WARNING);
		}
	}
}


