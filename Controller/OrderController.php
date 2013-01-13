<?php
/**
 *@property Subject $Subject
 */
class OrderController extends AppController {
	public $name = 'Order';
	public $uses = array('User', 'Subject', 'TeacherLesson', 'UserLesson', 'PendingUserLesson', 'AdaptivePayment', 'PendingAdaptivePayment');
	public $components = array(/*'Utils.FormPreserver'=>array('directPost'=>true,'actions'=>array('paymentPreapproval')), */'Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login'))/*, 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');


	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(	'index', 'init', 'calendar', 'setLessonDatetime', 'getLiveLessons', 'summary', 'paymentPreapprovalIpnNotificationUrl', 'paymentIpnNotificationUrl', 'paymentUpdateTest', 'getUpcomingOpenLessonForSubject');
		$this->Auth->deny( array('paymentPreapproval', 'status') );
        //$this->Security->requirePost('prerequisites');
	}
	
	public function index() {
        $this->redirect('/');
    }

    /**
     * Redirect to the relevant order step
     * Remove old session-order-parameters
     * @param $action teacherLesson|userLesson|subject
     * @param $id
     */
    public function init( $action, $id ) {
        $this->clearSession();
        $this->Session->write('order.redirect', $this->referer());

        //Check if record found in our DB
        $actionData = $this->getActionData($action, $id);
        if(!$actionData) {
            $this->redirect($this->getOrderData('redirect'));
        }

        $datetime = null;
        $extraParams = array();
        //$redirectAction = false;
        switch($action) {
            //TeacherLesson
            case 'join':
                $price = $actionData['TeacherLesson']['1_on_1_price'];
               if($actionData['TeacherLesson']['lesson_type']=='video') {
                   break;
               }

               //$redirectAction = 'summary';
               $datetime = $actionData['TeacherLesson']['datetime'];
            break;

            //UserLesson
            case 'accept':
                //$redirectAction = 'summary';
                $datetime = $actionData['UserLesson']['datetime'];
                $price = $actionData['UserLesson']['1_on_1_price'];
                break;

            case 'negotiate':
                if(!isSet($this->params->query['negotiate']) || $actionData['UserLesson']['student_user_id']!=$this->Auth->user('user_id')) {
                    //No negotiation parameters or its not the student
                    $this->redirect($this->getOrderData('redirect'));
                }

                $extraParams = Security::rijndael($this->params->query['negotiate'], Configure::read('Security.key'), 'decrypt');
                $extraParams = json_decode($extraParams, true);
                if(isSet($extraParams['1_on_1_price'])) {
                    $price = $extraParams['1_on_1_price'];
                }
                if(isSet($extraParams['datetime'])) {
                    $datetime = $extraParams['datetime'];
                }
            break;

            case 'order':
                if($actionData['Subject']['user_id']==$this->Auth->user('user_id')) {
                    //teachers can't order their own lessons
                    $this->redirect($this->getOrderData('redirect'));
                }
                $price = $actionData['Subject']['1_on_1_price'];
                break;

            default:
                    $this->redirect($this->getOrderData('redirect'));
                break;
            /*//Subject
            case 'order': //Regular order from the site
                if($actionData['Subject']['lesson_type']=='video') {
                    $redirectAction = 'summary';
                } else {
                    $redirectAction = 'calendar';
                }

            break;*/
        }

        $this->Session->write('order.extra', $extraParams);
        $this->Session->write('order.price', $price);
        $this->Session->write('order.lesson_type', $actionData['Subject']['lesson_type']);
        $this->Session->write('order.action', $action);
        $this->Session->write('order.id', $id);


        if($datetime) {
            $this->Session->write('order.datetime', $datetime);
        }

        $this->checkIfCanOrder($actionData);

        $this->redirect(array('controller'=>'Order', 'action'=>( ($datetime || $actionData['Subject']['lesson_type']=='video') ? 'summary' : 'calendar')));
    }

    private function loadCommonData($teacherUserId, $subjectId) {

        $this->User->recursive = -1;
        $teacherData = $this->User->findByUserId( $teacherUserId );
        if(!$teacherData) {
            return false;
        }


        $upcomingAvailableLessons = $this->TeacherLesson->getUpcomingOpenLessons(null, $subjectId, 3);
        $this->set('teacherData', $teacherData['User']);
        $this->set('upcomingAvailableLessons', $upcomingAvailableLessons);
        $this->set('upcomingAvailableLessonsLimit', 3);

        $this->setJSSetting('subject_id', $subjectId);

        return array('teacher'=>$teacherData, 'subject'=>$upcomingAvailableLessons);
    }

    /**
     * Shows the active meeting of the person the current user is trying to set a meeting with.
     */
    public function calendar() {
        $actionData = $this->getActionData();
        if(!$actionData || $actionData['Subject']['lesson_type']!='live') {
            $this->redirect($this->getOrderData('redirect'));
        }



        //get booking-auto-approve-settings
        App::import('Model', 'AutoApproveLessonRequest');
        $aalsObj = new AutoApproveLessonRequest();
        $aalr = $aalsObj->getSettings($actionData['Subject']['user_id']);


        $this->loadCommonData($actionData['Subject']['user_id'], $actionData['Subject']['subject_id']);

        $allLiveLessons = $this->User->getLiveLessons($actionData['Subject']['user_id'], false);
        $this->setJSSetting('calendarClickUrl', Router::url(array('controller'=>'Home', 'action'=>'teacherLesson', '{teacher_lesson_id}')));
        $this->setJSSetting('months', array(__('January'), __('February'), __('March'), __('April'), __('May'), __('June'), __('July'),
                                            __('August'), __('September'), __('October'), __('November'), __('December')));

        $this->set('allLiveLessons',	 	$allLiveLessons);
        $this->set('aalr', 					$aalr);
        $this->set('subjectData',     		$actionData['Subject']);
        $this->set('orderData',             $this->getOrderData());
    }

    public function setLessonDatetime() {
        if(!$this->request->is('post') || !isSet($this->data['UserLesson']['datetime'])) {
            $this->redirect($this->getOrderData('redirect'));
        }

        $datetime = $this->data['UserLesson']['datetime'];

        $datetime = mktime(($datetime['meridian']=='pm' ? $datetime['hour']+12 : $datetime['hour']), $datetime['min'], 0, $datetime['month'], $datetime['day'], $datetime['year']);
        $datetime = $this->UserLesson->timeExpression($datetime, false);
        $this->Session->write('order.datetime', $datetime);

        $this->redirect(array('controller'=>'Order', 'action'=>'summary'));
    }

    /*public function getLiveLessons($year=null, $month=null) {
        if(!$year) {
            $year = date('Y');
            $month = date('m');
        } else if(!$month) {
            $month = date('m');
        }

        $actionData = $this->getActionData();
        if(!$actionData) {
            $this->redirect($this->getOrderData('redirect'));
        }

        //Get student lessons for a given month
        $allLiveLessons = $this->User->getLiveLessonsByDate( $actionData['Subject']['user_id'], false, $year, $month);

        if ($this->RequestHandler->isAjax()) {
            return $this->success(1, array('results'=>$allLiveLessons));
        }
        return $allLiveLessons;
    }*/
    public function getUpcomingOpenLessonForSubject($subjectId, $limit=3, $page=1) {
        $upcomingAvailableLessons = $this->TeacherLesson->getUpcomingOpenLessons(null, $subjectId, $limit, $page);
        return $this->success(1, array('results'=>$upcomingAvailableLessons));
    }

    /**
     * Shows all the relevant details about the order
     */
    public function summary() {
        $actionData = $this->getActionData();
        if(!$actionData) {
            $this->redirect($this->getOrderData('redirect'));
        }

        $this->checkIfCanOrder($actionData);

        $viewParameters = array();
        $viewParameters['name']             = $actionData['Subject']['name'];
        $viewParameters['description']      = $actionData['Subject']['description'];
        $viewParameters['lesson_type']      = $actionData['Subject']['lesson_type'];
        $viewParameters['duration_minutes'] = $actionData['Subject']['duration_minutes'];
        $viewParameters['datetime']         = $this->getOrderData('datetime');
        $viewParameters['price']            = $this->getOrderData('price');
        if($actionData['Subject']['lesson_type']=='live') {
            $viewParameters['max_students']  = $actionData['Subject']['max_students'];
        }




        //Generate a list of summary parameters
        switch($this->getOrderData('action')) {
            case 'order':
                if($actionData['Subject']['max_students']>1) {
                    $viewParameters['full_group_student_price'] = $actionData['Subject']['full_group_student_price'];
                    $viewParameters['full_group_total_price']   = $actionData['Subject']['full_group_total_price'];
                }
            break;

            case 'join':
                if($actionData['TeacherLesson']['max_students']>1) {
                    $viewParameters['full_group_student_price'] = $actionData['TeacherLesson']['full_group_student_price'];
                    $viewParameters['full_group_total_price']   = $actionData['TeacherLesson']['full_group_total_price'];
                }
                $viewParameters['num_of_students']              = $actionData['TeacherLesson']['num_of_students'];
            break;

            case 'negotiate':
            case 'accept':
                if($actionData['UserLesson']['max_students']>1) {
                    $viewParameters['full_group_student_price'] = $actionData['UserLesson']['full_group_student_price'];
                    $viewParameters['full_group_total_price']   = $actionData['UserLesson']['full_group_total_price'];
                }
                $viewParameters['num_of_students']              = $actionData['TeacherLesson']['num_of_students'];
            break;
        }
        if($extra = $this->getOrderData('extra')) {
            $viewParameters = am($viewParameters, $extra);
        }


        $this->loadCommonData($actionData['Subject']['user_id'], $actionData['Subject']['subject_id']);

        $this->Session->write('order.viewedSummary', true);
        $this->set($viewParameters);
        $this->set('orderData',             $this->getOrderData());
    }

    /**
     * Generate userLesson on-the-fly if needed
     */
    public function prerequisites() {
        //TODO: security - you cannot fore POST due to fore login (login redirect cannot be done using POST)
        //In addition, $this->referer() is build upon the client browser headers, therefore it can get manipulated, anyway - it good enough
        if(!$this->request->is('post') && Router::normalize($this->referer())!=Router::normalize(Router::url($this->Auth->loginAction, true))) {
            //Its not POST and the user was not redirected here right after login
            $this->redirect($this->getOrderData('redirect'));
        }

        //if(!$this->request->is('post') && !$this->referer())
        if(!$this->getOrderData('viewedSummary')) {
            $this->redirect($this->getOrderData('redirect'));
        }
        $orderData = $this->getOrderData();

        $this->checkIfCanOrder($this->getActionData());

        $orderData['datetime'] = isSet($orderData['datetime']) ? $orderData['datetime'] : null;
        $this->Session->delete('order.viewedSummary');

        //Video cannot be public
        if($orderData['lesson_type']==LESSON_TYPE_VIDEO) {
            $this->request->data['is_public'] = SUBJECT_IS_PUBLIC_FALSE;
        }
        //TODO: make sure the user is not the teacher

        /**
         * Create PENDING-UserLesson if price>0 (not free).
         * The PENDING-UserLesson convert themselves into UserLesson when payment preapproval will arrive
         */
        $userLessonId = null;
        $pendingUserLessonId = null;

        if($orderData['action']=='join') {
            //Join
            if($orderData['price']>0) {
                $success = $this->PendingUserLesson->joinRequest( $orderData['id'], $this->Auth->user('user_id') );
                $pendingUserLessonId = $this->PendingUserLesson->id;
            } else {
                $success = $this->UserLesson->joinRequest( $orderData['id'], $this->Auth->user('user_id'));
                $userLessonId = $this->UserLesson->id;
            }

            if(!$success) {
                $this->Session->setFlash(__('Cannot join lesson'));
                $this->redirect($this->getOrderData('redirect'));
            }

        } else if($orderData['action']=='order') {
            //New order
            if($orderData['price']>0) {
                $success = $this->PendingUserLesson->lessonRequest($orderData['id'], $this->Auth->user('user_id'), $orderData['datetime'], false, array('is_public'=>$this->request->data['is_public']));
                $pendingUserLessonId = $this->PendingUserLesson->id;
            } else {

                $success = $this->UserLesson->lessonRequest($orderData['id'], $this->Auth->user('user_id'), $orderData['datetime'], false, array('is_public'=>$this->request->data['is_public']));
                $userLessonId = $this->UserLesson->id;
            }
            if(!$success) {
                $this->Session->setFlash(__('Cannot order lesson'));
                $this->redirect($this->getOrderData('redirect'));
            }

        } else if($orderData['action']=='negotiate' && !$this->AdaptivePayment->isValidApproval($orderData['id'], $orderData['price'], $orderData['datetime'])) {
            //Negotiation
            $success = $this->PendingUserLesson->reProposeRequest($orderData['id'], $this->Auth->user('user_id'), $orderData['extra']);
            $pendingUserLessonId = $this->PendingUserLesson->id;
            if(!$success) {
                $this->Session->setFlash(__('Cannot order lesson'));
                $this->redirect($this->getOrderData('redirect'));
            }

        } else if($orderData['action']=='accept' && !$this->AdaptivePayment->isValidApproval($orderData['id'], $orderData['price'], $orderData['datetime'])) {
            //Accept offer
            $success = $this->PendingUserLesson->acceptRequest($orderData['id'], $this->Auth->user('user_id'));
            $pendingUserLessonId = $this->PendingUserLesson->id;
            if(!$success) {
                $this->Session->setFlash(__('Cannot order lesson'));
                $this->redirect($this->getOrderData('redirect'));
            }

        } else {
            $this->clearSession();
            $this->Session->setFlash(__('Error'));
            $this->redirect($this->getOrderData('redirect'));
        }


        if($pendingUserLessonId) {
            //Lesson that cost money need to go through PayPal
            $this->paymentPreapproval($pendingUserLessonId);
        }

        $this->redirect(array('controller'=>'Order', 'action'=>'status', $orderData['action'], $userLessonId));
    }

    /**
     * Redirect users to the preapproval-paypal page
     */
    private function paymentPreapproval($pendingUserLessonId) {
        $action = $this->getOrderData('action');

        //Get UserLesson/PendingUserLesson data
        $pendingUserLessonData = $this->PendingUserLesson->findByPendingUserLessonId($pendingUserLessonId);
        if(!$pendingUserLessonData) {
            $this->redirect($this->getOrderData('redirect'));
        }
        $pendingUserLessonData = $pendingUserLessonData['PendingUserLesson'];

        //Make sure this student made the request
        if($pendingUserLessonData['student_user_id']!=$this->Auth->user('user_id') ) {
            $this->redirect($this->getOrderData('redirect'));
        }

        //http://80.230.10.163/Order/paymentPreapprovalIpnNotificationUrl/order/15
        $ipnNotificationUrl = Configure::read('public_domain').Router::url(array('controller'=>'Order', 'action'=>'paymentPreapprovalIpnNotificationUrl', $action, $pendingUserLessonId), (Configure::read('public_domain') ? false : true) );
        $returnUrl = Router::url(array('controller'=>'Order', 'action'=>'paidStatus', $action, $pendingUserLessonId), true);

        $url = $this->AdaptivePayment->getPreApprovalURL($pendingUserLessonId, $action, $returnUrl, $returnUrl, $this->request->clientIp(), $ipnNotificationUrl );
        $this->redirect($url);
    }

    /**
     * Tells if the order was successful or not
     */
    public function status($action, $userLessonId) {

        $this->UserLesson->Subject; //const
        $this->UserLesson->recursive = -1;
        $ulData = $this->UserLesson->findByUserLessonId($userLessonId);
        if(!$ulData) {
            $this->set('name', __('Error'));

            //Confirm that the viewer is the student
        } else if($ulData['UserLesson']['student_user_id']!=$this->Auth->user('user_id')) {
            $this->set('name', __('Error'));
        } else {
            $this->loadCommonData($ulData['UserLesson']['teacher_user_id'], $ulData['UserLesson']['subject_id']);


            //Check UserLesson stage
            $stage = $this->UserLesson->checkStage($ulData['UserLesson']);

            $this->set($stage);
            $this->set('subjectId', $ulData['UserLesson']['subject_id']);
            $this->set('name', $ulData['UserLesson']['name']);
            $this->set('orderData', array('action'=>$action, 'price'=>$ulData['UserLesson']['1_on_1_price'], 'lesson_type'=>$ulData['UserLesson']['lesson_type']));
        }
    }

    public function paidStatus($action, $pendingUserLessonId) {


        if(!$this->AdaptivePayment->updateUsingPreapprovalDetails($pendingUserLessonId, $action)) {
            $this->redirect($this->getOrderData('redirect'));
        }
        $status = $this->AdaptivePayment->getStatus($pendingUserLessonId);

        $this->redirect(array('action'=>'status', $action, $status['user_lesson_id']));
    }

    public function pay() {

    }

    /**
     * The user canceled the preapproval request
     */
    /*public function cancel($action, $userLessonId) {
        pr($this->UserLesson->findByUserLessonIdAndStudentUserId($userLessonId, $this->Auth->user('user_id')));
        $this->clearSession();

    }*/

    public function paymentPreapprovalIpnNotificationUrl($action, $pendingUserLessonId) {
        $data = $this->data;
        $data['action'] = $action;
        $data['pending_user_lesson_id'] = $pendingUserLessonId;

        $this->log(var_export($data, true), 'paypal_log');

        //Validate the request
        if($this->isValidIPN()) {
            $this->AdaptivePayment->paymentUpdate($data);
        } else {
            $this->log(var_export($data, true), 'paypal_hack');
        }

        echo 1; die;
    }

    /*public function testIPN() {
        $data = array (
            'max_number_of_payments' => 'null',
            'starting_date' => '2012-09-27T00:00:15.000Z',
            'pin_type' => 'NOT_REQUIRED',
            'currency_code' => 'USD',
            'sender_email' => 'buyer2_1347221285_per@gmail.com',
            'verify_sign' => 'AJ80yD.Z43pQ3jYcyXt6oA-ZB0gEAQeE.vVMpmpO7Juu0vN2lE6yxhWl',
            'test_ipn' => '1',
            'date_of_month' => '0',
            'current_number_of_payments' => '0',
            'preapproval_key' => 'PA-5V621152JG1461356',
            'ending_date' => '2013-09-27T23:59:15.000Z',
            'approved' => 'true',
            'transaction_type' => 'Adaptive Payment PREAPPROVAL',
            'day_of_week' => 'NO_DAY_SPECIFIED',
            'status' => 'ACTIVE',
            'current_total_amount_of_all_payments' => '0.00',
            'current_period_attempts' => '0',
            'charset' => 'windows-1252',
            'payment_period' => '0',
            'notify_version' => 'UNVERSIONED',
            'max_total_amount_of_all_payments' => '2.00',
            'action' => 'join',
            'pending_user_lesson_id' => '31',
        );
        /*$data['action'] = 'order';
        $data['pending_user_lesson_id'] = 51;* /
//echo 1; die;
        $this->AdaptivePayment->paymentUpdate($data);
    }*/

    private function isValidIPN() {
        App::import('Vendor', 'PHP-PayPal-IPN'.DS.'ipnlistener');
        $ipnListenerObj = new IpnListener();
        $ipnListenerObj->use_sandbox = true;
        $ipnListenerObj->force_ssl_v3 = true;

        try {
            $ipnListenerObj->requirePostMethod();
            $verified = $ipnListenerObj->processIpn();
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'paypal_hack');
            return false;
        }

        if(!$verified) {
            return false;
        }

        return true;
    }

    private function getOrderData($parameter=null) {
        if($parameter) {
            $parameter = '.'.$parameter;
        }
        return $this->Session->read('order'.$parameter);
    }

    private function checkIfCanOrder($actionData) {

        if(!$this->Auth->user('user_id')) {
            return false;
        }

        //Check if there are existing requests
        if($actionData['Subject']['lesson_type']=='live') {
            //Check if datetime is in the future
            if(!$this->UserLesson->isFutureDatetime($this->getOrderData('datetime'))) {
                $this->clearSession();
                $this->Session->setFlash(__('Datetime error'));
                $this->redirect($this->getOrderData('redirect'));
            }

            //Join request
            if(isSet($actionData['TeacherLesson'])) {
                $liveRequestStatus = $this->UserLesson->getLiveLessonStatus($actionData['TeacherLesson']['teacher_lesson_id'], $this->Auth->user('user_id'));

                if($liveRequestStatus['approved']) {
                    $this->Session->setFlash(__('You already ordered that lesson lesson'));
                    $this->redirect($this->getOrderData('redirect'));

                }
                //No need to check those - after accept - all pending request will get canceled
                /*else if($liveRequestStatus['pending_user_approval']) {
                    $this->Session->setFlash(__('The teacher already invited you, you can approve, decline or negotiate your participation in the control panel'));
                    $this->redirect($this->getOrderData('redirect'));

                } else if($liveRequestStatus['pending_teacher_approval']) {
                    $this->Session->setFlash(__('You already ordered that lesson and its waiting for the teacher\'s approval'));
                    $this->redirect($this->getOrderData('redirect'));
                }*/
            }


        } else if($actionData['Subject']['lesson_type']=='video') {
            $canWatchData = $this->UserLesson->getVideoLessonStatus($actionData['Subject']['subject_id'], $this->Auth->user('user_id'), false);

            if($canWatchData['approved']) {
                //User shouldn't pay for a lesson that he did not watched yet/watch time didn't over
                if(empty($canWatchData['datetime']) || $this->UserLesson->isFutureDatetime($canWatchData['end_datetime'])) {


                    $this->Session->setFlash(__('You already ordered that video lesson'));
                    $this->redirect($this->getOrderData('redirect'));

                } else if(/*$actionData['Subject']['1_on_1_price']*/$this->getOrderData('price')>0) {
                    //show indication to user that this will remove ads for 2 days
                    $this->Session->setFlash( sprintf(__('You already ordered that video lesson, by continue ordering - it will remove the advertisements for %d days'), (LESSON_TYPE_VIDEO_NO_ADS_TIME_SEC/DAY)) );
                } else {
                    $this->Session->setFlash(__('You already ordered that free video lesson')); //user doesn't need to order free lesson again.
                    $this->redirect($this->getOrderData('redirect'));
                }

            }
            //No need to check those - after accept - all pending request will get canceled
            /*else if($canWatchData['pending_user_approval']) {
                $this->Session->setFlash(__('The teacher in the video already invited you, you can approve, decline or negotiate your participation in the control panel'));
                $this->redirect($this->getOrderData('redirect'));

            } else if($canWatchData['pending_teacher_approval']) {
                $this->Session->setFlash(__('You already ordered that video lesson and its waiting for the teacher\'s approval'));
                $this->redirect($this->getOrderData('redirect'));
            }*/
        }
    }

    private function clearSession() {
        $r = $this->Session->read('order.redirect');
        $this->Session->delete('order');
        $this->Session->write('order.redirect', $r);
    }



    private function getActionData($action=null, $id=null) {
        if(!$action && !$id) {
            $id = $this->getOrderData('id');
            $action = $this->getOrderData('action');
        }

        switch($action) {
            //TeacherLesson
            case 'join':
                $this->TeacherLesson->resetRelationshipFields();
                $data = $this->TeacherLesson->findByTeacherLessonId($id);
                if(!$data ||
                    $data['TeacherLesson']['lesson_type']=='video' || $data['TeacherLesson']['is_deleted'] ||
                    $data['Subject']['is_enable']==SUBJECT_IS_ENABLE_FALSE) {

                    return false;
                }


                break;

            //UserLesson
            case 'accept':
            case 'negotiate':
                $this->UserLesson->resetRelationshipFields();
                $data = $this->UserLesson->findByUserLessonId($id);
                if(!$data ||
                    $data['UserLesson']['lesson_type']=='video' || $data['TeacherLesson']['is_deleted'] ||
                    $data['Subject']['is_enable']==SUBJECT_IS_ENABLE_FALSE) {

                    return false;
                }

                break;

            //Subject
            case 'order': //Regular order from the site
                $this->Subject->recursive = -1;
                $this->Subject->resetRelationshipFields();
                $data = $this->Subject->findBySubjectId($id);
                if(!$data || $data['Subject']['is_enable']==SUBJECT_IS_ENABLE_FALSE) {

                    return false;
                }
                break;
        }

        return $data;
    }
}