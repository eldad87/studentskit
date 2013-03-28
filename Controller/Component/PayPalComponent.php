<?php
class PaypalComponent extends Component {
    protected $currency = 'USD';

    const CHECKOUT_STATUS_PAYMENT_ACTION_NOT_INITIATED  = 'PaymentActionNotInitiated';
    const CHECKOUT_STATUS_PAYMENT_ACTION_FAILED         = 'PaymentActionFailed';
    const CHECKOUT_STATUS_PAYMENT_ACTION_INPROGRESS     = 'PaymentActionInProgress';
    const CHECKOUT_STATUS_PAYMENT_ACTION_COMPLETED      = 'PaymentActionCompleted';


    const PAYMENT_STATUS_NONE                           = 'None';
    const PAYMENT_STATUS_COMPLETED                      = 'Completed';
    const PAYMENT_STATUS_PENDING                        = 'Pending';
    const PAYMENT_STATUS_COMPLETED_FUNDS_HELD           = 'Completed-Funds-Held';
    const PAYMENT_STATUS_IN_PROGRESS                    = 'In-Progress';

    const PAYMENT_STATUS_PARTIALLY_REFUNDED             = 'Partially-Refunded';
    const PAYMENT_STATUS_REFUNDED                       = 'Refunded';
    const PAYMENT_STATUS_REVERSED                       = 'Reversed';
    /**
     * Not handled:
     * Canceled-Reversal
     * Denied
     * Expired
     * Failed
     * Processed
     * Voided
     */



    const PAYER_STATUS_VERIFIED                         = 'verified';
    const PAYER_STATUS_UNVERIFIED                       = 'unverified';

    /**
     * Generate a url, in which the user need to be redirected to
     *
     * @param $pendingUserLessonId
     * @param $amount
     * @param $returnUrl
     * @param $cancelUrl
     * @param null $ipn
     * @return bool|string false/url
     */
    public function setExpressCheckout($pendingUserLessonId, $amount, $returnUrl, $cancelUrl, $ipn=null) {
        $logger = new PPLoggingManager('SetExpressCheckout');

        //Get product info - Find Subject by PUL
        App::import('Model', 'PendingUserLesson');
        $pulObj = new PendingUserLesson();
        $pulObj->recursive = 2;
        $pulObj->unbindAll(array('belongsTo'=>array('Subject')));
        $pulObj->resetRelationshipFields();
        $pulData = $pulObj->findByPendingUserLessonId($pendingUserLessonId);


        //Create Product details
        $paymentDetails = new PaymentDetailsType();
        $paymentDetails->ItemTotal = new BasicAmountType($this->currency, $amount);
        $paymentDetails->OrderTotal = new BasicAmountType($this->currency, $amount);
        $paymentDetails->PaymentAction = 'Sale';

        $itemDetails = new PaymentDetailsItemType();
        $itemDetails->Name = $pulData['Subject']['name'];
        $itemDetails->Description = $pulData['Subject']['description'];
        $itemDetails->Amount = new BasicAmountType($this->currency, $amount);
        $itemDetails->Quantity = 1;
        $itemDetails->ItemCategory = 'Physical'; //'Digital';
        $paymentDetails->PaymentDetailsItem[] = $itemDetails;
        if($ipn) {
            $paymentDetails->NotifyURL = $ipn;
        }


        $setECReqDetails = new SetExpressCheckoutRequestDetailsType();
        $setECReqDetails->PaymentDetails[0] = $paymentDetails;
        $setECReqDetails->CancelURL = $cancelUrl;
        $setECReqDetails->ReturnURL = $returnUrl;
        $setECReqDetails->ReqConfirmShipping = 0;
        $setECReqDetails->NoShipping = 1;
        $setECReqDetails->AllowNote = 0;


        //Display options
        $setECReqDetails->BrandName = __('Universito');
        $setECReqDetails->cppheaderimage = Configure::read('public_domain').'/img/logo.png';
        /*$setECReqDetails->cppheaderbordercolor = 'green'; //'#22E01B'; //$_REQUEST['cppheaderbordercolor']; green
        $setECReqDetails->cppheaderbackcolor = 'red'; //'#E01B60';//$_REQUEST['cppheaderbackcolor']; red
        $setECReqDetails->cpppayflowcolor = 'blue'; //'#1B1EE0'; //$_REQUEST['cpppayflowcolor']; blue
        $setECReqDetails->cppcartbordercolor =  'yellow'; //'#D9E01B'; //$_REQUEST['cppcartbordercolor']; yellow
        $setECReqDetails->cpplogoimage = Configure::read('public_domain').'/img/logo.png';
        $setECReqDetails->PageStyle = $_REQUEST['pageStyle'];*/


        $setECReqType = new SetExpressCheckoutRequestType();
        $setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;
        $setECReq = new SetExpressCheckoutReq();
        $setECReq->SetExpressCheckoutRequest = $setECReqType;


        $paypalService = new PayPalAPIInterfaceServiceService();
        try {
            /* wrap API method calls on the service object with a try catch */
            $setECResponse = $paypalService->SetExpressCheckout($setECReq);
        } catch (Exception $e) {
            $this->logException($e);
            return false;
        }


        if($setECResponse->Ack!='Success' || $setECResponse->Errors) {
            //TODO: log
            return false;
        }

        //Save into DB
        $save = array(
            'pending_user_lesson_id'=> $pendingUserLessonId,
            'student_user_id'       => $pulData['PendingUserLesson']['student_user_id'],
            'amount'                => $amount,
            'token'                 => $setECResponse->Token,
            'currency'              => $this->currency,
            'history'               => $this->createHistory(null, $paypalService->getLastRequest(), $paypalService->getLastResponse())
        );

        App::import('Model', 'ExpressCheckout');
        $ecObj = new ExpressCheckout();
        if(!$ecObj->save($save)) {
            //TODO: log
            return false;
        }


        $ppp = PPConfigManager::getInstance();
        return $ppp->get('service.RedirectURL') . '_express-checkout&token=' . $setECResponse->Token;
    }

