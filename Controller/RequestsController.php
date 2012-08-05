<?php
class RequestsController extends AppController {
	public $name = 'Requests';
	public $uses = array('Subject', 'User', 'TeacherLesson', 'UserLesson');
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login'))/*, 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');


	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(	'index', 'searchSubject');
		$this->Auth->deny('makeRequest', 'testRequest');
	}
	
	public function index() {
	 	$newSubjects = $this->Subject->getNewest(false, SUBJECT_TYPE_REQUEST);
		$this->set('newSubjects', $newSubjects);
	}
	
	//This method can be used via Ajax from HomeController or using a <form> from RequestsController
	public function makeRequest() {
		if (!empty($this->request->data)) {
			App::import('Model', 'Subject');
			unset($this->request->data['Subject']['subject_id']);
			$this->request->data['Subject']['is_enable'] = 1;
			$this->request->data['Subject']['is_public'] = 1;
			$this->request->data['Subject']['user_id'] = $this->Auth->user('user_id');
			$this->request->data['Subject']['type'] = SUBJECT_TYPE_REQUEST;
			
			$this->Subject->set($this->request->data);
			if($this->Subject->save()) {
				if($this->RequestHandler->isAjax()) {
					return $this->success(1, array('subject_id'=>this));
				}
				$this->Session->setFlash('Request saved, you can browse and manage it through the control panel');
				$this->redirect(array('action'=>'index'));
			} else if($this->RequestHandler->isAjax()) {
				return $this->error(1, array('validation_errros'=>$this->Subject->validationErrors));
			}
		}

        //Get subejct categories
        App::Import('Model', 'SubjectCategory');
        $scObj = new SubjectCategory();
        $subjectCategories = $scObj->getAllCategoriesOptions();
        $this->set('subjectCategories', $subjectCategories);

        Configure::load('language');
        $this->set('language',Configure::read('language'));
	}


    public function subjectSuggestions() {
        $this->Subject;
        $this->request->query['type'] = SUBJECT_TYPE_REQUEST;
        $results = $this->requestAction(array('controller'=>'Home', 'action'=>'subjectSuggestions'), $this->request->query);
        $this->request->data = $this->request->query; //For search form
        return $this->success(1, array('results'=>$results));
    }

	public function searchSubject() {
		$this->Subject;
		$this->request->query['type'] = SUBJECT_TYPE_REQUEST;
		$subjectsData = $this->requestAction(array('controller'=>'Home', 'action'=>'searchSubject'), $this->request->query);
        $this->request->data = $this->request->query; //For search form
		$this->set('subjectsData', $subjectsData);
	}
	
	
	public function	offerLesson($requestSubjectId, $year=null, $month=null) {
		//Get subject data, students_amount
		$subjectData = $this->Subject->findBySubjectId( $requestSubjectId );
		if(!$subjectData || $subjectData['Subject']['is_enable']==SUBJECT_IS_ENABLE_FALSE) {
			$this->Session->setFlash('This subject is no longer available');
			$this->redirect($this->referer());
		}

        //You can offer suggestions only to subject request
		$subjectData = $subjectData['Subject'];
        if($subjectData['type']!=SUBJECT_TYPE_REQUEST) {
            $this->Session->setFlash('This lesson cannot be ordered');
            $this->redirect($this->referer());
        }




		if (!empty($this->request->data)) {
            //Format datetime
			$datetime = $this->request->data['UserLesson']['datetime'];
			$datetime = mktime(($datetime['meridian']=='pm' ? $datetime['hour']+12 : $datetime['hour']), $datetime['min'], 0, $datetime['month'], $datetime['day'], $datetime['year']);
			unset($this->request->data['UserLesson']['datetime']);


            if($this->UserLesson->lessonOffer($this->request->data['UserLesson']['subject_id'], $requestSubjectId, $datetime)) {
                if($this->RequestHandler->isAjax()) {
                    return $this->success(1, array('user_lesson_id'=>$this->UserLesson->id));
                }

                $this->Session->setFlash('Offer sent, you can browse and manage it through your control panel');
                $this->redirect(array('action'=>'index'));
            } else if($this->RequestHandler->isAjax()) {
                return $this->error(1, array('validation_errros'=>$this->UserLesson->validationErrors));
            }
		}


        //Get teacher subjects
        $teacherSubjectsData = $this->Subject->getbyTeacher($this->Auth->user('user_id'), true, SUBJECT_TYPE_OFFER, $subjectData['lesson_type']);

        //Build DropDown options
        $teacherSubjectsSuggestions = array();
        foreach($teacherSubjectsData AS $teacherSubject) {
            $teacherSubject = $teacherSubject['Subject'];
            $teacherSubjectsSuggestions[$teacherSubject['subject_id']] = $teacherSubject['name'];
        }

		//Get student data
		$studentData = $this->User->findByUserId( $subjectData['user_id'] );
		if(!$studentData) {
			$this->Session->setFlash('Internal error');
			$this->redirect($this->referer());
		}

		//Get student lessons for this month - unless $year/$month are set
        $allLiveLessons = $this->User->getLiveLessonsByDate( $subjectData['user_id'], false, $year, $month);


		$this->set('requestSubjectId', 			    $requestSubjectId);
		$this->set('subjectData', 			        $subjectData);
		$this->set('studentUserData',		        $studentData['User']);
		$this->set('allLiveLessons',	 	        $allLiveLessons);
        $this->set('teacherSubjectsSuggestions',    $teacherSubjectsSuggestions);
        $this->set('teacherSubjectsData',           $teacherSubjectsData);
	}
	
	

}
