<?php
class TeacherController extends AppController {
	public $name = 'Teacher';
	public $uses = array('Subject', 'User', 'Profile', 'TeacherLesson', 'UserLesson');
	public $components = array('Session', 'RequestHandler', 'Auth',/* 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');


    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow(	'subject');
    }
	
	public function index() {
		$aboutToStartLessons = $this->TeacherLesson->getUpcomming($this->Auth->user('user_id'). null, 2, 1);

		//TODO: get board messages
		
					
		$this->Set('aboutToStartLessons', $aboutToStartLessons);
	}
	
	public function subjects($limit=5, $page=1) {
		$subjects = $this->Subject->getbyTeacher($this->Auth->user('user_id'), true, SUBJECT_TYPE_OFFER, $page, $limit);
		$this->Set('teacherImage', $this->Auth->user('image'));
		$this->Set('subjects', $subjects);
	}
	
	public function subject( $subjectId=null ) {
		if($subjectId) {
			if(!$this->verifyOwnership('subject', $subjectId)) {
				$this->Session->setFlash('Cannot view this subject');
				$this->redirect($this->referer());
			}
			//Default subject data
			if (empty($this->request->data)) {
				$this->request->data = $this->Subject->findBySubjectId($subjectId);
			}
			$this->set('subjectId', $subjectId);
			
			/*
			//Get lessons for this subject
			$this->TeacherLesson->unbindModel(array('belongsTo'=>array('User')));
			$nextLessons = $this->TeacherLesson->getUpcomming($this->Auth->user('user_id'), $subjectId, 6);
			$this->set('nextLessons', $nextLessons);
			*/
			//Get subject FS
			App::import('Model', 'FileSystem');
			$fsObj = new FileSystem();
			$fileSystem = $fsObj->getFS('subject', $subjectId);
			$this->set('fileSystem', $fileSystem);
			
			//Get subject tests
			App::import('Model', 'StudentTest');
			$testObj = new StudentTest();
			$tests = $testObj->getTests('subject', $subjectId);
			$this->set('tests', $tests);
		}

        //TODO: select subejct categories

		if (!empty($this->request->data)) {
			App::import('Model', 'Subject');
			$this->request->data['Subject']['user_id'] = $this->Auth->user('user_id');
			$this->request->data['Subject']['type'] = SUBJECT_TYPE_OFFER;
			//$this->Subject->set($this->request->data);
			if($this->Subject->save($this->request->data)) {
                $this->Session->setFlash('Subject saved');
                $this->redirect(array('action'=>'subjects'));
            }
		}

        //$this->Subject->invalidate('name', 'name');
        //pr($this->Subject->validationErrors);

		//Group pricing
		if(	isSet($this->data['Subject']['1_on_1_price']) && 
			isSet($this->data['Subject']['full_group_total_price']) && !empty($this->data['Subject']['full_group_total_price']) &&
			isSet($this->data['Subject']['max_students']) && $this->data['Subject']['max_students']>1) {
			$groupPrice = $this->Subject->calcGroupPrice(	$this->data['Subject']['1_on_1_price'], $this->data['Subject']['full_group_total_price'], 
																							$this->data['Subject']['max_students'], $this->data['Subject']['max_students']); 
			$this->set('groupPrice', $groupPrice);
		}

