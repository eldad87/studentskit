<?php
/**
 *@property Subject $Subject
 *@property PayPalComponent $PayPal
 *@property PendingUserLesson $PendingUserLesson
 */
class OrderController extends AppController {
	public $name = 'Order';
	public $uses = array('User', 'Subject', 'TeacherLesson', 'UserLesson', 'PendingUserLesson', 'PendingUserLesson');
	public $components = array('Utils.FormPreserver'=>array('directPost'=>true,'actions'=>array('prerequisites'), 'priority' => 1),
                                'PayPal'
                                /*'Security'=>array(
                                    'csrfCheck'=>false,
                                    'requireSecure'=>true,
                                    'requirePost'=>array('prerequisites')
                                )*/);
	public $helpers = array('Layout');//'Form', 'Html', 'Js', 'Time'

    public function forceSSL() {
        $this->redirect('https://' . env('SERVER_NAME') . $this->here);
    }

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(	'index', 'init', 'calendar', 'setLessonDatetime', 'getLiveLessons', 'summary', 'paymentPreapprovalIpnNotificationUrl', 'paymentIpnNotificationUrl', 'paymentUpdateTest', 'getUpcomingOpenLessonForSubject');
		$this->Auth->deny( array('summary', 'prerequisites', 'status') );

        //$this->Security->csrfCheck = false;
        //$this->Security->blackHoleCallback = 'forceSSL';
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

                $extraParams = Security::rijndael(base64_decode($this->params->query['negotiate']), Configure::read('Security.key'), 'decrypt');
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

        //Set statistics data
        $statistics = $actionData['Subject'];
        $statistics['1_on_1_price'] = $this->getOrderData('price');
        $statistics['action'] = $this->getOrderData('action');
        $this->setStatisticsData($statistics);

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

