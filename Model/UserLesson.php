<?php
define('USER_LESSON_PENDING_TEACHER_APPROVAL', 1);
define('USER_LESSON_PENDING_STUDENT_APPROVAL', 2);
define('USER_LESSON_DENIED_BY_TEACHER', 3);
define('USER_LESSON_DENIED_BY_STUDENT', 4);
define('USER_LESSON_ACCEPTED', 5);
define('USER_LESSON_CANCELED_BY_TEACHER', 6);
define('USER_LESSON_CANCELED_BY_STUDENT', 7);
define('USER_LESSON_PENDING_RATING', 8);
define('USER_LESSON_PENDING_TEACHER_RATING', 9);
define('USER_LESSON_PENDING_STUDENT_RATING', 10);
define('USER_LESSON_DONE', 11);

App::import('Model', 'AppModel');
class UserLesson extends AppModel {
	public $name = 'UserLesson';
	public $useTable = 'user_lessons';
	public $primaryKey = 'user_lesson_id';
	public $belongsTo = array(
					'Teacher' => array(
						'className' => 'User',
						'foreignKey'=>'teacher_user_id',
						'fields'=>array('first_name', 'last_name', 'image', 'teacher_avarage_rating', 'teacher_total_lessons')
					),
					'Student' => array(
						'className' => 'User',
						'foreignKey'=>'student_user_id',
						'fields'=>array('first_name', 'last_name', 'image', 'student_avarage_rating', 'student_total_lessons')
					),
					'Subject' => array(
						'className' => 'Subject',
						'foreignKey'=>'subject_id',
						'fields'=>array('avarage_rating', 'image')
					),
					'TeacherLesson' => array(
						'className' => 'TeacherLesson',
						'foreignKey'=>'teacher_lesson_id',
						'fields'=>array('num_of_students', 'max_students')
					)
				);
				
		public $validate = array(
			'comment_by_student' => array(
				'between' => array(
					'rule'			=> array('between', 15, 255),
					'on'			=> 'update',
					'allowEmpty'	=> false,
					'message' 		=> 'Please write a review with 15-255 characters'
				)
			),
			'rating_by_student' => array(
				'numeric' => array(
					'rule'			=> 'numeric',
					'on'			=> 'update',
					'allowEmpty'	=> false,
					'message' 		=> 'Error, rating must be numeric'
				),
				'blank' => array(
					'rule'			=> array('range', 0, 5),
					'on'			=> 'update',
					'allowEmpty'	=> false,
					'message' 		=> 'Please enter a number between 0 and 5'
				),
			),
		);
	
	/**
	 * 
	 * Users makeing new lesson requests from teacher
	 * @param unknown_type $subjectId
	 * @param unknown_type $userId - the user/teacher id that does not own the subject 
	 * @param unknown_type $datetime
	 */
	public function lessonRequest( $subjectId, $userId, $datetime ) {
		//Find the teacher lesson
		App::import('Model', 'Subject');
		$subjectObj = new Subject();
		
		$subjectObj->recursive = -1;
		$subjectData = $subjectObj->findBySubjectId($subjectId);
		if( !$subjectData ) {
			return false;
		}
		$subjectData = $subjectData['Subject'];
		
		
		if($subjectData['type']!=SUBJECT_TYPE_OFFER) {
			return false;
		}
		//Determint student and teacher user_ids
		
		//if($subjectData['type']==SUBJECT_TYPE_OFFER) {
			$teacheruserId = $subjectData['user_id'];
			$studentUserId = $userId;
		/*} else {
			$teacheruserId = $userId;
			$studentUserId = $subjectData['user_id'];
		}*/
		
		//Convert timestamp to datetime
		$datetime = $this->Subject->datetimeToStr($datetime);
		
		//Preper the user lesson generic data
		$userLesson = array(
			//'teacher_lesson_id'		=> null,
			'subject_id'				=> $subjectId,
			'teacher_user_id'			=> $teacheruserId,
			'student_user_id'			=> $studentUserId,
			'stage'						=> ($subjectData['type']==SUBJECT_TYPE_OFFER ? USER_LESSON_PENDING_TEACHER_APPROVAL : USER_LESSON_PENDING_STUDENT_APPROVAL),
			'datetime'					=> $datetime,
			'subject_type'				=> $subjectData['type'],
			'lesson_type'				=> $subjectData['lesson_type'],
			'language'				    => $subjectData['language'],
			'name'						=> $subjectData['name'],
			'description'				=> $subjectData['description'],
			'subject_type'				=> $subjectData['type'],
			'duration_minutes'			=> $subjectData['duration_minutes'],
			'max_students'				=> $subjectData['max_students'],
			'1_on_1_price'				=> $subjectData['1_on_1_price'],
			'full_group_student_price'	=> $subjectData['full_group_student_price'],
			'full_group_total_price'	=> $subjectData['full_group_total_price']
		);
		
		$event = new CakeEvent('Model.TeacherLesson.beforeLessonRequest', $this, array('subject'=>$subjectData, 'user_lesson'=>$userLesson) );
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}
		
