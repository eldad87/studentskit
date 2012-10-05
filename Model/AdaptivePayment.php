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
    private $callerID = 'caller_1345633979_biz_api1.gmail.com'; //The API user
    private $siteOwnerId = 'web_1346436413_biz@gmail.com'; //user that will get commission
    private $paymentUrl = 'https://www.sandbox.paypal.com/webscr&cmd=_ap-preapproval&preapprovalkey='; //Payment Approval URL

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);

        App::import('Vendor', 'AdaptivePayments'.DS.'AdaptivePayments');
        $this->adaptivePayments = new AdaptivePayments( $this->callerID );
    }

    /**
     * @param $pendingUserLessonId
     * @param $cancelUrl
     * @param $returnUrl
     * @param null $clientIP
     * @return bool
     *
     * return string on success
     * return false on failure
     * return true if already approved/paid
     *
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
        $response = $this->adaptivePayments->preapproval( $pendingUserLessonData['1_on_1_price'], $pendingUserLessonData['student_user_id'], $clientIP, $cancelUrl, $returnUrl, $approvalValidThru, $ipnNotificationUrl );
        if(strtolower($response->responseEnvelope->ack)!='success') {
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

        return $this->paymentUrl.$response->preapprovalKey;
    }

    /**
     *
     * Charge all students of TeacherLessonId - only with 'approved' status.
     * @param $teacherLessonId
     * @return bool
     */
    public function pay( $teacherLessonId , $cancelUrl, $returnUrl) {
        $this->UserLesson->TeacherLesson->recursive = -1;
        $tlData = $this->UserLesson->TeacherLesson->findByTeacherLessonId($teacherLessonId);
        if(!$tlData || $tlData['TeacherLesson']['is_deleted'] || !$tlData['TeacherLesson']['1_on_1_price']) {
            return PAYMENT_STATUS_ERROR;
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

        //Calc how much each student need to pay
        $receivers = $this->generatePaymentRecivers($teacherLessonId);

        $successPayments = 0;
        foreach($aps AS $ap) {
            $ap = $ap['AdaptivePayment'];
            $response = $this->adaptivePayments->pay( $receivers, $ap['user_lesson_id'], $ap['preapproval_key'], $cancelUrl, $returnUrl );


            $status = PAYMENT_STATUS_DONE;
            if(strtolower($response->paymentExecStatus)=='completed') {
                $this->id = $ap['adaptive_payment_id'];
                $this->set('is_used', 1);
                $this->set('paid_amount', $receivers[0]['amount']);
                $this->save();
                $successPayments++;

            } else {
                $this->log(var_export($response, true), 'adaptive_payment_error');
                $this->setStatus($ap['user_lesson_id'], $ap['preapproval_key'], 'ERROR');
                $status = PAYMENT_STATUS_ERROR;
            }

            $event = new CakeEvent('Model.AdaptivePayment.AfterUserLessonPaid', $this, array('user_lesson_id'=>$ap['user_lesson_id'], 'teacher_lesson_id'=>$teacherLessonId, 'status'=>$status) );
            $this->getEventManager()->dispatch($event);
        }

        $paymentNeeded = count($aps);

        if($successPayments==$paymentNeeded) {
            return PAYMENT_STATUS_DONE; //All payments was successful
        } else if(!$successPayments) {
            return PAYMENT_STATUS_ERROR; //No successful payments
        } else {
            return PAYMENT_STATUS_PARTIAL; //Partical payments
        }

        //return true;
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
        $saveData = array('pending_user_lesson_id'=>$ipnData['pending_user_lesson_id'], 'is_approved'=>($ipnData['approved']=='true' ? 1 : 0), 'status'=>$ipnData['status'], 'max_amount'=>$ipnData['max_total_amount_of_all_payments'], 'paid_amount'=>$paid, 'is_used'=>( $paid ? 1 : 0 ));
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

    private function preapprovalDetails( $preapprovalKey ) {
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

    private function generatePaymentRecivers($teacherLessonId) {
        $this->UserLesson->TeacherLesson->resetRelationshipFields();
        $this->UserLesson->TeacherLesson->recursive = 1;
        $tlData = $this->UserLesson->TeacherLesson->findByTeacherLessonId($teacherLessonId);
        $teacher = $tlData['User'];
        $tlData = $tlData['TeacherLesson'];

        //If its a video lesson or only 1    student can be in this lesson, the user need to pay the full price
        if($tlData['lesson_type']=='video' || $tlData['max_students']==1 || $tlData['num_of_students']==1) {
            $price = $tlData['1_on_1_price'];
        } else {
            //$price = $this->UserLesson->Subject->calcStudentFullGroupPrice($tlData['1_on_1_price'], $tlData['full_group_total_price'], $tlData['max_students'], $tlData['num_of_students']);
            $price = $this->UserLesson->Subject->calcStudentPriceAfterDiscount( $tlData['1_on_1_price'], $tlData['max_students'], $tlData['num_of_students'], $tlData['full_group_student_price'] );
        }


        if(!$teacher['teacher_paypal_id']) {
            return false;
        }

        $receivers = array();
        $receivers[] = array(
            'email'         =>$teacher['teacher_paypal_id'],
            'amount'        =>$price,
            'paymentType'   =>'DIGITALGOODS',
            'primary'       =>true,
        );
        $receivers[] = array(
            'email'         =>$this->siteOwnerId,
            'amount'        =>($price<1 ? $price : 1),
            'paymentType'   =>'DIGITALGOODS',
            'primary'       =>false,
        );
        //TODO: dicanat


        return $receivers;
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