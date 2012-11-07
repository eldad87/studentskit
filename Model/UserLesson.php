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
    public $actsAs = array('LanguageFilter', 'Time');
	public $belongsTo = array(
					'Teacher' => array(
						'className' => 'User',
						'foreignKey'=>'teacher_user_id',
						'fields'=>array('first_name', 'last_name', 'username', 'image', 'image_source', 'teacher_avarage_rating', 'teacher_total_lessons')
					),
					'Student' => array(
						'className' => 'User',
						'foreignKey'=>'student_user_id',
						'fields'=>array('first_name', 'last_name', 'username', 'image', 'image_source', 'student_avarage_rating', 'student_total_lessons')
					),
					'Subject' => array(
						'className' => 'Subject',
						'foreignKey'=>'subject_id',
						'fields'=>array('avarage_rating', 'image', 'image_source', 'is_enable')
					),
					'TeacherLesson' => array(
						'className' => 'TeacherLesson',
						'foreignKey'=>'teacher_lesson_id',
						'fields'=>array('num_of_students', 'max_students', 'is_deleted')
					)
				);
				
		public $validate = array(
            'name'=> array(
                'between' => array(
                    'required'	=> 'create',
                    'allowEmpty'=> false,
                    'rule'    	=> array('between', 2, 45),
                    'message' 	=> 'Between %d to %d characters'
                )
            ),
            'description'=> array(
                'minLength' 	=> array(
                    'required'	=> 'create',
                    'allowEmpty'=> false,
                    'rule'    	=> array('minLength', 15),
                    'message' 	=> 'Must be more then %d characters'
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
                    'message' 	=> 'Lesson must be more then %d minutes and less then %d minutes'
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
                    'message' 	=> 'Price must be more then %d and less then %d'
                )
            ),
           'max_students'=> array(
                'max_students' 	=> array(
                    'allowEmpty'=> true,
                    'required'	=> 'create',
                    'rule'    	=> 'maxStudentsCheck',
                    'message' 	=> 'Error on max group price'
                ),
                'range' 		=> array(
                    'required'	=> 'create',
                    'allowEmpty'=> true,
                    'rule'    	=> array('range', 0, 1025),
                    'message' 	=> 'Lesson must have more then %d or less then %d students'
                ),
            ),

           /* 'full_group_total_price'=> array(
                'price' => array(
                    'allowEmpty'=> true,
                    'rule'    	=> 'numeric',
                    'message' 	=> 'Enter a valid group price'
                ),
                'price_range' => array(
                    'allowEmpty'=> true,
                    'rule'    	=> array('range', -1, 2501),
                    'message' 	=> 'Price must be more then %d and less then %d'
                ),
                'full_group_total_price' 	=> array(
                    //'required'	=> 'create',
                    'allowEmpty'=> true,
                    'rule'    	=> 'fullGroupTotalPriceCheck',
                    'message' 	=> 'You must set group price'
                )
            ),*/
            'full_group_student_price'=> array(
                'price' => array(
                    'allowEmpty'=> true,
                    'rule'    	=> 'numeric',
                    'message' 	=> 'Enter a valid group price'
                ),
                'full_group_total_price' 	=> array(
                    //'required'	=> 'create',
                    'allowEmpty'=> true,
                    'rule'    	=> 'fullGroupTotalPriceCheck',
                    'message' 	=> 'You must set a student full group price'
                )
            ),
			'comment_by_student' => array(
				'between' => array(
					'rule'			=> array('between', 15, 255),
					'on'			=> 'update',
					'allowEmpty'	=> false,
					'message' 		=> 'Please write a review with %d-%d characters'
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
					'message' 		=> 'Please enter a number between %d and %d'
				),
			),
		);

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        static $eventListenterAttached = false;

        if(!$eventListenterAttached) {
            //Connect the event manager of this model
            App::import( 'Event', 'UserLessonEventListener');
            $ulel =& UserLessonEventListener::getInstance();
            CakeEventManager::instance()->attach($ulel);
            $eventListenterAttached = true;
        }
    }
    public function isFutureDatetime($datetime) {
        if(isSet($datetime['datetime']) && is_array($datetime)) {
            $datetime = $datetime['datetime'];
        }

        return $this->toServerTime($datetime)>=$this->timeExpression( 'now', false );
    }
    //Make sure date time is 1 hour or more from now
    public function isFuture1HourDatetime($datetime) {
        if(isSet($datetime['datetime']) && is_array($datetime)) {
            $datetime = $datetime['datetime'];
        }

        return $this->toServerTime($datetime)>=$this->timeExpression( 'now +1 hour', false );
    }
    public function validateSubjectId($subjectID){
        $subjectID = $subjectID['subject_id'];

        //Load the requested subject
        $subjectData = $this->Subject->findBySubjectId($subjectID);
        if(!$subjectData) {
            $this->invalidate('subject_id', __('Invalid request subject'));
        }
        $subjectData = $subjectData['Subject'];

        //Validate its a subject offer
        if($subjectData['type']!=SUBJECT_TYPE_OFFER) {
            $this->invalidate('request_subject_id', __('must be a offer subject'));
        }

        //The teacher must be the subject owner
        if(isSet($this->data['UserLesson']['teacher_user_id']) && !empty($this->data['UserLesson']['teacher_user_id'])) {
            if($this->data['UserLesson']['teacher_user_id']!=$subjectData['user_id']) {
                $this->invalidate('request_subject_id', __('The teacher must be the subject owner'));
            }
        }

        return true;
    }


    public function validateRequestSubjectId($requestSubjectID){
        $requestSubjectID = $requestSubjectID['request_subject_id'];

        //Load the requested subject
        $requestSubjectData = $this->Subject->findBySubjectId($requestSubjectID);
        if(!$requestSubjectData) {
            $this->invalidate('request_subject_id', __('Invalid request subject'));
        }
        $requestSubjectData = $requestSubjectData['Subject'];

        //Validate its a subject request
        if($requestSubjectData['type']!=SUBJECT_TYPE_REQUEST) {
            $this->invalidate('request_subject_id', __('must be a request subject'));
        }

        //Validate the the 2 subjects share the same type live/video
        if(isSet($this->data['UserLesson']['lesson_type']) && !empty($this->data['UserLesson']['lesson_type'])) {
            if($requestSubjectData['lesson_type']!=$this->data['UserLesson']['lesson_type']) {
                if($requestSubjectData['type']==LESSON_TYPE_LIVE) {
                    $this->invalidate('request_subject_id', __('Please chose a LIVE lesson as a suggestion') );
                }  else if($requestSubjectData['type']==LESSON_TYPE_VIDEO) {
                    $this->invalidate('request_subject_id', __('Please chose a VIDEO lesson as a suggestion') );
                }
            }
        }

        //Check that the owner of $requestSubjectID is the main student
        /*if(isSet($this->data['UserLesson']['student_user_id']) && !empty($this->data['UserLesson']['student_user_id'])) {
            if($this->data['UserLesson']['student_user_id']!=$requestSubjectData['user_id']) {
                $this->invalidate('request_subject_id', __('The main student must be the owner of the requested subject'));
            }
        }*/

        return true;
    }
    public function fullGroupTotalPriceCheck( $price ) {
        if(!isSet($this->data[$this->name]['max_students']) || empty($this->data[$this->name]['max_students'])) {
            $this->invalidate('max_students', __('Please enter a valid max students'));
            //return false;
        } else  {
            if(	isSet($this->data[$this->name]['full_group_student_price']) && !empty($this->data[$this->name]['full_group_student_price']) &&
                isSet($this->data[$this->name]['1_on_1_price']) && $this->data[$this->name]['1_on_1_price']) {
                if($this->data[$this->name]['full_group_student_price']>$this->data[$this->name]['1_on_1_price']) {
                    $this->invalidate('full_group_student_price', sprintf(__('Full group student price must be less or equal to 1 on 1 price (%d)'), $this->data[$this->name]['1_on_1_price']) );
                }
            }
        }
        return true;
    }
    public function maxStudentsCheck( $maxStudents ) {
        if($maxStudents['max_students']>1 && (!isSet($this->data[$this->name]['full_group_student_price']) || !$this->data[$this->name]['full_group_student_price'])) {
            $this->invalidate('full_group_student_price', __('Please enter a valid full group student price or set Max students to 1'));
            //return false;
        }
        return true;
    }

    public function beforeValidate($options=array()) {
        parent::beforeValidate($options);

        App::import('Model', 'Subject');

        $exists = $this->exists(!empty($this->data['UserLesson'][$this->primaryKey]) ? $this->data['UserLesson'][$this->primaryKey] : null);
        Subject::calcFullGroupPriceIfNeeded($this->data['UserLesson'], $exists);
        Subject::extraValidation($this);

        $lessonType = false;

        /*
         * Make sure the actions are made on future lessons and a least 1 hour before lesson starts (negotiate can change the datetime)
         *
         * 1. Order/Join request are new records, therefore they must have datetime in-order to pass validation
         * 2. Make sure datetime is not set (I.e. by negotiation)
         * 3. State exists - this action must be made by lessonRequest/joinRequest/reProposeRequest/acceptRequest/cancelRequest
         * 4. This check must apply only on user actions and not by daemon/rating
         *
         * 5. There is no need to limit this check to LIVE lessons only. the reason is that VIDEO lessons get datetime only on the first watch
        */

        if( $exists && // (1) Record exists
            (!isSet($this->data['UserLesson']['datetime']) && empty($this->data['UserLesson']['datetime'])) && // (2) No datetime
            isSet($this->data['UserLesson']['stage']) && !empty($this->data['UserLesson']['stage']) && // (3) State exists
            in_array($this->data['UserLesson']['stage'], array( USER_LESSON_RESCHEDULED_BY_STUDENT, USER_LESSON_RESCHEDULED_BY_TEACHER, // (4) Negotiate
                                                                USER_LESSON_ACCEPTED, //(4) Accept
                                                                USER_LESSON_CANCELED_BY_TEACHER, USER_LESSON_DENIED_BY_TEACHER, //(4) Cancel
                                                                USER_LESSON_CANCELED_BY_STUDENT, USER_LESSON_DENIED_BY_STUDENT))) {


                //Find record
                $this->recursive = -1;
                $userLessonData = $this->findByUserLessonId($this->id ? $this->id : $this->data['UserLesson'][$this->primaryKey]);
                $lessonType = $userLessonData['UserLesson']['lesson_type'];


                // (5)
                $this->data['UserLesson']['datetime'] = $userLessonData['UserLesson']['datetime'];
        }



        //Get the lessonType from the subject
        if(!$lessonType && isSet($this->data['UserLesson']['subject_id']) && !empty($this->data['UserLesson']['subject_id'])) {
            $subjectData = $this->Subject->findBySubjectId($this->data['UserLesson']['subject_id']);
            if(!$subjectData) {
                return false;
            }

            $lessonType = $subjectData['Subject']['lesson_type'];
        }



        if($lessonType==LESSON_TYPE_LIVE) {
            $datetimeErrorMessage = $exists ? __('You cannot operate on a lesson that already started or will start in less then 1 hour') : __('Please set a 1-hour future datetime');

            //Make sure that datetime is not blank for live lessons and that its a future datetime + 1 hour from now
            $this->validator()->add('datetime', 'datetime', array(
                'required'	=> 'create',
                'allowEmpty'=> false,
                'rule'    	=> array('datetime', 'ymd'),
                'message' 	=> 'Invalid datetime format'
            ))->add('datetime', 'future_hour_datetime', array(
                'required'	=> 'create',
                'allowEmpty'=> false,
                'rule'    	=> 'isFuture1HourDatetime',
                'message' 	=> $datetimeErrorMessage
            ));

        } else if($lessonType==LESSON_TYPE_VIDEO) {
            $datetimeErrorMessage = $exists ? __('You cannot operate on a lesson that already started') :__('Please set a future datetime');

            //Allow datetime to be blank, or be set to now || any future datetime
            $this->validator()->add('datetime', 'datetime', array(
                'allowEmpty'=> true,
                'rule'    	=> array('datetime', 'ymd'),
                'message' 	=> __('Invalid datetime format')
            ))->add('datetime', 'future_datetime', array(
                'allowEmpty'=> true,
                'rule'    	=> 'isFutureDatetime',
                'message' 	=> $datetimeErrorMessage
            ));
        }
    }

    public function beforeSave($options = array()) {
        parent::beforeSave($options);
        $this->TeacherLesson; //Init const
        if( (isSet($this->data['UserLesson']['1_on_1_price']) && $this->data['UserLesson']['1_on_1_price']>0) ||
            (isSet($this->data['UserLesson']['full_group_student_price']) && $this->data['UserLesson']['full_group_student_price']>0)) {
            $this->data['UserLesson']['payment_status'] = PAYMENT_STATUS_PENDING;
        }

        $exists = $this->exists(!empty($this->data[$this->name][$this->primaryKey]) ? $this->data[$this->name][$this->primaryKey] : null);
        if($exists) {
            unset($this->data[$this->name]['lesson_type']);
        }

        $this->data['UserLesson']['version'] = String::uuid();
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
		//Find the teacher subject
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
			'datetime'					=> $datetime ? $datetime : null,
			'lesson_type'				=> $subjectData['lesson_type'],
			'language'				    => $subjectData['language'],
			'name'						=> $subjectData['name'],
			'description'				=> $subjectData['description'],
			'duration_minutes'			=> $subjectData['duration_minutes'],
			'max_students'				=> intval($subjectData['max_students']),
			'1_on_1_price'				=> $subjectData['1_on_1_price'],
			'full_group_student_price'	=> $subjectData['full_group_student_price'],
			'full_group_total_price'	=> $subjectData['full_group_total_price'],

			'image'	                    => $subjectData['image'],
			'image_source'	            => $subjectData['image_source'],
			'image_resize'	            => $subjectData['image_resize'],
			'image_crop_38x38'	        => $subjectData['image_crop_38x38'],
			'image_crop_58x58'	        => $subjectData['image_crop_58x58'],
			'image_crop_60x60'	        => $subjectData['image_crop_60x60'],
			'image_crop_63x63'	        => $subjectData['image_crop_63x63'],
			'image_crop_72x72'	        => $subjectData['image_crop_72x72'],
			'image_crop_78x78'	        => $subjectData['image_crop_78x78'],
			'image_crop_80x80'	        => $subjectData['image_crop_80x80'],
			'image_crop_149x182'        => $subjectData['image_crop_149x182'],
			'image_crop_200x210'        => $subjectData['image_crop_200x210'],
			'image_crop_436x214'        => $subjectData['image_crop_436x214'],
		);

        //Reverse the stages, in use for teacher invite students, or on SUBJECT_TYPE_REQUEST - sending requests to teachers
        if($reverseStage) {
           $userLesson['stage'] = ($userLesson['stage']==USER_LESSON_PENDING_TEACHER_APPROVAL) ? USER_LESSON_PENDING_STUDENT_APPROVAL : USER_LESSON_PENDING_TEACHER_APPROVAL;
           $userId = ($userId==$userLesson['teacher_user_id']) ? $userLesson['student_user_id'] : $userLesson['teacher_user_id'];
        }


        if($subjectData['lesson_type']==LESSON_TYPE_LIVE) {
            //Set the end of the lesson, video lesson end date is first-watching-time+2 days
            /*if(is_object($datetime)) {
                $datetime = $datetime->value;
            }*/
            //$userLesson['end_datetime'] = $this->timeExpression($datetime.' + '.$subjectData['duration_minutes'].' minutes' ,false);
            $userLesson['end_datetime'] = $this->getDataSource()->expression('DATE_ADD(`datetime`, INTERVAL `duration_minutes` MINUTE)');

        } else if($subjectData['lesson_type']==LESSON_TYPE_VIDEO) {
            //Make sure users doesn't order the same video when not needed
            $canWatchData = $this->getVideoLessonStatus($userLesson['subject_id'], $userLesson['student_user_id'], false);
            if($canWatchData['approved']) {

                if(empty($canWatchData['datetime']) || $this->isFutureDatetime($canWatchData['end_datetime']) || //User shouldn't pay for a lesson that he did not watched yet/watch time didn't over
                    $subjectData['1_on_1_price']==0) { //user doesn't need to order free lesson again.
                    return false;
                }
            }
        }

        $userLesson = am($userLesson, $extra);

		$event = new CakeEvent('Model.UserLesson.beforeLessonRequest', $this, array('user_lesson'=>$userLesson, 'by_user_id'=>$userId) );
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}

		$this->create(false);
        if(isSet($userLesson['user_lesson_id'])) {
            $this->id = $userLesson['user_lesson_id'];
        }
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
	public function joinRequest( $teacherLessonId, $studentUserId=null, $teacherUserId=null, $userLessonId=null, $extra=array() ) {
		//TODO: don't allow to send invitations if subject_type=request and the user did not approved his invitation yet
		
		//Find the teacher lesson
		/*App::import('Model', 'TeacherLesson');
		$teacherLessonObj = new TeacherLesson();*/
		
		$this->TeacherLesson->recursive = -1;
		$teacherLessonData = $this->TeacherLesson->findByTeacherLessonId($teacherLessonId);
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

        //Make sure the lesson will start < 1 hour in the future - will check in validation

        //make sure user is not in the lesson already
        $this->recursive = -1;
        $userLessonData = $this->find('first', array('conditions'=>array(	'UserLesson.student_user_id'=>$studentUserId,
                                                                            'UserLesson.teacher_lesson_id'=>$teacherLessonData['teacher_lesson_id'],
                                                                            'UserLesson.stage'=>array(USER_LESSON_ACCEPTED)),
                                                                        ));
        if($userLessonData) {
            //User already in lesson
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
			'full_group_total_price'	=> $teacherLessonData['full_group_total_price'],

            'image'	                    => $teacherLessonData['image'],
            'image_source'	            => $teacherLessonData['image_source'],
            'image_resize'	            => $teacherLessonData['image_resize'],
            'image_crop_38x38'	        => $teacherLessonData['image_crop_38x38'],
            'image_crop_58x58'	        => $teacherLessonData['image_crop_58x58'],
            'image_crop_60x60'	        => $teacherLessonData['image_crop_60x60'],
            'image_crop_63x63'	        => $teacherLessonData['image_crop_63x63'],
            'image_crop_72x72'	        => $teacherLessonData['image_crop_72x72'],
            'image_crop_78x78'	        => $teacherLessonData['image_crop_78x78'],
            'image_crop_80x80'	        => $teacherLessonData['image_crop_80x80'],
            'image_crop_149x182'        => $teacherLessonData['image_crop_149x182'],
            'image_crop_200x210'        => $teacherLessonData['image_crop_200x210'],
            'image_crop_436x214'        => $teacherLessonData['image_crop_436x214'],
		);
        if($userLessonId) {
            $userLesson['user_lesson_id'] = $userLessonId; //data that used in event
        }
        $userLesson = am($userLesson, $extra);

		$event = new CakeEvent('Model.UserLesson.beforeJoinRequest', $this, array('teacher_lesson'=>$teacherLessonData, 'user_lesson'=>$userLesson, 'by_user_id'=>( $teacherUserId ? $teacherUserId : $studentUserId), 'user_lesson_id'=>$userLessonId));
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}


        $this->create(false);
        if($userLessonId) {
            $this->id = $userLessonId; //used for orders only
        }
		$this->set($userLesson);
		if(!$this->save()) {
			return false;
		}
		$userLesson['user_lesson_id'] = $this->id;
		
		
		//Update the num_of_pending_invitations/num_of_pending_join_requests counter
		$counterDBField = ($teacherUserId ? 'num_of_pending_invitations' : 'num_of_pending_join_requests');
        $this->TeacherLesson->id = $teacherLessonData['teacher_lesson_id'];
        $this->TeacherLesson->set(array($counterDBField=>$this->getDataSource()->expression($counterDBField.'+1')));
        $this->TeacherLesson->save();
		
		
		
		$teacherLessonData[$counterDBField]++;
		$event = new CakeEvent('Model.UserLesson.afterJoinRequest', $this, array('teacher_lesson'=>$teacherLessonData, 'user_lesson'=>$userLesson, 'by_user_id'=>( $teacherUserId ? $teacherUserId : $studentUserId)));
		$this->getEventManager()->dispatch($event);
		
		return true;
	}

    private function validateTimeBeforeChange($userDatetime, $beforeTime='now') {
        return $this->toServerTime($userDatetime)>=$this->timeExpression($beforeTime, false);
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

		$event = new CakeEvent('Model.UserLesson.beforeCancelRequest', $this, array('user_lesson'=>$userLessonData, 'by_user_id'=>$byUserId));
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}
		
		$data = array();
		//Determent the new userLesson stage and counter
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

        //Update the user lesson
        $this->id = $userLessonId;
        $this->set($data);
        if(!$this->save()) {
            return false;
        }

		if($userLessonData['teacher_lesson_id']) {
            App::import('Model', 'TeacherLesson');
            $teacherLessonObj = new TeacherLesson();

			//Update the num_of_pending_invitations/num_of_pending_join_requests counter
			$teacherLessonObj->id = $userLessonData['teacher_lesson_id'];
			$teacherLessonObj->set(array($counterDBField=>$this->getDataSource()->expression($counterDBField.'-1')));
			$teacherLessonObj->save();
		}
		

		//$this->updateAll($data, array('user_lesson_id'=>$userLessonId));
		

		$event = new CakeEvent('Model.UserLesson.afterCancelRequest', $this, array('user_lesson'=>$userLessonData, 'data'=>$data, 'by_user_id'=>$byUserId));
		$this->getEventManager()->dispatch($event);
		
		
		return true;
	}
	
	public function acceptRequest( $userLessonId, $byUserId, $version=null ) {
		//Find user lesson
		$userLessonData = $this->findByUserLessonId($userLessonId);
		if( !$userLessonData ) {
			return false;
		}
		$userLessonData = $userLessonData['UserLesson'];

        //Check version
        if($version && $version!=$userLessonData['version']) {
            $this->invalidate('version', __('Invalid version'));
            return false;
        }

		//Check if $byUserId can accept this request
		if(!(($userLessonData['student_user_id']==$byUserId && ($userLessonData['stage']==USER_LESSON_PENDING_STUDENT_APPROVAL || $userLessonData['stage']==USER_LESSON_RESCHEDULED_BY_TEACHER)) ||
			($userLessonData['teacher_user_id']==$byUserId && ($userLessonData['stage']==USER_LESSON_PENDING_TEACHER_APPROVAL || $userLessonData['stage']==USER_LESSON_RESCHEDULED_BY_STUDENT)))) {
			return false;
		}

        //Generate teacherLesson if needed
		$event = new CakeEvent('Model.UserLesson.beforeAccept', $this, array('user_lesson'=>$userLessonData, 'by_user_id'=>$byUserId));
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}


        //Update user lesson stage
		$updateUserLesson = array('stage'=>USER_LESSON_ACCEPTED);
        if(isSet($event->result['teacher_lesson_id'])) {
            $updateUserLesson['teacher_lesson_id'] = $event->result['teacher_lesson_id'];
        }

		$this->updateAll($updateUserLesson, array('UserLesson.user_lesson_id'=>$userLessonId));


        //Cancel other pending request
        $cancelPendingUserLessonsData = null;
        if(isSet($userLessonData['teacher_lesson_id']) && $userLessonData['lesson_type']=='live') {
            $this->recursive = -1;
            $cancelPendingUserLessonsData = $this->find('all', array('conditions'=>array(	'UserLesson.student_user_id'    =>$userLessonData['student_user_id'],
                                                                                            'UserLesson.teacher_lesson_id'  =>$userLessonData['teacher_lesson_id'],
                                                                                            'UserLesson.stage'=>array(      USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT,
                                                                                                                            USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER)),
            ));


        } else if($userLessonData['lesson_type']=='video') {
            $this->recursive = -1;
            $cancelPendingUserLessonsData = $this->find('all', array('conditions'=>array(	'UserLesson.student_user_id'=>$userLessonData['student_user_id'],
                                                                                            'UserLesson.subject_id'     =>$userLessonData['subject_id'],
                                                                                            'UserLesson.stage'=>array(  USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT,
                                                                                                                        USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER)),
            ));
        }
        if($cancelPendingUserLessonsData) {
            $ulel =& UserLessonEventListener::getInstance();
            $ulel->setNotificationStatus('Model.UserLesson.afterCancelRequest', false);
            foreach($cancelPendingUserLessonsData AS $data) {
                $this->cancelRequest($data['UserLesson']['user_lesson_id'], $byUserId);
            }
            $ulel->setNotificationStatus('Model.UserLesson.afterCancelRequest', true);
        }


		$event = new CakeEvent('Model.UserLesson.afterAccept', $this, array('user_lesson'=>$userLessonData, 'data'=>$updateUserLesson, 'by_user_id'=>$byUserId));
		$this->getEventManager()->dispatch($event);
		
		
		return true;
	}

    public function getAcceptLessonCounter($stage) {
        return (($stage==USER_LESSON_PENDING_STUDENT_APPROVAL || $stage==USER_LESSON_RESCHEDULED_BY_TEACHER) ? 'num_of_pending_invitations' : 'num_of_pending_join_requests');
    }


    public function reProposeRequest($userLessonId, $byUserId, array $data=array(), $version=null) {
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

        //Check version
        if($version && $version!=$userLessonData['version']) {
            $this->invalidate('version', __('Invalid version'));
            return false;
        }

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
        $allowedFields = array('datetime', '1_on_1_price', 'max_students', 'full_group_student_price');
        if($userLessonData['lesson_type']==LESSON_TYPE_LIVE) {
            //Only live lesson can change the duration of the lesson, video lesson get duration form the main video
            $allowedFields[] = 'duration_minutes';
        }
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


        $event = new CakeEvent('Model.UserLesson.beforeReProposeRequest', $this, array('user_lesson'=>$userLessonData, 'update'=>$data, 'by_user_id'=>$byUserId));
        $this->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            return false;
        }

        $this->id = $userLessonId;
        $this->set($data);
        $this->save();

        $event = new CakeEvent('Model.UserLesson.afterReProposeRequest', $this, array('user_lesson'=>$userLessonData, 'update'=>$data, 'by_user_id'=>$byUserId));
        $this->getEventManager()->dispatch($event);

        return true;
    }
	
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
		
		$results = $this->find('all', array('conditions'=>array('UserLesson.teacher_lesson_id'=>$teacherLessonsId, 'UserLesson.stage'=>array(USER_LESSON_ACCEPTED, USER_LESSON_PENDING_STUDENT_APPROVAL,
                                                                                                                                                USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER,
                                                                                                                                                USER_LESSON_RESCHEDULED_BY_STUDENT))));
		foreach($results AS $result) {
			
			switch($result['UserLesson']['stage']) {
				case USER_LESSON_ACCEPTED:
					$return['students'][] = $result;
				break;
				case USER_LESSON_PENDING_TEACHER_APPROVAL:
				case USER_LESSON_RESCHEDULED_BY_STUDENT:
					$return['join_reuests'][] = $result;
				break;
				case USER_LESSON_PENDING_STUDENT_APPROVAL:
				case USER_LESSON_RESCHEDULED_BY_TEACHER:
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

		$startDate = $year.'-'.($month ? $month : 1).'-1 00:00:00';
		$endDate = $year.'-'.($month ? $month : 12).'-1 23:59:59';

        //Convert the client time to server time
        $startDate = $this->toServerTime($startDate);
        $endDate = $this->toServerTime($endDate);

		$conditions = array('student_user_id'=>$studentUserId, $this->alias.'.lesson_type'=>LESSON_TYPE_LIVE,
							'OR'=>array(
                                    'datetime BETWEEN ? AND ?' => array($startDate, $this->timeExpression($endDate.' + 1 month')),
                                    'end_datetime BETWEEN ? AND ?' => array($startDate, $this->timeExpression($endDate.' + 1 month'))

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
						'OR'=>array(
                                'AND'=>array(
                                    array('OR'=>array( 'UserLesson.end_datetime <'=>$this->timeExpression('now', false), 'UserLesson.end_datetime IS NOT NULL')),
                                    array('stage'=>array(	USER_LESSON_DENIED_BY_TEACHER, USER_LESSON_DENIED_BY_STUDENT,
                                                            USER_LESSON_CANCELED_BY_TEACHER, USER_LESSON_CANCELED_BY_STUDENT,
                                                            USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_TEACHER_RATING, USER_LESSON_PENDING_STUDENT_RATING,
                                                            USER_LESSON_DONE)),
                                    ),
                                array('OR'=>array('UserLesson.datetime <'=>$this->timeExpression('now +1 hour', false), 'UserLesson.datetime IS NOT NULL'),
                                        'stage'=>array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT, USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER ))
                        ));
        //TODO: use  $this->getLessons
	    return $this->find('all', array('conditions'=>$conditions,
										'order'=>'datetime',
										'limit'=>( $limit ? $limit : null),
										'page'=>$page
					));
	
		
	}

    public function getBooking($studentUserId, $limit=null, $page=1) {
        return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId), '>', $limit, $page, array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT), 'datetime', 'now +1 hour');
    }
	public function getInvitations($studentUserId, $limit=null, $page=1) {
		$this->Subject;
		return $this->getLessons(array('UserLesson.student_user_id'=>$studentUserId), '>', $limit, $page, array(USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER), 'datetime', 'now +1 hour');
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
		return $this->getLessons($conditions, '>', $limit, $page, array(USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER), 'datetime', 'now +1 hour');
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
		
		$conditions = array('UserLesson.teacher_user_id'=>$teacherUserId/*, 'UserLesson.teacher_lesson_id IS NULL'*/);
		if($subjectId) {
			$conditions['UserLesson.subject_id'] = $teacherUserId;
		}
		return $this->getLessons($conditions, '>', $limit, $page, array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT), 'datetime', 'now +1 hour');
	}


    /**
     *
     * Get rating for a given teacher - by his studnets
     * @param unknown_type $userId
     * @param unknown_type $limit
     * @param unknown_type $page
     */
    public function getTeacherReviews( $teacherUserId, $limit=12, $page=1 ) {
        App::import('Model', 'UserLesson');
        $ulObj = new UserLesson();
        $conditions = array('UserLesson.teacher_user_id'=>$teacherUserId, 'stage'=>array(USER_LESSON_PENDING_TEACHER_RATING, USER_LESSON_DONE));

        $ulObj->recursive = 2;
        $ulObj->unbindAll(array('belongsTo'=>array('Student')));
        $ulObj->Student->unbindAll();

        return $ulObj->find('all', array(	'conditions'=>$conditions,
            'fields'=>array('student_user_id', 'rating_by_student', 'comment_by_student', 'datetime'),
            'limit'=>$limit,
            'page'=>$page));

    }

    /**
     *
     * Get rating for a given student - by his teachers
     * @param unknown_type $userId
     * @param unknown_type $limit
     * @param unknown_type $page
     */
    public function getStudentReviews( $studentUserId, $limit=12, $page=1 ) {
        App::import('Model', 'UserLesson');
        $ulObj = new UserLesson();
        $conditions = array('UserLesson.student_user_id'=>$studentUserId, 'stage'=>array(USER_LESSON_PENDING_STUDENT_RATING, USER_LESSON_DONE));


        $ulObj->recursive = 2;
        $ulObj->unbindAll(array('belongsTo'=>array('Teacher')));
        $ulObj->Teacher->unbindAll();

        return $ulObj->find('all', array(	'conditions'=>$conditions,
            'fields'=>array('teacher_user_id', 'rating_by_teacher', 'comment_by_teacher', 'image', 'datetime'),
            'limit'=>$limit,
            'page'=>$page));

    }

	public function waitingTeacherReview($teacehrUserId, $limit=null, $page=1) {
        $this->Subject;
        //Teacher cannot rate video lesson student.
		return $this->getLessons(array('UserLesson.teacher_user_id'=>$teacehrUserId, 'UserLesson.lesson_type'=>LESSON_TYPE_LIVE), null, $limit, $page, array(USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_TEACHER_RATING));
	}

	public function getLessons($conditions, $time='>', $limit=null, $page=1, $stage=array(), $datetimeField='end_datetime', $timeExpression='now') {

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
                if($time=='>') { //Future lessons
                    $conditions['AND'][] = array(
                        'OR'=>array(
                            array($this->alias.'.'.$datetimeField.' >'=>$this->timeExpression($timeExpression, false)),
                            array('UserLesson.'.$datetimeField.' IS NULL')
                            )
                    );

                } else { //Past lessons
                    $conditions['AND'][] = array(
                        array('UserLesson.'.$datetimeField.$time=>$this->timeExpression($timeExpression, false)),
                        array('UserLesson.'.$datetimeField.' IS NOT NULL')
                    );

                }

			}
		}

		return $this->find($find, array('conditions'=>$conditions, 
										'order'=>'datetime',
										'limit'=>( $limit ? $limit : null),
										'page'=>$page
					));
	}

    public function getVideoLessonStatus($subjectId, $userId/*, $updateNullEndDatetime=true*/) {
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
        $userLessonsData = $this->find('all', array('conditions'=>array('subject_id'=>$subjectId, 'OR'=>array(  array('student_user_id'=>$userId),
                                                                                                                array('teacher_user_id'=>$userId))),
                                                    'order'=>'user_lesson_id DESC')); //Order is so the user can buy the lesson again to avoid ads


        $return =  array(
            'pending_teacher_approval'  =>false,
            'pending_user_approval'     =>false,
            'approved'                  =>false,
            'payment_needed'            =>($subjectData['1_on_1_price']>0),

            'is_teacher'                =>($userId==$subjectData['user_id']),
            'teacher_lesson_id'         =>false,
            'subject_id'                =>$subjectId,
            'lesson_name'               =>$subjectData['name'],
            'user_lesson_id'            =>false,
            'datetime'                  =>false,
            'end_datetime'              =>false,
        );

        if(!$userLessonsData) {
            return $return;
        }

        /**
         * 1. Arrange the UserLesson by their stage (accept|pending teacher|pending user)
         * 2. The newest records will prioritize.
         */
        $tmpRes = array();
        foreach($userLessonsData AS $userLessonData) {
            $userLessonData = $userLessonData['UserLesson'];


            //1.
            if(in_array($userLessonData['stage'], array(USER_LESSON_ACCEPTED,
                                                        USER_LESSON_PENDING_RATING,
                                                        USER_LESSON_PENDING_TEACHER_RATING,
                                                        USER_LESSON_PENDING_STUDENT_RATING,
                                                        USER_LESSON_DONE))) {


                $key = 'approved';

            } else if(in_array($userLessonData['stage'], array(USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER))) {
                $key = 'pending_user_approval';

            } else if(in_array($userLessonData['stage'], array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT))) {
                $key = 'pending_teacher_approval';

            } else {
                continue;
            }

            //2. Prioritize - The current UserLesson is newer, so skip
            if( isSet($tmpRes[$key]) &&
                (empty($tmpRes[$key]['datetime']) || (!empty($userLessonData['datetime']) && $tmpRes[$key]['datetime']>=$userLessonData['datetime']) ) ) {
                continue;
            }

            $tmpRes[$key]['teacher_lesson_id']= $userLessonData['teacher_lesson_id'];
            $tmpRes[$key]['user_lesson_id']   = $userLessonData['user_lesson_id'];
            $tmpRes[$key]['datetime']         = $userLessonData['datetime'];
            $tmpRes[$key]['end_datetime']     = $userLessonData['end_datetime'];
            $tmpRes[$key]['payment_needed']   = ($userLessonData['1_on_1_price']>0);
            $tmpRes[$key]['is_teacher']       = ($userId==$userLessonData['teacher_user_id']);
        }


        if($tmpRes) {
            if(isSet($tmpRes['approved'])) {
                $return['approved'] = true;
                $return = am($return, $tmpRes['approved']);

            } else if(isSet($tmpRes['pending_user_approval'])) {
                $return['pending_user_approval'] = true;
                $return = am($return, $tmpRes['pending_user_approval']);

            } else if(isSet($tmpRes['pending_teacher_approval'])) {
                $return['pending_teacher_approval'] = true;
                $return = am($return, $tmpRes['pending_teacher_approval']);
            }
        }

        return $return;


        /*//Check if there is existing request
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

                    //We need to check UserLesson because the subject price may changed until now
                    if(!$userLessonData['1_on_1_price']) {
                        $isFreeVideo = true;
                    }

                    break;

                } else if(in_array($userLessonData['stage'], array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT))) {
                    $waitingTeacherApproval = true;
                    $userLessonId = $userLessonData['user_lesson_id'];
                    break;
                } else if(in_array($userLessonData['stage'], array(USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER))) {
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
                    $this->lessonRequest($subjectId, $userId, $this->toClientTime('now'));
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
        );*/
    }
    public function setVideoStartEndDatetime($userLessonId) {
        $this->recursive = -1;
        $ulData = $this->findByUserLessonId($userLessonId);
        if(!$ulData || !$ulData['UserLesson']['teacher_lesson_id']) {
            return false;
        }

        //Update this UserLesson with end_datetime
        $this->create(false);

         $this->saveAssociated(
                array(
                    'UserLesson'=>array('user_lesson_id'=>$userLessonId, 'datetime'=>$this->timeExpression('now', true), 'end_datetime'=>$this->timeExpression('now + '.LESSON_TYPE_VIDEO_NO_ADS_TIME_SEC.' seconds', true)),
                    'TeacherLesson'=>array('teacher_lesson_id'=>$ulData['UserLesson']['teacher_lesson_id'], 'datetime'=>$this->timeExpression('now', true), 'end_datetime'=>$this->timeExpression('now + '.LESSON_TYPE_VIDEO_NO_ADS_TIME_SEC.' seconds', true))
                )
        );
    }

    public function getLiveLessonStatus($teacherLessonId, $userId) {
        //Find teacher lesson
        $this->Subject; //Init const
        $this->TeacherLesson->recursive = -1;
        $tlData = $this->TeacherLesson->find('first', array('conditions'=>array('teacher_lesson_id'=>$teacherLessonId)));
        if(!$tlData || $tlData['TeacherLesson']['is_deleted'] || $tlData['TeacherLesson']['lesson_type']!=LESSON_TYPE_LIVE) {
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
            'approved'                  =>false,
            'payment_needed'            =>($tlData['1_on_1_price']>0),

            'is_teacher'                =>($userId==$tlData['teacher_user_id']),
            'teacher_lesson_id'         =>$teacherLessonId,
            'subject_id'                =>$tlData['subject_id'],
            'lesson_name'               =>$tlData['name'],
            'user_lesson_id'            =>false,
            'datetime'                  =>$tlData['datetime'],
        );



        //Check the time status of the lesson
        $timing = $this->TeacherLesson->getLessonTiming($tlData['datetime'], $tlData['duration_minutes']);
        $return[$timing] = true;

        //If no user passed - return
        if(!$userId) {
            return $return;
        }

        //Check if this user is register for this lesson or not
        $this->recursive = -1;
        $this->cacheQueries = false;
        $userLessonsData = $this->find('all', array('conditions'=>array('teacher_lesson_id'=>$teacherLessonId, 'student_user_id'=>$userId)));

        if(!$userLessonsData) {
            return $return;
        }

        //Check if there more then 1 valid request
        if(count($userLessonsData)>1) {
            $this->log(var_export($userLessonsData, true), 'multi_user_lesson');
        }

        foreach($userLessonsData AS $userLessonData) {
            $userLessonData = $userLessonData['UserLesson'];
            /*if(in_array($userLessonData['stage'], array(USER_LESSON_ACCEPTED,
                                                        USER_LESSON_PENDING_RATING,
                                                        USER_LESSON_PENDING_TEACHER_RATING,
                                                        USER_LESSON_PENDING_STUDENT_RATING,
                                                        USER_LESSON_DONE))) {

                $return['approved'] = true;
                $return['user_lesson_id'] = $userLessonData['user_lesson_id'];

            } else if(in_array($userLessonData['stage'], array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT))) {
                $return['pending_teacher_approval'] = true;
                $return['user_lesson_id'] = $userLessonData['user_lesson_id'];

            } else if(in_array($userLessonData['stage'], array(USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER))) {
                $return['pending_user_approval'] = true;
                $return['user_lesson_id'] = $userLessonData['user_lesson_id'];

            }*/
            $return = am($return, $this->checkStage($userLessonData));
        }



        /*if(!$return['approved'] && !$return['pending_user_approval'] && !$return['pending_teacher_approval'] && !$return['is_teacher'] && !$return['payment_needed'] && $return['about_to_start']) {
            //There is no UserLesson and this is not the teacher, free lesson and the lesson did not started yet

            //Make UserLesson request
            $this->lessonRequest($tlData['subject_id'], $userId, $this->toClientTime('now'));
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

        }*/


        return $return;
    }

    public function checkStage($userLessonData) {
        if(in_array($userLessonData['stage'], array(USER_LESSON_ACCEPTED,
            USER_LESSON_PENDING_RATING,
            USER_LESSON_PENDING_TEACHER_RATING,
            USER_LESSON_PENDING_STUDENT_RATING,
            USER_LESSON_DONE))) {

            $return['approved'] = true;
            $return['user_lesson_id'] = $userLessonData['user_lesson_id'];

        } else if(in_array($userLessonData['stage'], array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT))) {
            $return['pending_teacher_approval'] = true;
            $return['user_lesson_id'] = $userLessonData['user_lesson_id'];

        } else if(in_array($userLessonData['stage'], array(USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER))) {
            $return['pending_user_approval'] = true;
            $return['user_lesson_id'] = $userLessonData['user_lesson_id'];
        }

        return $return;
    }
}
?>