		$this->create();
		$this->set($userLesson);
		if(!$this->save()) {
			return false;
		}
		
		$event = new CakeEvent('Model.TeacherLesson.afterLessonRequest', $this, array('subject'=>$subjectData, 'user_lesson'=>$userLesson) );
		
		//if($subjectData['type']==SUBJECT_TYPE_OFFER) {
			//Check if its auto approve, TODO: move to event Model.TeacherLesson.afterLessonRequest
			App::import('Model', 'AutoApproveLessonRequest');
			$aalsObj = new AutoApproveLessonRequest();
			if($aalsObj->isAutoApprove($teacheruserId, $subjectData['lesson_type'], $datetime)) {
				$this->acceptRequest($this->id, $teacheruserId);
			}
		//}

		
		return $this->id;
	}
	
	/**
	 * 
	 * Send a join request to the user/teacher
	 * 
	 * @param unknown_type $teacherLessonId - the lesson
	 * @param unknown_type $studentUserId - the student id
	 * @param unknown_type $teacherUserId - the teacher id, supply it only if you are the teacher (Invitation)
	 */
	public function joinRequest( $teacherLessonId, $studentUserId=null, $teacherUserId=null ) {
		//TODO: don't allow to send invitations if subject_type=request and the user did not approved his invitation yet
		
		//Find the teacher lesson
		App::import('Model', 'TeacherLesson');
		$teacherLessonObj = new TeacherLesson();
		
		$teacherLessonObj->recursive = -1;
		$teacherLessonData = $teacherLessonObj->findByTeacherLessonId($teacherLessonId);
		if( !$teacherLessonData ) {
			return false;
		}
		$teacherLessonData = $teacherLessonData['TeacherLesson'];
		
		//users can't join video lessons, only to live lessons, unless it's a lesson request
		if($teacherLessonData['lesson_type']==LESSON_TYPE_VIDEO && $teacherLessonData['subject_type']==SUBJECT_TYPE_OFFER) {
			return false;
		}
		if($teacherLessonData['subject_type']==SUBJECT_TYPE_REQUEST && is_null($studentUserId)) {
			$subjectData = $this->Subject->findBySubjectId($teacherLessonData['subject_id']);
			if(!$subjectData) {
				return false;
			}
			$studentUserId = $subjectData['Subject']['user_id'];
		}
		
		//Find the stage
		$stage = USER_LESSON_PENDING_TEACHER_APPROVAL;
		if( !is_null($teacherUserId) ) {
			//Check if that's the right teacher
			if( $teacherLessonData['teacher_user_id'] != $teacherUserId ) {
				return false;
			}
			$stage = USER_LESSON_PENDING_STUDENT_APPROVAL;
		}
		
		
		//Check if join request already exists or if the user is in that lesson already
		$userLessonData = $this->find('first', array('conditions'=>array(	'UserLesson.student_user_id'=>$studentUserId, 'UserLesson.teacher_lesson_id'=>$teacherLessonId, 
																			'UserLesson.stage'=>array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_ACCEPTED)),
																		));
		if($userLessonData) {
			$userLessonData = $userLessonData['UserLesson'];
			switch ($userLessonData['stage']) {
				case USER_LESSON_ACCEPTED:
					return true;
					
				default:
					if($stage==$userLessonData['stage']) {
						//Reqest already exists by that user
						return true;
					}
					//Oposit request exists, accept it
					return $this->acceptRequest($userLessonData['user_lesson_id'], ($teacherUserId ? $teacherUserId : $studentUserId));
			}
			return false;
		}
		

		//create record
		$userLesson = array(
			'teacher_lesson_id'			=> $teacherLessonData['teacher_lesson_id'],
			'subject_id'				=> $teacherLessonData['subject_id'],
			'teacher_user_id'			=> $teacherLessonData['teacher_user_id'],
			'student_user_id'			=> $studentUserId,
			'datetime'					=> $teacherLessonData['datetime'],
			'stage'						=> $stage,
			'lesson_type'				=> $teacherLessonData['lesson_type'],
			'language'				    => $teacherLessonData['language'],
			'name'						=> $teacherLessonData['name'],
			'description'				=> $teacherLessonData['description'],
			'subject_type'				=> $teacherLessonData['subject_type'],
			'duration_minutes'			=> $teacherLessonData['duration_minutes'],
			'max_students'				=> $teacherLessonData['max_students'],
			'1_on_1_price'				=> $teacherLessonData['1_on_1_price'],
			'full_group_student_price'	=> $teacherLessonData['full_group_student_price'],
			'full_group_total_price'	=> $teacherLessonData['full_group_total_price']
		);
		
		
		$event = new CakeEvent('Model.UserLesson.beforeJoinRequest', $this, array('teacher_lesson'=>$teacherLessonData, 'user_lesson'=>$userLesson));
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}
		
		$this->create();
		$this->set($userLesson);
		if(!$this->save()) {
			return false;
		}
		
		
		
		//Update the num_of_pending_invitations/num_of_pending_join_requests counter
		$counterDBField = ($teacherUserId ? 'num_of_pending_invitations' : 'num_of_pending_join_requests');
		$teacherLessonObj->id = $teacherLessonData['teacher_lesson_id'];
		$teacherLessonObj->set(array($counterDBField=>$this->getDataSource()->expression($counterDBField.'+1')));
		$teacherLessonObj->save();
		
		
		
		$teacherLessonData[$counterDBField]++;
		$event = new CakeEvent('Model.UserLesson.afterJoinRequest', $this, array('teacher_lesson'=>$teacherLessonData, 'user_lesson'=>$userLesson));
		$this->getEventManager()->dispatch($event);
		
		return true;
	}
	
	public function cancelRequest( $userLessonId, $byUserId ) {
		//Find user lesson
		$userLessonData = $this->findByUserLessonId($userLessonId);
		if( !$userLessonData ) {
			return false;
		}
		$userLessonData = $userLessonData['UserLesson'];
		
		
		//Check if $byUserId can cancel this request 
		if($userLessonData['student_user_id']!=$byUserId && $userLessonData['teacher_user_id']!=$byUserId) {
			return false;
		}
		//Check if the stage of the lesson is cancel-able
		if( $userLessonData['stage']!=USER_LESSON_PENDING_STUDENT_APPROVAL && 
			$userLessonData['stage']!=USER_LESSON_PENDING_TEACHER_APPROVAL && 
			$userLessonData['stage']!=USER_LESSON_ACCEPTED ) {
			return false;
		}
		
		
		$event = new CakeEvent('Model.UserLesson.beforeCancelRequest', $this, array('user_lesson'=>$userLessonData));
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}
		
		
		//Determnt the new userLesson stage and counter
		switch($userLessonData['stage']) {
			case USER_LESSON_ACCEPTED:
				$counterDBField = 'num_of_students';
				
				if($userLessonData['teacher_user_id']==$byUserId) {
					$newStage = USER_LESSON_CANCELED_BY_TEACHER;
				} else {
					$newStage = USER_LESSON_CANCELED_BY_STUDENT;
				}
			break;
			
			case USER_LESSON_PENDING_TEACHER_APPROVAL:
				$counterDBField = 'num_of_pending_join_requests';
				
				if($userLessonData['teacher_user_id']==$byUserId) { 
					$newStage = USER_LESSON_DENIED_BY_TEACHER;
				} else {
					$newStage = USER_LESSON_CANCELED_BY_STUDENT;
				}
			break;
			
			case USER_LESSON_PENDING_STUDENT_APPROVAL:
				$counterDBField = 'num_of_pending_invitations';
				
				if($userLessonData['student_user_id']==$byUserId) { 
					$newStage = USER_LESSON_DENIED_BY_STUDENT;
				} else {
					$newStage = USER_LESSON_CANCELED_BY_TEACHER;
				}
			break;
		}
			
		if($userLessonData['teacher_lesson_id']) {
			//if subject type=request, and the cancel user is the subject owner, cancel all other invitations+teacher lesson
			if($userLessonData['subject_type']==SUBJECT_TYPE_REQUEST) {
				//Find the teacher lesson
				$teacherLessonData = $teacherLessonObj->findByTeacherLessonId($userLessonData['teacher_lesson_id']);
				if($teacherLessonData['TeacherLesson']['student_user_id']==$byUserId) {
					if(!$teacherLessonObj->cancel($teacherLessonData['student_user_id'])) {
						return false;
					}
				}
				
			}
			
			//Update the num_of_pending_invitations/num_of_pending_join_requests counter
			App::import('Model', 'TeacherLesson');
			$teacherLessonObj = new TeacherLesson();
			$teacherLessonObj->id = $userLessonData['teacher_lesson_id'];
			$teacherLessonObj->set(array($counterDBField=>$this->getDataSource()->expression($counterDBField.'-1')));
			$teacherLessonObj->save();
		}
		
		//Update the user lesson
		$this->updateAll(array('stage'=>$newStage), array('user_lesson_id'=>$userLessonId));
		
		
		
		$userLessonData['stage'] = $newStage;
		$event = new CakeEvent('Model.UserLesson.afterCancelRequst', $this, array('user_lesson'=>$userLessonData));
		$this->getEventManager()->dispatch($event);
		
		
		return true;
	}
	
	public function acceptRequest( $userLessonId, $byUserId ) {
		//Find user lesson
		$userLessonData = $this->findByUserLessonId($userLessonId);
		if( !$userLessonData ) {
			return false;
		}
		$userLessonData = $userLessonData['UserLesson'];
		
		
		//Check if $byUserId can accept this request
		if(!($userLessonData['student_user_id']==$byUserId && $userLessonData['stage']==USER_LESSON_PENDING_STUDENT_APPROVAL ||
			$userLessonData['teacher_user_id']==$byUserId && $userLessonData['stage']==USER_LESSON_PENDING_TEACHER_APPROVAL)) {
			return false;
		}
		
		$event = new CakeEvent('Model.UserLesson.beforeAccept', $this, array('user_lesson'=>$userLessonData));
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}
		
		
		$updateUserLesson = array('stage'=>USER_LESSON_ACCEPTED);
		
		
		$counter = ($userLessonData['stage']==USER_LESSON_PENDING_STUDENT_APPROVAL ? 'num_of_pending_invitations' : 'num_of_pending_join_requests');
		
		//TODO: get teacher_lesson_id from event
		if(!$userLessonData['teacher_lesson_id'] && $userLessonData['teacher_user_id']==$byUserId) {
			//Create a lesson + set student_user_id
			$this->TeacherLesson->add($userLessonData['subject_id'], $userLessonData['datetime'], null, null, array('student_user_id'=>$userLessonData['student_user_id'],
																													$counter=>1));
			$userLessonData['teacher_lesson_id'] = $updateUserLesson['teacher_lesson_id'] = $this->TeacherLesson->id;
			
		}
		if($userLessonData['teacher_lesson_id']) {
			//Update the num_of_pending_invitations counter
			App::import('Model', 'TeacherLesson');
			$teacherLessonObj = new TeacherLesson();
			$teacherLessonObj->id = $userLessonData['teacher_lesson_id'];
			
			$teacherLessonObj->set(array($counter=>$this->getDataSource()->expression($counter.'-1'), 'num_of_students'=>$this->getDataSource()->expression('num_of_students+1')));
			$teacherLessonObj->save();
			
			//TODO: check num_of_students - if exceed max_students
		}
		
		
		
		
		//Update user lesson stage
		$this->updateAll($updateUserLesson, array('user_lesson_id'=>$userLessonId));
		
		
		
		$userLessonData['stage'] = USER_LESSON_ACCEPTED;
		$event = new CakeEvent('Model.UserLesson.afterAccept', $this, array('user_lesson'=>$userLessonData));
		$this->getEventManager()->dispatch($event);
		
		
		return true;
	}
	
	//TODO: cretae a daemon
	//if stage=USER_LESSON_ACCEPTED and datetime+duration<now then set stage=USER_LESSON_PENDING_RATING
	//Update teacher teacher_total_teaching_minutes, teacher_students_amount, teacher_total_lessons
	//Update subject students_amount, total_lessons
	//Update student teacher_total_lessons
	
	public function rate( $userLessonId, $byUserId, $rating, $comment ) {
		//on rate, if teacher - update student amount of raters + avarage rate. if student - update subject && teacher amount of raters + avarage rate
		$userLessonData = $this->findByUserLessonId($userLessonId);
		if(!$userLessonData) {
			return false;
		}
		$userLessonData = $userLessonData['UserLesson'];
		
		
		$dataSource = $this->getDataSource();
		
		
		
		App::import('Model', 'User');
		$userObj = new User();
		
		$userType = '';
		if($userLessonData['teacher_user_id']==$byUserId) {
			//Check if teacher can set rating
			if($userLessonData['stage']!=USER_LESSON_PENDING_RATING && $userLessonData['stage']!=USER_LESSON_PENDING_TEACHER_RATING ) {
				return false;
			}
			$userType = 'teacher';
			$newStage = ($userLessonData['stage']==USER_LESSON_PENDING_RATING) ? USER_LESSON_PENDING_STUDENT_RATING : USER_LESSON_DONE;
			
			//Start transaction
			$dataSource->begin();
			
			//Update student rating
			if(!$userObj->setRating($userLessonData['student_user_id'], $rating, 'student')) {
				$dataSource->rollback();
				return false;
			}

		} else if($userLessonData['student_user_id']==$byUserId) {
			
			
			//Check if student can set rating
			if($userLessonData['stage']!=USER_LESSON_PENDING_RATING && $userLessonData['stage']!=USER_LESSON_PENDING_STUDENT_RATING ) {
				return false;
			}
			
			$userType = 'student';
			$newStage = ($userLessonData['stage']==USER_LESSON_PENDING_RATING) ? USER_LESSON_PENDING_TEACHER_RATING : USER_LESSON_DONE;
			
			//Start transaction
			$dataSource->begin();
			
			//Update subject
			App::import('Model', 'Subject');
			$subObj = new Subject();
			if(!$subObj->setRating($userLessonData['subject_id'], $rating)) {
				$dataSource->rollback();
				return false;
			}
			
			//Update teacher rating
			if(!$userObj->setRating( $userLessonData['teacher_user_id'], 'teacher', $rating )) {
				$dataSource->rollback();
				return false;
			}
		} else {
			//This is not the student or the teacher
			return false;
		}
		
		
		//Update user lesson
		$updateUserLesson = array(	'rating_by_'.$userType	=>$rating,
									'comment_by_'.$userType	=>$comment,
									'stage'					=>$newStage);
		
		$event = new CakeEvent('Model.UserLesson.beforeRate', $this, array('user_lesson'=>$userLessonData, 'update'=>$updateUserLesson));
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			$dataSource->rollback();
			return false;
		}
		
		$this->id = $userLessonId;
		$this->set($updateUserLesson);
		if(!$this->save()) {
			$dataSource->rollback();
			return false;
		}
		
		$dataSource->commit();
		return true;
		
	}
	
	/**
	 * 
	 * Get all requests (students, join requests and invitations) for a spesific teacher lesson
	 * @param unknown_type $teacherLessonsId
	 */
	public function getStudentsForTeacherLesson( $teacherLessonsId ) {
		$return = array('students'=>array(), 'join_reuests'=>array(), 'invitations'=>array());
		
		$results = $this->find('all', array('conditions'=>array('UserLesson.teacher_lesson_id'=>$teacherLessonsId, 'UserLesson.stage'=>array(USER_LESSON_ACCEPTED, USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_PENDING_TEACHER_APPROVAL))));
		foreach($results AS $result) {
			
			switch($result['UserLesson']['stage']) {
				case USER_LESSON_ACCEPTED:
					$return['students'][] = $result;
				break;
				case USER_LESSON_PENDING_TEACHER_APPROVAL:
					$return['join_reuests'][] = $result;
				break;
				case USER_LESSON_PENDING_STUDENT_APPROVAL:
					$return['invitations'][] = $result;
				break;
			}
		}
		
		return $return;
	}
	
	/**
	 * 
	 * Get all new lessons requests for a spesific subject
	 * @param unknown_type $subjectId
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public function getNewLessonRequest( $subjectId, $page=1, $limit=1 ) {
		App::import('Model', 'Subject');
		$conditions = array('subject_id'=>$subjectId,
							'subject_type'=>SUBJECT_TYPE_OFFER,
							'teacher_lesson_id IS NULL',
							'stage'=>USER_LESSON_PENDING_TEACHER_APPROVAL);
		
		
		
		return $this->find('all', array('conditions'=>$conditions, 
								'order'=>'datetime',
								'limit'=>( $limit ? $limit : null),
								'page'=>$page
			));
	}
	
	/**
	 * 
	 * Get all student lessons for given $stages in a given year/month
	 * @param unknown_type $studentUserId
	 * @param unknown_type $stages
	 * @param unknown_type $year
	 * @param unknown_type $month
	 */
	public function getLessonsByDate( $studentUserId, $year, $month=null, $stages=array() ) {
		$this->getDataSource();
		
		$startDate = $year.'-'.($month ? $month : 1).'-1';
		$endDate = $year.'-'.($month ? $month : 12).'-1';
		
		$conditions = array('student_user_id'=>$studentUserId, 
							'datetime BETWEEN ? AND ?' => array($startDate, $this->getDataSource()->expression('date_add(\''.$endDate.'\',interval 1 month)')));
		if($stages) {
			$conditions['OR']=array('stage'=>$stages);
		}

		return $this->find('all', array('conditions'=>$conditions));
	}
	
	public function getUpcomming($studentUserId, $limit=null, $page=1) {
		$this->Subject;
		return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId), '>', $limit, $page, array(USER_LESSON_ACCEPTED), array(SUBJECT_TYPE_OFFER, SUBJECT_TYPE_REQUEST));
	}
	public function getBooking($studentUserId, $limit=null, $page=1) {
		return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId), '>', $limit, $page, array(USER_LESSON_PENDING_TEACHER_APPROVAL));
	}
	public function getArchive($studentUserId, $limit=null, $page=1) {

	$conditions = array('UserLesson.student_user_id'=>$studentUserId, 
						'OR'=>array('UserLesson.datetime<NOW()',
									'stage'=>array(	USER_LESSON_DENIED_BY_TEACHER, USER_LESSON_DENIED_BY_STUDENT,
													USER_LESSON_CANCELED_BY_TEACHER, USER_LESSON_CANCELED_BY_STUDENT,
													USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_TEACHER_RATING, USER_LESSON_PENDING_STUDENT_RATING,
													USER_LESSON_DONE)));
		
	return $this->find('all', array('conditions'=>$conditions, 
										'order'=>'datetime',
										'limit'=>( $limit ? $limit : null),
										'page'=>$page
					));
	
		
	}
	public function getInvitations($studentUserId, $limit=null, $page=1) {
		$this->Subject;
		return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId), '>', $limit, $page, USER_LESSON_PENDING_STUDENT_APPROVAL, array(SUBJECT_TYPE_OFFER, SUBJECT_TYPE_REQUEST));
	}
	
	/*public function withTeacherReview($studentUserId, $limit=null, $page=1) {
		return $this->getLessons(array('student_user_id'=>$studentUserId), null, $limit, $page, array(USER_LESSON_PENDING_STUDENT_RATING, USER_LESSON_DONE));
	}*/
	public function waitingStudentReview($studentUserId, $limit=null, $page=1) {
		return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId), null, $limit, $page, array(USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_STUDENT_RATING));
	}
	
	
	public function getTeacherInvitations($teacherUserId, $subjectId=null, $limit=null, $page=1) {
		$this->Subject;
		$conditions = array('UserLesson.teacher_user_id'=>$teacherUserId);
		if($subjectId) {
			$conditions['UserLesson.subject_id'] = $teacherUserId;
		}
		return $this->getLessons($conditions, '>', $limit, $page, USER_LESSON_PENDING_STUDENT_APPROVAL);
	}
	public function getWaitingForTeacherApproval($teacherUserId, $subjectId=null, $limit=null, $page=1) {
		$this->unbindModel(array('belongsTo'=>array('Teacher', 'TeacherLesson')));
		
		$conditions = array('UserLesson.teacher_user_id'=>$teacherUserId);
		if($subjectId) {
			$conditions['UserLesson.subject_id'] = $teacherUserId;
		}
		return $this->getLessons($conditions, '>', $limit, $page, array(USER_LESSON_PENDING_TEACHER_APPROVAL));
	}
	public function waitingTeacherReview($teacehrUserId, $limit=null, $page=1) {
		return $this->getLessons(array('UserLesson.teacher_user_id'=>$teacehrUserId), null, $limit, $page, array(USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_TEACHER_RATING));
	}
	
	public function getLessons($conditions, $time='>', $limit=null, $page=1, $stage=array(), $subjectType=null) {
		
		$find = 'all';
		App::import('Model', 'Subject');
		$conditions['UserLesson.subject_type'] = (!$subjectType ? SUBJECT_TYPE_OFFER : $subjectType);
		
		
		if(is_numeric($time)) {
			$conditions['UserLesson.user_lesson_id'] = $time;
			$find = 'first';
		} else {
		
			if($stage) {
				$conditions['UserLesson.stage'] = $stage;
			}
			if($time) {
				$conditions[] = 'UserLesson.datetime'.$time.'NOW()';
			}
		}
		
		
		//DboSource::expression('');
		return $this->find($find, array('conditions'=>$conditions, 
										'order'=>'datetime',
										'limit'=>( $limit ? $limit : null),
										'page'=>$page
					));
	}
	

}
?>
