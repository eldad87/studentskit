<?php
/**
 *@property Subject $Subject
 */
class StudentController extends AppController {
	public $name = 'Student';
	public $uses = array('Subject', 'WishList', 'User', 'Profile', 'TeacherLesson', 'UserLesson');
	public $components = array('Utils.FormPreserver'=>array('directPost'=>true), 'Session',  'RequestHandler'/*, 'Security'*/, 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')),/* 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');

    public function beforeFilter() {
        parent::beforeFilter();

        if($this->RequestHandler->isAjax()) {
            $this->layout = false;
        }

        $this->Auth->allow(	'turnNotificationsOff' );
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
		$this->Set('limit', 3);
	}

    public function latestUpdatedBoardPosts($limit, $page) {
        app::import('Model', 'Forum.Post');
        $postObj = new Post();
        $postObj->setLanguages($this->Session->read('languages_of_records'));
        $latestUpdatedTopics = $postObj->getGroupedLatestUpdatedTopicsByUser($this->Auth->user('user_id'), $limit, $page);
        $this->Set('limit', $limit);
        $this->Set('page', $page);
        $this->Set('latestUpdatedTopics', $latestUpdatedTopics);
    }

    public function lessons() {

    }

	public function lessonsUpcoming($limit=50, $page=1, $userLessonId=null) {
        $this->Set('limit', $limit);
        $this->Set('page', $page);
        $upcomingLessons = $this->UserLesson->getUpcoming($this->Auth->user('user_id'), $limit, $page, $userLessonId);

		return $this->success(1, array('upcomingLessons'=>$upcomingLessons));
	}
	public function lessonsBooking($limit=50, $page=1, $userLessonId=null) {
        $this->Set('limit', $limit);
        $this->Set('page', $page);
        $bookingLessons = $this->UserLesson->getBooking($this->Auth->user('user_id'), $limit, $page, $userLessonId);

		return $this->success(1, array('bookingLessons'=>$bookingLessons));
	}
	public function lessonsArchive($limit=50, $page=1) {
        $this->Set('limit', $limit);
        $this->Set('page', $page);
		$archiveLessons = $this->UserLesson->getArchive($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('archiveLessons'=>$archiveLessons));
	}
	public function lessonsInvitations($limit=50, $page=1) {
        $this->Set('limit', $limit);
        $this->Set('page', $page);
		$lessonInvitations = $this->UserLesson->getInvitations($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('lessonInvitations'=>$lessonInvitations));
	}
	public function wishList($limit=50, $page=1) {
        $this->Set('limit', $limit);
        $this->Set('page', $page);
        $this->Subject;
		$wishList = $this->WishList->getOffersByStudent($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('wishList'=>$wishList));
	}

    public function disableRequest($wishListId) {
        return $this->success(1, array('wish_list_id'=>$wishListId));
        $this->WishList->recursive = -1;
        $wishData = $this->WishList->findByWishListId($wishListId);

        if(!$wishData['WishList']['student_user_id']!=$this->Auth->user('user_Id')) {
            return $this->error(1, array('wish_list_id'=>$wishListId));
        }

        if(!$this->WishList->disable($wishListId)) {
            return $this->error(1, array('wish_list_id'=>$wishListId));
        }
        return $this->success(1, array('wish_list_id'=>$wishListId));
    }
	
	public function cancelUserLesson( $userLessonId ) {

		if(!$this->UserLesson->cancelRequest( $userLessonId, $this->Auth->user('user_id') )) {
			return $this->error(1, array('results'=>array('user_lesson_id'=>$userLessonId, 'validation_errors'=>$this->UserLesson->validationErrors)));
		}

        //Update credit points - this is ONLY needed when student accept
        $creditPoints = $this->User->getCreditPoints($this->Auth->user('user_id'));
        $userData = $this->Auth->user();
        $userData['credit_points'] = $creditPoints;
        $this->Auth->login($userData);

		return $this->success(1, array('results'=>array('user_lesson_id'=>$userLessonId, 'credit_points'=>$creditPoints)));
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

                $haveEnough = $this->UserLesson->haveEnoughTotalCreditPoints(   $this->Auth->user('user_id'),
                                                                                $maxAmount,
                                                                                $userLessonId);
                if($haveEnough!==true) {
                    $paymentPage = array('controller'=>'Order', 'action'=>'init', 'accept', $userLessonId, '?'=>array('returnURL'=>urlencode(Router::url(null, true))));

                    if(isSet($this->params['ext'])) {
                        //Redirect to order
                        return $this->error(1,  array('results'=>array('user_lesson_id'=>$userLessonId, 'orderURL'=>$paymentPage)));
                    }
                    $this->set('paymentPage', $paymentPage);
                    $this->set('paymentShortAmount', $haveEnough);
                }
            }

