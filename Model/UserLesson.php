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
            'name'=> array(
                'between' => array(
                    'required'	=> 'create',
                    'allowEmpty'=> false,
                    'rule'    	=> array('between', 2, 45),
                    'message' 	=> 'Between 2 to 45 characters'
                )
            ),
            'description'=> array(
                'minLength' 	=> array(
                    'required'	=> 'create',
                    'allowEmpty'=> false,
                    'rule'    	=> array('minLength', 15),
                    'message' 	=> 'Must be more then 15 characters'
                )
            ),
            'subject_id'=> array(
                'validate_subject_id' 	=> array(
                    'allowEmpty'=> true,
                    'rule'    	=> 'validateSubjectId',
                    'message' 	=> 'You cannot use this subject'
                )
            ),
            'request_subject_id'=> array(
                'validate_request_subject_id' 	=> array(
                    'allowEmpty'=> true,
                    'rule'    	=> 'validateRequestSubjectId',
                    'message' 	=> 'You cannot offer this subject'
                )
            ),
            //Datetime: Cannot be empty for lesson_type=live, it will be checked in beforeValidate below
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
                    'rule'    	=> array('range', -1, 500),
                    'message' 	=> 'Price must be more then 0 and less then 500'
                )
            ),
           'max_students'=> array(
                'max_students' 	=> array(
                    'allowEmpty'=> true,
                    'required'	=> 'create',
                    'rule'    	=>  'maxStudentsCheck',
                    'message' 	=> 'Error on max group price'
                ),
                'range' 		=> array(
                    'required'	=> 'create',
                    'allowEmpty'=> true,
                    'rule'    	=> array('range', 0, 1025),
                    'message' 	=> 'Lesson must have more then 1 or less then 1024 students'
                ),
            ),

            'full_group_total_price'=> array(
                'price' => array(
                    'allowEmpty'=> true,
                    'rule'    	=> 'numeric',
                    'message' 	=> 'Enter a valid group price'
                ),
                'price_range' => array(
                    'allowEmpty'=> true,
                    'rule'    	=> array('range', -1, 2500),
                    'message' 	=> 'Price must be more then 0 and less then 2500'
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
    public function isFutureDatetime($datetime) {
        return strtotime($datetime['datetime'])>=time();
    }
    public function validateSubjectId($subjectID){
        $subjectID = $subjectID['subject_id'];

        //Load the requested subject
        $subjectData = $this->Subject->findBySubjectId($subjectID);
        if(!$subjectData) {
            $this->invalidate('subject_id', 'Invalid request subject');
        }
        $subjectData = $subjectData['Subject'];

        //Validate its a subject offer
        if($subjectData['type']!=SUBJECT_TYPE_OFFER) {
            $this->invalidate('request_subject_id', 'must be a offer subject');
        }

        //The teacher must be the subject owner
        if(isSet($this->data['UserLesson']['teacher_user_id']) && !empty($this->data['UserLesson']['teacher_user_id'])) {
            if($this->data['UserLesson']['teacher_user_id']!=$subjectData['user_id']) {
                $this->invalidate('request_subject_id', 'The teacher must be the subject owner');
            }
        }

        return true;
    }


    public function validateRequestSubjectId($requestSubjectID){
        $requestSubjectID = $requestSubjectID['request_subject_id'];

        //Load the requested subject
        $requestSubjectData = $this->Subject->findBySubjectId($requestSubjectID);
        if(!$requestSubjectData) {
            $this->invalidate('request_subject_id', 'Invalid request subject');
        }
        $requestSubjectData = $requestSubjectData['Subject'];

        //Validate its a subject request
        if($requestSubjectData['type']!=SUBJECT_TYPE_REQUEST) {
            $this->invalidate('request_subject_id', 'must be a request subject');
        }

        //Validate the the 2 subjects share the same type live/video
        if(isSet($this->data['UserLesson']['lesson_type']) && !empty($this->data['UserLesson']['lesson_type'])) {
            if($requestSubjectData['lesson_type']!=$this->data['UserLesson']['lesson_type']) {
                $this->invalidate('request_subject_id', 'The lesson type must be type of '.$requestSubjectData['type']);
            }
        }

        //Check that the owner of $requestSubjectID is the main student
        /*if(isSet($this->data['UserLesson']['student_user_id']) && !empty($this->data['UserLesson']['student_user_id'])) {
            if($this->data['UserLesson']['student_user_id']!=$requestSubjectData['user_id']) {
                $this->invalidate('request_subject_id', 'The main student must be the owner of the requested subject');
            }
        }*/

        return true;
    }
    public function fullGroupTotalPriceCheck( $price ) {
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

    public function beforeValidate($options=array()) {
        parent::beforeValidate($options);

        App::import('Model', 'Subject');
        Subject::calcFullGroupStudentPriceIfNeeded($this->data['UserLesson'], ($this->id || !empty($this->data['Subject'][$this->primaryKey])));
        Subject::extraValidation($this);


        if(isSet($this->data['UserLesson']['subject_id']) || !empty($this->data['UserLesson']['subject_id'])) {
            $subjectData = $this->Subject->findBySubjectId($this->data['UserLesson']['subject_id']);
            if(!$subjectData) {
                return false;
            }
            $subjectData = $subjectData['Subject'];


            if($subjectData['lesson_type']==LESSON_TYPE_LIVE) {
                //Make sure that datetime is not blank for live lessons and that its a future datetime + 1 hour from now
                $this->validator()->add('datetime', 'datetime', array(
                    'required'	=> 'create',
                    'allowEmpty'=> false,
                    'rule'    	=> array('datetime', 'ymd'),
                    'message' 	=> 'Invalid datetime format'
                ))->add('datetime', 'future_datetime', array(
                    'required'	=> 'create',
                    'allowEmpty'=> false,
                    'rule'    	=> 'isFutureDatetime',
                    'message' 	=> 'Please set a future datetime'
                ));

            } else if($subjectData['lesson_type']==LESSON_TYPE_VIDEO) {

                //Allow datetime to be blank, or be set to now || any future datetime
                $this->validator()->add('datetime', 'datetime', array(
                    'allowEmpty'=> true,
                    'rule'    	=> array('datetime', 'ymd'),
                    'message' 	=> 'Invalid datetime format'
                ));
            }

        }
    }

    /**
     * Use to offer a teacher offer-subject against student request-subject
     * @param $teacherOfferSubjectId
     * @param $studentRequestSubjectId
     * @param $datetime
     * @return bool|mixed
     */
    public function lessonOffer($teacherOfferSubjectId, $studentRequestSubjectId, $datetime) {
        //Find the teacher subject
        App::import('Model', 'Subject');
        $subjectObj = new Subject();

        $subjectObj->recursive = -1;
        $subjectData = $subjectObj->findBySubjectId($studentRequestSubjectId);
        if( !$subjectData ) {
            return false;
        }
        $subjectData = $subjectData['Subject'];

        return $this->lessonRequest($teacherOfferSubjectId, $subjectData['user_id'], $datetime, true, array('request_subject_id'=>$studentRequestSubjectId));
    }


	/**
	 * 
	 * Users makeing new lesson requests from teacher
	 * @param unknown_type $subjectId
	 * @param unknown_type $userId - the user/teacher id that does not own the subject 
	 * @param unknown_type $datetime
	 * @param unknown_type $reverseStage - Reverse the stages, in use for teacher invite students, or on SUBJECT_TYPE_REQUEST - sending requests to students
	 */
	public function lessonRequest( $subjectId, $userId, $datetime=null, $reverseStage=false, $extra=array() ) {
		//Find the teacher subjcet
		App::import('Model', 'Subject');
		$subjectObj = new Subject();
		
		$subjectObj->recursive = -1;
		$subjectData = $subjectObj->findBySubjectId($subjectId);
		if( !$subjectData ) {
			return false;
		}
        $subjectData = $subjectData['Subject'];

        //User lesson must be for lesson type offer
        if($subjectData['type']!=SUBJECT_TYPE_OFFER) {
            return false;
        }

		//Prepare the user lesson generic data
		$userLesson = array(
			//'teacher_lesson_id'		=> null,
			'subject_id'				=> $subjectId,
			'teacher_user_id'			=> ($subjectData['type']==SUBJECT_TYPE_OFFER ? $subjectData['user_id']              : $userId),
			'student_user_id'			=> ($subjectData['type']==SUBJECT_TYPE_OFFER ? $userId                              : $subjectData['user_id']),
			'stage'						=> ($subjectData['type']==SUBJECT_TYPE_OFFER ? USER_LESSON_PENDING_TEACHER_APPROVAL : USER_LESSON_PENDING_STUDENT_APPROVAL),
			'subject_category_id'		=> $subjectData['subject_category_id'],
			'forum_id'		            => $subjectData['forum_id'],
			'datetime'					=> $datetime ? $this->Subject->datetimeToStr($datetime) : null,
			'lesson_type'				=> $subjectData['lesson_type'],
			'language'				    => $subjectData['language'],
			'name'						=> $subjectData['name'],
			'description'				=> $subjectData['description'],
			'duration_minutes'			=> $subjectData['duration_minutes'],
			'max_students'				=> $subjectData['max_students'],
			'1_on_1_price'				=> $subjectData['1_on_1_price'],
			'full_group_student_price'	=> $subjectData['full_group_student_price'],
			'full_group_total_price'	=> $subjectData['full_group_total_price']
		);


        //Reverse the stages, in use for teacher invite students, or on SUBJECT_TYPE_REQUEST - sending requests to teachers
        if($reverseStage) {
           $userLesson['stage'] = ($userLesson['stage']==USER_LESSON_PENDING_TEACHER_APPROVAL) ? USER_LESSON_PENDING_STUDENT_APPROVAL : USER_LESSON_PENDING_TEACHER_APPROVAL;
           $userId = ($userId==$userLesson['teacher_user_id']) ? $userLesson['student_user_id'] : $userLesson['teacher_user_id'];
        }

        //Set the end of the lesson, video lesson end date is first-watching-time+2 days
        if($subjectData['lesson_type']==LESSON_TYPE_LIVE) {
            $userLesson['end_datetime'] = $this->Subject->datetimeToStr($datetime, $subjectData['duration_minutes']);
        }
        $userLesson = am($userLesson, $extra);

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

        App::import('Model', 'Subject');
		//users can't join video lessons, only to live lessons
		if($teacherLessonData['lesson_type']==LESSON_TYPE_VIDEO) {
			return false;
		}
		if(!empty($teacherLessonData['request_subject_id']) && is_null($studentUserId)) {
			$subjectData = $this->Subject->findBySubjectId($teacherLessonData['request_subject_id']);
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
			'request_subject_id'        => $teacherLessonData['request_subject_id'],
			'datetime'					=> $teacherLessonData['datetime'],
			'end_datetime'				=> $teacherLessonData['end_datetime'],
			'stage'						=> $stage,
            'subject_category_id'		=> $teacherLessonData['subject_category_id'],
            'forum_id'		            => $teacherLessonData['forum_id'],
			'lesson_type'				=> $teacherLessonData['lesson_type'],
			'language'				    => $teacherLessonData['language'],
			'name'						=> $teacherLessonData['name'],
			'description'				=> $teacherLessonData['description'],
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

			//if the cancel user is the TeacherLesson owner student, cancel all other invitations+teacher lesson
            $teacherLessonData = $teacherLessonObj->findByTeacherLessonId($userLessonData['teacher_lesson_id']);
            if($teacherLessonData['TeacherLesson']['student_user_id']==$byUserId) {
                if(!$teacherLessonObj->cancel($teacherLessonData['student_user_id'])) {
                    return false;
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
		$this->Subject;
		
		
		App::import('Model', 'User');
		$userObj = new User();
		
		$userType = '';
		if($userLessonData['teacher_user_id']==$byUserId) {
			//Check if teacher can set rating
			if( $userLessonData['lesson_type']!=LESSON_TYPE_LIVE || ($userLessonData['stage']!=USER_LESSON_PENDING_RATING && $userLessonData['stage']!=USER_LESSON_PENDING_TEACHER_RATING) ) {
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
			$newStage = ($userLessonData['stage']==USER_LESSON_PENDING_RATING) ?
                            ( $userLessonData['lesson_type']!=LESSON_TYPE_LIVE ? USER_LESSON_PENDING_TEACHER_RATING : USER_LESSON_DONE ) : //teacher can rate only live lessons, otherwise don't let him
                        USER_LESSON_DONE;


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
	 * Get all student lessons for given $stages in a given year/month
	 * @param unknown_type $studentUserId
	 * @param unknown_type $stages
	 * @param unknown_type $year
	 * @param unknown_type $month
	 */
	public function getLiveLessonsByDate( $studentUserId, $year=null, $month=null, $stages=array() ) {
		$this->getDataSource();
		$this->Subject; //Init const LESSON_TYPE_LIVE

        if(!$year) {
            $year = date('Y');
            $month = date('m');
        } else if(!$month) {
            $month = date('m');
        }

		$startDate = $year.'-'.($month ? $month : 1).'-1';
		$endDate = $year.'-'.($month ? $month : 12).'-1';

		$conditions = array('student_user_id'=>$studentUserId, $this->alias.'.lesson_type'=>LESSON_TYPE_LIVE,
							'OR'=>array(
                                    'datetime BETWEEN ? AND ?' => array($startDate, $this->getDataSource()->expression('date_add(\''.$endDate.'\',interval 1 month)')),
                                    'end_datetime BETWEEN ? AND ?' => array($startDate, $this->getDataSource()->expression('date_add(\''.$endDate.'\',interval 1 month)'))

                                )
                            );
		if($stages) {
			$conditions['stage'] = $stages;
		}

		return $this->find('all', array('conditions'=>$conditions));
	}
	
	public function getUpcomming($studentUserId, $limit=null, $page=1) {
		$this->Subject;
		return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId, 'UserLesson.teacher_lesson_id IS NOT NULL'), '>', $limit, $page,
                                    array(USER_LESSON_ACCEPTED));
	}

	public function getArchive($studentUserId, $limit=null, $page=1) {

	    $conditions = array('UserLesson.student_user_id'=>$studentUserId,
						'OR'=>array(array('UserLesson.end_datetime<NOW()', 'UserLesson.end_datetime IS NOT NULL'),
									'stage'=>array(	USER_LESSON_DENIED_BY_TEACHER, USER_LESSON_DENIED_BY_STUDENT,
													USER_LESSON_CANCELED_BY_TEACHER, USER_LESSON_CANCELED_BY_STUDENT,
													USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_TEACHER_RATING, USER_LESSON_PENDING_STUDENT_RATING,
													USER_LESSON_DONE)));
        //TODO: use  $this->getLessons
	    return $this->find('all', array('conditions'=>$conditions,
										'order'=>'datetime',
										'limit'=>( $limit ? $limit : null),
										'page'=>$page
					));
	
		
	}

    public function getBooking($studentUserId, $limit=null, $page=1) {
        return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId), '>', $limit, $page, array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT));
    }
	public function getInvitations($studentUserId, $limit=null, $page=1) {
		$this->Subject;
		return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId), '>', $limit, $page, array(USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER));
	}
	
	/*public function withTeacherReview($studentUserId, $limit=null, $page=1) {
		return $this->getLessons(array('student_user_id'=>$studentUserId), null, $limit, $page, array(USER_LESSON_PENDING_STUDENT_RATING, USER_LESSON_DONE));
	}*/
	public function waitingStudentReview($studentUserId, $limit=null, $page=1) {
		return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId), null, $limit, $page, array(USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_STUDENT_RATING));
	}


	//Lessons that waiting for the student to approval
	public function getTeacherInvitations($teacherUserId, $subjectId=null, $limit=null, $page=1) {
		$this->Subject;
		$conditions = array('UserLesson.teacher_user_id'=>$teacherUserId, 'UserLesson.teacher_lesson_id IS NULL');
		if($subjectId) {
			$conditions['UserLesson.subject_id'] = $teacherUserId;
		}
		return $this->getLessons($conditions, '>', $limit, $page, array(USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER));
	}

    /*//Get lessons that waiting for student to approval and
	public function getPendingProposedTeacherLessons($teacherUserId, $subjectId=null, $limit=null, $page=1) {
        $this->unbindModel(array('belongsTo'=>array('Teacher', 'TeacherLesson')));

        $conditions = array('UserLesson.teacher_user_id'=>$teacherUserId, 'UserLesson.teacher_lesson_id IS NULL');
        if($subjectId) {
            $conditions['UserLesson.subject_id'] = $teacherUserId;
        }
        $conditions[] = 'request_subject_id IS NOT NULL';
        return $this->getLessons($conditions, '>', $limit, $page, array(USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER));
    }*/

    //Get lesson requests that waiting for the teacher approval
	public function  getWaitingForTeacherApproval($teacherUserId, $subjectId=null, $limit=null, $page=1) {
		$this->unbindModel(array('belongsTo'=>array('Teacher', 'TeacherLesson')));
		
		$conditions = array('UserLesson.teacher_user_id'=>$teacherUserId, 'UserLesson.teacher_lesson_id IS NULL');
		if($subjectId) {
			$conditions['UserLesson.subject_id'] = $teacherUserId;
		}
		return $this->getLessons($conditions, '>', $limit, $page, array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT));
	}

	public function waitingTeacherReview($teacehrUserId, $limit=null, $page=1) {
        $this->Subject;
        //Teacher cannot rate video lesson student.
		return $this->getLessons(array('UserLesson.teacher_user_id'=>$teacehrUserId, 'lesson_type'=>LESSON_TYPE_LIVE), null, $limit, $page, array(USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_TEACHER_RATING));
	}
	
	public function getLessons($conditions, $time='>', $limit=null, $page=1, $stage=array()) {
		
		$find = 'all';
		App::import('Model', 'Subject');
		
		
		if(is_numeric($time)) {
			$conditions['UserLesson.user_lesson_id'] = $time;
			$find = 'first';
		} else {
		
			if($stage) {
				$conditions['UserLesson.stage'] = $stage;
			}
			if($time) {
                //$conditions[] = 'UserLesson.end_datetime'.$time.'NOW()';
                if($time=='>') { //Future lessons
                    $conditions['AND'][] = array(
                        'OR'=>array(
                            array('UserLesson.end_datetime > NOW()'),
                            array('UserLesson.end_datetime IS NULL')
                            )
                    );

                } else { //Past lessons
                    $conditions['AND'][] = array(
                        array('UserLesson.end_datetime'.$time.'NOW()'),
                        array('UserLesson.end_datetime IS NOT NULL')
                    );

                }

			}
		}
		
		
		//DboSource::expression('');
		return $this->find($find, array('conditions'=>$conditions, 
										'order'=>'datetime',
										'limit'=>( $limit ? $limit : null),
										'page'=>$page
					));
	}

    public function getVideoLessonStatus($subjectId, $userId, $updateNullEndDatetime=true) {
        //Get subject
        $this->Subject->recursive = -1;
        $subjectData = $this->Subject->findBySubjectId($subjectId);
        if(!$subjectData || !$subjectData['Subject']['is_enable'] || $subjectData['Subject']['lesson_type']!=LESSON_TYPE_VIDEO) {
            return false;
        }
        $subjectData = $subjectData['Subject'];

        //Get user lesson data
        $this->recursive = -1;
        $this->cacheQueries = false;
        $userLessonsData = $this->find('all', array('conditions'=>array('subject_id'=>$subjectId, 'student_user_id'=>$userId), 'order'=>'user_lesson_id DESC')); //Order is so the user can buy the lesson again to avoid ads

        //Check if there is existing request
        $isFreeVideo = $hasEnded = $teacherLessonId = $userLessonId = $approved = $waitingTeacherApproval = $waitingStudentApproval = false;


        if($userLessonsData) {
            foreach($userLessonsData AS $userLessonData) {
                $userLessonData = $userLessonData['UserLesson'];
                if(in_array($userLessonData['stage'], array(USER_LESSON_ACCEPTED,
                                                            USER_LESSON_PENDING_RATING,
                                                            USER_LESSON_PENDING_TEACHER_RATING,
                                                            USER_LESSON_PENDING_STUDENT_RATING,
                                                            USER_LESSON_DONE))) {

                    $approved = true;
                    $teacherLessonId = $userLessonData['teacher_lesson_id'];


                    //Check if lesson has ended
                    if(!empty($userLessonData['end_datetime'])) {
                        //echo $this->Subject->datetimeToStr(strtotime($userLessonData['end_datetime'])),', ', $this->Subject->datetimeToStr(time()); die;
                        if(strtotime($userLessonData['end_datetime'])<time()) {
                            $hasEnded = true;
                        }
                    } else if(empty($userLessonData['datetime']) && $updateNullEndDatetime) {
                        $this->setVideoStartEndDatetime($userLessonData['user_lesson_id'], $userLessonData['teacher_lesson_id']);
                    }

                    //We need to check UserLesson beacuse the subject price may changed until now
                    if(!$userLessonData['1_on_1_price']) {
                        $isFreeVideo = true;
                    }

                    break;
                } else if(in_array($userLessonData['stage'], array(USER_LESSON_PENDING_TEACHER_APPROVAL))) {
                    $waitingTeacherApproval = true;
                    $userLessonId = $userLessonData['user_lesson_id'];
                    break;
                } else if(in_array($userLessonData['stage'], array(USER_LESSON_PENDING_STUDENT_APPROVAL))) {
                    $waitingStudentApproval = true;
                    $userLessonId = $userLessonData['user_lesson_id'];
                    break;
                }

            }
        }


        $showPayment = $showPendingTeacherApproval = $showPendingUserApproval = $showVideo = false;

        if($approved) {
            $showVideo = true;
        } else {
            if($waitingStudentApproval) {
                $showPendingUserApproval = true;
            } else if($waitingTeacherApproval) {
                $showPendingTeacherApproval = true;
            } else {
                //There is no UserLesson

                if(!$subjectData['1_on_1_price']) { //Free video
                    $isFreeVideo = true;

                    //Make UserLesson request
                    $this->lessonRequest($subjectId, $userId, time());
                    $this->cacheQueries = false;
                    $userLessonData = $this->findByUserLessonId($this->id);

                    //Check if the new lesson request is not auto-approved
                    if(in_array($userLessonData['UserLesson']['stage'], array( USER_LESSON_ACCEPTED,
                                                                                USER_LESSON_PENDING_RATING,
                                                                                USER_LESSON_PENDING_TEACHER_RATING,
                                                                                USER_LESSON_PENDING_STUDENT_RATING,
                                                                                USER_LESSON_DONE))) {

                        $showVideo = true;
                    } else {
                        //Lesson is waiting for teacher approval
                        $showPendingTeacherApproval = true;
                    }
                } else {
                    $showPayment = true;
                }
            }
        }

        return array(
            'show_video'                =>$showVideo,
            'pending_teacher_approval'  =>$showPendingTeacherApproval,
            'pending_user_approval'     =>$showPendingUserApproval,
            'payment_needed'            =>$showPayment,
            'user_lesson_id'            =>$userLessonId,
            'teacher_lesson_id'         =>$teacherLessonId,
            'has_ended'                 =>$hasEnded,
            'is_free'                   =>$isFreeVideo
        );
    }
    private function setVideoStartEndDatetime($userLessonId, $teacherLessonId) {
        //Update this UserLesson with end_datetime
        $this->create();

         $this->saveAssociated(
                array(
                    'UserLesson'=>array('user_lesson_id'=>$userLessonId, 'datetime'=>$this->getDataSource()->expression('NOW()'), 'end_datetime'=>$this->getDataSource()->expression('date_add(NOW(),interval '.LESSON_TYPE_VIDEO_NO_ADS_TIME_SEC.' SECOND)')),
                    'TeacherLesson'=>array('teacher_lesson_id'=>$teacherLessonId, 'datetime'=>$this->getDataSource()->expression('NOW()'), 'end_datetime'=>$this->getDataSource()->expression('date_add(NOW(),interval '.LESSON_TYPE_VIDEO_NO_ADS_TIME_SEC.' SECOND)'))
                )
        );
    }

    public function getLiveLessonStatus($teacherLessonId, $userId) {

        //Find teacher lesson
        $this->TeacherLesson->recursive = -1;
        $tlData = $this->TeacherLesson->find('first', array('teacher_lesson_id'=>$teacherLessonId));
        if(!$tlData || $tlData['TeacherLesson']['lesson_type']!=LESSON_TYPE_LIVE) {
            return false;
        }
        $tlData = $tlData['TeacherLesson'];

        //prepare the response
        $return =  array(
            'in_process'                =>false,
            'overdue'                   =>false,
            'about_to_start'            =>false,

            'pending_teacher_approval'  =>false,
            'pending_user_approval'     =>false,
            'payment_needed'            =>false,

            'is_teacher'                =>($userId==$tlData['teacher_user_id']),
            'teacher_lesson_id'         =>$teacherLessonId,
            'subject_id'                =>$tlData['subject_id'],
            'user_lesson_id'            =>false,
        );



        //Check the time status of the lesson
        $timing = $this->TeacherLesson->getLessonTiming($tlData['datetime'], $tlData['duration_minutes']);
        $return[$timing] = true;

        //Check if this user is register for this lesson or no
        $this->recursive = -1;
        $this->cacheQueries = false;
        $userLessonsData = $this->find('all', array('conditions'=>array('teacher_lesson_id'=>$teacherLessonId, 'student_user_id'=>$userId)));
        if($userLessonsData) {
            foreach($userLessonsData AS $userLessonData) {
                $userLessonData = $userLessonData['UserLesson'];
                if(in_array($userLessonData['stage'], array(USER_LESSON_ACCEPTED,
                                                            USER_LESSON_PENDING_RATING,
                                                            USER_LESSON_PENDING_TEACHER_RATING,
                                                            USER_LESSON_PENDING_STUDENT_RATING,
                                                            USER_LESSON_DONE))) {

                    $return['approved'] = true;
                    break;
                } else if(in_array($userLessonData['stage'], array(USER_LESSON_PENDING_TEACHER_APPROVAL))) {
                    $return['pending_teacher_approval'] = true;
                    $return['user_lesson_id'] = $userLessonData['user_lesson_id'];
                    break;
                } else if(in_array($userLessonData['stage'], array(USER_LESSON_PENDING_STUDENT_APPROVAL))) {
                    $return['pending_user_approval'] = true;
                    $return['user_lesson_id'] = $userLessonData['user_lesson_id'];
                    break;
                }
            }
        }


        if(!$return['approved'] && !$return['pending_user_approval'] && !$return['pending_teacher_approval'] && !$return['is_teacher']) {
            //There is no UserLesson and this is not the teacher

            if(!$tlData['1_on_1_price']) { //Free lesson

                //Make UserLesson request
                $this->lessonRequest($tlData['subject_id'], $userId, time());
                $this->cacheQueries = false;
                $userLessonData = $this->findByUserLessonId($this->id);

                //Check if the new lesson request is not auto-approved
                if(in_array($userLessonData['UserLesson']['stage'], array( USER_LESSON_ACCEPTED,
                                                                            USER_LESSON_PENDING_RATING,
                                                                            USER_LESSON_PENDING_TEACHER_RATING,
                                                                            USER_LESSON_PENDING_STUDENT_RATING,
                                                                            USER_LESSON_DONE))) {
                    $return['approved'] = true;
                } else {
                    //Lesson is waiting for teacher approval
                    $return['pending_teacher_approval'] = true;
                }
            } else {
                $return['payment_needed'] = true;
            }
        }


        return $return;
    }
}
?>