<?php
class RequestsController extends AppController {
	public $name = 'Requests';
	public $uses = array('Subject', 'User', 'TeacherLesson', 'UserLesson');
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login'))/*, 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');


	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(	'index', 'searchSubject', 'searchSubjectLoadMore', 'subjectSearchSuggestions');
		$this->Auth->deny('makeRequest', 'testRequest');
	}
	
	public function index() {
        $this->setJSSetting('search_suggestions_url', $this->getCurrentParamsWithDifferentURL(array('controller'=>'Requests','action'=>'subjectSearchSuggestions'), array('page', 'limit', 'term')));

        $this->Subject->setLanguages($this->Session->read('languages_of_records'));
	 	$newSubjects = $this->Subject->getNewest(false, SUBJECT_TYPE_REQUEST);

		$this->set('newSubjects', $newSubjects);
	}
	
	//This method can be used via Ajax from HomeController or using a <form> from RequestsController
	public function makeRequest($subjectId=null) {
        unset($this->request->data['Subject']['subject_id']);

        //Check that the user owns that subject
        if($subjectId) {
            $this->Subject->recursive = -1;
            $subjectData = $this->Subject->findBySubjectId($subjectId);
            if(!$subjectData || $subjectData['Subject']['user_id']!=$this->Auth->user('user_id')) {
                return false;
            }


        }


        if (empty($this->request->data)) {
            if($subjectId) {
                $this->request->data['Subject'] = $subjectData['Subject'];
            }
        } else {
            $this->Subject->create(false);
            $this->Subject->id = $subjectId;

			$this->request->data['Subject']['is_enable'] = 1;
			$this->request->data['Subject']['is_public'] = 1;
			$this->request->data['Subject']['user_id'] = $this->Auth->user('user_id');
			$this->request->data['Subject']['type'] = SUBJECT_TYPE_REQUEST;



			//$this->Subject->set($this->request->data);
			if($this->Subject->save($this->request->data)) {
				if($this->params['ext']) {
					return $this->success(1, array('subject_id'=>$this->Subject->id));
				}

                $this->set('success', true);
				//$this->Session->setFlash(__('Request saved, you can browse and manage it through the control panel'));
				//$this->redirect(array('action'=>'index'));
			} else if($this->params['ext']) {
				return $this->error(1, array('validation_errors'=>$this->Subject->validationErrors));
			}
		}

        //Get subject categories
        App::Import('Model', 'SubjectCategory');
        $scObj = new SubjectCategory();
        $subjectCategories = $scObj->getAllCategoriesOptions();
        $this->set('subjectCategories', $subjectCategories);

        App::uses('Languages', 'Utils.Lib');
        $lang = new Languages();
        $this->set('languages', $lang->lists('locale'));
	}


    public function subjectSearchSuggestions() {
        $this->Subject;
        $this->request->query['type'] = SUBJECT_TYPE_REQUEST;
        $results = $this->requestAction(array('controller'=>'Home', 'action'=>'subjectSearchSuggestions'), $this->request->query);
        $this->request->data = $this->request->query; //For search form
        return $this->success(1, array('results'=>$results));
    }

	public function searchSubjectLoadMore() {
		$this->Subject;
		$this->request->query['type'] = SUBJECT_TYPE_REQUEST;
		$subjectsData = $this->requestAction(array('controller'=>'Home', 'action'=>'searchSubjectLoadMore'), $this->request->query);
        $this->request->data = $this->request->query; //For search form

        if($subjectsData) {
            return $this->success(1, array('subjects'=>$subjectsData['subjects']));
        }
        return $this->success(1, array('subjects'=>array()));
	}
    public function searchSubject() {
        $this->setJSSetting('search_load_more_url', $this->getCurrentParamsWithDifferentURL(array('controller'=>'Requests','action'=>'searchSubjectLoadMore'), array('page', 'limit')));
        $this->setJSSetting('search_suggestions_url', $this->getCurrentParamsWithDifferentURL(array('controller'=>'Requests','action'=>'subjectSearchSuggestions'), array('page', 'limit', 'term')));

		$this->Subject;
		$this->request->query['type'] = SUBJECT_TYPE_REQUEST;
		$subjectsData = $this->requestAction(array('controller'=>'Home', 'action'=>'searchSubject'), $this->request->query);
        $this->request->data = $this->request->query; //For search form
		$this->set('subjectsData', $subjectsData);
		$this->set('subjectSearchLimit', 8); //Set in homeController::searchSubject, $this->_searchDefaultQueryParams();
	}
	
	
	public function	offerSubject($requestSubjectId=null) {



        //return $this->success(1, array('post'=>$this->request->data));
        if (!empty($this->request->data)) {
            if(isSet($this->request->data['UserLesson']['request_subject_id']) && !empty($this->request->data['UserLesson']['request_subject_id'])) {
                $requestSubjectId = $this->request->data['UserLesson']['request_subject_id'];
            }
            //Validate
            $requestSubjectData = $this->Subject->findBySubjectId( $requestSubjectId );
            if(!$requestSubjectData || $requestSubjectData['Subject']['is_enable']==SUBJECT_IS_ENABLE_FALSE) {
                //$this->Session->setFlash(__('This subject is no longer available'));
                return $this->error(1);
            }

            //You can offer suggestions only to subject request
            $requestSubjectData = $requestSubjectData['Subject'];
            if($requestSubjectData['type']!=SUBJECT_TYPE_REQUEST) {
                //$this->Session->setFlash(__('This lesson cannot be ordered'));
                return $this->error(2);
            }

            if($requestSubjectData['user_id']==$this->Auth->user('user_id')) {
                //$this->Session->setFlash(__('You cannot offer lessons to yourself'));
                return $this->error(3);
            }

            //By is missing
            if(!isSet($this->request->data['by']) || empty($this->request->data['by'])) {
                return $this->error(4);
            }


            if($this->request->data['by']=='teacher_lesson_id') {
                //Only live lessons can have 'teacher_lesson_id'
                $res = $this->UserLesson->joinRequest(  $this->request->data['UserLesson']['teacher_lesson_id'],
                                                        $requestSubjectData['user_id'],
                                                        $this->Auth->user('user_id'),
                                                        null,
                                                        array('request_subject_id'=>$requestSubjectId, 'offer_message'=>$this->request->data['UserLesson']['offer_message'])); //Send invitation

                if(!$res) {
                    return $this->error(5, array('validation_errros'=>$this->UserLesson->validationErrors));
                }
                return $this->success(1, array('results'=>array(    'user_lesson_id'=>$this->UserLesson->id,
                                                                    'subject_id'=>$this->request->data['UserLesson']['subject_id'],
                                                                    'request_subject_id'=>$requestSubjectId,
                                                                    'teacher_lesson_id'=>$this->request->data['UserLesson']['teacher_lesson_id'])));
            }

            //Format datetime
            $datetime = null;
            if($this->request->data['by']=='datetime') {
                if(isSet($this->request->data['UserLesson']) && !empty($this->request->data['UserLesson'])) {
                    $datetime = $this->request->data['UserLesson']['datetime'];
                    $datetime = mktime(($datetime['meridian']=='pm' ? $datetime['hour']+12 : $datetime['hour']), $datetime['min'], 0, $datetime['month'], $datetime['day'], $datetime['year']);
                    $datetime = $this->UserLesson->timeExpression($datetime, false);
                }
            }
            unset($this->request->data['UserLesson']['datetime']);



            if($this->UserLesson->lessonOffer($this->request->data['UserLesson']['subject_id'], $requestSubjectId, $datetime,
                                                array('offer_message'=>$this->request->data['UserLesson']['offer_message']))) {
                return $this->success(2, array('results'=>array(    'user_lesson_id'=>$this->UserLesson->id,
                                                                    'subject_id'=>$this->request->data['UserLesson']['subject_id'],
                                                                    'request_subject_id'=>$requestSubjectId)));
            }
            return $this->error(6, array('validation_errors'=>$this->UserLesson->validationErrors));

        }

        //Get teacher subjects
        $teacherSubjectsData = $this->Subject->getOffersByTeacher($this->Auth->user('user_id'), true/*, $lessonType*/);
        //Build DropDown options
        $teacherSubjectsSuggestions = array(0=>__('Please select a subject'));
        foreach($teacherSubjectsData AS $teacherSubject) {
            $teacherSubject = $teacherSubject['Subject'];
            $teacherSubjectsSuggestions[$teacherSubject['lesson_type']][$teacherSubject['subject_id']] = $teacherSubject['name'];
        }

        $this->set('teacherSubjectsSuggestions', $teacherSubjectsSuggestions);
        if($requestSubjectId) {
            $this->set('requestSubjectId', $requestSubjectId);
        }
    }

    /* Replaced by offerSubject
	public function	offerLesson($requestSubjectId, $year=null, $month=null) {
		//Get subject data, students_amount
		$subjectData = $this->Subject->findBySubjectId( $requestSubjectId );
		if(!$subjectData || $subjectData['Subject']['is_enable']==SUBJECT_IS_ENABLE_FALSE) {
			$this->Session->setFlash(__('This subject is no longer available'));
			$this->redirect($this->referer());
		}

        //You can offer suggestions only to subject request
		$subjectData = $subjectData['Subject'];
        if($subjectData['type']!=SUBJECT_TYPE_REQUEST) {
            $this->Session->setFlash(__('This lesson cannot be ordered'));
            $this->redirect($this->referer());
        }

        if($subjectData['user_id']==$this->Auth->user('user_id')) {
            $this->Session->setFlash(__('You cannot offer lessons to yourself'));
            $this->redirect($this->referer());
        }



		if (!empty($this->request->data)) {
            //Format datetime
            $datetime = null;
            if(isSet($this->request->data['UserLesson']) && !empty($this->request->data['UserLesson'])) {
                $datetime = $this->request->data['UserLesson']['datetime'];
                $datetime = mktime(($datetime['meridian']=='pm' ? $datetime['hour']+12 : $datetime['hour']), $datetime['min'], 0, $datetime['month'], $datetime['day'], $datetime['year']);
                $datetime = $this->UserLesson->timeExpression($datetime, false);
            }
			unset($this->request->data['UserLesson']['datetime']);


            if($this->UserLesson->lessonOffer($this->request->data['UserLesson']['subject_id'], $requestSubjectId, $datetime)) {
                if($this->RequestHandler->isAjax()) {
                    return $this->success(1, array('user_lesson_id'=>$this->UserLesson->id));
                }

                $this->Session->setFlash(__('Offer sent, you can browse and manage it through your control panel'));
                $this->redirect(array('action'=>'index'));
            } else if($this->RequestHandler->isAjax()) {
                return $this->error(1, array('validation_errros'=>$this->UserLesson->validationErrors));
            }
		}


        //Get teacher subjects
        $teacherSubjectsData = $this->Subject->getOffersByTeacher($this->Auth->user('user_id'), true, $subjectData['lesson_type']);

        //Build DropDown options
        $teacherSubjectsSuggestions = array();
        foreach($teacherSubjectsData AS $teacherSubject) {
            $teacherSubject = $teacherSubject['Subject'];
            $teacherSubjectsSuggestions[$teacherSubject['subject_id']] = $teacherSubject['name'];
        }

		//Get student data
		$studentData = $this->User->findByUserId( $subjectData['user_id'] );
		if(!$studentData) {
			$this->Session->setFlash(__('Internal error'));
			$this->redirect($this->referer());
		}

        $isLiveLesson = false;
        if($subjectData['lesson_type']==LESSON_TYPE_LIVE) {
            $isLiveLesson = true;

            //Get student lessons for this month - unless $year/$month are set
            $allLiveLessons = $this->User->getLiveLessonsByDate( $subjectData['user_id'], false, $year, $month);

            $this->set('allLiveLessons',	 	    $allLiveLessons);
        }

        $this->set('isLiveLesson',	 	            $isLiveLesson);
		$this->set('requestSubjectId', 			    $requestSubjectId);
		$this->set('subjectData', 			        $subjectData);
		$this->set('studentUserData',		        $studentData['User']);
        $this->set('teacherSubjectsSuggestions',    $teacherSubjectsSuggestions);
        $this->set('teacherSubjectsData',           $teacherSubjectsData);
	}*/
	
	

}
