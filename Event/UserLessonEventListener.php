<?php
App::uses('CakeEventManager', 'Event');
/**
 *@property Notification $notification
 *@property UserLseson $UserLseson
 */
class UserLessonEventListener implements CakeEventListener {
    private $notification;
    private $ExpressCheckout;
    private $UserLseson;
    private $disableNotificationsForEvents = array();
    private static $m_pInstance;

    private function __construct() {
    //public function UserLessonEventListener() {
        App::import('Model', 'Notification');
        App::import('Model', 'ExpressCheckout');
        App::import('Model', 'UserLesson');

        $this->notification = New Notification();
        $this->ExpressCheckout = new ExpressCheckout();
        App::import('Model', 'UserLesson');
        $this->UserLseson = new UserLesson();
    }

    public static function &getInstance() {
        if (!self::$m_pInstance) {
            self::$m_pInstance = new self();
        }

        return self::$m_pInstance;
    }


    public function implementedEvents() {
        return array(
            'Model.UserLesson.afterLessonRequest'       => 'afterLessonRequest',
            'Model.UserLesson.afterJoinRequest'         => 'afterJoinRequest',
            'Model.UserLesson.afterReProposeRequest'    => 'afterReProposeRequest',
            'Model.UserLesson.afterCancelRequest'       => 'afterCancelRequest',    //Cancel payment preapproval
            'Model.UserLesson.afterAccept'              => 'afterAccept',
            'Model.UserLesson.afterRate'                => 'afterRate',

            'Model.PayPal.afterPaymentUpdate'           => 'afterExpressPaymentUpdate',    //Update express payment status

            'Model.UserLesson.beforeAccept'             => 'beforeAccept',          //Make sure preapproval amount is OK
            'Model.UserLesson.beforeLessonRequest'      => 'beforeLessonRequest',   //Cannot use on paid lessons
            'Model.UserLesson.beforeJoinRequest'        => 'beforeJoinRequest',     //Cannot use on paid lessons
            'Model.UserLesson.beforeReProposeRequest'   => 'beforeReProposeRequest',//Make sure preapproval amount is OK
        );
    }

    public function afterPaid(CakeEvent $event) {
        App::import('Model', 'UserLesson');
        $ulObj = new UserLesson();
        $ulObj->id = $event->data['user_lesson_id'];
        $ulObj->set(array('payment_status'=>$event->data['status']));
        return $ulObj->save();
    }

    public function afterExpressPaymentUpdate(CakeEvent $event) {

        App::import('Model', 'PendingUserLesson');
        $pulObj = new PendingUserLesson();

        switch($event->data['current']['payment_status']) {
            case PaypalComponent::PAYMENT_STATUS_COMPLETED:

                //This should apply on the first attempt only
                if($event->data['old']['payment_status']==PaypalComponent::PAYMENT_STATUS_COMPLETED) {
                    break;
                }

                //Transfer funds to user account
                App::import('Model', 'User');
                $userObj = new User();
                $userObj->setCreditPoints($event->data['old']['student_user_id'], $event->data['current']['gross_amount']);


                //Add billing history
                App::import('Model', 'BillingHistory');
                $billingHistoryModel = new BillingHistory();
                $billingHistoryModel->addHistory(
                    $event->data['current']['gross_amount'],
                    $event->data['old']['student_user_id'],
                    'student.add',
                    null,
                    null,
                    array(
                        'creditPoints'  => $event->data['old']['amount']
                    )
                );


                //If PendingUserLesson - execute
                $pulObj->execute(
                    $event->data['old']['pending_user_lesson_id']
                );
                break;

            /**
             * IPN ONLY operations
             */
            case PaypalComponent::PAYMENT_STATUS_COMPLETED_FUNDS_HELD:
            case PaypalComponent::PAYMENT_STATUS_IN_PROGRESS:
            case PaypalComponent::PAYMENT_STATUS_PENDING:
                //Cancel PendingUserLesson - SO when the payment will finally be accepted - this won't get executed and funds will stay in the user account
                $pulObj->cancel(
                    $event->data['old']['pending_user_lesson_id']
                );
                break;

            case PaypalComponent::PAYMENT_STATUS_PARTIALLY_REFUNDED:
            case PaypalComponent::PAYMENT_STATUS_REFUNDED:
            case PaypalComponent::PAYMENT_STATUS_REVERSED:

                //Transfer funds to user account
                App::import('Model', 'User');
                $userObj = new User();
                $userObj->setCreditPoints($event->data['old']['student_user_id'], $event->data['current']['gross_amount']);


                //Add billing history
                App::import('Model', 'BillingHistory');
                $billingHistoryModel = new BillingHistory();
                $billingHistoryModel->addHistory(
                    $event->data['current']['gross_amount'],
                    $event->data['old']['student_user_id'],
                    'student.reduce',
                    null,
                    null,
                    array(
                        'creditPoints'  => $event->data['old']['amount']
                    )
                );


                //If PendingUserLesson - execute
                $pulObj->cancel(
                    $event->data['old']['pending_user_lesson_id']
                );

                break;

            default:
                    //TODO: log
                break;
        }
    }

