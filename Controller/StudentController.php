<?php
/**
 *@property Subject $Subject
 */
class StudentController extends AppController {
	public $name = 'Student';
	public $uses = array('Subject', 'User', 'Profile', 'TeacherLesson', 'UserLesson', 'AdaptivePayment');
	public $components = array('Utils.FormPreserver'=>array('directPost'=>true), 'Session', 'RequestHandler', 'Security', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')),/* 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');

	public function index() {
		//Get lessons that about to start
		$upcommingLessons= $this->UserLesson->getUpcomming($this->Auth->user('user_id'), 2, 1);
					
		//Get student latest forum messages
        app::import('Model', 'Forum.Post');
        $postObj = new Post();
        $postObj->setLanguages($this->Session->read('languages_of_records'));
        $latestUpdatedTopics = $postObj->getGroupedLatestUpdatedTopicsByUser($this->Auth->user('user_id'), 3);

		//TODO: get lesson suggestions
					
		$this->Set('upcommingLessons', $upcommingLessons);
		$this->Set('latestUpdatedTopics', $latestUpdatedTopics);
	}
	
	public function lessons($limit=5, $page=1) {
		//Get lessons that about to start - upcomming
		$upcommingLessons = $this->UserLesson->getUpcomming($this->Auth->user('user_id'), $limit, $page);
		$this->Set('upcommingLessons', $upcommingLessons);

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
	}

	public function lessonsUpcoming($limit=5, $page=1) {
		$upcommingLessons = $this->UserLesson->getUpcomming($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('upcommingLessons'=>$upcommingLessons));
	}
	public function lessonsBooking($limit=5, $page=1) {
		$bookingLessons = $this->UserLesson->getBooking($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('bookingLessons'=>$bookingLessons));
	}
	public function lessonsArchive($limit=5, $page=1) {
		$archiveLessons = $this->UserLesson->getArchive($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('archiveLessons'=>$archiveLessons));
	}
	public function lessonsInvitations($limit=5, $page=1) {
		$lessonInvitations = $this->UserLesson->getInvitations($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('lessonInvitations'=>$lessonInvitations));
	}
	public function subjectRequests($limit=5, $page=1) {
		$subjectRequests = $this->Subject->getOffersByStudent($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('subjectRequests'=>$subjectRequests));
	}
	
	public function cacnelUserLesson( $userLessonId ) {
		if(!$this->UserLesson->cancelRequest( $userLessonId, $this->Auth->user('user_id') )) {
			return $this->error(1, array('user_lesson_id'=>$userLessonId));
		}
		
		return $this->success(1, array('user_lesson_id'=>$userLessonId));
	}
	
	public function acceptUserLesson( $userLessonId ) {
        //TODO: force POST

        $userLessonData = $this->UserLesson->findByUserLessonId($userLessonId);

        //if done by the student - Check if preapproval is OK
        if($userLessonData['UserLesson']['student_user_id']==$this->Auth->user('user_id')) {
            $maxAmount = (isSet($userLessonData['UserLesson']['1_on_1_price']) ? $userLessonData['UserLesson']['1_on_1_price'] : null );
            $datetime = (isSet($userLessonData['UserLesson']['datetime']) ? $userLessonData['UserLesson']['datetime'] : null );
            if(!$this->AdaptivePayment->isValidApproval($userLessonId, $maxAmount, $datetime)) {
                if(isSet($this->params['ext'])) {

                    //Redirect to order
                    return $this->error(1, array('orderURL'=>array('controller'=>'Order', 'action'=>'init', 'accept', $userLessonId, '?'=>array('returnURL'=>urlencode(Router::url(null, true))))));
                }
                $this->redirect(array('controller'=>'Order', 'action'=>'init', 'accept', $userLessonId));
            }
        }

		if(!$this->UserLesson->acceptRequest( $userLessonId, $this->Auth->user('user_id') )) {
			return $this->error(1, array('user_lesson_id'=>$userLessonId));
		}
		return $this->success(1, array('user_lesson_id'=>$userLessonId));
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
        if (empty($this->request->data)) {
            $this->request->data = $userLessonData;
        } else {

            $maxAmount = (isSet($this->request->data['UserLesson']['1_on_1_price']) ? $this->request->data['UserLesson']['1_on_1_price'] : null );
            $datetime = (isSet($this->request->data['UserLesson']['datetime']) ? $this->request->data['UserLesson']['datetime'] : null );

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


                        if(isSet($this->params['ext'])) {

                            //Redirect to order
                            return $this->error(1, array('orderURL'=>array('controller'=>'Order', 'action'=>'init', 'negotiate', $userLessonId, '?'=>array('negotiate'=>$params))));
                        }
                        //$this->FormPreserver->preserve($this->data);
                        $this->redirect(array('controller'=>'Order', 'action'=>'init', 'negotiate', $userLessonId, '?'=>array('negotiate'=>$params)));
                    //}
                }
            }

            if(!$this->UserLesson->reProposeRequest($userLessonId, $this->Auth->user('user_id'), $this->request->data['UserLesson'])) {
                if(isSet($this->params['ext'])) {
                    return $this->error(1, array('validation_errors'=>$this->UserLesson->validationErrors));
                }
                $this->Session->setFlash(__('Error, cannot Re-Propose'));
            }

            if(isSet($this->params['ext'])) {
                return $this->success(1, array('user_lesson_id'=>$userLessonId));
            }

            $this->Session->setFlash(__('Re-Propose sent'));
        }

        //Group pricing
        if(	isSet($this->data['UserLesson']['1_on_1_price']) &&
            isSet($this->data['UserLesson']['full_group_total_price']) && !empty($this->data['UserLesson']['full_group_total_price']) &&
            isSet($this->data['UserLesson']['max_students']) && $this->data['UserLesson']['max_students']>1) {
            $groupPrice = $this->Subject->calcGroupPrice(	$this->data['UserLesson']['1_on_1_price'], $this->data['UserLesson']['full_group_total_price'],
                $this->data['UserLesson']['max_students'], $this->data['UserLesson']['max_students']);
            $this->set('groupPrice', $groupPrice);
        }
    }
	
	public function profile() {
		if (empty($this->request->data)) {
			$this->request->data = $this->User->findByUserId($this->Auth->user('user_id'));
		} else {
			  $this->User->set($this->request->data);
			  $this->User->save();
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