            //No additional payment needed
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
                    //Update credit points - this is ONLY needed when student accept
                    $creditPoints = $this->User->getCreditPoints($this->Auth->user('user_id'));
                    $this->set('creditPoints', $creditPoints);
                    $userData = $this->Auth->user();
                    $userData['credit_points'] = $creditPoints;
                    $this->Auth->login($userData);

                    if(isSet($this->request->data['removeElementAfterAccept'])) {
                        $this->set('removeElement', $this->request->data['removeElementAfterAccept']);
                    }
                    if(isSet($this->request->data['moveElementAfterAccept']) && isSet($this->request->data['moveToElementAfterAccept'])) {
                        $this->set('moveElement', $this->request->data['moveElementAfterAccept']);
                        $this->set('moveToElement', $this->request->data['moveToElementAfterAccept']);
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


            //Validate first, if valid, only then redirect (if needed) to payment page
            $valid = $this->UserLesson->reProposeRequest($userLessonId, $this->Auth->user('user_id'), $this->request->data['UserLesson'], null, false);
            if(!$valid) {
                if(isSet($this->params['ext'])) {
                    return $this->error(1, array('results'=>array('validation_errors'=>$this->UserLesson->validationErrors)));
                }
                return false;
            }

            $paymentPage = array();
            //if done by the student - Check if preapproval is OK
            if($userLessonData['UserLesson']['student_user_id']==$this->Auth->user('user_id') && ($maxAmount || $datetime)) {
                if($datetime) {
                    $datetime = mktime(($datetime['meridian']=='pm' ? $datetime['hour']+12 : $datetime['hour']), $datetime['min'], 0, $datetime['month'], $datetime['day'], $datetime['year']);
                    $datetime = $this->UserLesson->timeExpression($datetime, false);
                    $this->request->data['UserLesson']['datetime'] = $datetime;
                }


                $haveEnough = $this->UserLesson->haveEnoughTotalCreditPoints(   $this->Auth->user('user_id'),
                                                                                $maxAmount,
                                                                                $userLessonId);
                if($haveEnough!==true) {
                    /*if($isRestoredData) {
                        $this->request->data = $userLessonData; //so we won't redirect him if he comes back from the shopping cart manually in the middle of the process.
                    } else {*/

                        //Create negotiation parameters
                        $params = Security::rijndael(json_encode($this->request->data['UserLesson']), Configure::read('Security.key'), 'encrypt');

                        $paymentPage = array('controller'=>'Order', 'action'=>'init', 'negotiate', $userLessonId, '?'=>array('negotiate'=>base64_encode($params)));

                        if(isSet($this->params['ext'])) {

                            //Redirect to order
                            return $this->error(1,  array('results'=>array('user_lesson_id'=>$userLessonId, 'orderURL'=>$paymentPage)));
                        }
                        //$this->FormPreserver->preserve($this->data);
                        //$this->redirect(array('controller'=>'Order', 'action'=>'init', 'negotiate', $userLessonId, '?'=>array('negotiate'=>$params)));
                        $this->set('paymentPage', $paymentPage);
                        $this->set('paymentShortAmount', $haveEnough);
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
                    //Update credit points - this is ONLY needed when student accept
                    $creditPoints = $this->User->getCreditPoints($this->Auth->user('user_id'));
                    $this->set('creditPoints', $creditPoints);
                    $userData = $this->Auth->user();
                    $userData['credit_points'] = $creditPoints;
                    $this->Auth->login($userData);

                    //Need to refresh the tooltip info
                    /*if(isSet($this->request->data['updateTooltipAfterNegotiate'])) {
                        $this->set('updateTooltip', $this->request->data['updateTooltipAfterNegotiate']);
                        $this->set('userLessonData', $this->request->data['UserLesson']);
                    }*/
                    if(isSet($this->request->data['removeElementAfterNegotiate'])) {
                        $this->set('removeElement', $this->request->data['removeElementAfterNegotiate']);
                    }

                    if(isSet($this->request->data['updateLessonBoxAfterNegotiate'])) {
                        $this->UserLesson->cacheQueries = false;
                        $newULData = $this->UserLesson->getLessons(array(), $userLessonId);
                        $this->set('updateLessonBoxAfterNegotiate', array('element'=>$this->request->data['updateLessonBoxAfterNegotiate'],
                                                                            'data'=>$newULData));
                    }

                }
            }
        }


