<?php
App::uses('CakeEventManager', 'Event');
/**
 *@property Notification $notification
 */
class UserLessonEventListener implements CakeEventListener {
    private $notification;
    private $adaptivePayment;

    public function UserLessonEventListener() {
        App::import('Model', 'Notification');
        App::import('Model', 'AdaptivePayment');

        $this->notification = New Notification();
        $this->adaptivePayment = new AdaptivePayment();
    }


    public function implementedEvents() {
        return array(
            'Model.UserLesson.afterLessonRequest'       => 'afterLessonRequest',
            'Model.UserLesson.afterJoinRequest'         => 'afterJoinRequest',
            'Model.UserLesson.afterReProposeRequest'    => 'afterReProposeRequest',
            'Model.UserLesson.afterCancelRequest'       => 'afterCancelRequest',    //Cancel payment preapproval
            'Model.UserLesson.afterAccept'              => 'afterAccept',
            'Model.UserLesson.beforeAccept'             => 'beforeAccept',          //Make sure preapproval amount is OK
            'Model.UserLesson.afterRate'                => 'afterRate',

            //'Model.TeacherLesson.afterCancel'           => 'afterCancelTL',       //Cancel all preapproval's for this lesson
            'Model.AdaptivePayment.afterPaymentUpdate'  => 'afterPaymentUpdate',    //Update payment status
            'Model.UserLesson.beforeLessonRequest'      => 'beforeLessonRequest',   //Cannot use on paid lessons
            'Model.UserLesson.beforeJoinRequest'        => 'beforeJoinRequest',     //Cannot use on paid lessons
            'Model.UserLesson.beforeReProposeRequest'   => 'beforeReProposeRequest',//Make sure preapproval amount is OK
            'Model.AdaptivePayment.AfterUserLessonPaid' =>  'afterPaid'
        );
    }

    public function afterPaid(CakeEvent $event) {
        App::import('Model', 'UserLesson');
        $ulObj = new UserLesson();
        $ulObj->id = $event->data['user_lesson_id'];
        $ulObj->set(array('payment_status'=>$event->data['status']));
        return $ulObj->save();
    }

