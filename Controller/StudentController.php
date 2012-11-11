<?php
/**
 *@property Subject $Subject
 */
class StudentController extends AppController {
	public $name = 'Student';
	public $uses = array('Subject', 'User', 'Profile', 'TeacherLesson', 'UserLesson', 'AdaptivePayment');
	public $components = array('Utils.FormPreserver'=>array('directPost'=>true), 'Session',  'RequestHandler'/*, 'Security'*/, 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')),/* 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');

    public function beforeFilter() {
        parent::beforeFilter();

        if($this->RequestHandler->isAjax()) {
            $this->layout = false;
        }


    }

	public function index() {
		//Get lessons that about to start
		$upcomingLessons = $this->UserLesson->getUpcoming($this->Auth->user('user_id'), 2, 1);


		//Get student latest forum messages
        app::import('Model', 'Forum.Post');
        $postObj = new Post();
        $postObj->setLanguages($this->Session->read('languages_of_records'));
        $latestUpdatedTopics = $postObj->getGroupedLatestUpdatedTopicsByUser($this->Auth->user('user_id'), 3);



		//TODO: get lesson suggestions
					
		$this->Set('upcomingLessons', $upcomingLessons);
		$this->Set('latestUpdatedTopics', $latestUpdatedTopics);
	}

    public function lessons() {

    }
	/*public function lessons($limit=5, $page=1) {
		//Get lessons that about to start - upcoming
		$upcomingLessons = $this->UserLesson->getUpcoming($this->Auth->user('user_id'), $limit, $page);
		$this->Set('upcomingLessons', $upcomingLessons);

        //Get lessons that are over - archive
        $archiveLessons = $this->UserLesson->getArchive($this->Auth->user('user_id'), $limit, $page);
        $this->Set('archiveLessons', $archiveLessons);

		//Get lessons that pending for teacher approval - booking requests
		$bookingRequests = $this->UserLesson->getBooking($this->Auth->user('user_id'), $limit, $page);
		$this->Set('bookingRequests', $bookingRequests);
		
		//Get lessons invitations - invitations
		$lessonInvitations = $this->UserLesson->getInvitations($this->Auth->user('user_id'), $limit, $page);
		$this->Set('lessonInvitations', $lessonInvitations);
		
		//Get lesson requests - lesson offers
        $subjectRequests = $this->Subject->getOffersByStudent($this->Auth->user('user_id'), $limit, $page);
		$this->Set('subjectRequests', $subjectRequests);
	}*/

	public function lessonsUpcoming($limit=50, $page=1) {
        $upcomingLessons = $this->UserLesson->getUpcoming($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('upcomingLessons'=>$upcomingLessons));
	}
	public function lessonsBooking($limit=50, $page=1) {
		$bookingLessons = $this->UserLesson->getBooking($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('bookingLessons'=>$bookingLessons));
	}
	public function lessonsArchive($limit=50, $page=1) {
		$archiveLessons = $this->UserLesson->getArchive($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('archiveLessons'=>$archiveLessons));
	}
	public function lessonsInvitations($limit=50, $page=1) {
		$lessonInvitations = $this->UserLesson->getInvitations($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('lessonInvitations'=>$lessonInvitations));
	}
	public function subjectRequests($limit=50, $page=1) {
		$subjectRequests = $this->Subject->getOffersByStudent($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('subjectRequests'=>$subjectRequests));
	}
	
	public function cancelUserLesson( $userLessonId ) {

		if(!$this->UserLesson->cancelRequest( $userLessonId, $this->Auth->user('user_id') )) {
			return $this->error(1, array('results'=>array('user_lesson_id'=>$userLessonId, 'validation_errors'=>$this->UserLesson->validationErrors)));
		}
		
		return $this->success(1, array('results'=>array('user_lesson_id'=>$userLessonId)));
	}
	
