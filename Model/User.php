<?php
define('IMAGE_NONE', 0);
define('IMAGE_SUBJECT_OWNER', 1);
define('IMAGE_SUBJECT', 2);

class User extends AppModel {
	public $name = 'User';
	public $useTable = 'users';
	public $primaryKey = 'user_id';
    public $actsAs = array(
        'SignMeUp.SignMeUp',
        'Uploader.Attachment' => array(
            'imageUpload'=>array(
                'uploadDir'	            => 'img/users/',
                'appendNameToUploadDir' => true,
                'flagColumn'            => array('dbColumn'=>'image', 'value'=>IMAGE_SUBJECT_OWNER), //Flag DB.table.image with value of IMAGE_SUBJECT_OWNER
                'name'                  => 'formatImageName',
                'dbColumn'              => 'image_source',
                'transforms' => array(
                    array('method'=>'resize','width'=> 200,  'height'=>210,  'append'=>'_resize',   'overwrite'=>true, 'dbColumn'=>'image_resize', 'aspect'=>true, 'mode'=>Uploader::MODE_HEIGHT, 'setAsTransformationSource'=>true),
                    array('method'=>'crop', 'width' => 38,   'height'=>38,   'append'=>'_38x38',    'overwrite'=>true, 'dbColumn'=>'image_crop_38x38'),
                    array('method'=>'crop', 'width' => 60,   'height'=>60,   'append'=>'_60x60',    'overwrite'=>true, 'dbColumn'=>'image_crop_60x60'),
                    array('method'=>'crop', 'width' => 63,   'height'=>63,   'append'=>'_63x63',    'overwrite'=>true, 'dbColumn'=>'image_crop_63x63'),
                    array('method'=>'crop', 'width' => 72,   'height'=>72,   'append'=>'_72x72',    'overwrite'=>true, 'dbColumn'=>'image_crop_72x72'),
                    array('method'=>'crop', 'width' => 78,   'height'=>78,   'append'=>'_78x78',    'overwrite'=>true, 'dbColumn'=>'image_crop_78x78'),
                    array('method'=>'crop', 'width' => 80,   'height'=>80,   'append'=>'_80x80',    'overwrite'=>true, 'dbColumn'=>'image_crop_80x80'),
                    array('method'=>'crop', 'width' => 100,  'height'=>100,  'append'=>'_100x100',  'overwrite'=>true, 'dbColumn'=>'image_crop_100x100'),
                    array('method'=>'crop', 'width' => 149,  'height'=>182,  'append'=>'_149x182',  'overwrite'=>true, 'dbColumn'=>'image_crop_149x182'),
                    array('method'=>'crop', 'width' => 200,  'height'=>210,  'append'=>'_200x210',  'overwrite'=>true, 'dbColumn'=>'image_crop_200x210'),
                )
            )
        ),

        'Uploader.FileValidation' => array(
            'imageUpload' => array(
                'extension'	=> array('gif', 'jpg', 'png', 'jpeg'),
                'filesize'	=> 1048576,
                'minWidth'	=> 200,
                'minHeight'	=> 210,
                'required'	=> false
            )
        )
    );