        //Group pricing
        if(	isSet($this->data['UserLesson']['1_on_1_price']) &&
            isSet($this->data['UserLesson']['full_group_student_price']) && !empty($this->data['UserLesson']['full_group_student_price']) &&
            isSet($this->data['UserLesson']['max_students']) && $this->data['UserLesson']['max_students']>1) {
                $groupPrice = $this->Subject->calcStudentPriceAfterDiscount(	$this->data['UserLesson']['1_on_1_price'],
                                                                                $this->data['UserLesson']['max_students'], $this->data['UserLesson']['max_students'],
                                                                                $this->data['UserLesson']['full_group_student_price']);
                $this->set('groupPrice', $groupPrice);
        }
    }

    public function turnNotificationsOff() {

        //Check that we got encoded params
        if(!isSet($this->params->query['data'])) {
            $this->redirect('/');
        }


        //Decode data
        $data = Security::rijndael( base64_decode($this->params->query['data']), Configure::READ('Security.key'), 'decrypt');
        $data = json_decode($data, true);

        //Validate
        if(!$data || !isSet($data['email']) || !isSet($data['user_id'])) {
            $this->redirect('/');
        }

        //Find user
        $userData = $this->User->find('first', array('email'=>$data['email']));
        if(!$userData || $data['user_id']!=$userData['User']['user_id']) {
            $this->Session->setFlash(__('Cannot turn off notifications.'));
            $this->redirect('/');
        }

        //Update notifications
        $this->User->create(false);
        $this->User->id = $userData['User']['user_id'];

        //This action was called from the TeacherController.turnNotificationsOff
        $notificationsFlag = (empty($this->request->params['requested']) ? 'student_receive_notification' : 'teacher_receive_notification') ;

        if(!$this->User->save(array($notificationsFlag=>0))) {
            $this->Session->setFlash(__('Internal error. Cannot turn off notifications.'));
            $this->redirect('/');
        }

        //Success
        $this->Session->setFlash(__('Notifications turned off'));
        $this->redirect('/');
    }

	public function profile() {
		if (empty($this->request->data)) {
			$this->request->data = $this->User->findByUserId($this->Auth->user('user_id'));
		} else {
            //Bug in Uploader, empty files fields
            if(isSet($this->request->data['User']['image_source']) &&
                empty($this->request->data['User']['image_source'])) {

                unset($this->request->data['User']['image_source']);
            }

            $this->User->id = $this->Auth->user('user_id');
		    $res = $this->User->save($this->request->data, true, array('first_name', 'last_name', 'phone', 'student_about', 'student_receive_notification',
                'image_source',
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

            if($res) {
                //Update Auth data
                $this->User->recursive = -1;
                $userData = $this->User->findByUserId($this->Auth->user('id'));
                $this->Auth->login($userData['User']);
            }

		}
	}
	
	public function awaitingReview() {
        $reviews = $this->UserLesson->waitingStudentReview($this->Auth->user('user_id'));
		$this->set('reviews', $reviews);
		
		$userData = $this->User->findByUserId($this->Auth->user('user_id'));
		$this->set('averageRating', $userData['User']['student_average_rating']);
	}
	public function setReview($userLessonId) {
		if (empty($this->request->data)) {
            return $this->error(1);
        }

        if(!$this->UserLesson->rate(	$userLessonId, $this->Auth->user('user_id'),
                                        $this->request->data['rating'],
                                        $this->request->data['review'])) {

            return $this->error(2, array('results'=>array('user_lesson_id'=>$userLessonId, 'validation_errors'=>$this->UserLesson->validationErrors)));
        }

        return $this->success(1);
	}
	
	public function myReviews() {
        $reviews = $this->UserLesson->getStudentReviews( $this->Auth->user('user_id'), 10 );
		$this->Set('reviews', $reviews);

        $userData = $this->User->findByUserId($this->Auth->user('user_id'));
        $this->set('averageRating', $userData['User']['student_average_rating']);
	}
}