        Configure::load('language');
        $this->set('language',Configure::read('language'));

	}
	
	public function disableSubject($subjectId) {
		if(!$this->verifyOwnership('subject', $subjectId)) {
			return $this->error(1, array('subject_id'=>$subjectId));
		}
		
		if(!$this->Subject->disable($subjectId)) {
			return $this->error(1, array('subject_id'=>$subjectId));
		}
		return $this->success(1, array('subject_id'=>$subjectId));
	}
	
	//"upcoming", "archive", "booking requests", "invitations sent" and "proposed lessons"
	public function lessons($limit=5, $page=1) {
		$upcommingLessons = $this->TeacherLesson->getUpcomming($this->Auth->user('user_id'), null, $limit, $page);
		$this->Set('upcommingLessons', $upcommingLessons);
		
		//Get lessons that pending for teacher approval
		$bookingRequests = $this->UserLesson->getWaitingForTeacherApproval($this->Auth->user('user_id'), null, $limit, $page);
		$this->Set('bookingRequests', $bookingRequests);
		
		$archiveLessons = $this->TeacherLesson->getArchive($this->Auth->user('user_id'), null, $limit, $page);
		$this->Set('archiveLessons', $archiveLessons);
		
		//Get lessons invitations
		$lessonInvitations = $this->UserLesson->getTeacherInvitations($this->Auth->user('user_id'), null, $limit, $page);
		$this->Set('lessonInvitations', $lessonInvitations);
		
		//Get lesson requests
		$pendingProposedLessons = $this->TeacherLesson->getPendingProposedLessons($this->Auth->user('user_id'), null, $limit, $page);
		$this->Set('pendingProposedLessons', $pendingProposedLessons);
	}

	public function lessonsUpcoming( $limit=6, $page=1 ) {
		$nextLessons = $this->TeacherLesson->getUpcomming($this->Auth->user('user_id'), null, $limit, $page);
		return $this->success(1, array('upcoming_lessons'=>$nextLessons));
	}
	public function lessonBookingRequests($limit=6, $page=1) {
		$bookingRequests = $this->UserLesson->getWaitingForTeacherApproval($this->Auth->user('user_id'), $limit, $page);
		return $this->success(1, array('booking_requests'=>$bookingRequests));
	}
	public function lessonsArchive($limit=6, $page=1) {
		$archiveLessons = $this->TeacherLesson->getArchive($this->Auth->user('user_id'), null, $limit, $page);
		return $this->success(1, array('archive_lessons'=>$archiveLessons));
	}
	public function lessonsInvitations($limit=6, $page=1) {
		$lessonInvitations = $this->UserLesson->getTeacherInvitations($this->Auth->user('user_id'), null, $limit, $page);
		return $this->success(1, array('lesson_invitaions'=>$lessonInvitations));
	}
	public function lessonsProposed($limit=6, $page=1) {
		$pendingProposedLessons = $this->TeacherLesson->getPendingProposedLessons($this->Auth->user('user_id'), null, $limit, $page);
		return $this->success(1, array('proposed_lessons'=>$pendingProposedLessons));
	}


	public function cacnelTeacherLesson( $teacherLessonId ) {
		if($this->TeacherLesson->cancel($teacherLessonId, 'teacher', $this->Auth->user('user_id'))) {
			return $this->success(1, array('teacher_lesson_id'=>$teacherLessonId));
		}
		
		return $this->error(1, array('teacher_lesson_id'=>$teacherLessonId));
	}
	
	
	public function createTeacherLesson($subjectId) {
		if (!empty($this->request->data)) {
			if($this->TeacherLesson->add($subjectId, $this->request->data['TeacherLesson']['datetime'], $this->request->data['TeacherLesson']['is_public'], $this->Auth->user('user_id') )) {
				return $this->success(1, array('subject_id'=>$subjectId));
			}
			return $this->error(1, array('subject_id'=>$subjectId, 'validation_errors'=>$this->TeacherLesson->validationErrors));
		}
		//Remove this after testing + view "create_teacher_lessons.ctp"
		//return $this->error(2, array('subject_id'=>$subjectId));
	}
	public function manageTeacherLesson( $teacherLessonId ) {
		$teacherLessonData = $this->TeacherLesson->findByTeacherLessonId($teacherLessonId);
		$students = $this->UserLesson->getStudentsForTeacherLesson($teacherLessonId);
		
		//TODO: add student amount of lessons, FS and Tests managment
		$this->set('teacherLesson', $teacherLessonData['TeacherLesson']);
		$this->set('allStudents',	 $students);
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
		$awaitingReviews = $this->UserLesson->waitingTeacherReview($this->Auth->user('user_id'));
		$this->set('awaitingReviews', $awaitingReviews);
		
		$userData = $this->User->findByUserId($this->Auth->user('user_id'));
		$this->set('teacherAvarageRating', $userData['User']['teacher_avarage_rating']);
	}
	public function setReview($userLessonId) {
		if (!empty($this->request->data)) {
			if($this->UserLesson->rate(	$userLessonId, $this->Auth->user('user_id'), 
			  							$this->request->data['UserLesson']['rating_by_teacher'], 
			  							$this->request->data['UserLesson']['comment_by_teacher'])) {
				$this->redirect(array('action'=>'awaitingReview'));
			}
			 
		}
		
		$setReview = $this->UserLesson->getLessons(array('teacher_user_id'=>$this->Auth->user('user_id')), $userLessonId);
		$this->Set('setReview', $setReview);
	}
	
	public function myReviews() {
		//Get students comments for that teacher
		$teacherReviews = $this->User->getTeachertReviews( $this->Auth->user('user_id'), 10 );
		$this->Set('teacherReviews', $teacherReviews);
	}
	
	private function verifyOwnership($entityType, $entityId) {
		$foundRecord = false;
		switch($entityType) {
			case 'subject':
				$foundRecord = $this->Subject->find('first', array('conditions'=>array('subject_id'=>$entityId, 'user_id'=>$this->Auth->user('user_id'))));
			break;
			case 'teacher_lesson':
				$foundRecord = $this->TeacherLesson->find('first', array('conditions'=>array('teacher_lesson_id'=>$entityId, 'teacher_user_id'=>$this->Auth->user('user_id'))));
			break;
			case 'user_lesson':
				$foundRecord = true; //TODO
			break;
		}
		
		return $foundRecord ? true : false;
		
	}
}