    public function updateGetExpressCheckout($pendingUserLessonId) {
        App::import('Model', 'ExpressCheckout');
        $ecObj = new ExpressCheckout();

        //Find
        $ecObj->recursive = -1;
        $ecData = $ecObj->findByPendingUserLessonId($pendingUserLessonId);
        if(!$ecData) {
            return false;
        }
        $ecData = $ecData['ExpressCheckout'];


        //Try locking
        if(!$ecObj->lock($ecData['express_checkout_id'], 10)) {
            return false;
        }


        //Get Data from PayPal
        $logger = new PPLoggingManager('GetExpressCheckout');

        $getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($ecData['token']);

        $getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
        $getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

        $paypalService = new PayPalAPIInterfaceServiceService();
        try {
            /* wrap API method calls on the service object with a try catch */
            $getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
        } catch (Exception $e) {
            $this->logException($e);
            $ecObj->unlock($ecData['express_checkout_id']);
            return false;
        }


        //Save into DB
        $save = array(
            'history'           => $this->createHistory($ecData['history'], $paypalService->getLastRequest(), $paypalService->getLastResponse())
        );

        if(isSet($getECResponse->GetExpressCheckoutDetailsResponseDetails->CheckoutStatus) &&
            $getECResponse->GetExpressCheckoutDetailsResponseDetails->CheckoutStatus) {
            $save['checkout_status'] = $getECResponse->GetExpressCheckoutDetailsResponseDetails->CheckoutStatus;
        }
        if(isSet($getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID) &&
            $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID) {
            $save['payer_id'] = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID;
        } if($getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerStatus) {
            $save['payer_status'] = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerStatus;
        }
        if(isSet($getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails->TransactionId) &&
            $getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails->TransactionId) {
            $save['transaction_id'] = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails->TransactionId;
        }


        $ecObj->create(false);
        $ecObj->id = $ecData['express_checkout_id'];
        if(!$ecObj->save($save)) {
            //TODO: log
            $ecObj->unlock($ecData['express_checkout_id']);
            return false;
        }
        $ecObj->unlock($ecData['express_checkout_id']);


        if($getECResponse->Ack!='Success' || $getECResponse->Errors) {
            return false;
        }

        return true;
    }

