<?php
App::uses('CakeEvent', 'Event');
class TeacherLesson extends AppModel {
	public $name 		= 'TeacherLesson';
	public $useTable 	= 'teacher_lessons';
	public $primaryKey 	= 'teacher_lesson_id';
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
		'datetime'=> array(
			'between' => array(
            	'required'	=> 'create',
				'allowEmpty'=> false,
				'rule'    	=> array('datetime', 'ymd'),
				'message' 	=> 'Between 2 to 45 characters'
			)
		),
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
	);
	
	public $belongsTo 	= array(
					'User' => array(
						'className'	=> 'User',
						'foreignKey'=>'teacher_user_id',
						'fields'	=>array('first_name', 'last_name', 'image')
					),
					'Subject' => array(
						'className'	=> 'Subject',
						'foreignKey'=>'subject_id',
						'fields'	=>array('avarage_rating', 'image', 'type' )
					)
				);
	
	/* Taken from Subject model - start */
	public function fullGroupTotalPriceCheck( $price ) {
		if(!isSet($this->data['TeacherLesson']['max_students'])) {
			$this->invalidate('max_students', 'Please enter a valid max students');
			return false;
		} else  {
			if(	isSet($this->data['TeacherLesson']['full_group_total_price']) && !empty($this->data['TeacherLesson']['full_group_total_price']) && 
				$this->data['TeacherLesson']['max_students'] && $this->data['TeacherLesson']['1_on_1_price']) {
				
				//Check if full_group_total_price is MORE then  max_students*1_on_1_price
				$maxAllowed = $this->data['TeacherLesson']['max_students']*$this->data['TeacherLesson']['1_on_1_price'];
				if($this->data['TeacherLesson']['full_group_total_price']>$maxAllowed) {
					$this->invalidate('max_students', 'Group price error, max is '.$maxAllowed.'. (max students * 1 on 1 price)');

                    //Check if total group price is LESS then 1 on 1 price (1 on 1 price is NOT 0)
                } else if($this->data['TeacherLesson']['full_group_total_price']<=$this->data['TeacherLesson']['1_on_1_price']) {
                    $this->invalidate('full_group_total_price', 'Full group price must be more the 1 on 1 price ('.$this->data['TeacherLesson']['1_on_1_price'].')');
                }
			}
		}
		return true;
	}
	public function maxStudentsCheck( $maxStudents ) {
		if($maxStudents['max_students']>1 && (!isSet($this->data['TeacherLesson']['full_group_total_price']) || !$this->data['TeacherLesson']['full_group_total_price'])) {
			$this->invalidate('full_group_total_price', 'Please enter a valid group price or set Max students to 1');
			return false;
		}
		return true;
	}
	
	public function beforeSave($options=array()) {
		parent::beforeSave($options);
		$this->Subject->calcFullGroupStudentPriceIfNeeded($this->data['TeacherLesson']);
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
            $teacherLessonData  = array(
                'subject_id'				=> $source['id'],
                'teacher_user_id'			=> $subjectData['user_id'],
                'subject_type'				=> $subjectData['type'],
                'lesson_type'				=> $subjectData['lesson_type'],
                'language'				    => $subjectData['language'],
                'datetime'					=> $this->Subject->datetimeToStr($datetime), //Convert timestamp to datetime
                'name'						=> $subjectData['name'],
                'description'				=> $subjectData['description'],
                'is_public'					=> is_null($isPublic) ? $subjectData['is_public'] : $isPublic,
                'subject_type'				=> $subjectData['type'],
                'duration_minutes'			=> $subjectData['duration_minutes'],
                'max_students'				=> $subjectData['max_students'],
                '1_on_1_price'				=> $subjectData['1_on_1_price'],
                'full_group_student_price'	=> $subjectData['full_group_student_price'],
                'full_group_total_price'	=> $subjectData['full_group_total_price'],
            );

            if($teacherLessonData['subject_type'] == SUBJECT_TYPE_OFFER) {
                //Only the teacher that opened the subject can teach it
                unset($extra['teacher_user_id']);
            }
            $teacherLessonData = am($teacherLessonData, $extra);

            if($teacherLessonData['subject_type'] == SUBJECT_TYPE_REQUEST && $subjectData['user_id']==$teacherLessonData['teacher_user_id']) {
                //The subject owner cannot teach it
                return false;
            }

            if(!isSet($teacherLessonData['student_user_id'])) {
                if($teacherLessonData['subject_type'] == SUBJECT_TYPE_REQUEST) {
                    //Deafult student user id
                    $teacherLessonData['student_user_id'] = $subjectData['user_id'];
                } else {
                    return false;
                }
            }

        } else if($source['type']=='user_lesson') {
            App::import('Model', 'UserLesson');
            $ulObj = new UserLesson();
            $ulObj->recursive = -1;
            $ulData = $ulObj->findByUserLessonId($source['id']);
            if(!$ulData) {
                return false;
            }
            $teacherLessonData = $ulData['UserLesson'];

            if($teacherLessonData['subject_type'] == SUBJECT_TYPE_OFFER) {
                //Only the teacher that opened the subject can teach it
                unset($extra['teacher_user_id']);
            }
            $teacherLessonData = am($teacherLessonData, $extra);

        } else {
            return false;
        }
		
		$event = new CakeEvent('Model.TeacherLesson.beforeAdd', $this, array('teacher_lesson'=>$teacherLessonData, 'source'=>$source) );
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}
		$this->create();
		$this->set($teacherLessonData);
		if(!$this->save()) {
			return false;
		}

		$event = new CakeEvent('Model.TeacherLesson.afterAdd', $this, array('teacher_lesson'=>$teacherLessonData, 'source'=>$source) );
		$this->getEventManager()->dispatch($event);
		
		
		return $this->id;
	}
	
	
	public function cancel( $teacherLessonsId, $canceledBy='teacher',$teacherUserId=null ) {
		//Find the TeacherLesson
		$this->recursive = -1;
		$teacherLessonsData = $this->findByTeacherLessonId($teacherLessonsId);
		if(!$teacherLessonsData) {
			return false;
		}
		$teacherLessonsData = $teacherLessonsData['TeacherLesson'];
		
		if(!is_null($teacherUserId)) {
			//Check if that's the right teacher
			if($teacherLessonsData['teacher_user_id']!=$teacherUserId) {
				return false;
			}
		}
				
		if($teacherLessonsData['is_deleted']) {
			//Already deleted
			return true;
		}
		
		
		//TODO: move to event handler
		//Get all user lessons that are about to cacnel
		App::import('Model', 'Subject');
		App::import('Model', 'UserLesson');
		$userLessonObj = new UserLesson();
		$userLessonData = $userLessonObj->find('all', array('conditions'=>array('UserLesson.teacher_lesson_id'=>$teacherLessonsId, 
																				'UserLesson.stage'=>array( USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_ACCEPTED))));
		
		$event = new CakeEvent('Model.TeacherLesson.beforeCancel', $this, array('teacher_lesson'=>$teacherLessonsData,'user_lesson'=>$userLessonData));
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}
		
		
		//Update all users that are going to take place in that lesson
		App::import('Model', 'UserLesson');
		$userLessonObj = new UserLesson();
		$userLessonObj->updateAll(	array('UserLesson.stage'=>USER_LESSON_DENIED_BY_TEACHER),
									array('UserLesson.stage'=>array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_PENDING_STUDENT_APPROVAL, USER_LESSON_ACCEPTED), 'UserLesson.teacher_lesson_id'=>$teacherLessonsId));
										 
		
		//Delete the teacher lesson
		$this->id = $teacherLessonsId;
		$this->set(array('is_deleted'=>1));
		$this->save();
		//$this->delete($teacherLessonsId);						 
										 
		
		$event = new CakeEvent('Model.TeacherLesson.afterCancel', $this, array('teacher_lesson'=>$teacherLessonsData,'user_lesson'=>$userLessonData));
		$this->getEventManager()->dispatch($event);
		
		
		return true;
	}
	
	public function getLessonsByDate( $teacherUserId, $year, $month=null ) {
		$this->getDataSource();
		
		$startDate = $year.'-'.($month ? $month : 1).'-1';
		$endDate = $year.'-'.($month ? $month : 12).'-1';
		
		
		$conditions = array('teacher_user_id'=>$teacherUserId, 
							'datetime BETWEEN ? AND ?' => array($startDate, $this->getDataSource()->expression('date_add(\''.$endDate.'\',interval 1 month)')),
							'is_deleted'=>0 );
		
		return $this->find('all', array('conditions'=>$conditions));
	}
	
	public function getArchive($teacherUserId, $subectId=null, $limit=null, $page=1) {
		$conditions = array( 'teacher_user_id'=>$teacherUserId );
		if($subectId) {
			$conditions['TeacherLesson.subject_id'] = $subectId;
		}
		
		$conditions['OR'] = array(
			'datetime < NOW()',
			'is_deleted'=>1
		);
		
//		pr($conditions);
		return $this->find('all', array(
			'conditions'=>$conditions,
			'page'=>$page,
			'limit'=>$limit
		));
	}
	
	public function getUpcomming($teacherUserId, $subectId=null, $limit=null, $page=1) {
		$this->Subject;
		$conditions = array( 'teacher_user_id'=>$teacherUserId, 'datetime > NOW()', 'is_deleted'=>0,
								'OR'=>array(
										array('subject_type'=>SUBJECT_TYPE_OFFER), //Offers
										array('subject_type'=>SUBJECT_TYPE_REQUEST, 'num_of_students > 0') //approved lesson requests
									)
							);
		if($subectId) {
			$conditions['TeacherLesson.subject_id'] = $subectId;
		}
		
		return $this->find('all', array(
			'conditions'=>$conditions,
			'page'=>$page,
			'limit'=>$limit
		));
	}
	
	/*public function getPendingProposedLessons($teacherUserId, $subectId=null, $limit=null, $page=1) {
		$conditions = array( 'teacher_user_id'=>$teacherUserId, 'datetime > NOW()', 'is_deleted'=>0, 'subject_type'=>SUBJECT_TYPE_REQUEST, 'num_of_students'=>0 );
		if($subectId) {
			$conditions['TeacherLesson.subject_id'] = $subectId;
		}
		
		return $this->find('all', array(
			'conditions'=>$conditions,
			'page'=>$page,
			'limit'=>$limit
		));
	} */
	
}
?>