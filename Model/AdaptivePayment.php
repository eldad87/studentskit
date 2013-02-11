<?php
/**
 * This model is used to save the stage of the payments
 */
App::import('Model', 'AppModel');
class AdaptivePayment extends AppModel {
	public $name = 'AdaptivePayment';
	public $useTable = 'adaptive_payments';
	public $primaryKey = 'adaptive_payment_id';
	public $belongsTo = array(
                    'UserLesson' => array(
						'className' => 'UserLesson',
						'foreignKey'=>'user_lesson_id'
					)
				);

    private $adaptivePayments;

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);

        App::import('Vendor', 'AdaptivePayments'.DS.'AdaptivePayments');
        $this->adaptivePayments = new AdaptivePayments( Configure::read('paypal_api_username') );
    }

    /**
     * @param $pendingUserLessonId
     * @param $action
     * @param $cancelUrl
     * @param $returnUrl
     * @param null $clientIP
     * @param null $ipnNotificationUrl
     * @return bool|string
     *
     * return string on success
     * return false on failure
     * return true if already approved/paid
     */
    public function getPreApprovalURL( $pendingUserLessonId, $action, $cancelUrl, $returnUrl, $clientIP=null, $ipnNotificationUrl=null ) {
        App::import('Model', 'PendingUserLesson');
        $pulObj = new PendingUserLesson();
        $pendingUserLessonData = $pulObj->findByPendingUserLessonId($pendingUserLessonId);
        if(!$pendingUserLessonData) {
            return false;
        }
        $pendingUserLessonData = $pendingUserLessonData['PendingUserLesson'];

        //Cancel duplicate IN_PROCESS requests
        $this->cancelDuplications($pendingUserLessonData, 'IN_PROCESS');


        //Generate unique ID and append it to the URLS
        //$adaptivePaymentId = $this->getUnusedUserLessonId();

        $approvalValidThru = date('Y-m-d\Z', CakeTime::toUnix('now +1 year', 'UTC' )); //date('Y-m-d', time()+YEAR);
        try {
            $response = $this->adaptivePayments->preapproval( $pendingUserLessonData['1_on_1_price'], $pendingUserLessonData['student_user_id'], $clientIP, $cancelUrl, $returnUrl, $approvalValidThru, $ipnNotificationUrl );
        } catch(Exception $e) {
            $this->log( 'pendingUserLessonId: '.$pendingUserLessonId.', Exception: '. $e->getMessage(), 'adaptive_payment_error');
            return false;
        }

        if(strtolower($response->responseEnvelope->ack)!='success') {
            $this->log(var_export($response, true), 'adaptive_payment_error');
            return false;
        }


        //Save status in DB
        $this->create(false);
        $this->set(array(
            'pending_user_lesson_id'=>$pendingUserLessonId,
            'user_lesson_id'        =>$pendingUserLessonData['user_lesson_id'],
            'teacher_lesson_id'     =>$pendingUserLessonData['teacher_lesson_id'],
            'subject_id'            =>$pendingUserLessonData['subject_id'],
            'student_user_id'       =>$pendingUserLessonData['student_user_id'],
            'preapproval_response'  =>json_encode($response),
            'preapproval_key'       =>$response->preapprovalKey,
            'status'                =>'IN_PROCESS',
            'is_approved'           =>0,
            'is_used'               =>0,
            'max_amount'            =>$pendingUserLessonData['1_on_1_price'],
            'valid_thru'            =>$approvalValidThru,

        ));
        if(!$this->save()) {
            $this->cancelApproval( $pendingUserLessonId, $response->preapprovalKey );
            return false;
        }

        return Configure::read('paypal_preapproval_url').$response->preapprovalKey;
    }

    /**
     *
     * Charge all students of TeacherLessonId - only with 'approved' status.
     * @param $teacherLessonId
     * @param $cancelUrl
     * @param $returnUrl
     * @return int, on success an array of successTransactionsCount, status, perStudentPrice, perStudentCommission, receivers
     */
    public function pay( $teacherLessonId , $cancelUrl, $returnUrl) {
        $this->UserLesson->TeacherLesson->recursive = -1;
        $tlData = $this->UserLesson->TeacherLesson->findByTeacherLessonId($teacherLessonId);
        if(!$tlData || $tlData['TeacherLesson']['is_deleted'] || !$tlData['TeacherLesson']['1_on_1_price']) {
            return PAYMENT_STATUS_ERROR;
        }
        //Check if already used for payment
        if($tlData['TeacherLesson']['payment_status']!=PAYMENT_STATUS_PENDING) {
            return $tlData['TeacherLesson']['payment_status'];
        }

        //Calc how much each student need to pay and to which receiver
        $receiversPriceAndCommission = $this->generatePaymentReceiversPriceAndCommission($teacherLessonId);
        if(!is_array($receiversPriceAndCommission)) {
            return $receiversPriceAndCommission; //Return error message
        }


        //Get all approved payments + only approved students
        $aps = $this->find('all', array('conditions'=>array('UserLesson.teacher_lesson_id'=>$teacherLessonId,
                                                            'UserLesson.stage'=>array(  USER_LESSON_ACCEPTED, USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_TEACHER_RATING,
                                                                                        USER_LESSON_PENDING_STUDENT_RATING, USER_LESSON_DONE),
                                                            'is_approved'=>'1', 'status'=>'ACTIVE', 'is_used'=>0)));
        if(!$aps) {
            //There are no users in lesson
            return PAYMENT_STATUS_DONE;
        }

        //TODO - check all approval first - users may canceled their approval from PayPal during this process


        $successTransactionsCount = 0;
        foreach($aps AS $ap) {
            $ap = $ap['AdaptivePayment'];
            try {
                $response = $this->adaptivePayments->pay( $receiversPriceAndCommission['receivers'], $ap['user_lesson_id'], $ap['preapproval_key'], $cancelUrl, $returnUrl, null,
                    //I.e. T5U2# Live PHP Lesson
                    'T'.$teacherLessonId.'U'.$ap['user_lesson_id'].'# '.$tlData['TeacherLesson']['name']
                );

                $status = PAYMENT_STATUS_DONE;
                if(is_object($response) && strtolower($response->paymentExecStatus)=='completed') {
                    $this->id = $ap['adaptive_payment_id'];
                    $this->set('is_used', 1);
                    $this->set('paid_amount', $receiversPriceAndCommission['perStudentPrice']);
                    $this->set('pay_key',$response->payKey);
                    $this->set('pay_response',json_encode($response));
                    $this->save();
                    $successTransactionsCount++;

                } else {
                    $this->log(var_export($response, true), 'adaptive_payment_error');
                    $this->setStatus($ap['user_lesson_id'], $ap['preapproval_key'], 'ERROR');
                    $status = PAYMENT_STATUS_ERROR;
                }
            } catch(Exception $e) {
                $this->log(var_export($e, true), 'adaptive_payment_error');
                $this->setStatus($ap['user_lesson_id'], $ap['preapproval_key'], 'ERROR');
                $status = PAYMENT_STATUS_ERROR;
            }

            $event = new CakeEvent('Model.AdaptivePayment.AfterUserLessonPaid', $this, array('user_lesson_id'=>$ap['user_lesson_id'], 'teacher_lesson_id'=>$teacherLessonId, 'status'=>$status) );
            $this->getEventManager()->dispatch($event);
        }

        $return = $receiversPriceAndCommission;
        $return['successTransactionsCount'] = $successTransactionsCount;


        if($return['successTransactionsCount']==$tlData['TeacherLesson']['num_of_students']) {
            $return['status'] = PAYMENT_STATUS_DONE; //All payments was successful
        } else if(!$return['successTransactionsCount']) {
            $return['status'] = PAYMENT_STATUS_ERROR; //No successful payments
        } else {
            $return['status'] = PAYMENT_STATUS_PARTIAL; //Partial payments
        }

        return $return;
    }



    public function cancelTeacherLessonApprovals( $teacherLessonId ) {
        $this->UserLesson->TeacherLesson->recursive = -1;
        $aps = $this->find('all', array('conditions'=>array('teacher_lesson_id'=>$teacherLessonId, 'status'=>array('approved', 'pending_approval'))));
        if(!$aps) {
            return false;
        }

        foreach($aps AS $ap) {
            $this->cancelApproval($ap['AdaptivePayment']['user_lesson_id']);
        }

        return true;
    }

    public function cancelApproval( $userLessonId=null, $preapprovalKey=null ) {
        //$apData = $this->getStatus($userLessonId, null, $preapprovalKey);
        $this->recursive = -1;
        if($userLessonId && $preapprovalKey) {
            $apData = $this->findAllByUserLessonIdAndPreapprovalKey($userLessonId, $preapprovalKey);
        } else if($userLessonId) {
            $apData = $this->findAllByUserLessonId($userLessonId);
        } else if($preapprovalKey) {
            $apData = $this->findAllByPreapprovalKey($preapprovalKey);
        }

        if(!$apData) {
            return false;
        }

        App::import('Model', 'PendingUserLesson');
        $pulObj = new PendingUserLesson();

        foreach($apData AS $data) {
            $data = $data['AdaptivePayment'];

            //Already been used
            if($data['is_used']) {
                continue;
            }

            //Already canceled
            if($data['status']=='CANCELED') {
                continue;
            }

            $response = $this->adaptivePayments->cancelPreapproval($data['preapproval_key']);
            if(strtolower($response->responseEnvelope->ack)!='success') {
                return false;
            }
            if(!$this->setStatus($data[$this->primaryKey], 'CANCELED')) {
                return false;
            }

            if($data['pending_user_lesson_id']) {
                $pulObj->cancel($data['pending_user_lesson_id']);
            }
        }

        return true;
    }

    public function refundTeacherLesson( $teacherLessonId ) {
        $this->UserLesson->TeacherLesson->recursive = -1;
        $aps = $this->find('all', array('conditions'=>array('teacher_lesson_id'=>$teacherLessonId, 'status'=>array('paid'))));
        if(!$aps) {
            return false;
        }

        foreach($aps AS $ap) {
            $this->refund($ap['AdaptivePayment']['user_lesson_id']);
        }
    }

    //testConfirmPreapproval
    public function refund($userLessonId) {
        //TODO
    }


    public function paymentUpdate($ipnData) {
        /**
        approved Whether the preapproval request was approved. Possible values are:
         true – The request was approved
         false – The request was denied
         *
         *
        status Whether this preapproval is active, represented by the following values:
         ACTIVE – The preapproval is active
         CANCELED – The preapproval was explicitly canceled by the sender or by
        PayPal
         DEACTIVED – The preapproval is not active; you can be reactivate it by
        resetting the personal identification number (PIN) or by contacting PayPal
         */

        /**
         * approved
         * status
         * cur_payments_amount/current_total_amount_of_all_payments
         * max_total_amount_of_all_payments
         *
         */
        $apData = $this->getStatus($ipnData['pending_user_lesson_id'], null, $ipnData['preapproval_key']);
        if(!$apData) {
            return false;
        }


        $paid = 0;
        if(isSet($ipnData['current_total_amount_of_all_payments']) && $ipnData['current_total_amount_of_all_payments']>0) { //IPN
            $paid = $ipnData['current_total_amount_of_all_payments'];
        } else if(isSet($ipnData['cur_payments_amount']) && $ipnData['cur_payments_amount']>0) { //Details
            $paid = $ipnData['cur_payments_amount'];
        }


        //Update preapproval
        $this->create(false);
        $this->id = $apData['adaptive_payment_id'];
        $saveData = array('pending_user_lesson_id'=>$ipnData['pending_user_lesson_id'], 'is_approved'=>($ipnData['approved']=='true' ? 1 : 0),
                            'status'=>$ipnData['status'], 'max_amount'=>$ipnData['max_total_amount_of_all_payments'],
                            'paid_amount'=>$paid, 'is_used'=>( $paid ? 1 : 0 ), 'preapproval_ipn_received'=>json_encode($ipnData));
        if(!$this->save($saveData) && $apData['user_lesson_id']) {
            $this->cancelApproval($apData['user_lesson_id'], $ipnData['preapproval_key']);
            return false;
        }



        App::import('Model', 'UserLesson');
        $ulObj = new UserLesson(); //Bind events
        $event = new CakeEvent('Model.AdaptivePayment.afterPaymentUpdate', $this, array('current'=>$saveData, 'old'=>$apData) );
        $this->getEventManager()->dispatch($event);

        return true;
    }

    public function updateUsingPreapprovalDetails($pendingUserLessonId, $action) {
        $data = $this->findByPendingUserLessonId($pendingUserLessonId);

        $details = $this->preapprovalDetails($data['AdaptivePayment']['preapproval_key']);
        $details['action'] = $action;
        $details['pending_user_lesson_id'] = $pendingUserLessonId;
        $details['preapproval_key'] = $data['AdaptivePayment']['preapproval_key'];
        return $this->paymentUpdate($details);
    }

    public function getStatus( $pendingUserLessonId, $status=null, $preapprovalKey=null ) {
        $this->recursive = -1;
        if($status) {
            $apData = $this->findByPendingUserLessonIdAndStatus($pendingUserLessonId, $status);
        } else if($preapprovalKey) {
            $apData = $this->findByPendingUserLessonIdAndPreapprovalKey($pendingUserLessonId, $preapprovalKey);
        } else {
            $apData = $this->findByPendingUserLessonId($pendingUserLessonId);
            //$apData = $this->findByUserLessonId($userLessonId);
        }
        if(!$apData) {
            return false;
        }

        return $apData['AdaptivePayment'];
    }

    public function isPaid($userLessonId) {
        $this->cacheQueries = false;
        return $this->findByUserLessonIdAndIsUsed($userLessonId, 1) ? true : false;
    }

    public function isValidApproval($userLessonId, $amount=null, $datetime=null) {
        //User ask for a FREE lesson
        if(!is_null($amount) && !$amount) {
            return true;
        }

        $data = $this->findByUserLessonIdAndStatus($userLessonId, 'ACTIVE');
        if(!$data) {
            return false;
        }

        $data = $data['AdaptivePayment'];

        if(!$data['is_approved']) {
            return false;
        }
        //Already used
        if($data['is_used']) {
            return false;
        }

        //Check approved amount
        if($amount && $data['max_amount']<$amount) {
            return false;
        }

        //Check approved date
        if($datetime && $datetime>$data['valid_thru']) {
            return false;
        }

        return true;
    }


    public function bindToUserLessonId($adaptivePaymentId, $userLessonId) {
        $this->create(false);
        $this->id = $adaptivePaymentId;
        $this->set(array('user_lesson_id'=>$userLessonId));
        return $this->save();
    }

    public function preapprovalDetails( $preapprovalKey ) {
        $response = $this->adaptivePayments->preapprovalDetails($preapprovalKey);
        if(strtolower($response->responseEnvelope->ack)!='success') {
            return false;
        }

        unset($response->responseEnvelope);

        $return = array();
        foreach($response AS $key=>$val) {
            $return[Inflector::underscore($key)] = $val;
        }

        return $return;
    }

    public function paymentDetails( $preapprovalKey=null, $trackingNum=null ) {
        $response = $this->adaptivePayments->paymentDetails($preapprovalKey, $trackingNum);
        if(strtolower($response->responseEnvelope->ack)!='success') {
            return false;
        }

        unset($response->responseEnvelope);

        $return = array();
        foreach($response AS $key=>$val) {
            $return[Inflector::underscore($key)] = $val;
        }

        return $return;
    }

    private function setStatus( $userLessonId=null, $preapprovalKey=null, $status=null ) {
        if($userLessonId && $preapprovalKey && $status) {
            $apData = $this->findByUserLessonIdAndPreapprovalKey($userLessonId, $preapprovalKey);
        } else if(!$status) {
            $apData = $this->findByAdaptivePaymentId($userLessonId);
            $status = $preapprovalKey;
        }

        if(!$apData) {
            return false;
        }
        $apData = $apData[$this->name];

        if($apData['status']==$status) {
            return true;
        }

        $this->create(false);
        $this->id = $apData['adaptive_payment_id'];
        return $this->save(array('status'=>$status));
    }

    private function getUserLessonData($userLessonId) {
        $this->UserLesson->recursive = -1;
        $userLessonData = $this->UserLesson->findByUserLessonId( $userLessonId );
        if(!$userLessonData) {
            return false;
        }

        return $userLessonData['UserLesson'];
    }

    /**
     * Return an array of receivers, price per UL and commission to charge
     * @param $teacherLessonId
     * @return array|int
     */
    private function generatePaymentReceiversPriceAndCommission($teacherLessonId) {
        $this->UserLesson->TeacherLesson->resetRelationshipFields();
        $this->UserLesson->TeacherLesson->recursive = 1;
        $tlData = $this->UserLesson->TeacherLesson->findByTeacherLessonId($teacherLessonId);
        $teacher = $tlData['User'];
        $tlData = $tlData['TeacherLesson'];

        //If its a video lesson or only 1    student can be in this lesson, the user need to pay the full price
        if($tlData['lesson_type']=='video' || $tlData['max_students']==1 || $tlData['num_of_students']==1) {
            $perStudentPrice = $tlData['1_on_1_price'];
        } else {
            //$price = $this->UserLesson->Subject->calcStudentFullGroupPrice($tlData['1_on_1_price'], $tlData['full_group_total_price'], $tlData['max_students'], $tlData['num_of_students']);
            $perStudentPrice = $this->UserLesson->Subject->calcStudentPriceAfterDiscount( $tlData['1_on_1_price'], $tlData['max_students'], $tlData['num_of_students'], $tlData['full_group_student_price'] );
        }


        if(!$teacher['teacher_paypal_id']) {
            $this->log('TeacherID '.$teacher['user_id'].', does not have teacher_paypal_id set.', 'adaptive_payment_error');
            return PAYMENT_STATUS_ERROR_MISSING_TEACHER_ACCOUNT;
        }

        //Calc commission
        $perStudentCommission = $this->calcCommission($perStudentPrice, $tlData['lesson_type'], $tlData['duration_minutes']);

        $receivers = array();

        //Site
        $receivers[] = array(
            'email'         => Configure::read('paypal_site_username'),
            'amount'        => $perStudentPrice,
            'paymentType'   => 'DIGITALGOODS',
            'primary'       => true,
        );

        //Teacher
        $receivers[] = array(
            'email'         => $teacher['teacher_paypal_id'],
            'amount'        => ($perStudentPrice-$perStudentCommission),
            'paymentType'   => 'DIGITALGOODS',
            'primary'       => false,
        );



        return array('receivers'=>$receivers, 'perStudentPrice'=>$perStudentPrice, 'perStudentCommission'=>$perStudentCommission);
    }

    /**
     * Calc our commission
     * @param $perStudentPrice
     * @param $lessonType
     * @param $duration
     * @return mixed
     */
    private function calcCommission($perStudentPrice, $lessonType, $duration) {


        // 1. Max potential Paypal's commission
        $payPalMaxCommission = $this->calcPayPalMaxCommission($perStudentPrice);

        // 2. Teacher final payment
        $perStudentCommission = Configure::read('per_student_commission');
        $teacherPayment = $perStudentPrice - $perStudentCommission;

        // 3. Commission is paid by the teacher, therefore:
        if($teacherPayment>$payPalMaxCommission) {
            return $perStudentCommission;
        }

        // 4. We cannot take commission
        return 0;
    }

    private function calcPayPalMaxCommission($perStudentPrice) {
        return ($perStudentPrice * 0.05) + 0.30 + 0.30; // 5% + 0.30 cents + 0.30 cents
    }

    private function cancelDuplications($pendingUserLessonData, $status) {
        $cancelConditions = array();
        if($pendingUserLessonData['user_lesson_id']) {
            $cancelConditions['AdaptivePayment.user_lesson_id'] = $pendingUserLessonData['user_lesson_id'];
        } else if($pendingUserLessonData['teacher_lesson_id']) {
            $cancelConditions['AdaptivePayment.teacher_lesson_id'] = $pendingUserLessonData['teacher_lesson_id'];
        } else if($pendingUserLessonData['subject_id']) {
            $cancelConditions['AdaptivePayment.subject_id'] = $pendingUserLessonData['subject_id'];
        }

        if($cancelConditions) {
            $cancelConditions['status'] = $status;
            $cancelConditions['AdaptivePayment.student_user_id'] = $pendingUserLessonData['student_user_id'];
            $cancelCandidates = $this->find('all', array('conditions'=>$cancelConditions));

            if($cancelCandidates) {
                foreach($cancelCandidates AS $cancelCandidate) {
                    $this->cancelApproval(null, $cancelCandidate[$this->name]['preapproval_key']);
                }
            }
        }

        return true;
    }
}
?>