    public function beforeLessonRequest(CakeEvent $event) {

        //Make sure it was done by the student
        if($event->data['user_lesson']['student_user_id']==$event->data['by_user_id'] && $event->data['user_lesson']['price']>0) {

            if($event->subject()->haveEnoughTotalCreditPoints(  $event->data['by_user_id'],
                                                                $event->data['user_lesson']['price'],
                                                                $event->data['user_lesson']['user_lesson_id'])!==true) {

                return false;
            }
        }

        return true;
    }
    public function beforeJoinRequest(CakeEvent $event) {
        //$event->data = array('teacher_lesson'=>$teacherLessonData, 'user_lesson'=>$userLesson, 'by_user_id'=>( $teacherUserId ? $teacherUserId : $studentUserId))

        //Make sure it was done by the student
        if($event->data['user_lesson']['student_user_id']==$event->data['by_user_id'] && $event->data['user_lesson']['price']>0) {

            //Check if enough CP
            if($event->subject()->haveEnoughTotalCreditPoints(  $event->data['by_user_id'],
                                                                $event->data['user_lesson']['price'],
                                                                $event->data['user_lesson']['user_lesson_id'])!==true) {

                return false;
            }
        }

        return true;
    }

    public function beforeReProposeRequest(CakeEvent $event) {
        //$event->data = array('user_lesson'=>$userLessonData, 'update'=>$data, 'by_user_id'=>$byUserId)

        //Make sure it was done by the student
        if($event->data['user_lesson']['student_user_id']==$event->data['by_user_id']) {

            //Check if enough CP
            if($event->subject()->haveEnoughTotalCreditPoints(  $event->data['by_user_id'],
                                                                $event->data['user_lesson']['price'],
                                                                $event->data['user_lesson']['user_lesson_id'])!==true) {

                return false;
            }

        }
        return true;
    }