    public function DoExpressCheckoutIPN($pendingUserLessonId) {
        App::import('Model', 'ExpressCheckout');
        $ecObj = new ExpressCheckout();

        //Find
        $ecObj->recursive = -1;
        $ecData = $ecObj->findByPendingUserLessonId($pendingUserLessonId);
        if(!$ecData) {
            return false;
        }
        $ecData = $ecData['ExpressCheckout'];

        //Try locking
        if(!$ecObj->lock($ecData['express_checkout_id'], 10)) {
            return false;
        }


        $ipnMessage = new PPIPNMessage();
        if(!$ipnMessage->validate()) {
            return false;
        }

        $ipnData = $ipnMessage->getRawData();

        //Save into DB
        $save = array(
            'history'           => $this->createHistory($ecData['history'], null, $ipnData)
        );

        if(isSet($ipnData['payment_status']) && $ipnData['payment_status']) {
            $save['payment_status'] = $ipnData['payment_status'];
        }
        $transactionId = $ipnMessage->getTransactionId();
        if(isSet($transactionId) && $transactionId) {
            $save['transaction_id'] = $transactionId;
        }
        if(isSet($ipnData['mc_fee']) && $ipnData['mc_fee']) {
            $save['fee_amount'] = $ecObj->getDataSource()->expression('fee_amount+'.$ipnData['mc_fee']);
        }
        if(isSet($ipnData['mc_gross']) && $ipnData['mc_gross']) {
            $save['gross_amount'] = $ecObj->getDataSource()->expression('gross_amount+'.$ipnData['mc_gross']);
        }

        $ecObj->create(false);
        $ecObj->id = $ecData['express_checkout_id'];
        if(!$ecObj->save($save)) {
            //TODO: log
            $ecObj->unlock($ecData['express_checkout_id']);
            return false;
        }

        $ecObj->unlock($ecData['express_checkout_id']);



        $save['gross_amount'] = $ipnData['mc_gross']; //Add how much money did we received
        if($save['gross_amount']<0) {
            //TODO: log refund
        }

        //Dispatch event
        App::import('Model', 'UserLesson');
        new UserLesson(); //Bind events
        $event = new CakeEvent('Model.PayPal.afterPaymentUpdate', $this, array('current'=>$save, 'old'=>$ecData) );
        CakeEventManager::instance()->dispatch($event);


        return $ipnData['payment_status'];
    }