    public $hasOne = array('Forum.Profile');
    public $hasMany = array('Forum.Access', 'Forum.Moderator',
                            'TeacherCertificate'=>array(
                                'className'	=> 'TeacherCertificate',
                                'foreignKey'=>'teacher_user_id'
                            ),
                            'TeacherAboutVideo'=>array(
                                'className'	=> 'TeacherAboutVideo',
                                'foreignKey'=>'teacher_user_id'
                            )
    );
    public $validate = array(
		'user_id' => array(
			'blank' => array(
				'rule'	=> 'blank',
				'on'	=> 'create',
				'message' => 'This field must be left blank'
			),
			'numeric' => array(
				'rule'	=> 'numeric',
				'message' => 'This field must be a numeric value'
			)
		),
		'email' => array(
			'email' => array(
				'rule'		=> 'email',
				'message'	=> 'Enter a valid email',
			),
            'isUnique' => array(
				'rule'    		=> 'isUnique',
				'message' 		=> 'This email has already been taken.',
				'required'		=> true,
				'on'			=> 'create',
			),
		),
		/*'username' => array(
			'isUnique' => array(
				'rule'    		=> 'isUnique',
				'message' 		=> 'This email has already been taken.',
				'required'		=> true,
				'on'			=> 'create',
			),
		),*/
		
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
				'message' => 'Between %d to %d characters'
			)
		),
		
		'last_name'=>array(
			/*'alphaNumeric' => array(
				'rule'		=> 'alphaNumeric',
				'message'	=> 'Alphabets and numbers only',
				'required'	=> false,
				'allowEmpty'=>true
            ),*/
			'between' => array(
				'rule'    => array('between', 2, 45),
				'message' => 'Between %d to %d characters',
			)
		),
		'dob' => array(
			'rule'    		=> array('date', 'dmy'),
			'message' 		=> 'Enter a valid date.',
			'allowEmpty' 	=> true,
		),
		
		'phone'=>array(
			'rule'    		=> 'phone',
			'message' 		=> 'Enter a valid phone.',
			'allowEmpty' 	=> true,
		),
		'user_zipcpde'=>array(
			'rule'    		=> array('postal', null, 'all'),
			'message' 		=> 'Enter a valid zipcode.',
			'allowEmpty' 	=> true,
		),
		'teacher_zipcpde'=>array(
			'rule'    		=> array('postal', null, 'all'),
			'message' 		=> 'Enter a valid zipcode.',
			'allowEmpty' 	=> true,
		),
	);

    public function formatImageName($name, $field, $file) {
        return String::uuid();
    }

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['username'] = sprintf('CONCAT(%s.first_name, " ", %s.last_name)', $this->alias, $this->alias);
        $this->virtualFields['id'] = sprintf('%s.user_id', $this->alias); //Forum suppoer
    }

    /*public function beforeSave($options) {
        parent::beforeSave($options);

        //TODO: Detect user geo location using google API

        return true;
    }*/

    /*public function afterSave($created) {
        parent::afterSave($created);

        //check if image was updated
        if(isSet($this->data['User']['image']) && $this->data['User']['image']) {
            //update subjects with default user image
            App::import('Model', 'Subject');
            $subjectObj = new Subject();
            $subjectObj->recursive = -1;
            $subjectObj->updateAll(array('image'=>IMAGE_SUBJECT_OWNER), array('image'=>IMAGE_NONE, 'user_id'=>$this->id));

            //Update teacher lessons with default user image
            App::import('Model', 'TeacherLesson');
            $tlObj = new TeacherLesson();
            $tlObj->recursive = -1;
            $tlObj->updateAll(array('image'=>IMAGE_SUBJECT_OWNER), array('image'=>IMAGE_NONE, 'teacher_user_id'=>$this->id));
        }
    }*/

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
		$userLessons = $ulObj->getLiveLessonsByDate( $userId, $year, $month, ($isOwner ? null : array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT,
                                                                                                        USER_LESSON_RESCHEDULED_BY_TEACHER, USER_LESSON_RESCHEDULED_BY_STUDENT,
                                                                                                        USER_LESSON_ACCEPTED)));

		//Get teacher lessons for a given month
		$tlObj->recursive = -1;
		$teacherLessons = $tlObj->getLiveLessonsByDate( $userId, $year, $month); //Public to all
		
		$allLessons = array();

			//Dim all none public/subject request teacherLessons
			foreach($teacherLessons AS &$teacherLesson) {
				//If !$isOwner, hide details about teacherLessons
				if( !$isOwner && !$teacherLesson['TeacherLesson']['is_public'] )  {
					$allLessons[] = array(	'name'=>null, 'description'=>null,
                                            'type'=>'TeacherLesson',
                                            'datetime'=>$teacherLesson['TeacherLesson']['datetime'],
                                            'duration_minutes'=>$teacherLesson['TeacherLesson']['duration_minutes'],
                    );
					
				} else {
					$allLessons[] = array(	'teacher_lesson_id'=>$teacherLesson['TeacherLesson']['teacher_lesson_id'],
                                            'name'=>$teacherLesson['TeacherLesson']['name'], 'description'=>$teacherLesson['TeacherLesson']['description'], 'type'=>'TeacherLesson',
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
						$allLessons[] = array(	'name'=>null, 'description'=>null, 'type'=>'UserLesson',
												'datetime'=>$userLesson['UserLesson']['datetime'], 'duration_minutes'=>$userLesson['UserLesson']['duration_minutes']);
					} else {
						
						$allLessons[] = array(	'user_lesson_id'=>$userLesson['UserLesson']['user_lesson_id'], 'teacher_lesson_id'=>$userLesson['UserLesson']['teacher_lesson_id'],
												'name'=>$userLesson['UserLesson']['name'], 'description'=>$userLesson['UserLesson']['description'],
                                                'stage'=>$userLesson['UserLesson']['stage'], 'type'=>'UserLesson',
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