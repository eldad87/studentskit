<?php

class BillingController extends AppController {
	public $name = 'Billing';
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')), 'Security');

    public function beforeFilter() {
        parent::beforeFilter();

        if($this->RequestHandler->isAjax()) {
            $this->viewPath = $this->viewPath.DS.'ajax';
        }
    }

    //Show the current credit
    //Will have button for paymentTransferRequest
    public function index() {

    }

    //Teacher is asking to transfer his money
    //Make sure to check the invoice
    public function paymentTransferRequest() {

    }

    //After money is transferred to the teacher, place here the amount of money he got
    //Note: remember to reduce Paypal fees
    public function changeTeacherBillingBalance() {

    }

    //Download an invoice as PDF
    public function downloadReceipt($userLessonId) {

    }
}
