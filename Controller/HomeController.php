<?php
/**
 *@property Subject $Subject
 */
class HomeController extends AppController {
	public $name = 'Home';
	public $uses = array('Subject', 'User', 'Profile', 'TeacherLesson', 'UserLesson');
	public $components = array('Utils.FormPreserver'=>array('directPost'=>true,'actions'=>array('submitOrder')), 'Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')), 'Security');
	//public $helpers = array('Form', 'Html', 'Js', 'Time');


	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(	'index', 'searchSubject', 'subjectSuggestions', 'teacherSubject', 'teacher', 'subject', 'order',
							'getTeacherRatingByStudentsForSubject', 'getTeacherSubjects', 'getTeacherRatingByStudents', 'getOtherTeachersForSubject', 'getUserLessons', 'cleanSession'/*,
                            'test'*/);
		$this->Auth->deny('submitOrder');
	}

        public function testRoute() {
        $orderURL = array('controller'=>'Order', 'action'=>'init', 'negotiate', 1, '?'=>array('returnURL'=>urlencode(Router::url(null, true))));
        pr(Router::url($orderURL, true));
        pr($orderURL); die;
    }

    public function cleanSession() {
        $this->Session->destroy();
    }

	public function index() {
		//Get about to start messages
        $this->Subject->setLanguages($this->Session->read('languages_of_records'));
		$newSubjects = $this->Subject->getNewest(false);

        app::import('Model', 'Forum.Topic');
        $topicObj = new Topic();
        $topicObj->setLanguages($this->Session->read('languages_of_records'));
        $latestTopics = $topicObj->getLatest(5);

		$this->set('newSubjects', $newSubjects);
		$this->set('latestTopics', $latestTopics);

        /*
       App::uses('Languages', 'Utils.Lib');
       $lang = new Languages();
       $this->set('languages', $lang->lists('locale'));

       App::uses('Timezone', 'Lib');
       $tz = new Timezone();
       $this->set('timezones', $tz->getList());
        */
    }

    /*public function testCalcGroupPrice() {
        $currentStudents = 7;
        pr($this->Subject->calcGroupPrice(2, 20, 10, $currentStudents));
        pr($this->Subject->calcGroupPrice(2, 20, 10, $currentStudents)*$currentStudents);
        die;
    }

    public function testPreapprovalRequest() {
        $userLessonId   = 8;
        $customerId     = 1;
        $amount         = 10;
        $cancelUrl = Router::url(array('controller'=>'Home', 'action'=>'cancelUserLesson', $userLessonId), true);
        $returnUrl = Router::url(array('controller'=>'Home', 'action'=>'orderApproval', $userLessonId), true);


        app::import('Model', 'AdaptivePayment');
        $ap = new AdaptivePayment();
        $url = $ap->getPreApprovalURL($userLessonId, $cancelUrl, $returnUrl, $this->request->clientIp());
        pr($url);
        die;

        App::import('Vendor', 'AdaptivePayments'.DS.'AdaptivePayments');
        $ap = new AdaptivePayments();




        $response = $ap->preapproval( $amount, $customerId, $this->request->clientIp(), $cancelUrl, $returnUrl );
        pr($response);
        pr($response->responseEnvelope->ack);
        pr($response->preapprovalKey);
        pr('https://www.sandbox.paypal.com/webscr&cmd=_ap-preapproval&preapprovalkey='.$response->preapprovalKey);
        pr(Router::url(array('controller'=>'Home', 'action'=>'testPaymentDetails', $response->preapprovalKey), true));
        die;
    }

    public function testPaymentDetails( $preapprovalKey ) {
        App::import('Vendor', 'AdaptivePayments'.DS.'AdaptivePayments');
        $ap = new AdaptivePayments();
        $response = $ap->preapprovalDetails($preapprovalKey);
        pr($response);
        die;
    }
    public function testConfirmPreapproval( $preapprovalKey ) {
        App::import('Vendor', 'AdaptivePayments'.DS.'AdaptivePayments');
        $ap = new AdaptivePayments();
        $response = $ap->confirmPreapproval($preapprovalKey);
        pr($response);
        die;
    }*/
    /*public function testCancelPreapproval( $preapprovalKey ) {
        App::import('Vendor', 'AdaptivePayments'.DS.'AdaptivePayments');
        $ap = new AdaptivePayments();
        $response = $ap->cancelPreapproval($preapprovalKey);
        pr($response);
        pr($response->responseEnvelope->ack);
        die;
    }*/

    public function testPay( $preapprovalKey ) {
        App::import('Vendor', 'AdaptivePayments'.DS.'AdaptivePayments');
        $ap = new AdaptivePayments();

        $userLessonId   = time();
        $cancelUrl = Router::url(array('controller'=>'Home', 'action'=>'cancelUserLesson', $userLessonId), true);
        $returnUrl = Router::url(array('controller'=>'Home', 'action'=>'orderApproval', $userLessonId), true);

        $receivers = array();
        $receivers[] = array(
            'email'         =>'seller_1345633766_biz@gmail.com',
            'amount'        =>1,
            'paymentType'   =>'DIGITALGOODS',
            'primary'       =>true,
        );
        $receivers[] = array(
            'email'         =>'caller_1345633979_biz@gmail.com',
            'amount'        =>0.5,
            'paymentType'   =>'DIGITALGOODS',
            'primary'       =>false,
        );


        $response = $ap->pay( $receivers, $userLessonId, $preapprovalKey, $cancelUrl, $returnUrl );
        pr($response);
        pr($response->paymentExecStatus);
        die;
    }

    /*public function testForumMessages() {
        app::import('Model', 'Forum.Topic');
        $topicObj = new Topic();
        $topicObj->Post->setLanguages($this->Session->read('languages_of_records'));
        pr($topicObj->Post->getGroupedLatestByUser(4, 2));
        //die;
    }*/

    /*public function testAddCategory() {
        App::import('Model', 'SubjectCategory');
        $scObj = new SubjectCategory();


       $scObj->create();
        $scObj->set(array('name'=>'Spirituality', 'description'=>'about spirituality'));
        $scObj->save();
        $id = $scObj->id;


        $scObj->create();
        $scObj->locale = 'heb'; // we are going to save the german version
        $scObj->id = $id;
        $scObj->save(array('name'=>'רוחניות', 'description'=>'אודות רוחניות'));

        $scObj->locale = 'he_he';
        pr($scObj->find('all'));

        /*$scObj->create();
        $scObj->set(array('name'=>'Spirituality', 'description'=>'about spirituality'));
        $scObj->save();
        $id = $scObj->id;

            $scObj->create();
            $scObj->set(array('name'=>'Astrology', 'description'=>'Astrology', 'parent_subject_category_id'=>$id));
            $scObj->save();
            $id2 = $scObj->id;

        $scObj->create();
        $scObj->set(array('name'=>'Chinese Astrology', 'description'=>'Chinese Astrology', 'parent_subject_category_id'=>$id2));
        $scObj->save();

        $scObj->create();
        $scObj->set(array('name'=>'Vedic Astrology', 'description'=>'Vedic Astrology', 'parent_subject_category_id'=>$id2));
        $scObj->save();

    $scObj->create();
    $scObj->set(array('name'=>'Graphology', 'description'=>'Graphology', 'parent_subject_category_id'=>$id));
    $scObj->save();



$scObj->create();
$scObj->set(array('name'=>'Computers', 'description'=>'Computers'));
$scObj->save();
$id = $scObj->id;

    $scObj->create();
    $scObj->set(array('name'=>'Applications', 'description'=>'Applications', 'parent_subject_category_id'=>$id));
    $scObj->save();
    $id2 = $scObj->id;

        $scObj->create();
        $scObj->set(array('name'=>'CAD', 'description'=>'CAD', 'parent_subject_category_id'=>$id2));
        $scObj->save();

        $scObj->create();
        $scObj->set(array('name'=>'SAP', 'description'=>'SAP', 'parent_subject_category_id'=>$id2));
        $scObj->save();

    $scObj->create();
    $scObj->set(array('name'=>'Databases', 'description'=>'Databases', 'parent_subject_category_id'=>$id));
    $scObj->save();
    $id2 = $scObj->id;

        $scObj->create();
        $scObj->set(array('name'=>'MySQL', 'description'=>'MySQL', 'parent_subject_category_id'=>$id2));
        $scObj->save();

        $scObj->create();
        $scObj->set(array('name'=>'NoSQL', 'description'=>'NoSQL', 'parent_subject_category_id'=>$id2));
        $scObj->save();
    }*/
    /*public function test() {
        App::import('Model', 'Notification');
        $notificationObj = new Notification();

        $notificationObj->addNotification(4, array('message_enum'=>'teacher.subject.request.offer.sent', 'params'=>array('teacher_user_id'=>4, 'student_user_id'=>5 , 'name'=>'lesson name', 'datetime'=>'10/2/87')));
        $notificationObj->addNotification(4, array('message_enum'=>'teacher.subject.request.offer.sent', 'params'=>array('teacher_user_id'=>4, 'student_user_id'=>5 , 'name'=>'lesson name', 'datetime'=>'10/2/87')));
        $notificationObj->addNotification(4, array('message_enum'=>'teacher.subject.request.offer.sent', 'params'=>array('teacher_user_id'=>4, 'student_user_id'=>5 , 'name'=>'lesson name', 'datetime'=>'10/2/87')));
    }*/

	public function searchSubject() {
        $query = $this->_searchDefaultQueryParams();

        //Search
        $subjectType = (isSet($this->request->query['type']) ? $this->request->query['type'] : SUBJECT_TYPE_OFFER);
        $subjectsData = $this->Subject->search($query, $subjectType);


        App::Import('Model', 'SubjectCategory');
        $scObj = new SubjectCategory();

        //Generate sub categories from facet
        if(isSet($subjectsData['facet']['name']) && $subjectsData['facet']['name']=='categories') {
            $categoryIds = array(); //Hold all ids
            $categories = array(); //Hold final results

            //Generate array(subject_category_id, count) for each category
            foreach($subjectsData['facet']['results'] AS $path=>$count) {
                $category = explode(',', $path);
                $categoryId = end($category);
                $categoryIds[] = $categoryId;
                $categories[$categoryId] = array('subject_category_id'=>$categoryId, 'count'=>$count);
            }


            //Add category name
            $foundCategories = $scObj->find('list', array('conditions'=>array('subject_category_id'=>$categoryIds), 'fields'=>array('subject_category_id', 'name')));
            foreach($foundCategories AS $subjectCategoryId=>$name) {
                $categories[$subjectCategoryId]['name'] = $name;
            }
            $subjectsData['categories'] = $categories;
        }

        //Add breadcrumbs
        $subjectsData['breadcrumbs'] = array();
        if(isSet($this->request->query['category_id'])) {
            $scData = $scObj->findBySubjectCategoryId($this->request->query['category_id']);
            $scData = $scData['SubjectCategory'];

            if(!empty($scData['path'])); {
                $subjectsData['breadcrumbs'] = $scObj->find('list', array('fields'=>array('subject_category_id', 'name'), 'conditions'=>array('subject_category_id'=>explode(',', $scData['path']))));
            }
            $subjectsData['breadcrumbs'][$this->request->query['category_id']] = $scData['name'];
        }


		if(isSet($this->params['ext'])) {
			$data = array();
			foreach($subjectsData['subjects'] AS &$subj) {
				$data['subjects']['subject'][] = $subj['Subject'];
			}
            if(isSet($subjectsData['facet'])) {
                $data['facet'] = $subjectsData['facet'];
            }
			return $this->success(1, array('results'=>$data));
		} else {
			if (empty($this->request->params['requested'])) {
                $this->request->data = $this->request->query; //For search form
				$this->set('subjectsData', $subjectsData);
			} else {
				return $subjectsData;
			}
		}
	}

    //http://studentskit/Home/subjectSuggestions.json?search_terms=for%20the%20d
    public function subjectSuggestions() {


        $query = $this->_searchDefaultQueryParams();

        $subjectType = (isSet($this->request->query['type']) ? $this->request->query['type'] : SUBJECT_TYPE_OFFER);
        $results = $this->Subject->searchSuggestions($query, $subjectType);


        if (empty($this->request->params['requested'])) {
            return $this->success(1, array('results'=>$results));
        } else {
            return $results;
        }
    }

    private function _searchDefaultQueryParams() {
        if (!empty($this->request->params['requested'])) {
            $this->request->query = $this->params->named;
        }


        $this->Subject; //For loading the const
        $searchTerms = !empty($this->request->query['search_terms'])        ? $this->request->query['search_terms']             : '*';

        $categoryId  = (isSet($this->request->query['category_id'])         ? $this->request->query['category_id']	            : 0);
        $limit       = (isSet($this->request->query['limit']) 			    ? $this->request->query['limit']		            : 6);
        $page        = (isSet($this->request->query['page']) 			    ? $this->request->query['page']		                : 1);
        $language    = (isSet($this->request->query['languages_of_records'])? $this->request->query['languages_of_records'] 	:
                        ($this->Session->read('languagesOfRecords')         ? $this->Session->read('languagesOfRecords')        : null));

        $lessonType = array();
        if(isSet($this->request->query['lesson_type_video'])) {
            $lessonType[]  = LESSON_TYPE_VIDEO;
        }
        if(isSet($this->request->query['lesson_type_live'])) {
            $lessonType[]  = LESSON_TYPE_LIVE;
        }

        $query = array(
            'search'=>$searchTerms,
            'fq'=>array('is_public'=>SUBJECT_IS_PUBLIC_TRUE),
            'page'=>$page,
            'limit'=>$limit
        );
        if(!is_null($categoryId)) {
            $query['fq']['category_id'] = $categoryId;
        }
        if($language && (is_array($language) || $language!='all')) {
            $query['fq']['language'] = '('.implode(' OR ',$language).')';
        }
        if($lessonType) {
            $query['fq']['lesson_type'] = '('.implode(' OR ',$lessonType).')';
        }
        return $query;
    }



	public function teacherSubject($subjectId) {
		//Get subject data, students_amount, raters_amount, avarage_rating
		$subjectData = $this->Subject->findBySubjectId( $subjectId );
		if(!$subjectData || $subjectData['Subject']['is_enable']==SUBJECT_IS_ENABLE_FALSE) {
			
			if (!$this->RequestHandler->isAjax()) {
				$this->Session->setFlash(__('Cannot view this subject'));
				$this->redirect($this->referer());
			}
			return false;
		}
		$subjectData = $subjectData['Subject'];

		//$totalTeachingTime = $subjectData['students_amount'] * $subjectData['duration_minutes'];
		
		//Get students comments for that subject
		$subjectRatingByStudents = $this->Subject->getRatingByStudents( $subjectId, 2 );
		
		//Get teacher other subjects
        $this->Subject->setLanguages($this->Session->read('languages_of_records'));
		$teacherOtherSubjects = $this->Subject->getOffersByTeacher( $subjectData['user_id'], false, null, 1, 6, null, $subjectId );

		//Get teacher data
		$teacherData = $this->User->findByUserId( $subjectData['user_id'] );
		if(!$teacherData) {
			if (!$this->RequestHandler->isAjax()) {
				$this->Session->setFlash(__('Internal error'));
				$this->redirect($this->referer());
			}
			return false;
		}


		//Get other teacher's subjects same as this one
		if(!empty($subjectData['category_id'])) {
            if(!$lor = $this->Session->read('languagesOfRecords')) {
                $lor = array();
            }
            if(!in_array($subjectData['language'],$lor)) {
                $lor[] = $subjectData['language'];
            }
            $query = array(
                'search'=>$subjectData['name'],
                'fq'=>array('is_public'=>SUBJECT_IS_PUBLIC_TRUE, 'category_id'=>$subjectData['category_id'], 'language'=>$lor),
                'page'=>1,
                'limit'=>6
            );
           $otherTeacherForThisSubject = $this->Subject->search($query, $subjectData['type']);
           $this->set('otherTeacherForThisSubject', $otherTeacherForThisSubject);
        }
        /*if($subjectData['catalog_id']) {
			$otherTeacherForThisSubject = $this->Subject->getbyCatalog( $subjectData['catalog_id'], $subjectId, 6 );
			$this->set('otherTeacherForThisSubject', $otherTeacherForThisSubject);
		}*/

        //TODO: video lesson - check if there is a request pending/approved



        $this->set('showOrderButton', true);


        //Video lesson, show order button if no request already made
        //else show play button
        if($subjectData['lesson_type']=='video') {
            $this->set('showVideoPlayButton', false);

            $canWatchVideo = $this->UserLesson->getVideoLessonStatus($subjectId, $this->Auth->user('user_id'), false);
            if(!$canWatchVideo) {
                $this->redirect('/');
            }

            if($canWatchVideo['payment_needed']) {
                $this->set('showOrderButton', true);
                $this->set('showVideoPlayButton', false);
            } else if($canWatchVideo['pending_teacher_approval']) {
                //TODO: Show message on the page "pending for teacher approval, please come back later"
                $this->set('showOrderButton', false);
                $this->set('showVideoPlayButton', false);
            } else if($canWatchVideo['pending_user_approval']) {
                //TODO: Show message on the page "pending for your approval, click <a>here</a> to review it in your panel"
                $this->set('showOrderButton', false);
                $this->set('showVideoPlayButton', false); //TODO: onclick - open approval popup (dialog)
            } else if($canWatchVideo['show_video']) {
                $this->set('showOrderButton', false);
                $this->set('showVideoPlayButton', true);
            }
        }

        //showOrderButton
        //showVideoPlayButton
		$this->set('subjectData', 				$subjectData);
		$this->set('subjectRatingByStudents', 	$subjectRatingByStudents);
		$this->set('teacherOtherSubjects', 		$teacherOtherSubjects);
		$this->set('teacherUserData', 			$teacherData['User']);
	}

   /* public function canWatchVideo($subjectId) {
        $canWatchVideo = $this->UserLesson->getVideoLessonStatus($subjectId, $this->Auth->user('user_id'), true);
        if(!$canWatchVideo) {
            return $this->error(1);
        }

        if($canWatchVideo['payment_needed']) {
            return $this->success(1, array('url'=>Router::url(array('controller'=>'Home', 'action'=>'order', $subjectId), true)));
        } else if($canWatchVideo['pending_teacher_approval']) {
            return $this->success(2);
        } else if($canWatchVideo['pending_user_approval']) {
            //TODO: if payment approval is set, then auto approve it and show the video
            return $this->success(3, array('url'=>Router::url(array('controller'=>'Student', 'action'=>'lessons', 'tab'=>'invitations', $canWatchVideo['user_lesson_id']), true)));
        } else if($canWatchVideo['show_video']) {
            return $this->success(4, array('url'=>Router::url(array('controller'=>'Lessons', 'action'=>'video', $subjectId), true)));
        }

        return $this->error(2);
    }*/

    //Only for join to live lessons
    public function teacherLesson($teacherLessonId) {
        $this->TeacherLesson->recursive = -1;
        $teacherLessonData = $this->TeacherLesson->findByTeacherLessonId($teacherLessonId);
        if(!$teacherLessonData || !$teacherLessonData['Subject']['is_enable']) {
            $this->redirect('/');
        } else if ($teacherLessonData['lesson_type']!='live' || $teacherLessonData['TeacherLesson']['is_deleted']) {
            //redirect to subject page
            $this->redirect($this->referer(array('controller'=>'Home', 'action'=>'teacherSubject', $teacherLessonData['subject_id'])));
        }
        $teacherLessonData = $teacherLessonData['TeacherLesson'];


        $this->set('showPayment', false);
        $this->set('showPendingTeacherApproval', false);
        $this->set('showPendingUserApproval', false);
        $this->set('showLessonPage', false);

        //Get the lesson status
        $liveRequestStatus = $this->UserLesson->getLiveLessonStatus($teacherLessonId, $this->Auth->user('user_id'));
        if($liveRequestStatus['overdue']) {
            $this->Session->setFlash(__('Lesson is overdue'));
            $this->redirect(array('controller'=>'Home', 'action'=>'teacherSubject', $teacherLessonData['subject_id']));
        } else if($liveRequestStatus['payment_needed']) {
            $this->set('showPayment', true);
        } else if($liveRequestStatus['pending_teacher_approval']) {
            //TODO: Show message on the page "pending for teacher approval, please come back later"
            $this->set('showPendingTeacherApproval', true);
        } else if($liveRequestStatus['pending_user_approval']) {
            //TODO: Show message on the page "pending for your approval, click <a>here</a> to review it in your panel"
            $this->set('showPendingUserApproval', true);
        } else {
            $this->set('showLessonPage', true);
        }

        $this->set('teacherLessonData', $teacherLessonData);
    }

	public function getOtherTeachersForSubject($subjectId, $limit=6, $page=1) {
		$subjectData = $this->Subject->findBySubjectId( $subjectId );
		if(!$subjectData) {
			return $this->error(1);
		}
		if(!$subjectData['Subject']['catalog_id']) {
			return $this->success(1);
		}


        $query = array(
            'search'=>$subjectData['Subject']['name'],
            'fq'=>array('is_public'=>SUBJECT_IS_PUBLIC_TRUE, 'category_id'=>$subjectData['Subject']['category_id'], 'language'=>$subjectData['language']),
            'page'=>1,
            'limit'=>6
        );
        $otherTeacherForThisSubject = $this->Subject->search($query, $subjectData['Subject']['type']);
		//$otherTeacherForThisSubject = $this->Subject->getbyCatalog( $subjectData['Subject']['catalog_id'], $subjectId, $limit, $page );

		return $this->success(1, array('subjects'=>$otherTeacherForThisSubject));
	}
	public function getTeacherRatingByStudentsForSubject($subjectId, $limit=2, $page=1) {
		$subjectRatingByStudents = $this->Subject->getRatingByStudents( $subjectId, $limit, $page );
		return $this->success(1, array('rating'=>$subjectRatingByStudents));
	}
	
	public function teacher($teacherUserId) {
		//Get teacher data
		$teacherData = $this->User->findByUserId( $teacherUserId );
		if(!$teacherData) {
			return false;
		}
		
		//Get teacher other subjects
        $this->Subject->setLanguages($this->Session->read('languages_of_records'));
		$teacherSubjects = $this->Subject->getOffersByTeacher( $teacherUserId, false, null, 1, 6 );


		//Get students comments for that teacher
        $this->UserLesson->setLanguages($this->Session->read('languages_of_records'));
		$teacherReviews = $this->UserLesson->getTeachertReviews( $teacherUserId, 2 );

		//get forum latest posts
        app::import('Model', 'Forum.Post');
        $postObj = new Post();
        $postObj->setLanguages($this->Session->read('languages_of_records'));
        $latestPosts = $postObj->getLatestByUser($teacherUserId, 2);

		 
		$this->set('teacherUserData', 	$teacherData['User']);
		$this->set('teacherSubjects', 	$teacherSubjects);
		$this->set('teacherReviews', 	$teacherReviews);
		$this->set('latestPosts', 	    $latestPosts);
	}
	public function getTeacherRatingByStudents($teacherUserId, $limit=2, $page=1) {
        if($this->Auth->user('user_id')!=$teacherUserId) {
            $this->UserLesson->setLanguages($this->Session->read('languages_of_records'));
            $subjectRatingByStudents = $this->UserLesson->getTeachertReviews( $teacherUserId, $limit, $page );
        }
		return $this->success(1, array('rating'=>$subjectRatingByStudents));
	}
	public function getTeacherSubjects($teacherUserId, $limit=6, $page=1) {
        $this->Subject->setLanguages($this->Session->read('languages_of_records'));
		$teacherOtherSubjects = $this->Subject->getOffersByTeacher( $teacherUserId, false, null, $page, $limit );
		return $this->success(1, array('subjects'=>$teacherOtherSubjects));
	}
	
	
	public function subject() {
		//TODO: find related subjects by categories
		//TODO: find teachers by catalog
		//TODO: get board messages
	}

	/*public function	order($subjectId, $year=null, $month=null) {
        //TODO: video - there is no need to show calendar

		//Get subject data, students_amount, raters_amount, avarage_rating
		$subjectData = $this->Subject->findBySubjectId( $subjectId );
		if(!$subjectData || $subjectData['Subject']['is_enable']==SUBJECT_IS_ENABLE_FALSE) {
			$this->Session->setFlash(__('This subject is no longer available'));
			$this->redirect($this->referer());
		}
		$subjectData = $subjectData['Subject'];
		
		if($subjectData['type']!=SUBJECT_TYPE_OFFER) {
			$this->Session->setFlash(__('This lesson cannot be ordered'));
			$this->redirect($this->referer());
		}
		
		//Get teacher data
		$teacherData = $this->User->findByUserId( $subjectData['user_id'] );
		if(!$teacherData) {
			$this->Session->setFlash(__('Internal error'));
			$this->redirect($this->referer());
		}
		
		
		//get booking-auto-approve-settings
		App::import('Model', 'AutoApproveLessonRequest');
		$aalsObj = new AutoApproveLessonRequest();
		$aalr = $aalsObj->getSettings($subjectData['user_id']);
		

        //Only live lesson needs a calender and have group
        $isLiveLesson = false;
        if($subjectData['lesson_type']==LESSON_TYPE_LIVE) {
            $isLiveLesson = true;
            //Get student lessons for a given month
            $allLiveLessons = $this->User->getLiveLessonsByDate( $subjectData['user_id'], false, $year, $month);

            $groupLessons = array();
            foreach($allLiveLessons AS $lesson) {
                if($lesson['type']=='TeacherLesson' && isSet($lesson['max_students']) && $lesson['max_students']>1 &&  $lesson['max_students']>$lesson['num_of_students']) {
                    $groupLessons[] = $lesson;
                }
            }
            $this->set('groupLessons',	 		$groupLessons);
            $this->set('allLiveLessons',	 	$allLiveLessons);
        }
		
		$this->set('isLiveLesson', 		    $isLiveLesson);
		$this->set('subjectData', 			$subjectData);
		$this->set('teacherUserData',		$teacherData['User']);
		$this->set('aalr', 					$aalr);

	}*/
	public function getUserLessons($userId, $year, $month=null) {
		$allLessons = $this->User->getLiveLessonsByDate( $userId, false, $year, $month);
		return $this->success(1, array('lessons'=>$allLessons));
	}

    /*public function submitOrder($requestType, $subjectId) {
		App::import('Model', 'Subject');
		App::import('Model', 'UserLesson');

		
		//TODO: add more params, max_students, price, public
		if(strtolower($requestType)=='join') {
			//Join
			if(!$this->UserLesson->joinRequest( $subjectId, $this->Auth->user('user_id') )) {
				$this->Session->setFlash__(('Cannot join lesson'));
				$this->redirect($this->referer());
			}
		} else { //New

			//Create timestamp TODO: check user timezone
            $datetime = null;
            if(isSet($this->data['UserLesson']['datetime']) && !empty($this->data['UserLesson']['datetime'])) {
                $datetime = $this->data['UserLesson']['datetime'];
            } else if(isSet($this->data['datetime']) && !empty($this->data['datetime'])) {
                $datetime = $this->data['datetime'];
            }

            if($datetime) {
                $datetime = mktime(($datetime['meridian']=='pm' ? $datetime['hour']+12 : $datetime['hour']), $datetime['min'], 0, $datetime['month'], $datetime['day'], $datetime['year']);
                $datetime = $this->UserLesson->timeExpression($datetime, false);
            }

			if(!$this->UserLesson->lessonRequest($subjectId, $this->Auth->user('user_id'), $datetime)) {
                $this->Session->setFlash(__('Cannot order lesson'));
                $this->redirect($this->referer());
            }
		}
    }*/


    /*public function testListTimezones() {
        App::uses('Locale', 'Utility');
        pr(Locale::listTimezones());
    }*/

    /*public function testFind() {
        //pr($this->UserLesson->getAssociated()); die;
        Configure::write('Config.timezone', 'Asia/Tokyo');
        //Configure::write('Config.timezone', 'Asia/Jerusalem');
        //$allLiveLessons = $this->User->getLiveLessonsByDate( array(1,2,3,4,5), false, 2012, 9);
        //pr($allLiveLessons);
        $this->User->recursive = 2;
        pr($this->User->find('all'));

    }
    public function testSave() {
        Configure::write('Config.timezone', 'Asia/Tokyo');
        //$this->UserLesson->saveAll( array('name'=>'uname', 'Subject'=>array('name'=>'sname', 'user_id'=>4), 'TeacherLesson'=>'tname'),array('validate'=>false));
        $this->UserLesson->saveAll( array('name'=>'uname', 'description'=>'aa', 'language'=>'en',
            'Subject'=>array('name'=>'sname', 'description'=>1, 'user_id'=>4, 'language'=>'en'),
            'TeacherLesson'=>array(array('name'=>'tname1'), array('name'=>'tname2'))),array('validate'=>false));
    }*/

    public function testTime() {
        //$this->UserLesson->find();
        App::uses('CakeTime', 'Utility');

        $userTimezone = 'Asia/Tokyo'; //Asia/Jerusalem
        $serverTime = date('Y-m-d H:i:s');


        Configure::write('Config.timezone', 'Asia/Jerusalem');


        pr($serverTime);
        pr($this->UserLesson->toServerTime('now'));
        pr($this->UserLesson->toClientTime('now'));
        pr($this->UserLesson->timeExpression('now', false));

        die;
        $startDate = '2012-03-01 01:00:00';
        $serverStartDate = $this->UserLesson->toServerTime($startDate);

        pr($startDate);
        pr($serverStartDate );
        die;

        pr( date('Y-m-d H:i:s') );
        pr( CakeTime::format('Y-m-d H:i:s', CakeTime::fromString('now + 60 seconds')) );
        pr( CakeTime::format('Y-m-d H:i:s', CakeTime::fromString('now')) );
        pr( date('Y-m-d H:i:s', CakeTime::fromString(date('Y-m-d H:i:s').' + 1 day')) );
        pr( date('Y-m-d H:i:s', CakeTime::fromString('2012-02 + 1 day')) );
        pr( date('Y-m-d H:i:s', CakeTime::fromString('now')) );

        //From server to user
        /*$userTimeTS = CakeTime::fromString($serverTime, $userTimezone); //Timestamp



        //From user to server
        $serverTime2 = CakeTime::toServer(date('Y-m-d H:i:s', $userTimeTS) , $userTimezone);


        pr($serverTime);
        pr(date('Y-m-d H:i:s', $userTimeTS));
        pr($serverTime2);*/


        /*$userTime = '2012-08-16 15:40:38';
        $serverTime2 = CakeTime::toServer($userTime , $userTimezone);
        pr($userTime);
        pr($serverTime2);*/
        die;
    }

}