        $datetime = mktime($datetime['hour'], $datetime['min'], 0, $datetime['month'], $datetime['day'], $datetime['year']);
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
                }

                //Calc how much CP the user need to buy
                $haveEnought = $this->UserLesson->haveEnoughTotalCreditPoints($this->Auth->user('user_id'), $viewParameters['price']);
                $viewParameters['price_actual_purchase'] = ($haveEnought===true ? 0 : $haveEnought);

                break;

            case 'join':
                if($actionData['TeacherLesson']['max_students']>1) {
                    $viewParameters['full_group_student_price'] = $actionData['TeacherLesson']['full_group_student_price'];
                }
                $viewParameters['num_of_students']              = $actionData['TeacherLesson']['num_of_students'];

                //Calc how much CP the user need to buy
                $haveEnought = $this->UserLesson->haveEnoughTotalCreditPoints($this->Auth->user('user_id'), $viewParameters['price']);
                $viewParameters['price_actual_purchase'] = ($haveEnought===true ? 0 : $haveEnought);
            break;

            case 'negotiate':
            case 'accept':
                if($actionData['UserLesson']['max_students']>1) {
                    $viewParameters['full_group_student_price'] = $actionData['UserLesson']['full_group_student_price'];
                }
                $viewParameters['num_of_students']              = $actionData['TeacherLesson']['num_of_students'];

                //Calc how much CP the user need to buy
                $haveEnought = $this->UserLesson->haveEnoughTotalCreditPoints($this->Auth->user('user_id'), $viewParameters['price'], $actionData['id']);
                $viewParameters['price_actual_purchase'] = ($haveEnought===true ? 0 : $haveEnought);
            break;
        }
        if($extra = $this->getOrderData('extra')) {
            $viewParameters = am($viewParameters, $extra);
        }

        //Set statistics data
        $statistics = $actionData['Subject'] + $viewParameters;
        $statistics['1_on_1_price'] = $viewParameters['price'];
        $statistics['action'] = $this->getOrderData('action');
        $this->setStatisticsData($statistics);


        $this->loadCommonData($actionData['Subject']['user_id'], $actionData['Subject']['subject_id']);
        $this->Session->write('order.viewedSummary', true);
        $this->set($viewParameters);
        $this->set('orderData', $this->getOrderData());
    }
    private function setStatisticsData($statistics) {
        $data = array(
            'action'                    => $statistics['action'],
            'subject_id'                => $statistics['subject_id'],
            'teacher_user_id'           => $statistics['user_id'],
            'category_id'       => $statistics['category_id'],
            'lesson_type'               => $statistics['lesson_type'],
            'language'                  => $statistics['language'],
            'name'                      => $statistics['name'],
            'duration_minutes'          => $statistics['duration_minutes'],
            '1_on_1_price'              => $statistics['1_on_1_price'],
            'max_students'              => $statistics['max_students'],
            'full_group_student_price'  => $statistics['full_group_student_price'],
            'total_lessons'             => $statistics['total_lessons'],
            'students_amount'           => $statistics['students_amount'],
            'raters_amount'             => $statistics['raters_amount'],
            'average_rating'            => $statistics['average_rating'],
            'created'                   => $statistics['created']
        );

        if(isSet($statistics['datetime'])) {
            $data['datetime'] = $statistics['datetime'];
        }

        if(isSet($statistics['price_actual_purchase'])) {
            $data['price_actual_purchase'] = $statistics['price_actual_purchase'];
        }

        $this->set('statistics', $data);
    }

    /**
     * Generate userLesson on-the-fly if needed
     */
    public function prerequisites() {
        //TODO: security - you cannot fore POST due to fore login (login redirect cannot be done using POST)
        //In addition, $this->referer() is build upon the client browser headers, therefore it can get manipulated, anyway - it good enough
        if(!$this->request->is('post')/* && Router::normalize($this->referer())!=Router::normalize(Router::url($this->Auth->loginAction, true))*/) {
            //Its not POST and the user was not redirected here right after login
            $this->Session->setFlash(__('Error.'));
            $this->redirect($this->getOrderData('redirect'));
        }

        //if(!$this->request->is('post') && !$this->referer())
        if(!$this->getOrderData('viewedSummary')) {
            $this->redirect($this->getOrderData('redirect'));
        }
        $orderData = $this->getOrderData();


        $this->checkIfCanOrder($this->getActionData(), true);


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

        } else if($orderData['action']=='negotiate' &&
                    $this->UserLesson->haveEnoughTotalCreditPoints(null, $orderData['price'], $orderData['id'])!==true ) {

            //Negotiation
            $success = $this->PendingUserLesson->reProposeRequest($orderData['id'], $this->Auth->user('user_id'), $orderData['extra']);
            $pendingUserLessonId = $this->PendingUserLesson->id;
            if(!$success) {
                $this->Session->setFlash(__('Cannot order lesson'));
                $this->redirect($this->getOrderData('redirect'));
            }

        } else if($orderData['action']=='accept' &&
                    $this->UserLesson->haveEnoughTotalCreditPoints(null, $orderData['price'], $orderData['id'])!==true ) {

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
            //$this->paymentPreapproval($pendingUserLessonId);
            $paymentGateway = 'paypalExpressCheckout'; //Can be change dynamically by the use choice
            $this->_gateway($pendingUserLessonId, $orderData['action'], $paymentGateway);
        }

        $this->redirect(array('controller'=>'Order', 'action'=>'status', $orderData['action'], $userLessonId));
    }

    /**
     * Determent which gateway should be used, if any
     *
     * @param $pendingUserLessonId
     * @param $action
     * @param string $gateway
     * @return bool
     */
    private function _gateway($pendingUserLessonId, $action, $gateway='paypalExpressCheckout') {

        //Check if user have enough funds + check if UL have any funds (in case of negotiation)
        //1. Get pendingUserLesson.1_on_1_price
        $this->PendingUserLesson->recursive = -1;
        $pulData = $this->PendingUserLesson->find('first', array('conditions'=>array('pending_user_lesson_id'=>$pendingUserLessonId)));
        if(!$pulData) {
            return false;
        }
        $pulData = $pulData['PendingUserLesson'];


        //Check if user have enought CP - if so, no need to ask him to pay more
        $haseEnought = $this->UserLesson->haveEnoughTotalCreditPoints(  $pulData['student_user_id'],
                                                                $pulData['1_on_1_price'],
                                                                $pulData['user_lesson_id']);
        if($haseEnought===true) {

            //execute, credit-points will be taken after PendingUserLesson will be execute
            $result = $this->PendingUserLesson->execute(
                $pendingUserLessonId
            );

            $this->clearSession();
            if($result) {
                $this->redirect(array('action'=>'status', $action, $pulData['user_lesson_id']));
            } else {
                $this->Session->setFlash(__('Error, please try again later.'));
                $this->redirect($this->getOrderData('redirect'));
            }
        }

        //Ask user to pay
        $this->{$gateway}($pendingUserLessonId, $action, max($haseEnought, 1));
    }


    /**
     * Paypal express checkout - redirect to paypal
     *
     * @param $pendingUserLessonId
     * @param $action
     * @param $paymentRequire
     */
    private function paypalExpressCheckout($pendingUserLessonId, $action, $paymentRequire) {

        $returnUrl = Router::url(array('controller'=>'Order', 'action'=>'expressPaidStatus', $action, $pendingUserLessonId), true);
        $cancelUrl = Router::url($this->getOrderData('redirect'), true);
        $url = $this->PayPal->setExpressCheckout($pendingUserLessonId, $paymentRequire, $returnUrl, $cancelUrl);

        $this->redirect($url);
    }

    /**
     * Landing page - when the user finish the payment process
     * @param $action
     * @param $pendingUserLessonId
     */
    public function expressPaidStatus($action, $pendingUserLessonId) {
        //Check if this use owns this PUL
        $this->PendingUserLesson->recursive = -1;
        $pulData = $this->PendingUserLesson->findByPendingUserLessonId($pendingUserLessonId);
        if(!$pulData || $pulData['PendingUserLesson']['student_user_id']!=$this->Auth->user('user_id')) {
            $this->Session->setFlash(__('Error.'));
            $this->redirect($this->getOrderData('redirect'));
        }


        $ipn = Router::url(array('controller'=>'Order', 'action'=>'expressPaidStatus', 'expressPaidIPN', $pendingUserLessonId), true);

        //Charge user
        $paymentStatus = $this->PayPal->DoExpressCheckout($pendingUserLessonId, $ipn);
        if(!$paymentStatus) {
            $this->Session->setFlash(__('We couldn\'t process your payment.'));
            $this->redirect($this->getOrderData('redirect'));
        }

        if($paymentStatus==PaypalComponent::PAYMENT_STATUS_COMPLETED) {
            $this->redirect(array('action'=>'status', $action, $pulData['PendingUserLesson']['user_lesson_id']));
        }

        //TODO::Handle PaymentStatus - show to user
    }

    public function expressPaidIPN($pendingUserLessonId) {
        echo $this->PayPal->DoExpressCheckoutIPN($pendingUserLessonId);
        die;
    }


    /**
     * Tells if the order was successful or not
     */
    public function status($action, $userLessonId) {

        $this->UserLesson->Subject; //const
        $this->UserLesson->recursive = 1;
        $this->UserLesson->resetRelationshipFields();
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
            $this->set('userLessonId', $userLessonId);
            $this->set('subjectId', $ulData['UserLesson']['subject_id']);
            $this->set('name', $ulData['UserLesson']['name']);
            $this->set('orderData', array('action'=>$action, 'price'=>$ulData['UserLesson']['1_on_1_price'], 'lesson_type'=>$ulData['UserLesson']['lesson_type']));


            //Set statistics data
            $statistics                     = $ulData['UserLesson'];
            $statistics['total_lessons']    = $ulData['Subject']['total_lessons'];
            $statistics['students_amount']  = $ulData['Subject']['students_amount'];
            $statistics['raters_amount']    = $ulData['Subject']['raters_amount'];
            $statistics['average_rating']   = $ulData['Subject']['average_rating'];
            $statistics['created']          = $ulData['Subject']['created'];


            $statistics['user_id']  = $statistics['teacher_user_id'];
            $statistics['action']   = $action;
            $this->setStatisticsData($statistics);


            //Set credit points
            $creditPoints = $this->User->getCreditPoints($this->Auth->user('user_id'));
            $userData = $this->Auth->user();
            $userData['credit_points'] = $creditPoints;
            $this->Auth->login($userData);
        }
    }

    private function getOrderData($parameter=null) {
        if($parameter) {
            $parameter = '.'.$parameter;
        }

        if($parameter=='redirect') {
            $redirect = $this->Session->read('order'.$parameter);
            if(!$redirect) {
                return '/';
            }
        }
        return $this->Session->read('order'.$parameter);
    }

    private function checkIfCanOrder($actionData, $a=false) {
        if(!$actionData) {
            $this->redirect($this->getOrderData('redirect'));
        }
        if(!$this->Auth->user('user_id')) {
            return false;
        }


        //Check if there are existing requests
        if($actionData['Subject']['lesson_type']==LESSON_TYPE_LIVE) {
            //Check if datetime is in the future

            if($this->getOrderData('datetime') && !$this->UserLesson->isFuture1HourDatetime($this->getOrderData('datetime'))) {
                $this->clearSession();
                $this->Session->setFlash(__('Please select a minimum +1 hour future date-time'));
                $this->redirect($this->getOrderData('redirect'));
            }

            //Join request
            if(isSet($actionData['TeacherLesson'])) {
                $liveRequestStatus = $this->UserLesson->getLiveLessonStatus($actionData['TeacherLesson']['teacher_lesson_id'], $this->Auth->user('user_id'));

                if($liveRequestStatus['approved']) {
                    $this->Session->setFlash(__('You already ordered that lesson'));
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

        $data = false;
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