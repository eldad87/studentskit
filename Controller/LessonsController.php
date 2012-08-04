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
        $videoRequestStatus = $this->UserLesson->getLiveLessonStatus($teacherLessonId, $this->Auth->user('user_id'));
        if(!$videoRequestStatus) {
            $this->Session->setFlash('Invalid request');
            $this->redirect('/');
        }

        //Check if overdue
        if($videoRequestStatus['overdue']) {
            $this->Session->setFlash('The lesson you\'re trying to enter is overdue');
            return $this->error(2, array('url'=>array('controller'=>'Home', 'action'=>'teacherSubject', $videoRequestStatus['subject_id'])));

        } else if($videoRequestStatus['in_process'] || $videoRequestStatus['about_to_start']) {
            $this->set('is_teacher', $videoRequestStatus['is_teacher']);

            if($videoRequestStatus['is_teacher']) {
                //Enter lesson - its the teacher
                $this->set('meeting', $this->TeacherLesson->getLiveLessonMeeting($teacherLessonId));
            } else if($videoRequestStatus['pending_teacher_approval'] || $videoRequestStatus['pending_user_approval'] || $videoRequestStatus['payment_needed']) {
                //User need to pay/approve/wait for approval
                $this->redirect(array('controller'=>'Home', 'action'=>'teacherLesson', $videoRequestStatus['teacher_lesson_id']));
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
            $this->Session->setFlash('Invalid request');
            $this->redirect('/');
        }

        if(!$canWatchData['show_video']) {
            if($canWatchData['pending_teacher_approval']) {
                $this->Session->setFlash('This video is waiting the teacher\'s approval');
            } else if($canWatchData['pending_user_approval']) {
                $this->Session->setFlash('This video require your approval');
                $this->redirect(array('controller'=>'Student', 'action'=>'lessons', 'tab'=>'invitations', $canWatchData['user_lesson_id']));
            } else if($canWatchData['payment_needed']) {
                $this->Session->setFlash('This video is a premium video, please pay for it first');

            }

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

                $message .= Router::url(array('controller'=>'Home', 'action'=>'teacherLesson', $id), true);
                break;
            case 'Subject':
                $message .= Router::url(array('controller'=>'Home', 'action'=>'teacherSubject', $id), true);
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
            $this->UserLesson->lessonRequest($subjectId, $user['user_id'], time(), true); //Send invitation
        }
        return array_flip($emailAsKeys);
    }
    /*
     * The lesson will take place here.
     * in case the lesson is taking place in the future - details about it will be shown.
     */
    /*public function lessonPage($teacherLessonId) {
        //Find teacher lesson
        $this->TeacherLesson->recursive = -1;
        $tlData = $this->TeacherLesson->find('first', array('teacher_lesson_id'=>$teacherLessonId));
        if(!$tlData) {
            $this->Session->setFlash('Lesson not found');
            $this->redirect($this->referer());
        }
        $tlData = $tlData['TeacherLesson'];
        $isTeacher = $this->Auth->user('user_id')==$tlData['teacher_user_id'] ? true : false;

        //Check if this user is register for this lesson or no
        $this->UserLesson->recursive = -1;
        $userLessonData = $this->UserLesson->find('first', array('conditions'=>array('teacher_lesson_id'=>$teacherLessonId, 'student_user_id'=>$this->Auth->user('user_id'))));
        if($userLessonData) {
            $userLessonData = $userLessonData['UserLessonId'];
        }

        if($tlData['datetime']<time()-($tlData['duration']*MIN)) {
            //Lesson overdue

            $this->Session->setFlash('Lesson over due');
            if($userLessonData) {
                //User paid for this lesson
                $this->redirect(array('controller'=>'Student', 'action'=>'lessons', 'tab'=>'archive', 'user_lesson_id'=>$userLessonData['user_lesson_id']));

            } else if ($isTeacher) {
                $this->redirect(array('controller'=>'Teacher', 'action'=>'lessons', 'tab'=>'archive', 'teacher_lesson_id'=>$teacherLessonId));
            } else {
                $this->redirect('/');
            }
        } else {
            if($userLessonData) {
                //TODO: show counter
            } else if ($isTeacher) {
                //TODO: let him edit the lesson
            } else {
                //Take user to order page
                //TODO: show a page with info about the lesson, with "order" button
                $this->redirect(array('action'=>'submitOrder', 'join', $teacherLessonId));
            }
        }

    }*/
}
