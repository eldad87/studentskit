<?php
App::import('Model','Subject');
class RequestsController extends AppController {
	public $name = 'Requests';
	public $uses = array('Subject', 'WishList', 'User', 'TeacherLesson', 'UserLesson');
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login'))/*, 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');


	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(	'index', 'searchSubject', 'searchSubjectLoadMore', 'subjectSearchSuggestions');
		$this->Auth->deny('makeRequest', 'testRequest');
	}
	
	public function index() {
        $this->setJSSetting('search_suggestions_url', $this->getCurrentParamsWithDifferentURL(array('controller'=>'Requests','action'=>'subjectSearchSuggestions'), array('page', 'limit', 'term')));

        $this->WishList->setLanguages($this->Session->read('languages_of_records'));
        $newWishList = $this->WishList->getNewest(false);

		$this->set('newWishList', $newWishList);
	}
	
	//This method can be used via Ajax from HomeController or using a <form> from RequestsController
	public function makeRequest($wishListId=null) {
        unset($this->request->data['WishList']['wish_list_id']);

        //Check that the user owns that WishList
        if($wishListId) {
            $this->WishList->recursive = -1;
            $wishData = $this->WishList->findByWishListId($wishListId);
            if(!$wishData || $wishData['WishList']['student_user_id']!=$this->Auth->user('user_id')) {
                return false;
            }


        }
        if (empty($this->request->data)) {
            if($wishListId) {
                $this->request->data['WishList'] = $wishData['WishList'];
            }
        } else {
            $this->WishList->create(false);
            $this->WishList->id = $wishListId;

			$this->request->data['WishList']['is_enable'] = 1;
			$this->request->data['WishList']['is_public'] = 1;
			$this->request->data['WishList']['student_user_id'] = $this->Auth->user('user_id');



			//$this->WishList->set($this->request->data);
			if($this->WishList->save($this->request->data)) {
				if($this->params['ext']) {
					return $this->success(1, array('wish_list_id'=>$this->WishList->id));
				}

                $this->set('success', true);

                if(isSet($this->request->data['appendTemplate'])) {
                    if(isSet($this->request->data['prependTo']) ) {
                        $this->set('prependTo', $this->request->data['prependTo']);
                    } else if(isSet($this->request->data['replaceWith']) ) {
                        $this->set('replaceWith', $this->request->data['replaceWith']);
                    }

                    $this->set('appendTemplate', $this->request->data['appendTemplate']);

                    //Load subject data
                    $this->WishList->recursive = -1;
                    $this->WishList->cacheQueries = false;
                    $wishData = $this->WishList->findByWishListId(($this->WishList->id));
                    $this->set('wishData', $wishData);
                }
				//$this->Session->setFlash(__('Request saved, you can browse and manage it through the control panel'));
				//$this->redirect(array('action'=>'index'));
			} else if($this->params['ext']) {
				return $this->error(1, array('validation_errors'=>$this->WishList->validationErrors));
			}
		}

        //Get subject categories
        App::Import('Model', 'Category');
        $cObj = new Category();
        $subjectCategories = $cObj->getAllCategoriesOptions();
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
    public function searchRequest() {
        $this->setJSSetting('search_load_more_url', $this->getCurrentParamsWithDifferentURL(array('controller'=>'Requests','action'=>'searchSubjectLoadMore'), array('page', 'limit')));
        $this->setJSSetting('search_suggestions_url', $this->getCurrentParamsWithDifferentURL(array('controller'=>'Requests','action'=>'subjectSearchSuggestions'), array('page', 'limit', 'term')));

		$this->Subject;
		$this->request->query['type'] = SUBJECT_TYPE_REQUEST;
		$subjectsData = $this->requestAction(array('controller'=>'Home', 'action'=>'searchSubject'), $this->request->query);
        $this->request->data = $this->request->query; //For search form
		$this->set('subjectsData', $subjectsData);
		$this->set('subjectSearchLimit', 8); //Set in homeController::searchSubject, $this->_searchDefaultQueryParams();
	}
	
	
	public function	makeOffer($wishListId=null) {

        //return $this->success(1, array('post'=>$this->request->data));
        if (!empty($this->request->data)) {
            if(isSet($this->request->data['UserLesson']['wish_list_id']) && !empty($this->request->data['UserLesson']['wish_list_id'])) {
                $wishListId = $this->request->data['UserLesson']['wish_list_id'];
            }
            //Validate
            $wishData = $this->WishList->findByWishListId( $wishListId );
            if(!$wishData || $wishData['WishList']['is_enable']==SUBJECT_IS_ENABLE_FALSE) {
                //$this->Session->setFlash(__('This subject is no longer available'));
                return $this->error(1);
            }

            //You can offer suggestions only to subject request
            $wishData = $wishData['WishList'];

            if($wishData['user_id']==$this->Auth->user('user_id')) {
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
                                                        $wishData['user_id'],
                                                        $this->Auth->user('user_id'),
                                                        null,
                                                        array('wish_list_id'=>$wishListId, 'offer_message'=>$this->request->data['UserLesson']['offer_message'])); //Send invitation

                if(!$res) {
                    return $this->error(5, array('validation_errros'=>$this->UserLesson->validationErrors));
                }
                return $this->success(1, array('results'=>array(    'user_lesson_id'=>$this->UserLesson->id,
                                                                    'subject_id'=>$this->request->data['UserLesson']['subject_id'],
                                                                    'request_subject_id'=>$wishListId,
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



            if($this->UserLesson->lessonOffer($this->request->data['UserLesson']['subject_id'], $wishListId, $datetime,
                                                array('offer_message'=>$this->request->data['UserLesson']['offer_message']))) {
                return $this->success(2, array('results'=>array(    'user_lesson_id'=>$this->UserLesson->id,
                                                                    'subject_id'=>$this->request->data['UserLesson']['subject_id'],
                                                                    'request_subject_id'=>$wishListId)));
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
        if($wishListId) {
            $this->set('requestSubjectId', $wishListId);
        }
    }

}