	public function acceptUserLesson( $userLessonId ) {
        //force POST

        $userLessonData = $this->UserLesson->findByUserLessonId($userLessonId);
        $this->set('lessonType', $userLessonData['UserLesson']['lesson_type']);
        $this->set('userLessonData', $userLessonData['UserLesson']);

        if($this->RequestHandler->isPost()) {
            $paymentPage = array();


            //if done by the student - Check if preapproval is OK
            if($userLessonData['UserLesson']['student_user_id']==$this->Auth->user('user_id')) {
                $maxAmount = (isSet($userLessonData['UserLesson']['1_on_1_price']) ? $userLessonData['UserLesson']['1_on_1_price'] : null );
                $datetime = (isSet($userLessonData['UserLesson']['datetime']) ? $userLessonData['UserLesson']['datetime'] : null );
                if(!$this->AdaptivePayment->isValidApproval($userLessonId, $maxAmount, $datetime)) {
                    $paymentPage = array('controller'=>'Order', 'action'=>'init', 'accept', $userLessonId, '?'=>array('returnURL'=>urlencode(Router::url(null, true))));

                    if(isSet($this->params['ext'])) {
                        //Redirect to order
                        return $this->error(1,  array('results'=>array('user_lesson_id'=>$userLessonId, 'orderURL'=>$paymentPage)));
                    }
                    $this->set('paymentPage', $paymentPage);
                }
            }

            //No payment needed
            if(!$paymentPage) {

                if(!$this->UserLesson->acceptRequest( $userLessonId, $this->Auth->user('user_id') )) {

                    if(isSet($this->params['ext'])) {
                        return $this->error(1, array('results'=>array('user_lesson_id'=>$userLessonId, 'validation_errors'=>$this->UserLesson->validationErrors)));
                    }

                    $this->set('error', true);
                } else {

                    if(isSet($this->params['ext'])) {
                        //Redirect to order
                        return $this->success(1, array('results'=>array('user_lesson_id'=>$userLessonId)));
                    }

                    //Success
                    $this->set('success', true);

                    if(isSet($this->request->data['removeElementAfterAccept'])) {
                        $this->set('removeElement', $this->request->data['removeElementAfterAccept']);
                    }
                }
            }
        }
	}

    public function reProposeRequest($userLessonId) {
        //TODO: force POST

        /*//Restore only if referrer is "status" from order
        $referrer = Router::parse($this->referer(null, true));
        if(strtolower($referrer['controller'])=='order' && strtolower($referrer['action'])=='status') {
            //Fore restore - when users return from paypal their request will apply now
            $isRestoredData = $this->FormPreserver->restore();
        }*/



        $userLessonData = $this->UserLesson->findByUserLessonId($userLessonId);
        $this->set('lessonType', $userLessonData['UserLesson']['lesson_type']);
        if (empty($this->request->data)) {
            $this->request->data = $userLessonData;
        } else {

            $maxAmount = (isSet($this->request->data['UserLesson']['1_on_1_price']) ? $this->request->data['UserLesson']['1_on_1_price'] : null );
            $datetime = (isSet($this->request->data['UserLesson']['datetime']) ? $this->request->data['UserLesson']['datetime'] : null );


            $paymentPage = array();
            //if done by the student - Check if preapproval is OK
            if($userLessonData['UserLesson']['student_user_id']==$this->Auth->user('user_id') && ($maxAmount || $datetime)) {
                if($datetime) {
                    $datetime = mktime(($datetime['meridian']=='pm' ? $datetime['hour']+12 : $datetime['hour']), $datetime['min'], 0, $datetime['month'], $datetime['day'], $datetime['year']);
                    $datetime = $this->UserLesson->timeExpression($datetime, false);
                    $this->request->data['UserLesson']['datetime'] = $datetime;
                }

                if(!$this->AdaptivePayment->isValidApproval($userLessonId, $maxAmount, $datetime)) {
                    /*if($isRestoredData) {
                        $this->request->data = $userLessonData; //so we won't redirect him if he comes back from the shopping cart manually in the middle of the process.
                    } else {*/

                        //Create negotiation parameters
                        $params = Security::rijndael(json_encode($this->request->data['UserLesson']), Configure::read('Security.key'), 'encrypt');

                        $paymentPage = array('controller'=>'Order', 'action'=>'init', 'negotiate', $userLessonId, '?'=>array('negotiate'=>$params));

                        if(isSet($this->params['ext'])) {

                            //Redirect to order
                            return $this->error(1,  array('results'=>array('user_lesson_id'=>$userLessonId, 'orderURL'=>$paymentPage)));
                        }
                        //$this->FormPreserver->preserve($this->data);
                        //$this->redirect(array('controller'=>'Order', 'action'=>'init', 'negotiate', $userLessonId, '?'=>array('negotiate'=>$params)));
                        $this->set('paymentPage', $paymentPage);
                    //}
                }
            }

            //No additional payment is needed
            if(!$paymentPage) {
                if(!$this->UserLesson->reProposeRequest($userLessonId, $this->Auth->user('user_id'), $this->request->data['UserLesson'])) {
                    if(isSet($this->params['ext'])) {
                        return $this->error(1, array('results'=>array('validation_errors'=>$this->UserLesson->validationErrors)));
                    }
                    //$this->Session->setFlash(__('Error, cannot Re-Propose'));
                    $this->set('error', true);
                } else {

                    if(isSet($this->params['ext'])) {
                        return $this->success(1, array('results'=>array('user_lesson_id'=>$userLessonId)));
                    }
                    //$this->Session->setFlash(__('Re-Propose sent'));

                    //Success
                    $this->set('success', true);

                    //Need to refresh the tooltip info
                    if(isSet($this->request->data['updateTooltipAfterNegotiate'])) {
                        $this->set('updateTooltip', $this->request->data['updateTooltipAfterNegotiate']);
                        $this->set('userLessonData', $this->request->data['UserLesson']);
                    }
                    if(isSet($this->request->data['removeElementAfterNegotiate'])) {
                        $this->set('removeElement', $this->request->data['removeElementAfterNegotiate']);
                    }


                }
            }
        }


        //Group pricing
        if(	isSet($this->data['UserLesson']['1_on_1_price']) &&
            isSet($this->data['UserLesson']['full_group_student_price']) && !empty($this->data['UserLesson']['full_group_student_price']) &&
            isSet($this->data['UserLesson']['max_students']) && $this->data['UserLesson']['max_students']>1) {
                /*$groupPrice = $this->Subject->calcStudentFullGroupPrice(	$this->data['UserLesson']['1_on_1_price'], $this->data['UserLesson']['full_group_total_price'],
                                                                            $this->data['UserLesson']['max_students'], $this->data['UserLesson']['max_students']);*/
                $groupPrice = $this->Subject->calcStudentPriceAfterDiscount(	$this->data['UserLesson']['1_on_1_price'],
                                                                                $this->data['UserLesson']['max_students'], $this->data['UserLesson']['max_students'],
                                                                                $this->data['UserLesson']['full_group_student_price']);
                $this->set('groupPrice', $groupPrice);
        }
    }
	