    /**
     * Change the stage of the UL according to the payment data
     * @param CakeEvent $event
     */
    public function afterPaymentUpdate(CakeEvent $event) {
        //$event->data = array('user_lesson_id'=>$ipnData['user_lesson_id'], 'is_approved'=>($ipnData['approved']=='true' ? 1 : 0), 'status'=>$ipnData['status'], 'max_amount'=>$ipnData['max_total_amount_of_all_payments'], 'paid_amount'=>$paid, 'is_used'=>( $paid ? 1 : 0 ));
        $paymentStatus = $this->adaptivePayment->getStatus($event->data['current']['pending_user_lesson_id'], null, $event->data['old']['preapproval_key']);
            if(!$paymentStatus) {
            return false;
        }



        //Check if we need to convert the UserLesson
        if($event->data['current']['status']=='ACTIVE' && $event->data['current']['is_approved']) {

            //Execute PreApproval
            App::import('Model', 'PendingUserLesson');
            $pulObj = new PendingUserLesson();
            $userLessonId = $pulObj->execute($event->data['current']['pending_user_lesson_id']);
            if(!$userLessonId) {
                //Cannot convert, cancel approval
                $this->adaptivePayment->cancelApproval($event->data['old']['user_lesson_id'], $event->data['old']['preapproval_key']);

                return false;
            }

            //Update adaptivePayments, make sure the pending_user_lesson_id is no longer in use
            //$event->subject()->updateAll(array('pending_user_lesson_id'=>null), array('adaptive_payment_id'=>$paymentStatus['adaptive_payment_id']));



            //Check if there is an existing ACTIVE/IN_PROCESS approval for the user_lesson_id
            if($event->data['old']['user_lesson_id']) {
                $activePP = $event->subject()->find('first', array('conditions'=>array('AdaptivePayment.user_lesson_id'=>$event->data['old']['user_lesson_id'], 'status'=>array('IN_PROCESS', 'ACTIVE'),
                                                                                        'NOT'=>array('preapproval_key'=>$event->data['old']['preapproval_key']))));
                if($activePP) {
                    //Cancel the old ACTIVE preapproval
                    $event->subject()->cancelApproval($event->data['old']['user_lesson_id'], $activePP[$event->subject()->name]['preapproval_key']);
                }
            }

            if($paymentStatus['user_lesson_id']) {
                return true;
            }

            //Bind payment to UserLesson
            return $event->subject()->bindToUserLessonId($paymentStatus['adaptive_payment_id'], $userLessonId);

        } else if($event->data['current']['status']=='CANCELED') {
            if($event->data['old']['pending_user_lesson_id']) {
                App::import('Model', 'PendingUserLesson');
                $pulObj = new PendingUserLesson();
                $pulObj->cancel($event->data['old']['pending_user_lesson_id']);
            }

            if($event->data['old']['status']=='ACTIVE' && $event->data['old']['is_approved'] && $event->data['old']['user_lesson_id'] ) {

                //Check if there are no other ACTIVE preApprovals
                if(!$event->subject()->findByUserLessonIdAndStatus($event->data['old']['user_lesson_id'], 'ACTIVE')) {
                    //There are no other ACTIVE preapproval, cancel UserLesson
                    App::import('Model', 'UserLesson');
                    $ulObj = new UserLesson();
                    $ulData = $ulObj->findByUserLessonId($event->data['current']['user_lesson_id']);
                    $ulObj->cancelRequest($event->data['old']['user_lesson_id'], $ulData['UserLesson']['student_user_id']);
                }
            }

        }

        return true;
    }
    public function beforeLessonRequest(CakeEvent $event) {
        //$event->data = array('user_lesson'=>$userLesson, 'by_user_id'=>$userId)
        //Make sure it was done by the student
        if($event->data['user_lesson']['student_user_id']==$event->data['by_user_id']&& $event->data['user_lesson']['1_on_1_price']>0) {

            //$this->adaptivePayment->
            if(!$this->adaptivePayment->isValidApproval($event->data['user_lesson']['user_lesson_id'], $event->data['user_lesson']['1_on_1_price'], $event->data['user_lesson']['datetime'])) {
                return false;
            }
        }

        return true;
    }
    public function beforeJoinRequest(CakeEvent $event) {
        //$event->data = array('teacher_lesson'=>$teacherLessonData, 'user_lesson'=>$userLesson, 'by_user_id'=>( $teacherUserId ? $teacherUserId : $studentUserId))

        //Make sure it was done by the student
        if($event->data['user_lesson']['student_user_id']==$event->data['by_user_id'] && $event->data['user_lesson']['1_on_1_price']>0) {


            if(!$this->adaptivePayment->isValidApproval($event->data['user_lesson']['user_lesson_id'], $event->data['user_lesson']['1_on_1_price'], $event->data['user_lesson']['datetime'])) {
                return false;
            }
        }

        return true;
    }

    public function beforeReProposeRequest(CakeEvent $event) {
        //$event->data = array('user_lesson'=>$userLessonData, 'update'=>$data, 'by_user_id'=>$byUserId)

        //Make sure it was done by the student
        if($event->data['user_lesson']['student_user_id']==$event->data['by_user_id']) {
            //Make sure preapproval is OK
            $maxAmount = (isSet($event->data['user_lesson']['update']['1_on_1_price']) ? $event->data['user_lesson']['update']['1_on_1_price'] : null );
            $datetime = (isSet($event->data['user_lesson']['update']['datetime']) ? $event->data['user_lesson']['update']['datetime'] : null );
            return $this->adaptivePayment->isValidApproval($event->data['user_lesson']['user_lesson_id'], $maxAmount, $datetime);
        }

        return true;
    }

    /**
     * Cancel preapproval's
     * @param CakeEvent $event
     * @return bool
     */
    /*public function afterCancelTL(CakeEvent $event) {
        //$event->data = array('teacher_lesson'=>$teacherLessonsData,'user_lessons'=>$userLessonData)
        return $this->adaptivePayment->cancelTeacherLessonApprovals($event->data['teacher_lesson']['teacher_lesson_id']);
    }*/




