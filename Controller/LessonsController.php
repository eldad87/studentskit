<?php
/**
 *@property Subject $Subject
 *@property User $User
 *@property Profile $Profile
 *@property TeacherLesson $TeacherLesson
 *@property UserLesson $UserLesson
 */
class LessonsController extends AppController {
	public $name = 'Lessons';
	public $uses = array('Subject', 'User', 'Profile', 'TeacherLesson', 'UserLesson');
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')), 'Security');
	//public $helpers = array('Form', 'Html', 'Js', 'Time');


	public function beforeFilter() {
		parent::beforeFilter();
		/*$this->Auth->allow(	'index');
		$this->Auth->deny('submitOrder');*/
	}

    /**
     * Live lesson page
     */
    public function index($teacherLessonId) {
        $liveRequestStatus = $this->UserLesson->getLiveLessonStatus($teacherLessonId, $this->Auth->user('user_id'));
        if(!$liveRequestStatus) {
            $this->Session->setFlash(__('Invalid request'));
            $this->redirect('/');
        }

        //Check if overdue
        if($liveRequestStatus['overdue']) {
            $this->Session->setFlash(__('The lesson you\'re trying to enter is overdue'));
            return $this->error(2, array('url'=>array('controller'=>'Home', 'action'=>'teacherSubject', $liveRequestStatus['subject_id'])));

        } else if($liveRequestStatus['in_process'] || $liveRequestStatus['about_to_start']) {
            $this->set('is_teacher', $liveRequestStatus['is_teacher']);

            if($liveRequestStatus['is_teacher']) {
                //Enter lesson - its the teacher
                $this->set('meeting', $this->TeacherLesson->getLiveLessonMeeting($teacherLessonId));
            } else if($liveRequestStatus['pending_teacher_approval'] || $liveRequestStatus['pending_user_approval'] || $liveRequestStatus['payment_needed']) {
                //User need to pay/approve/wait for approval
                $this->redirect(array('controller'=>'Home', 'action'=>'teacherLesson', $liveRequestStatus['teacher_lesson_id']));
            } else {
                //Enter lesson
                $this->set('meeting', $this->TeacherLesson->getLiveLessonMeeting($teacherLessonId));
            }


        }

    }



    /**
     * Video lesson page
     *
     * If its a free video - show it
     * If its a paid video
     *      if the user paid for it - show video
     *      else show "payment" button and 10 sec preview
     */
    public function video($subjectId) {

        $canWatchData = $this->UserLesson->getVideoLessonStatus($subjectId, $this->Auth->user('user_id'), true);
        if(!$canWatchData) {
            $this->Session->setFlash(__('Invalid request'));
            $this->redirect('/');
        }

        if(!$canWatchData['show_video']) {
            /* no need for flash messages, teacherSubject page will show them anyway
            if($canWatchData['pending_teacher_approval']) {
                $this->Session->setFlash(__('This video is waiting the teacher\'s approval'));
            } else if($canWatchData['pending_user_approval']) {
                $this->Session->setFlash(__('This video require your approval'));
                $this->redirect(array('controller'=>'Student', 'action'=>'lessons', 'tab'=>'invitations', $canWatchData['user_lesson_id']));
            } else if($canWatchData['payment_needed']) {
                $this->Session->setFlash(__('This video is a premium video, please pay for it first'));

            }*/

            $this->redirect(array('controller'=>'Home', 'action'=>'teacherSubject', $subjectId));
        }


        $this->set('subjectUrl', $this->TeacherLesson->getVideoUrl($subjectId));
        $this->set('showAds', ($canWatchData['has_ended'] || $canWatchData['is_free']) );
        $this->set('fileSystem', $this->TeacherLesson->getFileSystem($canWatchData['teacher_lesson_id']));
		$this->set('tests', $this->TeacherLesson->getTests($canWatchData['teacher_lesson_id']));
    }