	public function profile() {
		if (empty($this->request->data)) {
			$this->request->data = $this->User->findByUserId($this->Auth->user('user_id'));
		} else {
            $this->User->id = $this->Auth->user('user_id');
		    $this->User->save($this->request->data, true, array('first_name', 'last_name', 'phone', 'student_about',
                'imageUpload',
                'image',
                'image_source',
                'image_resize',
                'image_crop_38x38',
                'image_crop_60x60',
                'image_crop_63x63',
                'image_crop_72x72',
                'image_crop_78x78',
                'image_crop_80x80',
                'image_crop_100x100',
                'image_crop_149x182',
                'image_crop_200x210'));
		}
	}
	
	public function awaitingReview() {
		$awaitingReviews = $this->UserLesson->waitingStudentReview($this->Auth->user('user_id'));
		$this->set('awaitingReviews', $awaitingReviews);
		
		$userData = $this->User->findByUserId($this->Auth->user('user_id'));
		$this->set('studentAvarageRating', $userData['User']['student_avarage_rating']);
	}
	public function setReview($userLessonId) {
		if (!empty($this->request->data)) {
			if($this->UserLesson->rate(	$userLessonId, $this->Auth->user('user_id'), 
			  							$this->request->data['UserLesson']['rating_by_student'], 
			  							$this->request->data['UserLesson']['comment_by_student'])) {
				$this->redirect(array('action'=>'awaitingReview'));
			}
			 
		}
		$setReview = $this->UserLesson->getLessons(array('student_user_id'=>$this->Auth->user('user_id')), $userLessonId);
		$this->Set('setReview', $setReview);
	}
	
	public function myReviews() {
		//Get students comments for that teacher
		$studentReviews = $this->UserLesson->getStudentReviews( $this->Auth->user('user_id'), 10 );
		$this->Set('studentReviews', $studentReviews);
	}
}