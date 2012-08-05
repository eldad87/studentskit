<?php
class User extends AppModel {
	public $name = 'User';
	public $useTable = 'users';
	public $primaryKey = 'user_id';
	public $actsAs = array('SignMeUp.SignMeUp');


    public $hasOne = array('Forum.Profile');
    public $hasMany = array('Forum.Access', 'Forum.Moderator');

    public $validate = array(
		'user_id' => array(
			'blank' => array(
				'rule'	=> 'blank',
				'on'	=> 'create',
				'message' => 'This field must be left blank'
			),
			'numeric' => array(
				'rule'	=> 'numeric',
				'message' => 'This field contain numeric value'
			)
		),
		'email' => array(
			'isUnique' => array(
				'rule'    		=> 'isUnique',
				'message' 		=> 'This email has already been taken.',
				'required'		=> true,
				'on'			=> 'create',
			),
			'email' => array(
				'rule'		=> 'email',
				'message'	=> 'Enter a valid email',
			)
		),
		'username' => array(
			'isUnique' => array(
				'rule'    		=> 'isUnique',
				'message' 		=> 'This username has already been taken.',
				'required'		=> true,
				'on'			=> 'create',
			),
		),
		
		/*'password'=>array(
			'minLengh' => array(
				'rule'		=> array('minLength', '6'),
				'message'	=> 'Minimum 6 characters long',
				'required'		=> true,
				'on'	=> 'create',
				'allowEmpty' => false
			)
		),*/
		
		'first_name'=> array(
			'alphaNumeric' => array(
				'rule'		=> 'alphaNumeric',
				'message'	=> 'Alphabets and numbers only',
				'required'		=> true,
				'on'	=> 'create',
				'allowEmpty' => false
            ),
			'between' => array(
				'rule'    => array('between', 2, 45),
				'message' => 'Between 2 to 45 characters'
			)
		),
		
		'last_name'=>array(
			'alphaNumeric' => array(
				'rule'		=> 'alphaNumeric',
				'message'	=> 'Alphabets and numbers only',
				'required'	=> false,
				'allowEmpty'=>true
            ),
			'between' => array(
				'rule'    => array('between', 2, 45),
				'message' => 'Between 2 to 45 characters',
			)
		),
		'dob' => array(
			'rule'    		=> array('date', 'dmy'),
			'message' 		=> 'Enter a valid date.',
			'allowEmpty' 	=> false,
		),
		
		'phone'=>array(
			'rule'    		=> 'phone',
			'message' 		=> 'Enter a valid phone.',
			'allowEmpty' 	=> false,
		),
		'user_zipcpde'=>array(
			'rule'    		=> array('postal', null, 'all'),
			'message' 		=> 'Enter a valid zipcode.',
			'allowEmpty' 	=> false,
		),
		'teacher_zipcpde'=>array(
			'rule'    		=> array('postal', null, 'all'),
			'message' 		=> 'Enter a valid zipcode.',
			'allowEmpty' 	=> false,
		),
	);

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['username'] = sprintf('CONCAT(%s.first_name, " ", %s.last_name)', $this->alias, $this->alias);
        $this->virtualFields['id'] = sprintf('%s.user_id', $this->alias); //Forum suppoer
    }

    public function beforeSave($options) {
        parent::beforeSave($options=array());

        //Detect user geo location using google API
    }

	/**
	 * 
	 * Get rating for a given teacher - by his studnets
	 * @param unknown_type $userId
	 * @param unknown_type $limit
	 * @param unknown_type $page
	 */
	public function getTeachertReviews( $teacherUserId, $limit=12, $page=1 ) {
		App::import('Model', 'UserLesson');
		$ulObj = new UserLesson();
		$conditions = array('UserLesson.teacher_user_id'=>$teacherUserId, 'stage'=>array(USER_LESSON_PENDING_TEACHER_RATING, USER_LESSON_DONE));
		
		return $ulObj->find('all', array(	'conditions'=>$conditions,
											'fields'=>array('student_user_id', 'rating_by_student', 'comment_by_student', 'student_image', 'datetime'),
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
		$conditions = array('teacher_user_id'=>$studentUserId, 'stage'=>array(USER_LESSON_PENDING_STUDENT_RATING, USER_LESSON_DONE));
		
		return $ulObj->find('all', array(	'conditions'=>$conditions,
											'fields'=>array('teacher_user_id', 'rating_by_teacher', 'comment_by_teacher', 'image', 'datetime'),
											'limit'=>$limit,
											'page'=>$page));
		
	}
	
	public function setRating($userId, $userType, $rating) {
		$update = array(
			$userType.'_avarage_rating'	=>'(('.$userType.'_raters_amount*'.$userType.'_avarage_rating)+'.$rating.')/('.$userType.'_raters_amount+1)',
			$userType.'_raters_amount'	=>$userType.'_raters_amount+1'
		);
		$this->id = $userId;
		$this->set($update);
		return $this->save();
	}
	
	public function getLiveLessonsByDate( $userId, $isOwner=true, $year=null, $month=null ) {
		App::import('Model', 'TeacherLesson');
		App::import('Model', 'UserLesson');
		$tlObj = new TeacherLesson();
		$ulObj = new UserLesson();
		

		
		//Get student lessons for a given month
		//$ulObj->unbindModel(array('belongsTo' => array('Student')));
		$ulObj->recursive = -1;
		$userLessons = $ulObj->getLiveLessonsByDate( $userId, $year, $month, ($isOwner ? null : array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_ACCEPTED)));
		
		//Get teacher lessons for a given month
		$tlObj->recursive = -1;
		$teacherLessons = $tlObj->getLiveLessonsByDate( $userId, $year, $month); //Public to all
		
		$allLessons = array();

			//Dim all none public/subject request teacherLessons
			foreach($teacherLessons AS &$teacherLesson) {
				//If !$isOwner, hide details about teacherLessons
				if( !$isOwner && !$teacherLesson['TeacherLesson']['is_public'] )  {
					$allLessons[] = array(	'name'=>null, 'type'=>'TeacherLesson', 
											'datetime'=>$teacherLesson['TeacherLesson']['datetime'], 'duration_minutes'=>$teacherLesson['TeacherLesson']['duration_minutes']);
					
				} else {
					$allLessons[] = array(	'teacher_lesson_id'=>$teacherLesson['TeacherLesson']['teacher_lesson_id'], 
											'name'=>$teacherLesson['TeacherLesson']['name'], 'type'=>'TeacherLesson', 
											'datetime'=>$teacherLesson['TeacherLesson']['datetime'], 'duration_minutes'=>$teacherLesson['TeacherLesson']['duration_minutes'],
											'num_of_students'=>$teacherLesson['TeacherLesson']['num_of_students'],'max_students'=>$teacherLesson['TeacherLesson']['max_students'],
											'1_on_1_price'=>$teacherLesson['TeacherLesson']['1_on_1_price'],'full_group_total_price'=>$teacherLesson['TeacherLesson']['full_group_total_price'],
											'full_group_student_price'=>$teacherLesson['TeacherLesson']['full_group_student_price'],
											'request_subject_id'=>$teacherLesson['TeacherLesson']['request_subject_id']
											);
				}
			}
			
			//TODO: check if user allow to show his userLessons
			$showUserLessonsToOthers=false;
			
			//Dim all none public userLessons or if user does not allow to publish his lessons as a student (!$showUserLessonsToOthers)
			foreach($userLessons AS &$userLesson) {
				//If !$isOwner, hide details about userLessons
					if(!$isOwner && (!$showUserLessonsToOthers || !$userLesson['UserLesson']['is_public'])) {
						//unset($userLesson['Subject'], $userLesson['Teacher'], $userLesson['Student']);
						$allLessons[] = array(	'name'=>null, 'type'=>'UserLesson', 
												'datetime'=>$userLesson['UserLesson']['datetime'], 'duration_minutes'=>$userLesson['UserLesson']['duration_minutes']);
					} else {
						
						$allLessons[] = array(	'user_lesson_id'=>$userLesson['UserLesson']['user_lesson_id'], 'teacher_lesson_id'=>$userLesson['UserLesson']['teacher_lesson_id'],
												'name'=>$userLesson['UserLesson']['name'], 'stage'=>$userLesson['UserLesson']['stage'], 'type'=>'UserLesson',
												'datetime'=>$userLesson['UserLesson']['datetime'], 'duration_minutes'=>$userLesson['UserLesson']['duration_minutes'],
												'num_of_students'=>$userLesson['TeacherLesson']['num_of_students'], 'max_students'=>$userLesson['TeacherLesson']['max_students'],
												'1_on_1_price'=>$userLesson['UserLesson']['1_on_1_price'], 'full_group_total_price'=>$userLesson['UserLesson']['full_group_total_price'],
												'full_group_student_price'=>$userLesson['UserLesson']['full_group_student_price'],
												'request_subject_id'=>$userLesson['UserLesson']['request_subject_id']
												);
					}
			}
		
		
		return $allLessons;
	}
}
?>