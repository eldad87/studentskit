<?php
class AdaptivePayments {
    private $adaptivePaymentsService;
    private $currencyCode = 'USD';

    public function __construct( $callerId='seller_1358606654_biz_api1.gmail.comm' ) {

        //Load the AdaptivePayments libraries
        set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'Vendor' . DS . 'AdaptivePayments'.DS.'lib');
        App::import('Vendor', 'AdaptivePayments'.DS.'lib'.DS.'services'.DS.'AdaptivePayments'.DS.'AdaptivePaymentsService');

        //Init objects
        $this->adaptivePaymentsService = new AdaptivePaymentsService();
        $this->callerId = $callerId;
    }

    public function setCurrency( $currency ) {
        $this->currencyCode = $currency;
    }

    public function cancelPreapproval($preapprovalKey) {
        $cancelPreapprovalRequest = new CancelPreapprovalRequest($this->getRequestEnvelope(), $preapprovalKey);

        try {
            $response = $this->adaptivePaymentsService->CancelPreapproval($cancelPreapprovalRequest, $this->callerId);
        } catch(Exception $ex) {
            throw new Exception('Error occurred in PaymentDetails method');
        }
        return $response;
    }

    public function confirmPreapproval($preapprovalKey) {
        $confirmPreapprovalRequest = new ConfirmPreapprovalRequest($this->getRequestEnvelope(), $preapprovalKey);

        try {
            $response = $this->adaptivePaymentsService->ConfirmPreapproval($confirmPreapprovalRequest, $this->callerId);
        } catch(Exception $ex) {
            throw new Exception('Error occurred in PaymentDetails method');
        }
        return $response;
    }


    /**
     * Get the user approval for a specific amount
     * @param $amount
     * @param $customerId
     * @param $clientIp
     * @param $cancelUrl
     * @param $returnUrl
     * @param null $approvalValidThru
     * @param null $ipnNotificationUrl
     * @return PreapprovalResponse
     * @throws Exception
     */
    public function preapproval( $amount, $customerId, $clientIp, $cancelUrl, $returnUrl, $approvalValidThru=null, $ipnNotificationUrl=null ) {


        $preapprovalRequest = new PreapprovalRequest($this->getRequestEnvelope(), $cancelUrl, $this->currencyCode, $returnUrl, /*date('Y-m-d'));*/ date('Y-m-d\Z', CakeTime::toUnix('now', 'UTC' )));

        if(!$approvalValidThru) {
            $approvalValidThru = /*date('Y-m-d', time()+YEAR);*/ date('Y-m-d\Z', CakeTime::toUnix('now +1 year', 'UTC' ));
        }

        $preapprovalRequest->endingDate                     = $approvalValidThru;
        $preapprovalRequest->maxTotalAmountOfAllPayments    = $amount;
        $preapprovalRequest->feesPayer                      = 'SECONDARYONLY';
        $preapprovalRequest->ipnNotificationUrl             = $ipnNotificationUrl;

        $preapprovalRequest->clientDetails                  = new ClientDetailsType();
        $preapprovalRequest->clientDetails->customerId      = $customerId;
        if($clientIp) {
            $preapprovalRequest->clientDetails->ipAddress   = $clientIp;
        }
        try {
            $response = $this->adaptivePaymentsService->Preapproval($preapprovalRequest, $this->callerId);
        } catch(Exception $ex) {
            throw new Exception('Error occurred in PaymentDetails method');
        }


        return $response;
    }

    public function preapprovalDetails( $preapprovalKey ) {
        $preapprovalDetailsRequest = new PreapprovalDetailsRequest($this->getRequestEnvelope());
        $preapprovalDetailsRequest->preapprovalKey = $preapprovalKey;

        try {
            $response = $this->adaptivePaymentsService->PreapprovalDetails($preapprovalDetailsRequest, $this->callerId);
        } catch(Exception $ex) {
            throw new Exception('Error occurred in PaymentDetails method');
        }

        return $response;
    }

    public function paymentDetails( $preapprovalKey=null, $trackingId=null ) {
        $paymentDetailsReq = new PaymentDetailsRequest($this->getRequestEnvelope());
        if($preapprovalKey) {
            $paymentDetailsReq->payKey = $preapprovalKey;
        }
        if($trackingId) {
            $paymentDetailsReq->trackingId = $trackingId;
        }

        try {
            $response = $this->adaptivePaymentsService->PaymentDetails($paymentDetailsReq);
        } catch(Exception $ex) {
            throw new Exception('Error occurred in PaymentDetails method');
        }

        return $response;
    }

    public function pay( $receivers, $trackingId=null, $preapprovalKey=null, $cancelUrl=null, $returnUrl=null, $ipnNotificationUrl=null, $memo=null  ) {

        //Process receivers
        $receiver = array();
        foreach($receivers AS $key=>$r) {
            $receiver[$key] = new Receiver();
            $receiver[$key]->email          = $r['email'];
            $receiver[$key]->amount         = $r['amount'];
            $receiver[$key]->primary        = $r['primary'];
            $receiver[$key]->paymentType    = $r['paymentType'];
        }
        $receiverList = new ReceiverList($receiver);

        $payRequest = new PayRequest($this->getRequestEnvelope(), 'PAY', $cancelUrl, $this->currencyCode, $receiverList, $returnUrl);
        $payRequest->preapprovalKey     = $preapprovalKey;
        $payRequest->trackingId         = $trackingId;
        $payRequest->feesPayer          = 'SECONDARYONLY';
        $payRequest->ipnNotificationUrl = $ipnNotificationUrl;
        $payRequest->memo               = $memo;
        //senderEmail


        try {
            $response = $this->adaptivePaymentsService->Pay($payRequest, $this->callerId);
        } catch(Exception $ex) {
            throw new Exception('Error occurred in PaymentDetails method');
        }

        return $response;
    }

    /*public function validate_ipn() {
        // parse the paypal URL
        $url_parsed=parse_url('https://www.paypal.com/cgi-bin/webscr');

        // generate the post string from the _POST vars aswell as load the
        // _POST vars into an arry so we can play with them from the calling
        // script.
        $post_string = '';
        foreach ($_POST as $field=>$value) {
            $this->ipn_data["$field"] = $value;
            $post_string .= $field.'='.urlencode(stripslashes($value)).'&';
        }
        $post_string.="cmd=_notify-validate"; // append ipn command

        // open the connection to paypal
        $fp = fsockopen($url_parsed[host],"80",$err_num,$err_str,30);
        if(!$fp) {

            // could not open the connection.  If loggin is on, the error message
            // will be in the log.
            $this->last_error = "fsockopen error no. $errnum: $errstr";
            $this->log_ipn_results(false);
            return false;

        } else {

            // Post the data back to paypal
            fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n");
            fputs($fp, "Host: $url_parsed[host]\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: ".strlen($post_string)."\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $post_string . "\r\n\r\n");

            // loop through the response from the server and append to variable
            while(!feof($fp)) {
                $this->ipn_response .= fgets($fp, 1024);
            }

            fclose($fp); // close connection

        }

        if (eregi("VERIFIED",$this->ipn_response)) {

            // Valid IPN transaction.
            $this->log_ipn_results(true);
            return true;

        } else {

            // Invalid IPN transaction.  Check the log for details.
            $this->last_error = 'IPN Validation Failed.';
            $this->log_ipn_results(false);
            return false;

        }

    }*/

    private function getRequestEnvelope() {
        return new RequestEnvelope("en_US");
    }
}
?>