    public function afterCancelRequest(CakeEvent $event) {
        $toUserId = $messageType = null;
        $byUserId = $event->data['by_user_id'];

        //Remove all assigned credit points
        $this->UserLseson->setTotalCreditPoints($event->data['user_lesson']['user_lesson_id'], 0);


        //Delete all PendingUserLessons
        App::import('Model', 'PendingUserLesson');
        $pulObj = new PendingUserLesson();
        $pulObj->deleteAll(array('PendingUserLesson.user_lesson_id'=>$event->data['user_lesson']['user_lesson_id']));


        //If the student that initiated the TeacherLesson canceled his participation, cancel all other invitations/booking requests
        /*if($event->data['user_lesson']['teacher_lesson_id']) {
            App::import('Model', 'TeacherLesson');
            $teacherLessonObj = new TeacherLesson();


            $teacherLessonData = $teacherLessonObj->findByTeacherLessonId($event->data['user_lesson']['teacher_lesson_id']);
            if($teacherLessonData['TeacherLesson']['student_user_id']==$byUserId) {
                //1. check if this is the only student - with no invitation/booking requests - then cancel the lesson
                $userLessonsData = $event->subject()->find('all', array('conditions'=>array('UserLesson.teacher_lesson_id'=>$event->data['user_lesson']['teacher_lesson_id'],
                    'UserLesson.stage'=>array( USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER, USER_LESSON_RESCHEDULED_BY_STUDENT, USER_LESSON_ACCEPTED))));
                if(!$userLessonsData) {
                    //2. if not only student, continue
                    if(!$teacherLessonObj->cancel($event->data['user_lesson']['teacher_lesson_id'], 'student', $byUserId)) {
                        return false;
                    }
                }

                return true;
            }
        }*/

        if(!$this->getNotificationStatus('Model.UserLesson.afterCancelRequest')) {
            //No notifications needed
            return true;
        }

        if($event->data['user_lesson']['teacher_user_id']==$byUserId) {
            $toUserId = $event->data['user_lesson']['student_user_id'];

            if(empty($event->data['user_lesson']['wish_list_id'])) {
                //Made on subject offer
                switch($event->data['user_lesson']['stage']) {

                    case USER_LESSON_PENDING_STUDENT_APPROVAL:
                        //Teacher rescheduled his first invitation
                        $messageType = 'teacher.invitation.canceled';
                        break;
                    case USER_LESSON_RESCHEDULED_BY_STUDENT:
                        $messageType = 'teacher.booking.request.decline';
                        break;
                    case USER_LESSON_RESCHEDULED_BY_TEACHER:
                        //Teacher rescheduled again
                        $messageType = 'teacher.invitation.canceled';
                        break;
                    case USER_LESSON_PENDING_TEACHER_APPROVAL:
                        $messageType = 'teacher.booking.request.decline';
                        break;
                    case USER_LESSON_ACCEPTED:
                        $messageType = 'teacher.lesson.canceled';
                        break;
                }
            } else {
                //Made on subject request
                switch($event->data['user_lesson']['stage']) {
                    case USER_LESSON_RESCHEDULED_BY_TEACHER:
                        //Teacher rescheduled again
                        $messageType = 'teacher.subject.request.offer.canceled';
                        break;
                    case USER_LESSON_PENDING_STUDENT_APPROVAL:
                        //Teacher rescheduled his first invitation
                        $messageType = 'teacher.subject.request.offer.canceled';
                        break;

                    case USER_LESSON_PENDING_TEACHER_APPROVAL:
                        die; //User cannot invite teachers
                        break;
                    case USER_LESSON_RESCHEDULED_BY_STUDENT:
                        $messageType = 'teacher.subject.request.offer.canceledstudent';
                        break;
                    case USER_LESSON_ACCEPTED:
                        $messageType = 'teacher.subject.request.lesson.canceled';
                        break;

                }
            }
        } else {
            $toUserId = $event->data['user_lesson']['teacher_user_id'];
            if(empty($event->data['user_lesson']['wish_list_id'])) {
                //Made on subject offer
                switch($event->data['user_lesson']['stage']) {
                    case USER_LESSON_RESCHEDULED_BY_STUDENT:
                        //Student rescheduled again
                        $messageType = 'student.booking.request.canceled';
                        break;
                    case USER_LESSON_PENDING_TEACHER_APPROVAL:
                        //Student rescheduled his first invitation
                        $messageType = 'student.booking.request.canceled';
                        break;

                    case USER_LESSON_RESCHEDULED_BY_TEACHER:
                        $messageType = 'student.invitation.decline';
                        break;
                    case USER_LESSON_PENDING_STUDENT_APPROVAL:
                        $messageType = 'student.invitation.decline';
                        break;
                    case USER_LESSON_ACCEPTED:
                        $messageType = 'student.lesson.canceled';
                        break;
                }
            } else {
                //Made on subject request
                switch($event->data['user_lesson']['stage']) {
                    case USER_LESSON_RESCHEDULED_BY_STUDENT:
                        $messageType = 'student.subject.request.offer.decline';
                        break;
                    case USER_LESSON_PENDING_TEACHER_APPROVAL:
                        die; //User cannot invite teachers
                        break;
                    case USER_LESSON_RESCHEDULED_BY_TEACHER:
                        //Teacher rescheduled again
                        $messageType = 'student.subject.request.offer.decline.teacher';
                        break;
                    case USER_LESSON_PENDING_STUDENT_APPROVAL:
                        //Teacher rescheduled his first invitation
                        $messageType = 'student.subject.request.offer.decline.teacher';
                        break;
                    case USER_LESSON_ACCEPTED:
                        $messageType = 'student.subject.request.lesson.canceled';
                        break;
                }
            }
        }

        return $this->notification->addNotification(    $toUserId, $byUserId, //To user id
                                                        array( 'message_enum'=>$messageType, 'params'=>$event->data['user_lesson']) );//Message
    }

