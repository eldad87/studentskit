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
            'image_source' => array(
                'finalPath'     => 'img/users/',
                'nameCallback'  => 'formatImageName',
                'overwrite'     => true,
                'allowEmpty'    => true,
                'transforms'    => array(
                    'image_resize'=>array(  'method'=>'resize','width'=> 200,  'height'=>210,  'append'=>'_resize', 'overwrite'=>true,
                                            'aspect'=>true, 'mode'=>'height', 'setAsTransformationSource'=>true,    'nameCallback'  => 'formatImageName' ),
                    'image_crop_38x38'  =>array('method'=>'crop', 'width' => 38,   'height'=>38,   'append'=>'_38x38',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_60x60'  =>array('method'=>'crop', 'width' => 60,   'height'=>60,   'append'=>'_60x60',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_63x63'  =>array('method'=>'crop', 'width' => 63,   'height'=>63,   'append'=>'_63x63',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_72x72'  =>array('method'=>'crop', 'width' => 72,   'height'=>72,   'append'=>'_72x72',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_78x78'  =>array('method'=>'crop', 'width' => 78,   'height'=>78,   'append'=>'_78x78',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_80x80'  =>array('method'=>'crop', 'width' => 80,   'height'=>80,   'append'=>'_80x80',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_100x100'=>array('method'=>'crop', 'width' => 100,  'height'=>100,  'append'=>'_100x100',  'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_149x182'=>array('method'=>'crop', 'width' => 149,  'height'=>182,  'append'=>'_149x182',  'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_200x210'=>array('method'=>'crop', 'width' => 200,  'height'=>210,  'append'=>'_200x210',  'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                ),
                'transport' => array(
                    'class'     => 's3',
                    'accessKey' => 'AKIAIV2BMVHTLRF64V7Q',
                    'secretKey' => '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',
                    'bucket'    => S3_BUCKET,
                    'region'    => 'us-east-1',
                    'folder'    => 'img/user/'
                )
            )
        ),
        'Uploader.FileValidation' => array(
            'image_source' => array(
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
        'password' => array(
            'confirmPasswordNotAWeakPassword' => array(
                'rule'      => array('confirmPasswordNotAWeakPassword'),
                'message'   => 'Password is too common, please try a different one'
            ),
        ),
		'first_name'=> array(
			'alphaNumeric' => array(
				'rule'		=> 'alphaNumeric',
				'message'	=> 'First name can contain alphabets and numbers only',
				'required'	=> true,
				'on'	    => 'create',
				'allowEmpty' => false
            ),
			'between' => array(
				'rule'    => array('between', 2, 45),
				'message' => 'First name can have %d to %d characters'
			)
		),
		
		'last_name'=>array(
			'between' => array(
				'rule'    => array('between', 2, 45),
                'allowEmpty' => true,
                'required'	=> false,
				'message' => 'Last name can have %d to %d characters',
			)
		),
		'dob' => array(
			'rule'    		=> array('date', 'dmy'),
			'message' 		=> 'Enter a valid date of birth.',
			'allowEmpty' 	=> true,
		),
		
		'phone'=>array(
			'rule'    		=> 'phone',
			'message' 		=> 'Enter a valid phone.',
			'allowEmpty' 	=> true,
		),
		'teacher_zipcpde'=>array(
			'rule'    		=> array('postal', null, 'all'),
			'message' 		=> 'Enter a valid zipcode.',
			'allowEmpty' 	=> true,
		),
	);

    public function confirmPasswordNotAWeakPassword($field) {
        App::import('Model', 'WeakPassword');
        $weakPassObj = new WeakPassword();

        return $weakPassObj->isInDictionary($this->data[$this->alias]['password']) ? false : true;
    }


    public function setCreditPoints($userId, $creditPoints) {

        $expression = (($creditPoints>0) ? '+' : '-') . abs($creditPoints);

        $this->create(false);
        $this->id = $userId;
        $save = array(
            'credit_points' => $this->getDataSource()->expression('credit_points'.$expression)
        );

        return $this->save(
            $save
        );
    }

    public function getCreditPoints($userId) {
        $this->recursive = -1;
        $this->cacheQueries = false;
        $u = $this->find('first', array(    'conditions'=>array('user_id'=>$userId),
                                            'fields'=>array('credit_points')));
        if(!$u) {
            return 0;
        }

        return $u[$this->alias]['credit_points'];
    }

    //Change upload folder
    public function beforeTransport($options) {
        $options['folder'] .= String::uuid() . '/';
        return $options;
    }

    //Remove the "resize-100x100" from transformations file
    public function formatImageName($name, $file) {
        return $this->getUploadedFile()->name();
    }

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['username'] = sprintf('CONCAT(%s.first_name, " ", %s.last_name)', $this->alias, $this->alias);
        $this->virtualFields['slug'] = sprintf('%s.user_id', $this->alias); //Comments
        $this->virtualFields['id'] = sprintf('%s.user_id', $this->alias); //Forum/Uploader
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
            $userType.'_avarage_rating' =>$this->getDataSource()->expression('CASE WHEN '.$userType.'_raters_amount=0 THEN '.$rating.'
                                                                    ELSE (('.$userType.'_raters_amount*'.$userType.'_avarage_rating)+'.$rating.')/('.$userType.'_raters_amount+1) END'),
			$userType.'_raters_amount'	=>$this->getDataSource()->expression($userType.'_raters_amount+1')
		);
		$this->id = $userId;
		$this->set($update);
		return $this->save();
	}
	

    /**
     * @param $userId
     * @param bool $isOwner - if not owner, dim lesson info
     * @param bool $futureOnly
     * @return array
     */
    //public function getLiveLessonsByDate( $userId, $isOwner=true, $year=null, $month=null ) {
    public function getLiveLessons( $userId, $isOwner=true, $futureOnly=true ) {
		App::import('Model', 'TeacherLesson');
		App::import('Model', 'UserLesson');
		$tlObj = new TeacherLesson();
		$ulObj = new UserLesson();
		

		
		//Get student lessons for a given month
		//$ulObj->unbindModel(array('belongsTo' => array('Student')));
		$ulObj->recursive = 0;
		/*$userLessons = $ulObj->getLiveLessonsByDate( $userId, $year, $month, ($isOwner ? null : array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT,
                                                                                                        USER_LESSON_RESCHEDULED_BY_TEACHER, USER_LESSON_RESCHEDULED_BY_STUDENT,
                                                                                                        USER_LESSON_ACCEPTED)));*/
        $userLessons = $ulObj->getLiveLessons(  $userId,
                                                array(  USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT,
                                                        USER_LESSON_RESCHEDULED_BY_TEACHER, USER_LESSON_RESCHEDULED_BY_STUDENT,
                                                        USER_LESSON_ACCEPTED),
                                                $futureOnly);

		//Get teacher lessons for a given month
		$tlObj->recursive = -1;
		//$teacherLessons = $tlObj->getLiveLessonsByDate( $userId, $year, $month); //Public to all
		$teacherLessons = $tlObj->getLiveLessons( $userId, $futureOnly );

		$allLessons = array();

			//Dim all none public/subject request teacherLessons
			foreach($teacherLessons AS &$teacherLesson) {
				//If !$isOwner, hide details about teacherLessons
				if( !$isOwner && !$teacherLesson['TeacherLesson']['is_public'] )  {
					$allLessons[] = array(	'type'              => 'TeacherLesson',
                                            'datetime'          => $teacherLesson['TeacherLesson']['datetime'],
                                            'duration_minutes'  => $teacherLesson['TeacherLesson']['duration_minutes'],
                    );
					
				} else {
					$allLessons[] = array(	'teacher_lesson_id'         => $teacherLesson['TeacherLesson']['teacher_lesson_id'],
                                            'name'                      => $teacherLesson['TeacherLesson']['name'],
                                            'description'               => $teacherLesson['TeacherLesson']['description'], 'type'=>'TeacherLesson',
											'datetime'                  => $teacherLesson['TeacherLesson']['datetime'],
                                            'duration_minutes'          => $teacherLesson['TeacherLesson']['duration_minutes'],
											'num_of_students'           => $teacherLesson['TeacherLesson']['num_of_students'],
                                            'max_students'              => $teacherLesson['TeacherLesson']['max_students'],
											'1_on_1_price'              => $teacherLesson['TeacherLesson']['1_on_1_price'],
                                            'full_group_total_price'    => $teacherLesson['TeacherLesson']['full_group_total_price'],
											'full_group_student_price'  => $teacherLesson['TeacherLesson']['full_group_student_price'],
											'request_subject_id'        => $teacherLesson['TeacherLesson']['request_subject_id'],
											'image_source'              => $teacherLesson['TeacherLesson']['image_source']
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
						$allLessons[] = array(	'type'              => 'UserLesson',
												'datetime'          => $userLesson['UserLesson']['datetime'],
                                                'duration_minutes'  => $userLesson['UserLesson']['duration_minutes']);
					} else {
						
						$allLessons[] = array(	'user_lesson_id'            => $userLesson['UserLesson']['user_lesson_id'],
                                                'teacher_lesson_id'         => $userLesson['UserLesson']['teacher_lesson_id'],
                                                'name'                      => $userLesson['UserLesson']['name'],
                                                'description'               => $userLesson['UserLesson']['description'],
                                                'stage'                     => $userLesson['UserLesson']['stage'],
                                                'type'                      => 'UserLesson',
                                                'datetime'                  => $userLesson['UserLesson']['datetime'],
                                                'duration_minutes'          => $userLesson['UserLesson']['duration_minutes'],
                                                'num_of_students'           => $userLesson['TeacherLesson']['num_of_students'],
                                                'max_students'              => $userLesson['TeacherLesson']['max_students'],
                                                '1_on_1_price'              => $userLesson['UserLesson']['1_on_1_price'],
                                                'full_group_total_price'    => $userLesson['UserLesson']['full_group_total_price'],
                                                'full_group_student_price'  => $userLesson['UserLesson']['full_group_student_price'],
                                                'request_subject_id'        => $userLesson['UserLesson']['request_subject_id'],
                                                'image_source'              => $userLesson['UserLesson']['image_source']
												);
					}
			}
		
		
		return $allLessons;
	}
}
?>