    public function afterCancelRequest(CakeEvent $event) {
        $toUserId = $messageType = null;
        $byUserId = $event->data['by_user_id'];

        //cancel preApproval payment
        if(!$this->adaptivePayment->cancelApproval($event->data['user_lesson']['user_lesson_id'])) {
            $event->subject()->log('Cannot cancel UserLesson '.$event->data['user_lesson']['user_lesson_id'], 'paypal_error');
        }

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

        if($event->data['user_lesson']['teacher_user_id']==$byUserId) {
            $toUserId = $event->data['user_lesson']['student_user_id'];

            if(empty($event->data['user_lesson']['request_subject_id'])) {
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
            if(empty($event->data['user_lesson']['request_subject_id'])) {
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

        return $this->notification->addNotification(    $toUserId, //To user id
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

        return $this->notification->addNotification(    $toUserId, //To user id
            array( 'message_enum'=>$messageType, 'params'=>$event->data['user_lesson']) );//Message
    }

    public function beforeAccept(CakeEvent $event) {
        //$event->data = array('user_lesson'=>$userLessonData, 'by_user_id'=>$byUserId);
        //Make sure it was done by the student
        if($event->data['user_lesson']['student_user_id']==$event->data['by_user_id']) {
            //Make sure preapproval is OK
            $maxAmount = (isSet($event->data['user_lesson']['update']['1_on_1_price']) ? $event->data['user_lesson']['update']['1_on_1_price'] : null );
            $datetime = (isSet($event->data['user_lesson']['update']['datetime']) ? $event->data['user_lesson']['update']['datetime'] : null );
            if(!$this->adaptivePayment->isValidApproval($event->data['user_lesson']['user_lesson_id'], $maxAmount, $datetime)) {
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
        $toUserId = $messageType = null;
        $byUserId = $event->data['by_user_id'];


        if($event->data['user_lesson']['teacher_user_id']==$byUserId) {
            $toUserId = $event->data['user_lesson']['student_user_id'];
            if(empty($event->data['user_lesson']['request_subject_id'])) {
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

            if(empty($event->data['user_lesson']['request_subject_id'])) {
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

        return $this->notification->addNotification(    $toUserId, //To user id
                                                        array( 'message_enum'=>$messageType, 'params'=>$event->data['user_lesson']) );//Message
    }
    public function afterReProposeRequest(CakeEvent $event) {
        $toUserId = $messageType = null;
        $byUserId = $event->data['by_user_id'];


        if($event->data['user_lesson']['teacher_user_id']==$byUserId) {
            //Proposal is made by the teacher
            $toUserId = $event->data['user_lesson']['student_user_id'];

            if(empty($event->data['user_lesson']['request_subject_id'])) {
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

            if(empty($event->data['user_lesson']['request_subject_id'])) {
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

        return $this->notification->addNotification(    $toUserId, //To user id
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
        if(empty($event->data['user_lesson']['request_subject_id'])) {
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
                    CakeEventManager::instance()->detach($this, 'Model.UserLesson.afterAccept');
                    if($event->subject()->acceptRequest($event->subject()->id, $event->data['user_lesson']['teacher_user_id'])) {
                        //Send a confirmation - that his request been auto-approved
                        $this->notification->addNotification(   $event->data['user_lesson']['student_user_id'], //To user id
                            array( 'message_enum'=>'teacher.booking.request.auto.approve', 'params'=>$event->data['user_lesson']) ); //Message
                        $this->notification->addNotification(   $event->data['user_lesson']['teacher_user_id'], //To user id
                            array( 'message_enum'=>'student.booking.request.auto.approve', 'params'=>$event->data['user_lesson'])); //Message

                        $toUserId = $messageType = null;
                    }
                    CakeEventManager::instance()->attach($this, 'Model.UserLesson.afterAccept');
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
                                                         array( 'message_enum'=>$messageType, 'params'=>$event->data['user_lesson']) );//Message
        }


        return true;
    }
}
?>