    public function afterRate(CakeEvent $event) {
        $toUserId = $messageType = null;
        $byUserId = $event->data['by_user_id'];

        if($event->data['user_lesson']['teacher_user_id']==$byUserId) {
            $toUserId = $event->data['user_lesson']['student_user_id'];
            $messageType = 'teacher.rate.student';
        } else {
            $toUserId = $event->data['user_lesson']['teacher_user_id'];
            $messageType = 'student.rate.teacher';
        }

        return $this->notification->addNotification(    $toUserId, $byUserId, //To user id
            array( 'message_enum'=>$messageType, 'params'=>$event->data['user_lesson']) );//Message
    }

    public function beforeAccept(CakeEvent $event) {
        //$event->data = array('user_lesson'=>$userLessonData, 'by_user_id'=>$byUserId);
        //Make sure it was done by the student
        if($event->data['user_lesson']['student_user_id']==$event->data['by_user_id']) {

            if($event->subject()->haveEnoughTotalCreditPoints(  $event->data['by_user_id'],
                                                                $event->data['user_lesson']['price'],
                                                                $event->data['user_lesson']['user_lesson_id'])!==true) {

                return false;
            }
        }

        App::import('Model', 'TeacherLesson');
        $tlObj = new TeacherLesson();

        if(empty($event->data['user_lesson']['teacher_lesson_id'])) {

            //Create a lesson + set student_user_id
            if(!$tlObj->add(array('type'=>'user_lesson','id'=>$event->data['user_lesson']['user_lesson_id']), null, null, array('teacher_user_id'=>$event->data['user_lesson']['teacher_user_id'],
                                    'student_user_id'=>$event->data['user_lesson']['student_user_id'],
                                    'num_of_students'=>$tlObj->getDataSource()->expression('num_of_students+1')))) {

                return false;
            }

            $event->result['teacher_lesson_id'] = $tlObj->id;
        } else {
            $counter = $event->subject()->getAcceptLessonCounter($event->data['user_lesson']['stage']);
            //Update the num_of_pending_invitations counter
            $tlObj->id = $event->data['user_lesson']['teacher_lesson_id'];
            $tlObj->set(array($counter=>$tlObj->getDataSource()->expression($counter.'-1'), 'num_of_students'=>$tlObj->getDataSource()->expression('num_of_students+1')));
            if(!$tlObj->save()) {
                return false;
            }

            return true;
        }
    }
    public function afterAccept(CakeEvent $event) {
        /*
         * Transfer funds - Make sure that all funds been transferred
         * If its an invitation that sent by teacher, and accepted by student - no CP should be allocated yet
         */
        if($event->data['user_lesson']['student_user_id']==$event->data['by_user_id']) {
            $event->subject()->setTotalCreditPoints(
                $event->data['user_lesson']['user_lesson_id'],
                $event->data['user_lesson']['price']
            );

            //Add billing history
            App::import('Model', 'BillingHistory');
            $billingHistoryModel = new BillingHistory();
            $billingHistoryModel->addHistory(
                $event->data['old']['amount'],
                $event->data['old']['student_user_id'],
                'student.add',
                null,
                null,
                array(
                    'creditPoints'  => $event->data['old']['amount']
                )
            );
        }

        //Add FS
        if(!$event->data['user_lesson']['root_file_system_id']) {

            //Check if we already have root for this subject, perhaps from a different UL that was accepted
            $event->subject()->recursive = -1;
            $ulData = $event->subject()->find('first', array('conditions'=>array('subject_id'=>$event->data['user_lesson']['subject_id'],
                                                                            $event->subject()->getDataSource()->expression('root_file_system_id IS NOT NULL')
                 )));


            $update = array();
            if($ulData) {
                $update['root_file_system_id'] = $ulData['UserLesson']['root_file_system_id'];


            //Create a new user folder
            } else {
                ///Load student data
                $event->subject()->Student->recursive = -1;
                $studentData = $event->subject()->Student->find('first', array('conditions'=>array('user_id'=>$event->data['user_lesson']['student_user_id'])));

                //Load subject data
                $event->subject()->Subject->recursve = -1;
                $subjectData = $event->subject()->Subject->findBySubjectId($event->data['user_lesson']['subject_id']);
                $subjectData = $subjectData['Subject'];

                //Create User folder
                App::import('Model', 'FileSystem');
                $fsObj = new FileSystem();
                $fsObj->createFS('subject', $subjectData['subject_id'], $event->data['user_lesson']['student_user_id'],
                                    $subjectData['user_upload_root_file_system_id'], $studentData['Student']['username']);

                $update['root_file_system_id'] = $fsObj->id;
            }

            //Update UL with new upload folder
            $event->subject()->create(false);
            $event->subject()->id = $event->data['user_lesson']['user_lesson_id'];
            $event->subject()->set($update);
            if(!$event->subject()->save()) {
                return false;
            }
        }


        //Check if notifications are needed
        if(!$this->getNotificationStatus('Model.UserLesson.afterAccept')) {
            //No notifications needed
            return true;
        }

        $toUserId = $messageType = null;
        $byUserId = $event->data['by_user_id'];


        if($event->data['user_lesson']['teacher_user_id']==$byUserId) {
            $toUserId = $event->data['user_lesson']['student_user_id'];
            if(empty($event->data['user_lesson']['wish_list_id'])) {
                //Made on subject offer
                switch($event->data['user_lesson']['stage']) {
                    case USER_LESSON_PENDING_TEACHER_APPROVAL:
                        $messageType = 'teacher.booking.request.accepted';
                        break;
                    case USER_LESSON_RESCHEDULED_BY_STUDENT:
                        $messageType = 'teacher.booking.request.accepted';
                        break;
                    case USER_LESSON_RESCHEDULED_BY_TEACHER:
                        //Teacher rescheduled again
                        die; //Teacher cannot approve his offer
                        break;
                    case USER_LESSON_PENDING_STUDENT_APPROVAL:
                        //Teacher rescheduled his first invitation
                        die; //Teacher cannot approve his offer
                        break;
                }
            } else {
                //Made on subject request
                switch($event->data['user_lesson']['stage']) {
                    case USER_LESSON_RESCHEDULED_BY_STUDENT:
                        $messageType = 'teacher.subject.request.offer.accept.student';
                        break;
                    case USER_LESSON_PENDING_TEACHER_APPROVAL:
                        die; //User cannot invite teachers
                        break;
                    case USER_LESSON_RESCHEDULED_BY_TEACHER:
                        //Teacher rescheduled again
                        die; //Teacher cannot approve his offer
                        break;
                    case USER_LESSON_PENDING_STUDENT_APPROVAL:
                        //Teacher rescheduled his first invitation
                        die; //Teacher cannot approve his offer
                        break;
                }
            }
        } else {
            $toUserId = $event->data['user_lesson']['teacher_user_id'];

            if(empty($event->data['user_lesson']['wish_list_id'])) {
                //Made on subject offer
                switch($event->data['user_lesson']['stage']) {
                    case USER_LESSON_RESCHEDULED_BY_TEACHER:
                        $messageType = 'student.invitation.accepted';
                        break;
                    case USER_LESSON_PENDING_STUDENT_APPROVAL:
                        $messageType = 'student.invitation.accepted';
                        break;

                    case USER_LESSON_RESCHEDULED_BY_STUDENT:
                        die; //Student cannot approve his offer
                        break;
                    case USER_LESSON_PENDING_TEACHER_APPROVAL:
                        die; //Student cannot approve his offer
                        break;
                }
            } else {
                //Made on subject request
                switch($event->data['user_lesson']['stage']) {
                    case USER_LESSON_RESCHEDULED_BY_TEACHER:
                        //Teacher rescheduled again
                        $messageType = 'student.subject.request.offer.accepted';
                        break;
                    case USER_LESSON_PENDING_STUDENT_APPROVAL:
                        //Teacher rescheduled his first invitation
                        $messageType = 'student.subject.request.offer.accepted';
                        break;
                    case USER_LESSON_RESCHEDULED_BY_STUDENT:
                        die; //Student cannot approve his offer
                        break;
                    case USER_LESSON_PENDING_TEACHER_APPROVAL:
                        die; //Student cannot approve his offer
                        break;
                }
            }
        }

        return $this->notification->addNotification(    $toUserId, $byUserId, //To user id
                                                        array( 'message_enum'=>$messageType, 'params'=>$event->data['user_lesson']) );//Message
    }
    public function afterReProposeRequest(CakeEvent $event) {
        $toUserId = $messageType = null;
        $byUserId = $event->data['by_user_id'];


        //If made by user - allocate CP
        if($byUserId==$event->data['user_lesson']['student_user_id'] &&
            isSet($event->data['update']['price'])) {

            $event->subject()->setTotalCreditPoints(
                $event->data['user_lesson']['user_lesson_id'],
                $event->data['update']['price']
            );
        }


        if($event->data['user_lesson']['teacher_user_id']==$byUserId) {
            //Proposal is made by the teacher
            $toUserId = $event->data['user_lesson']['student_user_id'];

            if(empty($event->data['user_lesson']['wish_list_id'])) {
                //Made on subject offer

                switch($event->data['user_lesson']['stage']) {
                    case USER_LESSON_RESCHEDULED_BY_TEACHER:
                        //Teacher rescheduled again
                        $messageType = 'teacher.invitation.rescheduled';
                        break;
                    case USER_LESSON_PENDING_STUDENT_APPROVAL:
                        //Teacher rescheduled his first invitation
                        $messageType = 'teacher.invitation.rescheduled';
                        break;

                    case USER_LESSON_PENDING_TEACHER_APPROVAL:
                        $messageType = 'teacher.booking.request.rescheduled';
                        break;
                    case USER_LESSON_RESCHEDULED_BY_STUDENT:
                        $messageType = 'teacher.booking.request.rescheduled';
                        break;
                }
            } else {
                //Made on subject request
                switch($event->data['user_lesson']['stage']) {
                    case USER_LESSON_RESCHEDULED_BY_TEACHER:
                        //Teacher rescheduled again
                        $messageType = 'teacher.subject.request.offer.rescheduled';
                        break;
                    case USER_LESSON_PENDING_STUDENT_APPROVAL:
                        //Teacher rescheduled his first invitation
                        $messageType = 'teacher.subject.request.offer.rescheduled';
                        break;

                    case USER_LESSON_PENDING_TEACHER_APPROVAL:
                        die; //User cannot invite teachers
                        break;
                    case USER_LESSON_RESCHEDULED_BY_STUDENT:
                        $messageType = 'teacher.subject.request.offer.rescheduledstudent';
                        break;
                }
            }

        } else {
            //Proposal is made by the student
            $toUserId = $event->data['user_lesson']['teacher_user_id'];

            if(empty($event->data['user_lesson']['wish_list_id'])) {
                //Made on subject offer
                switch($event->data['user_lesson']['stage']) {
                    case USER_LESSON_RESCHEDULED_BY_STUDENT:
                        //Student rescheduled again
                        $messageType = 'student.booking.request.rescheduled';
                        break;
                    case USER_LESSON_PENDING_TEACHER_APPROVAL:
                        //Student rescheduled his first invitation
                        $messageType = 'student.booking.request.rescheduled';
                        break;

                    case USER_LESSON_RESCHEDULED_BY_TEACHER:
                        $messageType = 'student.invitation.rescheduled';
                        break;
                    case USER_LESSON_PENDING_STUDENT_APPROVAL:
                        $messageType = 'student.invitation.rescheduled';
                        break;
                }
            } else {
                //Made on subject request
                switch($event->data['user_lesson']['stage']) {
                    case USER_LESSON_RESCHEDULED_BY_STUDENT:
                        $messageType = 'student.subject.request.offer.rescheduled';
                        break;
                    case USER_LESSON_PENDING_TEACHER_APPROVAL:
                        die; //User cannot invite teachers
                        break;
                    case USER_LESSON_RESCHEDULED_BY_TEACHER:
                        //Teacher rescheduled again
                        $messageType = 'student.subject.request.offer.rescheduled.teacher';
                        break;
                    case USER_LESSON_PENDING_STUDENT_APPROVAL:
                        //Teacher rescheduled his first invitation
                        $messageType = 'student.subject.request.offer.rescheduled.teacher';
                        break;
                }
            }
        }

        return $this->notification->addNotification(    $toUserId, $byUserId, //To user id
                                                        array( 'message_enum'=>$messageType, 'params'=>$event->data['user_lesson']) );//Message

    }

