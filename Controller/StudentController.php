<?php
/**
 *@property Subject $Subject
 */
class StudentController extends AppController {
	public $name = 'Student';
	public $uses = array('Subject', 'User', 'Profile', 'TeacherLesson', 'UserLesson');
	public $components = array('Session', 'RequestHandler', 'Auth',/* 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');

	public function index() {
		//Get lessons that about to start
		$upcommingLessons= $this->UserLesson->getUpcomming($this->Auth->user('user_id'), 2, 1);
					
		//TODO: get board messages
		
		//TODO: get lesson suggestions
					
		$this->Set('upcommingLessons', $upcommingLessons);
	}
	
	public function lessons($limit=5, $page=1, $lang=null) {
		//Get lessons that about to start
		$upcommingLessons = $this->UserLesson->getUpcomming($this->Auth->user('user_id'), $limit, $page);
		$this->Set('upcommingLessons', $upcommingLessons);
		
		//Get lessons that pending for teacher approval
		$bookingRequests = $this->UserLesson->getBooking($this->Auth->user('user_id'), $limit, $page);
		$this->Set('bookingRequests', $bookingRequests);
		
		//Get lessons that are over
		$archiveLessons = $this->UserLesson->getArchive($this->Auth->user('user_id'), $limit, $page);
		$this->Set('archiveLessons', $archiveLessons);
		
		//Get lessons invitations
		$lessonInvitations = $this->UserLesson->getInvitations($this->Auth->user('user_id'), $limit, $page);
		$this->Set('lessonInvitations', $lessonInvitations);
		
		//Get lesson requests
        $subjectRequests = $this->Subject->getSubjectRequestsForStudent($this->Auth->user('user_id'), $limit, $page);
		//$subjectRequests = $this->Subject->search(SUBJECT_TYPE_REQUEST, true, $lang, $this->Auth->user('user_id'), null, null, null, $limit, $page );
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
		$subjectRequests = $this->Subject->getSubjectRequestsForStudent($this->Auth->user('user_id'), $limit, $page);
		//$subjectRequests = $this->Subject->search(SUBJECT_TYPE_REQUEST, true, $lang, $this->Auth->user('user_id'), null, null, null, $limit, $page );
		return $this->success(1, array('subjectRequests'=>$subjectRequests));
	}
	
	public function cacnelUserLesson( $userLessonId ) {
		if(!$this->UserLesson->cancelRequest( $userLessonId, $this->Auth->user('user_id') )) {
			return $this->error(1, array('user_lesson_id'=>$userLessonId));
		}
		
		return $this->success(1, array('user_lesson_id'=>$userLessonId));
	}
	
	public function acceptUserLesson( $userLessonId ) {
		if(!$this->UserLesson->acceptRequest( $userLessonId, $this->Auth->user('user_id') )) {
			return $this->error(1, array('user_lesson_id'=>$userLessonId));
		}
		
		return $this->success(1, array('user_lesson_id'=>$userLessonId));
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
		$studentReviews = $this->User->getStudentReviews( $this->Auth->user('user_id'), 10 );
		$this->Set('studentReviews', $studentReviews);
	}
}