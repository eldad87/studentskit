<?php
App::uses('CakeEventManager', 'Event');
/**
 *@property Notification $notification
 */
class UserLessonEventListener implements CakeEventListener {
    private $notification;

    public function UserLessonEventListener() {
        App::import('Model', 'Notification');
        $this->notification = New Notification();
    }


    public function implementedEvents() {
        return array(
            'Model.UserLesson.afterLessonRequest'       => 'afterLessonRequest',
            'Model.UserLesson.afterJoinRequest'         => 'afterJoinRequest',
            'Model.UserLesson.afterReProposeRequest'    => 'afterReProposeRequest',
            'Model.UserLesson.afterCancelRequest'       => 'afterCancelRequest',
            'Model.UserLesson.afterAccept'              => 'afterAccept',
            'Model.UserLesson.beforeAccept'             => 'beforeAccept',
            'Model.UserLesson.afterRate'                => 'afterRate',
        );
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
    public function afterCancelRequest(CakeEvent $event) {
        $toUserId = $messageType = null;
        $byUserId = $event->data['by_user_id'];

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
                }
            }
        }

        return $this->notification->addNotification(    $toUserId, //To user id
                                                        array( 'message_enum'=>$messageType, 'params'=>$event->data['user_lesson']) );//Message
    }
    public function beforeAccept(CakeEvent $event) {
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
            $this->TeacherLesson->id = $event->data['user_lesson']['teacher_lesson_id'];

            $this->TeacherLesson->set(array($counter=>$tlObj->getDataSource()->expression($counter.'-1'), 'num_of_students'=>$tlObj->getDataSource()->expression('num_of_students+1')));
            if(!$this->TeacherLesson->save()) {
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

                if($aalsObj->isAutoApprove($event->data['user_lesson']['teacher_user_id'], $event->data['user_lesson']['lesson_type'], $event->data['user_lesson']['datetime'])) {
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