    public function afterJoinRequest(CakeEvent $event) {
        /*//Cancel OTHER existing requests
        $event->subject()->recursive = -1;
        $userLessonsData = $event->subject()->find('first', array('conditions'=>array(	'UserLesson.student_user_id'=>$event->data['user_lesson']['student_user_id'],
                                                                                        'UserLesson.teacher_lesson_id'=>$event->data['user_lesson']['teacher_lesson_id'],
                                                                                        'UserLesson.stage'=>array(  USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT,
                                                                                                                    USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER)),
        ));

        if($userLessonsData) {
            foreach($userLessonsData AS $userLessonData) {
                $event->subject()->cancelRequest($userLessonData['UserLesson']['user_lesson_id'], $event->data['by_user_id']);
            }
        }*/

        return $this->afterLessonRequest($event);
    }
    public function afterLessonRequest(CakeEvent $event) {
        $toUserId = $messageType = null;
        $byUserId = $event->data['by_user_id'];


        //If made by user - allocate CP
        if($byUserId==$event->data['user_lesson']['student_user_id']) {
            $event->subject()->setTotalCreditPoints(
                $event->data['user_lesson']['user_lesson_id'],
                $event->data['user_lesson']['price']
            );
        }

        if(empty($event->data['user_lesson']['wish_list_id'])) {
            if($byUserId==$event->data['user_lesson']['teacher_user_id']) {

                //Invitation sent by teacher
                $toUserId = $event->data['user_lesson']['student_user_id'];
                $messageType = 'teacher.invitation.sent';

            } else {
                //Order lesson by student
                $toUserId = $event->data['user_lesson']['teacher_user_id'];
                if(isSet($event->data['user_lesson']['teacher_lessson_id']) && $event->data['user_lesson']['teacher_lessson_id']) {
                    $messageType = 'student.booking.request.join.sent';
                } else {
                    $messageType = 'student.booking.request.sent';
                }


                //Check if its auto approve
                App::import('Model', 'AutoApproveLessonRequest');
                $aalsObj = new AutoApproveLessonRequest();

                $autoApprove = $aalsObj->isAutoApprove($event->data['user_lesson']['teacher_user_id'], $event->data['user_lesson']['lesson_type'], $event->data['user_lesson']['datetime']);
                if(!$autoApprove && $event->data['user_lesson']['lesson_type']==LESSON_TYPE_VIDEO) {
                    //Check if this video approved already and this is the 2nd purchase
                    $videoStatus = $event->subject()->getVideoLessonStatus($event->data['user_lesson']['subject_id'], $event->data['user_lesson']['student_user_id']);
                    $autoApprove = $videoStatus['approved'];
                    $event->subject()->log(var_export($autoApprove, true), '2ndApprove');
                }

                if($autoApprove) {
                    $this->setNotificationStatus('Model.UserLesson.afterAccept', false);
                    //CakeEventManager::instance()->detach($this, 'Model.UserLesson.afterAccept');
                    if($event->subject()->acceptRequest($event->subject()->id, $event->data['user_lesson']['teacher_user_id'])) {
                        //Send a confirmation - that his request been auto-approved
                        $this->notification->addNotification(   $event->data['user_lesson']['student_user_id'], //To user id
                                                                $event->data['user_lesson']['teacher_user_id'], //From user id
                                                                array( 'message_enum'=>'teacher.booking.request.auto.approve', 'params'=>$event->data['user_lesson']) ); //Message
                        $this->notification->addNotification(   $event->data['user_lesson']['teacher_user_id'], //To user id
                                                                $event->data['user_lesson']['student_user_id'], //From user id
                                                                array( 'message_enum'=>'student.booking.request.auto.approve', 'params'=>$event->data['user_lesson'])); //Message

                        $toUserId = $messageType = null;
                    } else {
                        $event->subject()->log('Cannot Auto-Accept UserLesson '.$event->subject()->id, 'error');
                        return false;
                    }
                    //CakeEventManager::instance()->attach($this, 'Model.UserLesson.afterAccept');
                    $this->setNotificationStatus('Model.UserLesson.afterAccept', true);
                }
            }
        } else {
            if($byUserId==$event->data['user_lesson']['teacher_user_id']) {
                //Send lesson offer
                $toUserId = $event->data['user_lesson']['student_user_id'];
                $messageType = 'teacher.subject.request.offer.sent';
            } else {
                die; //Users cannot invite teachers to subject-request
            }
        }

        if($toUserId && $messageType) {
            return $this->notification->addNotification( $toUserId, //To user id
                                                         $byUserId, array( 'message_enum'=>$messageType, 'params'=>$event->data['user_lesson']) );//Message
        }


        return true;
    }

    public function setNotificationStatus($event, $status) {
        $this->disableNotificationsForEvents[$event] = $status;
    }
    private function getNotificationStatus($event) {
        if(isSet($this->disableNotificationsForEvents[$event])) {
            return $this->disableNotificationsForEvents[$event];
        }
        return true;
    }
}
?>