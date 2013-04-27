<?php
App::uses('CakeEvent', 'Event');

define('PAYMENT_STATUS_NO_NEED', 0);
define('PAYMENT_STATUS_PENDING', 1);
define('PAYMENT_STATUS_DONE', 2);
define('PAYMENT_STATUS_PARTIAL', 3);
define('PAYMENT_STATUS_ERROR', 4);
define('PAYMENT_STATUS_RETURN_DUE_TO_OVERDUE_REQUEST', 5);

define('RATING_STATUS_PENDING', 0);
define('RATING_STATUS_DONE', 1);

class TeacherLesson extends AppModel {
	public $name 		= 'TeacherLesson';
	public $useTable 	= 'teacher_lessons';
	public $primaryKey 	= 'teacher_lesson_id';
    public $actsAs = array('LanguageFilter', 'Time', 'Lock', 'Lesson');
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
        'wish_list_id'=> array(
            'validate_wish_list_id' 	=> array(
                'allowEmpty'=> true,
                'rule'    	=> 'validateWishListId',
                'message' 	=> 'You cannot offer this subject'
            )
        ),
        //Datetime: Cannot be empty for lesson_type=live, it will be checked in beforeValidate below

		'duration_minutes'=> array(
			'range' 		=> array(
				'required'	=> 'create',
				'allowEmpty'=> false,
				'rule'    	=> array('range', 4, 241),
				'message' 	=> 'Must be more then %d minutes and less then %d minutes'
			)
		),
		'price'=> array(
			'price' => array(
            	'required'	=> 'create',
				'allowEmpty'=> false,
				'rule'    	=> 'numeric',
				'message' 	=> 'Enter a valid price, for a FREE lesson, set 0'
			),
			'price_range' => array(
				'required'	=> 'create',
				'allowEmpty'=> false,
				'rule'    	=> array('priceRangeCheck', 'price'),
                'message' 	=> 'Price range error'
			)
		),
        'max_students'=> array(
            'range' 		=> array(
                'required'	=> 'create',
                'allowEmpty'=> true,
                'rule'    	=> array('range', 0, 1025),
                'message' 	=> 'Lesson must have more then %d or less then %d students'
            ),
			'numeric' => array(
				'required'	=> 'create',
				'allowEmpty'=> false,
				'rule'    	=> 'numeric',
				'message' 	=> 'Enter a valid number'
			)
        ),
        'full_group_student_price'=> array(
            'price' => array(
                'allowEmpty'=> true,
                'rule'    	=> 'numeric',
                'message' 	=> 'Enter a valid group price'
            ),
            'full_group_student_price' 	=> array(
                'allowEmpty'=> true,
                'rule'    	=> 'fullGroupStudentPriceCheck',
                'message' 	=> 'You must set a student full group price'
            )
        ),
	);

	public $belongsTo 	= array(
					'User' => array(
						'className'	=> 'User',
						'foreignKey'=>'teacher_user_id',
						'fields'	=>array('first_name', 'last_name', 'image', 'teacher_paypal_id')
					),
					'Subject' => array(
						'className'	=> 'Subject',
						'foreignKey'=>'subject_id',
						'fields'	=>array('average_rating', 'image', 'type', 'is_enable' )
					)
				);

    public function __construct($id = false, $table = null, $ds = null) {

        parent::__construct($id, $table, $ds);
        static $eventListenerAttached = false;

        if(!$eventListenerAttached) {
            //Connect the event manager of this model
            App::import( 'Event', 'TeacherLessonEventListener');
            $tlel =& TeacherLessonEventListener::getInstance();
            CakeEventManager::instance()->attach($tlel);
            $eventListenerAttached = true;
        }
    }

    public function validateSubjectId($subjectID){
        $subjectID = $subjectID['subject_id'];

        //Load the requested subject
        $subjectData = $this->Subject->findBySubjectId($subjectID);
        if(!$subjectData) {
            $this->invalidate('subject_id', ___('Invalid request subject'));
        }
        $subjectData = $subjectData['Subject'];


        //The teacher must be the subject owner
        if(isSet($this->data['TeacherLesson']['teacher_user_id']) && !empty($this->data['TeacherLesson']['teacher_user_id'])) {
            if($this->data['TeacherLesson']['teacher_user_id']!=$subjectData['user_id']) {
                $this->invalidate('wish_list_id', __('The teacher must be the subject owner'));
            }
        }

        return true;
    }

	
    public function beforeValidate($options=array()) {
        parent::beforeValidate($options);

        App::import('Model', 'Subject');

        $exists = $this->exists(!empty($this->data['TeacherLesson'][$this->primaryKey]) ? $this->data['TeacherLesson'][$this->primaryKey] : null);
        /*Subject::calcFullGroupPriceIfNeeded($this->data['UserLesson'], $exists );
        Subject::extraValidation($this);*/
        $this->validateRules($this);


        $lessonType = false;

        //If teacher ask to cancel a TeacherLesson
        if($exists &&
            isSet($this->data['TeacherLesson']['is_deleted']) && $this->data['TeacherLesson']['is_deleted']==1 &&
            (!isSet($this->data['TeacherLesson']['datetime']) || empty($this->data['TeacherLesson']['datetime']))) { //There is no datetime set

            $this->recursive = -1;
            $teacherLessonsData = $this->findByTeacherLessonId($this->id ? $this->id : $this->data['TeacherLesson'][$this->primaryKey]);
            $lessonType = $teacherLessonsData['TeacherLesson']['lesson_type'];


                //There is no need to limit this check to LIVE lessons only. the reason is that VIDEO lessons get datetime only on the first watch
            if($lessonType==LESSON_TYPE_VIDEO || //Live lesson
                // allow teacher to cancel 1 hour before the lesson starts only if the lessons have no students
                $teacherLessonsData['TeacherLesson']['num_of_students']>0) { //Live lesson with students

                //Set datetime so it will get checked
                $this->data['TeacherLesson']['datetime'] = $teacherLessonsData['TeacherLesson']['datetime'];
            }

        }

        //Find TeacherLesson if not set yet
        if(!$lessonType && isSet($this->data['TeacherLesson']['subject_id']) || !empty($this->data['TeacherLesson']['subject_id'])) {
            $subjectData = $this->Subject->findBySubjectId($this->data['TeacherLesson']['subject_id']);
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
            ))->remove('duration_minutes');
        }

    }

    public function beforeSave($options = array()) {
        parent::beforeSave($options);

        if( (isSet($this->data['TeacherLesson']['price']) && $this->data['TeacherLesson']['price']>0) ||
            (isSet($this->data['TeacherLesson']['full_group_student_price']) && $this->data['TeacherLesson']['full_group_student_price']>0)) {
            $this->data['TeacherLesson']['payment_status'] = PAYMENT_STATUS_PENDING;
        }

        $exists = $this->exists(!empty($this->data[$this->name][$this->primaryKey]) ? $this->data[$this->name][$this->primaryKey] : null);
        if($exists) {
            unset($this->data[$this->name]['lesson_type']);
        }

    }



	/* Taken from Subject model - end */
	public function add( $source, $datetime=null, $isPublic=null, $extra=array() ) {
        //TODO: check if there is no lesson at that time
        $teacherLessonData = array();
        if($source['type']=='subject') {
            App::import('Model', 'Subject');
            $subjectObj = new Subject();

            //Find the subject
            $subjectObj->recursive = -1;
            $subjectData = $subjectObj->findBySubjectId($source['id']);
            if(!$subjectData || $subjectData['Subject']['is_enable']==SUBJECT_IS_ENABLE_FALSE) {
                return false;
            }
            $subjectData = $subjectData['Subject'];


            //Preparer the teacher lesson generic data
            $teacherLessonData  = array(//wish_list_id
                'subject_id'				=> $source['id'],
                'teacher_user_id'			=> $subjectData['user_id'],
                'lesson_type'				=> $subjectData['lesson_type'],
                'language'				    => $subjectData['language'],
                'datetime'					=> $datetime, //Convert timestamp to datetime
                'category_id'		        => $subjectData['category_id'],
                'forum_id'		            => $subjectData['forum_id'],
                'name'						=> $subjectData['name'],
                'description'				=> $subjectData['description'],
                'is_public'					=> is_null($isPublic) ? $subjectData['is_public'] : $isPublic,
                'duration_minutes'			=> $subjectData['duration_minutes'],
                'max_students'				=> $subjectData['max_students'],
                'price'				=> $subjectData['price'],
                'full_group_student_price'	=> $subjectData['full_group_student_price'],

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

            //Set the end of the lesson, video lesson end date is first-watching-time+2 days
            if($subjectData['lesson_type']==LESSON_TYPE_LIVE && $datetime) {
                /*if(is_object($datetime)) {
                    $datetime = $datetime->value;
                }*/
                $teacherLessonData['end_datetime'] = $this->getDataSource()->expression('DATE_ADD(`datetime`, INTERVAL `duration_minutes` MINUTE)');
            }

            //The teacher must be the subject owner
            if(isSet($extra['teacher_user_id'])) {
                if($subjectData['user_id']!=$extra['teacher_user_id']) {
                    return false;
                }
            }
            $teacherLessonData = am($teacherLessonData, $extra);

            /* Teacher can create lessons from his panel - this field will be null
            if(!isSet($teacherLessonData['student_user_id'])) {
                return false;
            }*/

        } else if($source['type']=='user_lesson') {
            App::import('Model', 'UserLesson');
            $ulObj = new UserLesson();
            $ulObj->recursive = -1;
            $ulData = $ulObj->findByUserLessonId($source['id']);
            if(!$ulData) {
                return false;
            }
            $teacherLessonData = $ulData['UserLesson'];

            //Only the teacher that opened the subject can teach it
            unset($extra['teacher_user_id']);
            $teacherLessonData = am($teacherLessonData, $extra);

        } else {
            return false;
        }

		$event = new CakeEvent('Model.TeacherLesson.beforeAdd', $this, array('teacher_lesson'=>$teacherLessonData, 'source'=>$source) );
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}
		$this->create(false);
		$this->set($teacherLessonData);
		if(!$this->save()) {
			return false;
		}

        $teacherLessonData['teacher_lesson_id'] = $this->id;
		$event = new CakeEvent('Model.TeacherLesson.afterAdd', $this, array('teacher_lesson'=>$teacherLessonData, 'source'=>$source) );
		$this->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            $this->delete($this->id);
            return false;
        }
		
		return $this->id;
	}
	
	
	public function cancel( $teacherLessonsId/*, $studentUserId=null*/ ) {
		//Find the TeacherLesson
		$this->recursive = -1;
		$teacherLessonsData = $this->findByTeacherLessonId($teacherLessonsId);
		if(!$teacherLessonsData) {
			return false;
		}
		$teacherLessonsData = $teacherLessonsData['TeacherLesson'];
		
		/*if(!is_null($teacherUserId)) {
			//Check if that's the right teacher
			if($teacherLessonsData['teacher_user_id']!=$teacherUserId) {
				return false;
			}
		}*/
				
		if($teacherLessonsData['is_deleted']) {
			//Already deleted
			return true;
		}

		//Get all user lessons that are about to cancel
		App::import('Model', 'Subject');
		App::import('Model', 'UserLesson');
		$userLessonObj = new UserLesson();
		$userLessonsData = $userLessonObj->find('all', array('conditions'=>array(   'UserLesson.teacher_lesson_id'=>$teacherLessonsId,
																				    'UserLesson.stage'=>array(  USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_PENDING_STUDENT_APPROVAL,
                                                                                                                USER_LESSON_RESCHEDULED_BY_TEACHER, USER_LESSON_RESCHEDULED_BY_STUDENT,
                                                                                                                USER_LESSON_ACCEPTED))));

        /*if($studentUserId && $userLessonsData) {
            return false; //Student cant' cancel this lesson if there are existing requests
        }*/

		$event = new CakeEvent('Model.TeacherLesson.beforeCancel', $this, array('teacher_lesson'=>$teacherLessonsData,'user_lesson'=>$userLessonsData));
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}



        //Delete the teacher lesson
        $this->id = $teacherLessonsId;
        $this->set(array('is_deleted'=>1));
        if(!$this->save()) {
            return false;
        }

		
		//Update all users that are going to take place in that lesson
		App::import('Model', 'UserLesson');
		$userLessonObj = new UserLesson();

        foreach($userLessonsData AS $userLessonData) {
            $userLessonObj->cancelRequest($userLessonData['UserLesson']['user_lesson_id'], $teacherLessonsData['teacher_user_id']);
        }
		/*$userLessonObj->updateAll(	array('UserLesson.stage'=>USER_LESSON_DENIED_BY_TEACHER),
									array('UserLesson.stage'=>array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_RESCHEDULED_BY_TEACHER, USER_LESSON_RESCHEDULED_BY_STUDENT, USER_LESSON_ACCEPTED), 'UserLesson.teacher_lesson_id'=>$teacherLessonsId));*/
										 
		

		//$this->delete($teacherLessonsId);						 
										 
		
		$event = new CakeEvent('Model.TeacherLesson.afterCancel', $this, array('teacher_lesson'=>$teacherLessonsData,'user_lessons'=>$userLessonsData));
		$this->getEventManager()->dispatch($event);
		
		
		return true;
	}

    /**
     * Return a list of lessons for a given subject which the user can join to
     */
    public function getUpcomingOpenLessons($teacherUserId=null, $subjectId=null, $limit=2, $page=1) {
        if(!$teacherUserId && !$subjectId) {
            return false;
        }
        $this->recursive = -1;
        $conditions = array(
            'datetime >'=>$this->timeExpression( 'now +1 hour', false),
            'max_students >'=>'num_of_students',
            'is_deleted'=>'0');
        if($subjectId) {
            $conditions['subject_id'] = $subjectId;
        }
        if($teacherUserId) {
            $conditions['teacher_user_id'] = $teacherUserId;
        }
        return $this->find('all', array('conditions'=>$conditions, 'limit'=>$limit, 'page'=>$page));
    }

    public function getLiveLessons( $teacherUserId, $futureOnly=true ) {
        $this->Subject; //Init const
        $this->getDataSource();
        $conditions = array($this->alias.'.teacher_user_id'=>$teacherUserId, $this->alias.'.lesson_type'=>LESSON_TYPE_LIVE,
                            'is_deleted'=>0 );
        if($futureOnly) {
            $conditions[] = 'datetime > NOW()';
        }

        return $this->find('all', array('conditions'=>$conditions));
    }

	public function getLiveLessonsByDate( $teacherUserId, $year, $month=null ) {
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

		$conditions = array('teacher_user_id'=>$teacherUserId, $this->alias.'.lesson_type'=>LESSON_TYPE_LIVE,
                            'OR'=>array(
                                'datetime BETWEEN ? AND ?' => array($startDate, $this->timeExpression($endDate.' + 1 month')),
                                'end_datetime BETWEEN ? AND ?' => array($startDate, $this->timeExpression($endDate.' + 1 month'))

                            ),
							'is_deleted'=>0 );

		return $this->find('all', array('conditions'=>$conditions));
	}

    /**
     * @param $teacherUserId
     * @param null $subjectId
     * @param null $teacherLessonId - if provided, subjectId will be ignore
     * @param null $limit
     * @param int $page
     * @return array
     */
    public function getArchive($teacherUserId, $subjectId=null, $teacherLessonId=null, $limit=null, $page=1) {
		$conditions = array( 'teacher_user_id'=>$teacherUserId );
		if ($teacherLessonId) {
            $conditions['TeacherLesson.teacher_lesson_id'] = $teacherLessonId;
        } else if($subjectId) {
            $conditions['TeacherLesson.subject_id'] = $subjectId;
        }
		
		$conditions['OR'] = array(
			array('end_datetime <'=>$this->timeExpression('now', false), 'end_datetime IS NOT NULL' ),
			'is_deleted'=>1
		);

        //$this->recursive = -1;
		return $this->find('all', array(
			'conditions'=>$conditions,
			'page'=>$page,
			'limit'=>$limit
		));
	}
	
	public function getUpcoming($teacherUserId, $subjectId=null, $limit=null, $page=1) {
		$this->Subject;
		$conditions = array( 'teacher_user_id'=>$teacherUserId, 'is_deleted'=>0,
                'OR'=>array(
                    array('end_datetime >'=>$this->timeExpression('now', false)),
                    array('end_datetime IS NULL')

                )
		);




		if($subjectId) {
			$conditions['TeacherLesson.subject_id'] = $subjectId;
		}

		return $this->find('all', array(
			'conditions'=>$conditions,
			'page'=>$page,
			'limit'=>$limit
		));
	}
	
	/*public function getPendingProposedLessons($teacherUserId, $subectId=null, $limit=null, $page=1) {
		$conditions = array( 'teacher_user_id'=>$teacherUserId, 'datetime >'=>$this->timeExpression('now', false), 'is_deleted'=>0, 'request_subject_id IS NOT NULL', 'num_of_students'=>0 );
		if($subectId) {
			$conditions['TeacherLesson.subject_id'] = $subectId;
		}
		
		return $this->find('all', array(
			'conditions'=>$conditions,
			'page'=>$page,
			'limit'=>$limit
		));
	} */

    public function pay($teacherLessonId) {
        $payRes = $this->_pay($teacherLessonId);

        $this->create(false);
        $this->id = $teacherLessonId;

        if(!is_array($payRes)) {
            $update = array('payment_status'=>$payRes);
        } else {
            $update = array(
                            'payment_status'                            =>$payRes['status'],
                            'payment_success_transactions_count'        =>$payRes['success_transactions_count'],
                            'payment_per_student_price'                 =>$payRes['per_student_price'],
                            'payment_per_student_commission'            =>$payRes['per_student_commission'],
                            'payment_per_student_gateway_max_commission'=>$payRes['per_student_gateway_max_commission'],

            );
        }

        $this->set($update);
        if(!$this->save()) {
            return false;
        }

        //Billing history for teacher
        App::import('Model', 'TeacherLesson');
        $teacherLessonModel = new TeacherLesson();
        $teacherLessonModel->recursive = -1;
        $tlData = $teacherLessonModel->findByTeacherLessonId($teacherLessonId);


        App::import('Model', 'BillingHistory');
        $billingHistoryModel = new BillingHistory();
        $billingHistoryModel->addHistory(
            ($payRes['success_transactions_count']*$payRes['per_student_price']),
            $tlData['TeacherLesson']['teacher_user_id'],
            'teacher.payment',
            $teacherLessonId,
            null,
            array(
                'creditPoints'  => $payRes['per_student_price']                         * $payRes['success_transactions_count'],
                'commission'    => $payRes['per_student_commission']                    * $payRes['success_transactions_count'],
                'gatewayFee'    => $payRes['payment_per_student_gateway_max_commission']* $payRes['success_transactions_count'],

            )
        );


        return $payRes['status'];
    }

    /**
     * Charge all students of TeacherLessonId
     * @param $teacherLessonId
     * @return int, on success an array of success_transactions_count, status, per_student_price, per_student_commission, per_student_gateway_max_commission
     */
    public function _pay( $teacherLessonId ) {
        $this->recursive = -1;
        $tlData = $this->findByTeacherLessonId($teacherLessonId);
        if(!$tlData || $tlData['TeacherLesson']['is_deleted'] || !$tlData['TeacherLesson']['price']) {
            return PAYMENT_STATUS_ERROR;
        }
        //Check if already used for payment
        if($tlData['TeacherLesson']['payment_status']!=PAYMENT_STATUS_PENDING) {
            return $tlData['TeacherLesson']['payment_status'];
        }


        //Get student price and comissions
        $studentPriceAndComissions = $this->calcFinalStudentPriceAndCommissions($teacherLessonId);

        App::import('Model', 'UserLesson');
        $ulObj = new UserLesson();

        //Find all UL that approved
        $ulObj->recursive = -1;
        $uls = $ulObj->find('all', array('conditions'=>array(
                                                        'teacher_lesson_id'=>$teacherLessonId,
                                                        'stage'=>array( USER_LESSON_ACCEPTED, USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_TEACHER_RATING,
                                                                        USER_LESSON_PENDING_STUDENT_RATING, USER_LESSON_DONE ),
                                                        'payment_status'=>PAYMENT_STATUS_PENDING
                                                    ),
                                            'fields'=>array('user_lesson_id', 'student_user_id') ));
        if(!$uls) {
            //There are no users in lesson
            return PAYMENT_STATUS_DONE;
        }

        App::import('Model', 'BillingHistory');
        $billingHistoryModel = new BillingHistory();

        //Go over all ULs
        $successTransactionsCount = 0;
        foreach($uls AS $ul) {
            $ul = $ul['UserLesson'];


            //Make sure there are enough CP
            if($ulObj->haveEnoughTotalCreditPoints(null, $studentPriceAndComissions['per_student_price'], $ul['user_lesson_id'])
                !==true) {
                $paymentStatus = PAYMENT_STATUS_ERROR;

                $this->log('TeacherLesson::Pay - not enough CP!: UserLesson'.$ul['user_lesson_id'], 'credit_points');
            } else {
                $paymentStatus = PAYMENT_STATUS_DONE;

                //Balance CP for UL, any remainder should go to the student
                $ulObj->setTotalCreditPoints($ul['user_lesson_id'], $studentPriceAndComissions['per_student_price']);



                //Calc who need to get and how much
                $transferToUs = $studentPriceAndComissions['per_student_commission'] + $studentPriceAndComissions['per_student_gateway_max_commission'];
                $transferToTeacher = $studentPriceAndComissions['per_student_price'] - $transferToUs;

                //Transfer to teacher account per_student_price-per_student_commission-per_student_gateway_max_commission
                $ulObj->transferCPToUser($ul['user_lesson_id'], $transferToTeacher, $tlData['TeacherLesson']['teacher_user_id']);

                //Transfer remainder to us :)
                $ulObj->transferCPToUser($ul['user_lesson_id'], $transferToUs, Configure::read('system_user_id'));




                //Billing history for user
                $billingHistoryModel->addHistory(
                    $studentPriceAndComissions['per_student_price'],
                    $ul['student_user_id'],
                    'student.payment',
                    $teacherLessonId,
                    $ul['user_lesson_id'],
                    array(
                        'creditPoints'=>$studentPriceAndComissions['per_student_price']
                    )
                );

                $successTransactionsCount++;

                $event = new CakeEvent('Model.AdaptivePayment.AfterUserLessonPaid', $this, array('user_lesson_id'=>$ul['user_lesson_id'], 'teacher_lesson_id'=>$teacherLessonId, 'status'=>$paymentStatus) );
                $this->getEventManager()->dispatch($event);
            }

            ///Update UL payment status
            $ulObj->create(false);
            $ulObj->id = $ul['user_lesson_id'];
            $ulObj->save(array('payment_status'=>$paymentStatus));
        }


        $return = $studentPriceAndComissions;
        $return['success_transactions_count'] = $successTransactionsCount;


        if($return['success_transactions_count']==$tlData['TeacherLesson']['num_of_students']) {
            $return['status'] = PAYMENT_STATUS_DONE; //All payments was successful
        } else if(!$return['successTransactionsCount']) {
            $return['status'] = PAYMENT_STATUS_ERROR; //No successful payments
        } else {
            $return['status'] = PAYMENT_STATUS_PARTIAL; //Partial payments
        }

        return $return;
    }

    /**
     * Calc how much each student should pay, in addition our commission and gateway commission
     * @param $teacherLessonId
     * @return array (
     *      per_student_price
     *      per_student_commission
     *      per_student_gateway_max_commission
     * );
     */
    public function calcFinalStudentPriceAndCommissions($teacherLessonId) {
        $return = array();

        $this->recursive = -1;
        $tlData = $this->findByTeacherLessonId($teacherLessonId);
        $tlData = $tlData['TeacherLesson'];

        //1. Calc student price
        if($tlData['lesson_type']=='video' || $tlData['max_students']==1 || $tlData['num_of_students']==1) {
            $return['per_student_price'] = $tlData['price'];
        } else {
            $return['per_student_price'] = $this->Subject->calcStudentPriceAfterDiscount( $tlData['price'], $tlData['max_students'], $tlData['num_of_students'], $tlData['full_group_student_price'] );
        }

        //Calc our commission
        $return['per_student_commission'] = Configure::read('per_student_commission');

        //PayPal fees
        $return['per_student_gateway_max_commission']  = ($return['per_student_price'] * 0.05) + 0.30; // 5% + 0.30 cents

        return $return;
    }


    /*public function getLiveLessonMeeting($teacherLessonId) {
        return 'wfg-213';
    }
    public function getVideoUrl($teacherLessonId) {
        return 'videoUrl';
    }*/
    public function getFileSystem($teacherLessonId) {
        App::import('Model', 'FileSystem');
        $fsObj = new FileSystem();
        return $fsObj->getFS('lesson', $teacherLessonId);
    }

    public function getTests($teacherLessonId) {
        //Get subject tests
        App::import('Model', 'StudentTest');
        $testObj = new StudentTest();
        return $testObj->getTests('lesson', $teacherLessonId);;
    }

    public function getLessonTiming($datetime, $duration) {
        //Check the time status of the lesson
        if($this->toServerTime($datetime)>$this->timeExpression( 'now', false)) {
            return 'about_to_start';
        }  else if( $this->timeExpression($this->toServerTime($datetime).' + '.$duration.' minute')>$this->timeExpression( 'now', false)) { //Lesson already started
            return 'in_process';
        } else {
            return 'overdue';
        }
    }

}
?>