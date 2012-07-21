<?php
class RequestsController extends AppController {
	public $name = 'Requests';
	public $uses = array('Subject', 'User', 'Profile', 'TeacherLesson', 'UserLesson');
	public $components = array('Session', 'RequestHandler', 'Auth', 'Security');
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
	
	
	public function	offerLesson($subjectId, $year=null, $month=null) {
		//Get subject data, students_amount, raters_amount, avarage_rating
		$subjectData = $this->Subject->findBySubjectId( $subjectId );
		if(!$subjectData || $subjectData['Subject']['is_enable']==SUBJECT_IS_ENABLE_FALSE) {
			$this->Session->setFlash('This subject is no longer available');
			$this->redirect($this->referer());
		}
		
		$subjectData = $subjectData['Subject'];
		if (empty($this->request->data)) {
			$this->request->data['TeacherLesson'] = $subjectData;
		} else {
			$datetime = $this->request->data['TeacherLesson']['date'];
			$datetime = mktime(($datetime['meridian']=='pm' ? $datetime['hour']+12 : $datetime['hour']), $datetime['min'], 0, $datetime['month'], $datetime['day'], $datetime['year']);
			unset($this->request->data['TeacherLesson']['date']);
			if($this->TeacherLesson->add($subjectId, $datetime, 0, $this->Auth->user('user_id'), $this->request->data['TeacherLesson'])) {			
				if($this->RequestHandler->isAjax()) {
					return $this->success(1, array('teacher_lesson_id'=>$this->TeacherLesson->id));
				}
				
				$this->Session->setFlash('Offer saved, you can browse and manage it through the control panel');
				$this->redirect(array('action'=>'index'));
			} else if($this->RequestHandler->isAjax()) {
				return $this->error(1, array('validation_errros'=>$this->TeacherLesson->validationErrors));
			}
		}
		
		
		if(!$year) {
			$year = date('Y');
		}
		if(!$month) {
			$month = date('m');
		}
		
		//make sure this is the current month or a future one
		if($year<date('Y')) {
			$this->Session->setFlash('Invalid order date');
			$this->redirect($this->referer());
		} else if($year==date('Y') && $month<date('m')) {
			$this->Session->setFlash('Invalid order date');
			$this->redirect($this->referer());
		}
		
		
		
		
		if($subjectData['type']!=SUBJECT_TYPE_REQUEST) {
			$this->Session->setFlash('This lesson cannot be ordered');
			$this->redirect($this->referer());
		}
		
		//Get student data
		$studentData = $this->User->findByUserId( $subjectData['user_id'] );
		if(!$studentData) {
			$this->Session->setFlash('Internal error');
			$this->redirect($this->referer());
		}
		
		
		
		//Get student lessons for a given month
		$allLessons = $this->User->getLessons( $subjectData['user_id'], false, $year, $month);
		
		
		
		$this->set('subjectData', 			$subjectData);
		$this->set('studentUserData',		$studentData['User']);
		$this->set('allLessons',	 		$allLessons);
	}
	
	

}