    public function invite() {
        $this->request->data['subject_id'] = 1;
        $this->request->data['emails'] = 'sivaneshokol@gmail.com';
        $this->request->data['message'] = 'My message';

        if (!empty($this->request->data)) {
            if((!isSet($this->request->data['teacher_lesson_id']) && !isSet($this->request->data['subject_id']) ) ||
                !isSet($this->request->data['emails']) || !isSet($this->request->data['message'])) {

                return $this->error(1);
            }
            $this->request->data['emails'] = explode(',', $this->request->data['emails']);

            //Sent with teacher_lesson_id
            if(isSet($this->request->data['teacher_lesson_id']) && !empty($this->request->data['teacher_lesson_id'])) {
                //Find teacher lesson
                $this->TeacherLesson->recursive = -1;
                $tlData = $this->TeacherLesson->find('first', array('teacher_lesson_id'=>$this->request->data['teacher_lesson_id']));
                if(!$tlData) {
                    return $this->error(2);
                }
                $tlData = $tlData['TeacherLesson'];



                //Just in case we won't handle it later on
                unset($this->request->data['subject_id']);

                //If its the teacher, send invitations in the system
                if($tlData['lesson_type']==LESSON_TYPE_LIVE) {
                    //TODO: check if lesson is overdue/started

                    //Email users
                    $this->emailUsers($this->request->data['emails'], $tlData['name'], $this->request->data['message'], 'TeacherLesson', $this->request->data['teacher_lesson_id']);

                    if($this->Auth->user('user_id')==$tlData['teacher_user_id']) {
                        $this->sendLiveLessonsJoinRequestsByTeacher($this->request->data['emails'], $this->request->data['teacher_lesson_id']);
                    }
                } else {
                    $this->request->data['subject_id'] = $tlData['subject_id'];
                }

            }

            if(isSet($this->request->data['subject_id']) && !empty($this->request->data['subject_id'])) {
                $this->Subject->recursive = -1;
                $subjectData = $this->Subject->findBySubjectId($this->request->data['subject_id']);
                if(!$subjectData) {
                    return $this->error(3);
                }
                $subjectData = $subjectData['Subject'];

                $this->emailUsers($this->request->data['emails'], $subjectData['name'], $this->request->data['message'], 'Subject', $this->request->data['subject_id']);

                //OIts a video offer, and it's the teacher
                if($this->Auth->user('user_id')==$subjectData['user_id'] && $subjectData['type']==SUBJECT_TYPE_OFFER && $subjectData['lesson_type']==LESSON_TYPE_VIDEO) {
                    $this->sendVideoLessonsInvitationsByTeacher($this->request->data['emails'], $this->request->data['subject_id']);
                }
            }

            return $this->success(1);
        }
    }

    private function emailUsers($emails, $name, $message, $type, $id) {
        $message .= "\n\n".'In order to view the invitation, click here:';
        switch($type) {
            case 'TeacherLesson':
                $message .= Router::url(array('controller'=>'Home', 'action'=>'teacherLesson', $id), true); //Live lesson
                break;
            case 'Subject':
                $message .= Router::url(array('controller'=>'Home', 'action'=>'teacherSubject', $id), true); //Video lesson
                break;
        }

      /*  App::uses('CakeEmail', 'Network/Email');
        $email = new CakeEmail();

        foreach($emails AS $toEmail) {
            $email->from(array('doNotReplay@studentskit.com' => 'Studentskit'));
            $email->subject('Invitation for '.$name);
            $email->to($toEmail);
            $email->send($message);
        }*/
    }
    private function sendLiveLessonsJoinRequestsByTeacher($emails, $teacherLessonId) {
        $this->TeacherLesson->recursive = -1;
        $tlData = $this->TeacherLesson->find('first', array('teacher_lesson_id'=>$teacherLessonId));

        $this->User->recursive = -1;
        $users = $this->User->find('all', array('conditions'=>array('email'=>$emails)));

        $emailAsKeys = array_flip($emails);
        foreach($users AS $user) {
            $user = $user['User'];
            unset($emailAsKeys[$user['email']]); //Remove user from emailing list
            $this->UserLesson->joinRequest($teacherLessonId, $user['user_id'], $tlData['TeacherLesson']['teacher_user_id']); //Send invitation
        }
        return array_flip($emailAsKeys);
    }
    private function sendVideoLessonsInvitationsByTeacher($emails, $subjectId) {
        $this->User->recursive = -1;
        $users = $this->User->find('all', array('conditions'=>array('email'=>$emails)));
        $emailAsKeys = array_flip($emails);
        foreach($users AS $user) {
            $user = $user['User'];
            unset($emailAsKeys[$user['email']]); //Remove user from emailing list
            $this->UserLesson->lessonRequest($subjectId, $user['user_id'], $this->UserLesson->toClientTime('now'), true); //Send invitation
        }
        return array_flip($emailAsKeys);
    }
}
