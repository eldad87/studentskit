<?php
define('USER_LESSON_PENDING_TEACHER_APPROVAL', 1);
define('USER_LESSON_PENDING_STUDENT_APPROVAL', 2);
define('USER_LESSON_DENIED_BY_TEACHER', 3);
define('USER_LESSON_DENIED_BY_STUDENT', 4);

define('USER_LESSON_RESCHEDULED_BY_TEACHER', 5);
define('USER_LESSON_RESCHEDULED_BY_STUDENT', 6);

define('USER_LESSON_ACCEPTED', 7);
define('USER_LESSON_CANCELED_BY_TEACHER', 8);
define('USER_LESSON_CANCELED_BY_STUDENT', 9);
define('USER_LESSON_PENDING_RATING', 10);
define('USER_LESSON_PENDING_TEACHER_RATING', 11);
define('USER_LESSON_PENDING_STUDENT_RATING', 12);
define('USER_LESSON_DONE', 13);

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
            'duration_minutes'=> array(
                'range' 		=> array(
                    'required'	=> 'create',
                    'allowEmpty'=> false,
                    'rule'    	=> array('range', 4, 241),
                    'message' 	=> 'Lesson must be more then 5 minutes and less then 240 minutes (4 hours)'
                )
            ),
            '1_on_1_price'=> array(
                'price' => array(
                    'required'	=> 'create',
                    'allowEmpty'=> false,
                    'rule'    	=> 'numeric',
                    'message' 	=> 'Enter a valid price'
                ),
                'price_range' => array(
                    'required'	=> 'create',
                    'allowEmpty'=> false,
                    'rule'    	=> array('range', 1, 500),
                    'message' 	=> 'Price must be more then 1 and less then 500'
                )
            ),
            'max_students'=> array(
                'range' 		=> array(
                    'required'	=> 'create',
                    'allowEmpty'=> false,
                    'rule'    	=> array('range', 0, 1025),
                    'message' 	=> 'Lesson must have more then 1 or less then 1024 students'
                ),
                'max_students' 	=> array(
                    'required'	=> 'create',
                    'allowEmpty'=> false,
                    'rule'    	=> 'maxStudentsCheck',
                    'message' 	=> 'You must set group price'
                )
            ),
            'full_group_total_price'=> array(
                'price' => array(
                    'allowEmpty'=> true,
                    'rule'    	=> 'numeric',
                    'message' 	=> 'Enter a valid group price'
                ),
                'price_range' => array(
                    'allowEmpty'=> true,
                    'rule'    	=> array('range', 1, 2500),
                    'message' 	=> 'Price must be more then 1 and less then 2500'
                ),
                'full_group_total_price' 	=> array(
                    //'required'	=> 'create',
                    'allowEmpty'=> true,
                    'rule'    	=> 'fullGroupTotalPriceCheck',
                    'message' 	=> 'You must set group price'
                )
            ),
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

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        static $eventListenterAttached = false;

        if(!$eventListenterAttached) {
            //Connect the event manager of this model
            App::import( 'Event', 'UserLessonEventListener');
            $ulel = new UserLessonEventListener();
            CakeEventManager::instance()->attach($ulel);
            $eventListenterAttached = true;
        }
    }
    public function fullGroupTotalPriceCheck( $price) {
        if(!isSet($this->data['UserLesson']['max_students'])) {
            $this->invalidate('max_students', 'Please enter a valid max students');
            return false;
        } else  {
            if(	isSet($this->data['UserLesson']['full_group_total_price']) && !empty($this->data['UserLesson']['full_group_total_price']) &&
                $this->data['UserLesson']['max_students'] && $this->data['UserLesson']['1_on_1_price']) {

                //Check if full_group_total_price is MORE then  max_students*1_on_1_price
                $maxAllowed = $this->data['UserLesson']['max_students']*$this->data['UserLesson']['1_on_1_price'];
                if($this->data['UserLesson']['full_group_total_price']>$maxAllowed) {
                    $this->invalidate('max_students', 'Group price error, max is '.$maxAllowed.'. (max students * 1 on 1 price)');

                    //Check if total group price is LESS then 1 on 1 price (1 on 1 price is NOT 0)
                } else if($this->data['UserLesson']['full_group_total_price']<=$this->data['UserLesson']['1_on_1_price']) {
                    $this->invalidate('full_group_total_price', 'Full group price must be more the 1 on 1 price ('.$this->data['UserLesson']['1_on_1_price'].')');
                }
            }
        }
        return true;
    }
    public function maxStudentsCheck( $maxStudents ) {
        if($maxStudents['max_students']>1 && (!isSet($this->data['UserLesson']['full_group_total_price']) || !$this->data['UserLesson']['full_group_total_price'])) {
            $this->invalidate('full_group_total_price', 'Please enter a valid group price or set Max students to 1');
            return false;
        }
        return true;
    }

    public function beforeSave($options=array()) {
        parent::beforeSave($options);
        App::import('Model', 'Subject');

        //If no teacher_lesson_id
        //if(!isSet($this->data['UserLesson']['teacher_lesson_id']) ) {
            Subject::calcFullGroupStudentPriceIfNeeded($this->data['UserLesson']);
        //}
    }

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

		//Convert timestamp to datetime
		$datetime = $this->Subject->datetimeToStr($datetime);

		//Preper the user lesson generic data
		$userLesson = array(
			//'teacher_lesson_id'		=> null,
			'subject_id'				=> $subjectId,
			'teacher_user_id'			=> ($subjectData['type']==SUBJECT_TYPE_OFFER ? $subjectData['user_id']              : $userId),
			'student_user_id'			=> ($subjectData['type']==SUBJECT_TYPE_OFFER ? $userId                              : $subjectData['user_id']),
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
		
		$event = new CakeEvent('Model.UserLesson.beforeLessonRequest', $this, array('user_lesson'=>$userLesson, 'by_user_id'=>$userId) );
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}
		
		$this->create();
		$this->set($userLesson);
		if(!$this->save()) {
			return false;
		}
        $userLesson['user_lesson_id'] = $this->id;
		
		$event = new CakeEvent('Model.UserLesson.afterLessonRequest', $this, array('user_lesson'=>$userLesson, 'by_user_id'=>$userId) );
        $this->getEventManager()->dispatch($event);
		return $this->id;
	}
	
	/**
	 * 
	 * Send a join request to the user/teacher
	 * 
	 * @param unknown_type $teacherLessonId - the lesson
	 * @param unknown_type $studentUserId - the student id, leave null only if subject_type==SUBJECT_TYPE_REQUEST
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
		
		
		$event = new CakeEvent('Model.UserLesson.beforeJoinRequest', $this, array('teacher_lesson'=>$teacherLessonData, 'user_lesson'=>$userLesson, 'by_user_id'=>( $teacherUserId ? $teacherUserId : $studentUserId)));
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}
		
		$this->create();
		$this->set($userLesson);
		if(!$this->save()) {
			return false;
		}
		$userLesson['user_lesson_id'] = $this->id;
		
		
		//Update the num_of_pending_invitations/num_of_pending_join_requests counter
		$counterDBField = ($teacherUserId ? 'num_of_pending_invitations' : 'num_of_pending_join_requests');
		$teacherLessonObj->id = $teacherLessonData['teacher_lesson_id'];
		$teacherLessonObj->set(array($counterDBField=>$this->getDataSource()->expression($counterDBField.'+1')));
		$teacherLessonObj->save();
		
		
		
		$teacherLessonData[$counterDBField]++;
		$event = new CakeEvent('Model.UserLesson.afterJoinRequest', $this, array('teacher_lesson'=>$teacherLessonData, 'user_lesson'=>$userLesson, 'by_user_id'=>( $teacherUserId ? $teacherUserId : $studentUserId)));
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
        if(!in_array(intval($userLessonData['stage']), array(   USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_PENDING_TEACHER_APPROVAL,
                                                                USER_LESSON_RESCHEDULED_BY_TEACHER, USER_LESSON_RESCHEDULED_BY_STUDENT,
                                                                USER_LESSON_ACCEPTED))) {
            return false;
        }
		/*if( $userLessonData['stage']!=USER_LESSON_PENDING_STUDENT_APPROVAL &&
			$userLessonData['stage']!=USER_LESSON_PENDING_TEACHER_APPROVAL && 
			$userLessonData['stage']!=USER_LESSON_ACCEPTED ) {
			return false;
		}*/
		
		
		$event = new CakeEvent('Model.UserLesson.beforeCancelRequest', $this, array('user_lesson'=>$userLessonData, 'by_user_id'=>$byUserId));
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}
		
		$data = array();
		//Determnt the new userLesson stage and counter
		switch($userLessonData['stage']) {
			case USER_LESSON_ACCEPTED:
				$counterDBField = 'num_of_students';
				
				if($userLessonData['teacher_user_id']==$byUserId) {
                    $data['stage'] = USER_LESSON_CANCELED_BY_TEACHER;
				} else {
                    $data['stage'] = USER_LESSON_CANCELED_BY_STUDENT;
				}
			break;
			
			case USER_LESSON_PENDING_TEACHER_APPROVAL:
			case USER_LESSON_RESCHEDULED_BY_STUDENT:
				$counterDBField = 'num_of_pending_join_requests';
				
				if($userLessonData['teacher_user_id']==$byUserId) {
                    $data['stage'] = USER_LESSON_DENIED_BY_TEACHER;
				} else {
                    $data['stage'] = USER_LESSON_CANCELED_BY_STUDENT;
				}
			break;
			
			case USER_LESSON_PENDING_STUDENT_APPROVAL:
			case USER_LESSON_RESCHEDULED_BY_TEACHER:
				$counterDBField = 'num_of_pending_invitations';
				
				if($userLessonData['student_user_id']==$byUserId) {
                    $data['stage'] = USER_LESSON_DENIED_BY_STUDENT;
				} else {
                    $data['stage'] = USER_LESSON_CANCELED_BY_TEACHER;
				}
			break;
		}

		if($userLessonData['teacher_lesson_id']) {
            App::import('Model', 'TeacherLesson');
            $teacherLessonObj = new TeacherLesson();

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
			$teacherLessonObj->id = $userLessonData['teacher_lesson_id'];
			$teacherLessonObj->set(array($counterDBField=>$this->getDataSource()->expression($counterDBField.'-1')));
			$teacherLessonObj->save();
		}
		
		//Update the user lesson
		$this->updateAll($data, array('user_lesson_id'=>$userLessonId));
		

		$event = new CakeEvent('Model.UserLesson.afterCancelRequest', $this, array('user_lesson'=>$userLessonData, 'data'=>$data, 'by_user_id'=>$byUserId));
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
		if(!(($userLessonData['student_user_id']==$byUserId && ($userLessonData['stage']==USER_LESSON_PENDING_STUDENT_APPROVAL || $userLessonData['stage']==USER_LESSON_RESCHEDULED_BY_TEACHER)) ||
			($userLessonData['teacher_user_id']==$byUserId && ($userLessonData['stage']==USER_LESSON_PENDING_TEACHER_APPROVAL || $userLessonData['stage']==USER_LESSON_RESCHEDULED_BY_STUDENT)))) {
			return false;
		}
		
		$event = new CakeEvent('Model.UserLesson.beforeAccept', $this, array('user_lesson'=>$userLessonData, 'by_user_id'=>$byUserId));
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}

		
		$updateUserLesson = array('stage'=>USER_LESSON_ACCEPTED);
        if(isSet($event->result['teacher_lesson_id'])) {
            $updateUserLesson['teacher_lesson_id'] = $event->result['teacher_lesson_id'];
        }
		
		/*$counter = (($userLessonData['stage']==USER_LESSON_PENDING_STUDENT_APPROVAL ||
                    $userLessonData['stage']==USER_LESSON_RESCHEDULED_BY_TEACHER) ? 'num_of_pending_invitations' : 'num_of_pending_join_requests');*/
		
		//TODO: get teacher_lesson_id from event
		/*if(!$userLessonData['teacher_lesson_id']) {

			//Create a lesson + set student_user_id
			if(!$this->TeacherLesson->add(array('type'=>'user_lesson','id'=>$userLessonData['user_lesson_id']), null, null, array(  'teacher_user_id'=>$userLessonData['teacher_user_id'],
                                                                                                                                    'student_user_id'=>$userLessonData['student_user_id'],
                                                                                                                                    $this->getAcceptLessonCounter($userLessonData['stage'])=>1))) {
                return false;
            }

			$userLessonData['teacher_lesson_id'] = $updateUserLesson['teacher_lesson_id'] = $this->TeacherLesson->id;
		}
        if($userLessonData['teacher_lesson_id']) { //accepting join request
            $counter = $this->getAcceptLessonCounter($userLessonData['stage']);
			//Update the num_of_pending_invitations counter
            $this->TeacherLesson->id = $userLessonData['teacher_lesson_id'];

            $this->TeacherLesson->set(array($counter=>$this->TeacherLesson->getDataSource()->expression($counter.'-1'), 'num_of_students'=>$this->TeacherLesson->getDataSource()->expression('num_of_students+1')));
            $this->TeacherLesson->save();
			
			//TODO: check num_of_students - if exceed max_students
		}*/
		
		
		//Update user lesson stage
		$this->updateAll($updateUserLesson, array('UserLesson.user_lesson_id'=>$userLessonId));
		
		

		$event = new CakeEvent('Model.UserLesson.afterAccept', $this, array('user_lesson'=>$userLessonData, 'data'=>$updateUserLesson, 'by_user_id'=>$byUserId));
		$this->getEventManager()->dispatch($event);
		
		
		return true;
	}

    public function getAcceptLessonCounter($stage) {
        return (($stage==USER_LESSON_PENDING_STUDENT_APPROVAL || $stage==USER_LESSON_RESCHEDULED_BY_TEACHER) ? 'num_of_pending_invitations' : 'num_of_pending_join_requests');
    }


    public function reProposeRequest($userLessonId, $byUserId, array $data=array()) {
        if(!$data) {
            //Nothing to change
            return true;
        }

        //Find the lesson
        $this->recursive = -1;
        $userLessonData = $this->findByUserLessonId($userLessonId);
        if(!$userLessonData) {
            return false;
        }
        $userLessonData = $userLessonData['UserLesson'];

        if(!empty($userLessonData['teacher_lesson_id'])) {
            //Only MEW lessons that not been approved yet can be re-propose
            return false;
        }

        //Only those stages can be changed
        if(!in_array(intval($userLessonData['stage']), array(   USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_PENDING_TEACHER_APPROVAL,
                                                                USER_LESSON_RESCHEDULED_BY_TEACHER, USER_LESSON_RESCHEDULED_BY_STUDENT))) {
            return false;
        }

        //Remove unauthorized fields
        $allowedFields = array('datetime', 'duration_minutes', '1_on_1_price', 'max_students', 'full_group_total_price');
        foreach($data AS $field=>$value) {
            if(!in_array($field, $allowedFields)) {
                unset($data['field']);
            }
        }

        //set new stage, NO-NEED to change counters, because we check that there is no 'teacher_lesson_id'
        if($userLessonData['student_user_id']==$byUserId) {
            $data['stage'] = USER_LESSON_RESCHEDULED_BY_STUDENT;
        } else if($userLessonData['teacher_user_id']==$byUserId) {
            $data['stage'] = USER_LESSON_RESCHEDULED_BY_TEACHER;
        } else {
            //User not allowed to do it
            return false;
        }


        $event = new CakeEvent('Model.UserLesson.beforeReProposeRequest', $this, array('user_lesson'=>$userLessonData, 'data'=>$data, 'by_user_id'=>$byUserId));
        $this->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            return false;
        }

        $this->id = $userLessonId;
        $this->set($data);
        $this->save();

        $event = new CakeEvent('Model.UserLesson.afterReProposeRequest', $this, array('user_lesson'=>$userLessonData, 'data'=>$data, 'by_user_id'=>$byUserId));
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
		
		$event = new CakeEvent('Model.UserLesson.beforeRate', $this, array('user_lesson'=>$userLessonData, 'data'=>$updateUserLesson, 'by_user_id'=>$byUserId));
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

        $event = new CakeEvent('Model.UserLesson.afterRate', $this, array('user_lesson'=>$userLessonData, 'data'=>$updateUserLesson, 'by_user_id'=>$byUserId));
		$this->getEventManager()->dispatch($event);

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
		return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId, 'UserLesson.teacher_lesson_id IS NOT NULL'), '>', $limit, $page,
                                    array(USER_LESSON_ACCEPTED), array(SUBJECT_TYPE_OFFER, SUBJECT_TYPE_REQUEST));
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

    public function getBooking($studentUserId, $limit=null, $page=1) {
        return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId), '>', $limit, $page, array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT),
                                                                                                            array(SUBJECT_TYPE_OFFER, SUBJECT_TYPE_REQUEST));
    }
	public function getInvitations($studentUserId, $limit=null, $page=1) {
		$this->Subject;
		return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId), '>', $limit, $page, array(USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER),
                                                                                                            array(SUBJECT_TYPE_OFFER, SUBJECT_TYPE_REQUEST));
	}
	
	/*public function withTeacherReview($studentUserId, $limit=null, $page=1) {
		return $this->getLessons(array('student_user_id'=>$studentUserId), null, $limit, $page, array(USER_LESSON_PENDING_STUDENT_RATING, USER_LESSON_DONE));
	}*/
	public function waitingStudentReview($studentUserId, $limit=null, $page=1) {
		return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId), null, $limit, $page, array(USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_STUDENT_RATING));
	}


	//Lessons that waiting for the student to approval and SUBJECT_TYPE_OFFER
	public function getTeacherInvitations($teacherUserId, $subjectId=null, $limit=null, $page=1) {
		$this->Subject;
		$conditions = array('UserLesson.teacher_user_id'=>$teacherUserId, 'UserLesson.teacher_lesson_id IS NULL');
		if($subjectId) {
			$conditions['UserLesson.subject_id'] = $teacherUserId;
		}
		return $this->getLessons($conditions, '>', $limit, $page, array(USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER), SUBJECT_TYPE_OFFER);
	}

    //Get lessons that waiting for student to approval and SUBJECT_TYPE_REQUEST
	public function getPendingProposedTeacherLessons($teacherUserId, $subjectId=null, $limit=null, $page=1) {
        $this->unbindModel(array('belongsTo'=>array('Teacher', 'TeacherLesson')));

        $conditions = array('UserLesson.teacher_user_id'=>$teacherUserId, 'UserLesson.teacher_lesson_id IS NULL');
        if($subjectId) {
            $conditions['UserLesson.subject_id'] = $teacherUserId;
        }
        return $this->getLessons($conditions, '>', $limit, $page, array(USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER), SUBJECT_TYPE_REQUEST);
    }

    //Get lesson requests that waiting for the teacher approval
	public function  getWaitingForTeacherApproval($teacherUserId, $subjectId=null, $limit=null, $page=1) {
		$this->unbindModel(array('belongsTo'=>array('Teacher', 'TeacherLesson')));
		
		$conditions = array('UserLesson.teacher_user_id'=>$teacherUserId, 'UserLesson.teacher_lesson_id IS NULL');
		if($subjectId) {
			$conditions['UserLesson.subject_id'] = $teacherUserId;
		}
		return $this->getLessons($conditions, '>', $limit, $page, array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT), array(SUBJECT_TYPE_OFFER, SUBJECT_TYPE_REQUEST));
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