    public function DoExpressCheckout($pendingUserLessonId, $ipn=null) {
        App::import('Model', 'ExpressCheckout');
        $ecObj = new ExpressCheckout();

        //Find
        $ecObj->cacheQueries = false;
        $ecObj->recursive = -1;
        $ecData = $ecObj->findByPendingUserLessonId($pendingUserLessonId);
        if(!$ecData) {
            return false;
        }
        $ecData = $ecData['ExpressCheckout'];


        //Try locking
        if(!$ecObj->lock($ecData['express_checkout_id'], 10)) {
            return false;
        }



        //Update data from PayPal
        if(!$this->updateGetExpressCheckout($pendingUserLessonId)) {
            return false;
        }
        //Find again, after PayPal update
        $ecObj->cacheQueries = false;
        $ecData = $ecObj->findByPendingUserLessonId($pendingUserLessonId);
        if(!$ecData) {
            return false;
        }
        $ecData = $ecData['ExpressCheckout'];




        //Check that user is verified
        if( $ecData['payer_status']!=PaypalComponent::PAYER_STATUS_VERIFIED &&
            empty( $ecData['payer_id']) ) {

            return false;
        }

        //Check if we tried to pay using this record before, if so Updates should arrive through IPN only
        if( $ecData['payment_status'] != PaypalComponent::PAYMENT_STATUS_NONE ||
            !empty( $ecData['transaction_id'] ) ) {
            return $ecData['payment_status'];
        }



        //Charge user
        $logger = new PPLoggingManager('DoExpressCheckout');


        $orderTotal = new BasicAmountType();
        $orderTotal->currencyID = $ecData['currency'];
        $orderTotal->value = $ecData['amount'];

        $paymentDetails= new PaymentDetailsType();
        $paymentDetails->OrderTotal = $orderTotal;

        if($ipn) {
            $paymentDetails->NotifyURL = $ipn;
        }

        $DoECRequestDetails = new DoExpressCheckoutPaymentRequestDetailsType();
        $DoECRequestDetails->PayerID = $ecData['payer_id'];
        $DoECRequestDetails->Token = $ecData['token'];
        $DoECRequestDetails->PaymentAction = 'Sale';
        $DoECRequestDetails->PaymentDetails[] = $paymentDetails;

        $DoECRequest = new DoExpressCheckoutPaymentRequestType();
        $DoECRequest->DoExpressCheckoutPaymentRequestDetails = $DoECRequestDetails;


        $DoECReq = new DoExpressCheckoutPaymentReq();
        $DoECReq->DoExpressCheckoutPaymentRequest = $DoECRequest;

        $paypalService = new PayPalAPIInterfaceServiceService();
        try {
            /* wrap API method calls on the service object with a try catch */
            $DoECResponse = $paypalService->DoExpressCheckoutPayment($DoECReq);
        } catch (Exception $e) {
            $this->logException($e);
            $ecObj->unlock($ecData['express_checkout_id']);
            return false;
        }



        //Save into DB
        $save = array(
            'history'           => $this->createHistory($ecData['history'], $paypalService->getLastRequest(), $paypalService->getLastResponse())
        );
        if(isSet($DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->PaymentStatus) &&
            $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->PaymentStatus) {
            $save['payment_status'] = $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->PaymentStatus;
        }
        if(isSet($DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->TransactionID) &&
            $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->TransactionID) {
            $save['transaction_id'] = $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->TransactionID;
        }
        if(isSet($DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->FeeAmount->value) &&
            $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->FeeAmount->value) {
            $save['fee_amount'] = $ecObj->getDataSource()->expression('fee_amount+'.$DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->FeeAmount->value);
        }
        if(isSet($DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->GrossAmount->value) &&
            $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->GrossAmount->value) {
            $save['gross_amount'] = $ecObj->getDataSource()->expression('gross_amount+'.$DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->GrossAmount->value);
        }


        $ecObj->create(false);
        $ecObj->id = $ecData['express_checkout_id'];
        if(!$ecObj->save($save)) {
            //TODO: log
            $ecObj->unlock($ecData['express_checkout_id']);
            return false;
        }

        $ecObj->unlock($ecData['express_checkout_id']);


        //If error, quit
        if($DoECResponse->Ack!='Success' || $DoECResponse->Errors) {
            return false;
        }

        $save['gross_amount'] = $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->GrossAmount->value; //Add how much money did we received

        //Dispatch event
        App::import('Model', 'UserLesson');
        new UserLesson(); //Bind events
        $event = new CakeEvent('Model.PayPal.afterPaymentUpdate', $this, array('current'=>$save, 'old'=>$ecData) );
        CakeEventManager::instance()->dispatch($event);

        return $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->PaymentStatus;
    }


    private function logException($ex) {
        $ex_message = $ex->getMessage();
        $ex_type = get_class($ex);
        $ex_detailed_message = "";

        if($ex instanceof PPConnectionException) {
            $ex_detailed_message = "Error connecting to " . $ex->getUrl();
        } else if($ex instanceof PPMissingCredentialException || $ex instanceof PPInvalidCredentialException) {
            $ex_detailed_message = $ex->errorMessage();
        } else if($ex instanceof PPConfigurationException) {
            $ex_detailed_message = "Invalid configuration. Please check your configuration file";
        }


    }

    /**
     * Append request/response to current history
     * @param $currentHistory
     * @param $request
     * @param $response
     * @return string
     */
    private function createHistory($currentHistory, $request, $response) {
        if(!$currentHistory) {
            $currentHistory = array();
        } else if(is_string($currentHistory)) {
            $currentHistory = json_decode($currentHistory, true);
        }

        $currentHistory[] = array(
            'request' => $request,
            'response' => $response
        );

        return json_encode($currentHistory);
    }
}