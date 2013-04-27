
<?php
define('LESSON_TYPE_VIDEO', 'video');
define('LESSON_TYPE_LIVE', 'live');
define('LESSON_TYPE_COURSE', 'course');

define('LESSON_TYPE_VIDEO_NO_ADS_TIME_SEC', DAY*2);


define('SUBJECT_TYPE_OFFER', 1);
define('SUBJECT_TYPE_REQUEST', 2);

define('SUBJECT_IS_ENABLE_FALSE', 0);
define('SUBJECT_IS_ENABLE_TRUE', 1);

define('SUBJECT_IS_PUBLIC_FALSE', 0);
define('SUBJECT_IS_PUBLIC_TRUE', 1);



define('CREATION_STAGE_NEW', 0);
define('CREATION_STAGE_SUBJECT', 1);
define('CREATION_STAGE_MEETING', 2);
define('CREATION_STAGE_FILES', 3);
define('CREATION_STAGE_TESTS', 4);
define('CREATION_STAGE_PUBLISH', 5);


App::import('Model', 'User'); //for IMAGE_SUBJECT
App::import('Model', 'SolrSearch');
class Subject extends SolrSearch {
    protected $solrCore = SUBJECT_TYPE_OFFER;

	public $name = 'Subject';
	public $useTable = 'subjects';
	public $primaryKey = 'subject_id';
    public $actsAs = array(
        'Lock',
        'LanguageFilter',
        'Lesson',
        'Uploader.Attachment' => array(
            'image_source' => array(
                'finalPath'     => 'img/subjects/',
                'nameCallback'  => 'formatImageName',
                'overwrite'     => true,
                'allowEmpty'    => true,
                'transforms'    => array(
                    'image_resize'=>array(  'method'=>'resize','width'=> 200,  'height'=>210,  'append'=>'_resize', 'overwrite'=>true,
                        'aspect'=>true, 'mode'=>'height', 'setAsTransformationSource'=>true,    'nameCallback'  => 'formatImageName' ),
                    'image_crop_38x38'  =>array('method'=>'crop', 'width' => 38,   'height'=>38,   'append'=>'_38x38',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_58x58'  =>array('method'=>'crop', 'width' => 58,   'height'=>58,   'append'=>'_58x58',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_60x60'  =>array('method'=>'crop', 'width' => 60,   'height'=>60,   'append'=>'_60x60',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_63x63'  =>array('method'=>'crop', 'width' => 63,   'height'=>63,   'append'=>'_63x63',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_72x72'  =>array('method'=>'crop', 'width' => 72,   'height'=>72,   'append'=>'_72x72',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_78x78'  =>array('method'=>'crop', 'width' => 78,   'height'=>78,   'append'=>'_78x78',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_80x80'  =>array('method'=>'crop', 'width' => 80,   'height'=>80,   'append'=>'_80x80',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_100x100'=>array('method'=>'crop', 'width' => 100,  'height'=>100,  'append'=>'_100x100',  'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_128x95' =>array('method'=>'crop', 'width' => 128,  'height'=>95,   'append'=>'_128x95',   'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_149x182'=>array('method'=>'crop', 'width' => 149,  'height'=>182,  'append'=>'_149x182',  'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_200x210'=>array('method'=>'crop', 'width' => 200,  'height'=>210,  'append'=>'_200x210',  'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                    'image_crop_436x214'=>array('method'=>'crop', 'width' => 436,  'height'=>214,  'append'=>'_436x214',  'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                ),
                'transport' => array(
                    'class'     => 's3',
                    'accessKey' => 'AKIAIV2BMVHTLRF64V7Q',
                    'secretKey' => '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',
                    'bucket'    => S3_BUCKET,
                    'region'    => 'us-east-1',
                    'folder'    => 'img/subjects/'
                )
            ),
            'video_source'=>array(
                'finalPath'	            => 'vid/subjects/about_videos/',
                'appendNameToUploadDir' => true,
                'name'                  => 'formatFileName',
                'allowEmpty'            => true,
                'transport' => array(
                    'class'     => 's3',
                    'accessKey' => 'AKIAIV2BMVHTLRF64V7Q',
                    'secretKey' => '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',
                    'bucket'    => S3_BUCKET,
                    'region'    => 'us-east-1',
                    'folder'    => 'vid/subjects/about_videos/'
                )
            )
        ),
        'Uploader.FileValidation' => array(
            'video_source' => array(
                'extension'	=> array('webm', 'ogv', 'mp4', 'flv', 'mov'),
                'filesize'	=> 104857600, //100MB
                'required'	=> false
            ),
            'image_source' => array(
                'extension'	=> array('gif', 'jpg', 'png', 'jpeg'),
                'filesize'	=> 1048576,
                'minWidth'	=> 440,
                'minHeight'	=> 215,
                'required'	=> false
            )
        )
    );

	public $validate = array(
		'name'=> array(
			'between' => array(
            	'required'	=> 'create',
				'allowEmpty'=> false,
                'rule'    	=> array('between', 2, 45),
				'message' 	=> 'Name must have %d to %d characters',
                'last'      =>true
			)
		),
		'description'=> array(
			'minLength' 	=> array(
				'required'	=> 'create',
				'allowEmpty'=> false,
				'rule'    	=> array('minLength', 15),
				'message' 	=> 'Description must have more then %d characters'
			)
		),

        'lesson_type'=> array(
            'inList' => array(
                'required'	=> 'create',
                'allowEmpty'=> false,
                'rule'    	=> array('inList', array(LESSON_TYPE_VIDEO, LESSON_TYPE_LIVE) ),//array(LESSON_TYPE_VIDEO, LESSON_TYPE_LIVE)
                'message' 	=> 'Please select a lesson type',
                //'last'      =>true
            )
        ),
		'duration_minutes'=> array(
			'range' 		=> array(
				'required'	=> 'create',
				'allowEmpty'=> false,
				'rule'    	=> array('range', 4, 241),
				'message' 	=> 'Must be more then %d minutes and less then %d minutes'
			)
		),
		'1_on_1_price'=> array(
			'price' => array(
            	'required'	=> 'create',
				'allowEmpty'=> false,
				'rule'    	=> 'numeric',
				'message' 	=> 'Enter a valid price, for a FREE lesson, set 0'
			),
			'price_range' => array(
				'required'	=> 'create',
				'allowEmpty'=> false,
                'rule'    	=> array('priceRangeCheck', '1_on_1_price'),
                'message' 	=> 'Price range error'
			)
		),
		'max_students'=> array(
			'range' 		=> array(
				'required'	=> 'create',
				'allowEmpty'=> false,
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
                'message' 	=> 'Enter a valid price'
            ),
            'full_group_student_price' 	=> array(
                'allowEmpty'=> true,
                'rule'    	=> 'fullGroupStudentPriceCheck',
                'message' 	=> 'You must set a price'
            )
        ),
	);

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
        $this->virtualFields['id'] = sprintf('%s.%s', $this->alias, $this->primaryKey); //Uploader
    }

    /*
     * Add image_source to comments
     */
    public function commentBeforeFind($options) {
        $result = $this->Behaviors->dispatchMethod($this, 'commentBeforeFind', array($options));

        $userModel = $this->Behaviors->Commentable->settings[$this->alias]['userModelAlias'];
        $this->Comment->belongsTo[$userModel]['fields'][] = 'username';
        $this->Comment->belongsTo[$userModel]['fields'][] = 'image_source';

        return $result;
    }


    public function beforeValidate($options=array()) {
        parent::beforeValidate($options);

        App::uses('Languages', 'Utils.Lib');
        $lang = new Languages();
        $this->validator()->add('language', 'inList', array(
            'required'	=> 'create',
            'allowEmpty'=> false,
            'rule'    	=> array('inList', array_flip($lang->lists('locale'))),
            'message' 	=> __('Please select a language'),
            'last'      =>true
        ));


        $this->validateRules($this);
    }






    public function beforeSave($options=array()) {
		parent::beforeSave($options);

        $pKey = !empty($this->data[$this->name][$this->primaryKey]) ? $this->data[$this->name][$this->primaryKey] : $this->id;
        $exists = $this->exists($pKey);
        if($exists) {
            unset($this->data[$this->name]['lesson_type']);
        }


        /**
         * Default settings for is_public (in case not provided).
         * Based on creation_stage value
         */
        /*if(isSet($this->data['Subject']['creation_stage'])) {
            //This is based on the fact that you cannot downgrade the creation stage.
            if($this->data['Subject']['creation_stage']==CREATION_STAGE_PUBLISH) {
                if(!isSet($this->data['Subject']['is_public'])) {
                    $this->data['Subject']['is_public'] = SUBJECT_IS_PUBLIC_TRUE;
                }
            } else {
                $this->data['Subject']['is_public'] = SUBJECT_IS_PUBLIC_FALSE;
            }

        } else if(!$exists) {
            //New subject-offer record, and no creation-stage, set is_public to default
            $this->data['Subject']['is_public'] = SUBJECT_IS_PUBLIC_FALSE;
        }*/

        //Existing subject - having a subject image
        if( $exists && isSet($this->data['Subject']['image']) && $this->data['Subject']['image']==IMAGE_SUBJECT ) {
            App::uses('Sanitize', 'Utility');
            //Update subject teacher lessons
            App::import('Model', 'TeacherLesson');
            $tlObj = new TeacherLesson();
            $tlObj->recursive = -1;

            $tlObj->updateAll(array('image'             =>IMAGE_SUBJECT,
                                    'image_source'      =>'\''.Sanitize::escape($this->data['Subject']['image_source'],      $this->useDbConfig).'\'',

                                    'image_resize'      =>'\''.Sanitize::escape($this->data['Subject']['image_resize'],      $this->useDbConfig).'\'',
                                    'image_crop_38x38'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_38x38'],  $this->useDbConfig).'\'',
                                    'image_crop_58x58'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_58x58'],  $this->useDbConfig).'\'',
                                    'image_crop_60x60'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_60x60'],  $this->useDbConfig).'\'',
                                    'image_crop_63x63'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_63x63'],  $this->useDbConfig).'\'',
                                    'image_crop_72x72'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_72x72'],  $this->useDbConfig).'\'',
                                    'image_crop_78x78'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_78x78'],  $this->useDbConfig).'\'',
                                    'image_crop_80x80'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_80x80'],  $this->useDbConfig).'\'',
                                    'image_crop_100x100'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_100x100'],$this->useDbConfig).'\'',
                                    'image_crop_128x95' =>'\''.Sanitize::escape($this->data['Subject']['image_crop_128x95'],$this->useDbConfig).'\'',
                                    'image_crop_149x182'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_149x182'],$this->useDbConfig).'\'',
                                    'image_crop_200x210'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_200x210'],$this->useDbConfig).'\'',
                                    'image_crop_436x214'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_436x214'],$this->useDbConfig).'\''),

                                array($tlObj->name.'.subject_id'=>$pKey));

            //Update subject user lessons
            App::import('Model', 'userLesson');
            $ulObj = new userLesson();
            $ulObj->recursive = -1;
            $ulObj->updateAll(array('image'             =>IMAGE_SUBJECT,
                                    'image_source'      =>'\''.Sanitize::escape($this->data['Subject']['image_source'],      $this->useDbConfig).'\'',

                                    'image_resize'      =>'\''.Sanitize::escape($this->data['Subject']['image_resize'],      $this->useDbConfig).'\'',
                                    'image_crop_38x38'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_38x38'],  $this->useDbConfig).'\'',
                                    'image_crop_58x58'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_58x58'],  $this->useDbConfig).'\'',
                                    'image_crop_60x60'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_60x60'],  $this->useDbConfig).'\'',
                                    'image_crop_63x63'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_63x63'],  $this->useDbConfig).'\'',
                                    'image_crop_72x72'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_72x72'],  $this->useDbConfig).'\'',
                                    'image_crop_78x78'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_78x78'],  $this->useDbConfig).'\'',
                                    'image_crop_80x80'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_80x80'],  $this->useDbConfig).'\'',
                                    'image_crop_100x100'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_100x100'],$this->useDbConfig).'\'',
                                    'image_crop_128x95' =>'\''.Sanitize::escape($this->data['Subject']['image_crop_128x95'], $this->useDbConfig).'\'',
                                    'image_crop_149x182'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_149x182'],$this->useDbConfig).'\'',
                                    'image_crop_200x210'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_200x210'],$this->useDbConfig).'\'',
                                    'image_crop_436x214'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_436x214'],$this->useDbConfig).'\''),

                                    array($ulObj->name.'.subject_id'=>$pKey));
        }

        if(isSet($this->data['Subject']['category_id'])) {
            //bind subject to a forum
            App::import('Model', 'Category');
            $scObj = new Category();
            $cData = $scObj->findByCategoryId($this->data['Subject']['category_id']);
            if($cData && $cData['Category']['forum_id']) {
                $this->data['Subject']['forum_id'] = $cData['Category']['forum_id'];
            }
        }

        return true;
	}




    /*public static function calcFullGroupPriceIfNeeded(&$data, $existingRecord) {
        if(isSet($data['max_students']) && $data['max_students']) {
            if($data['max_students']==1) {
                $data['full_group_student_price'] = null;

            } else if($data['max_students']>1 &&
                        isSet($data['full_group_student_price']) && !empty($data['full_group_student_price'])) {

                //Calculate full_group_student_price

            }
        } else {
            unset(	$data['full_group_student_price']);
        }
    }



    public static function extraValidation(&$obj) {
        $objData =& $obj->data[$obj->name];

        $lessonType = null;
        if(isSet($objData['lesson_type'])) {
            $lessonType = $objData['lesson_type'];
        } else {
            //Find object PK
            $objId = false;
            if($obj->id) {
                $objId = $obj->id;
            } else if(isSet($objData[$obj->primaryKey]) && !empty($objData[$obj->primaryKey])) {
                $objId = $objData[$obj->primaryKey];
            }
            if(!$objId) {
                return true;
            }

            //Load object data
            $foundData = $obj->find('first', array('conditions'=>array($obj->primaryKey=>$objId)));
            if(!$foundData) {
                return false;
            }

            $lessonType = $foundData[$obj->name]['lesson_type'];
        }

        if($lessonType==LESSON_TYPE_VIDEO) {
            $objData['max_students'] = 1;
            unset($objData['full_group_student_price']);
        }
    }*/


    public function afterSave($created) {
        parent::afterSave($created);

        //Set file system
        if($created) {
            App::import('Model', 'FileSystem');
            $fsObj = new FileSystem();

            //Create root filesystem
            $fsObj->createFS('subject', $this->id, 0, 0, $this->data['Subject']['name']);
            $rootFS = $fsObj->id;

            //Create users upload root dir
            $fsObj->addFolder($rootFS, __('Users uploads'), false); //TODO: use Teacher language/subject language
            $usersUploadRoot = $fsObj->id;

            //$this->set(array('root_file_system_id'=>$rootFS, 'user_upload_root_file_system_id'=>$usersUploadRoot));
            if(!$this->updateAll(array('root_file_system_id'=>$rootFS, 'user_upload_root_file_system_id'=>$usersUploadRoot), array($this->primaryKey=>$this->id))) {
                return false;
            }
        }

        if( isSet($this->data['Subject']['name']) ||
            isSet($this->data['Subject']['description']) ||
            isSet($this->data['Subject']['language']) ||
            isSet($this->data['Subject']['lesson_type']) ||
            isSet($this->data['Subject']['average_rating']) ||
            isSet($this->data['Subject']['is_public']) ||
            isSet($this->data['Subject']['1_on_1_price']) ||
            isSet($this->data['Subject']['category_id']) ||
            (isSet($this->data['Subject']['creation_stage']) &&
                $this->data['Subject']['creation_stage'] == CREATION_STAGE_PUBLISH
            )) {


            //Find the subject
            $this->recursive = -1;
            $subjectData = $this->findBySubjectId($this->id);
            $subjectData = $subjectData['Subject'];

            //Only if creation stage is final - add to solr
            if($subjectData['creation_stage']!=CREATION_STAGE_PUBLISH) {
                return true;
            }


            //TODO: add user location, max_students and total_group_price


            $update['subject_id']               = $subjectData['subject_id'];
            $update['language']                 = $subjectData['language'];
            $update['name']                     = $subjectData['name'];
            $update[$update['language'].'_t']   = $subjectData['description'];
            $update['1_on_1_price']             = $subjectData['1_on_1_price'];
            $update['lesson_type']              = intval($subjectData['lesson_type']);
            $update['average_rating']           = $subjectData['average_rating'];
            $update['is_public']                = (boolean) $subjectData['is_public'];
            $update['last_modified']            = $subjectData['modified'] ? $subjectData['modified'] : $subjectData['created'];

            if($subjectData['category_id'] && !empty($subjectData['category_id'])) {
                App::import('Model', 'Category');
                $cObj = new Category();
                $update['categories']   = $cObj->getPathHierarchy($subjectData['category_id'], true);
                $update['category_id']  = $subjectData['category_id'];
            } else {
                unset($update['categories'], $update['category_id']);
            }

            App::import('Vendor', 'Solr');
            $SolrObj = new Solr($this->solrCore);
            if(!$SolrObj->addDocument($update)) {
                return false;
            }
        }




        return true;
    }

    public static function calcStudentPriceAfterDiscount( $onOnOnePrice, $maxStudents, $currentStudents, $fullGroupStudentPrice ) {
        if($currentStudents<=1 || $maxStudents<=0) {
            return $onOnOnePrice;
        }

        $maxDiscount = $onOnOnePrice-$fullGroupStudentPrice;
        return ($onOnOnePrice - $maxDiscount*$currentStudents/$maxStudents);
    }
	
	public static function calcStudentFullGroupPrice( $onOnOnePrice, $totalGroupPrice, $maxStudents, $currentStudents ) {
		return ($onOnOnePrice+(($totalGroupPrice-$onOnOnePrice)/($maxStudents-1))*($currentStudents-1))/$currentStudents;
	}

	
	/**
	 * 
	 * Get ratings that was submited by the students on this subject
     * @param $subjectId
     * @param int $limit
     * @param int $page
     * @return array
     */
    public function getRatingByStudents( $subjectId, $limit=12, $page=1 ) {
		App::import('Model', 'UserLesson');
		$ulObj = new UserLesson();
		$conditions = array('UserLesson.subject_id'=>$subjectId, 'stage'=>array(USER_LESSON_PENDING_TEACHER_RATING, USER_LESSON_DONE));

        $ulObj->recursive = 2;
        $ulObj->unbindAll(array('belongsTo'=>array('Student')));
        $ulObj->Student->unbindAll();

		return $ulObj->find('all', array(	'conditions'=>$conditions,
														'fields'=>array('student_user_id', 'rating_by_student', 'comment_by_student', 'datetime'),
														'limit'=>$limit,
														'page'=>$page));
	}

	public function getOffersByTeacher($teacherUserId, $isOwner=true, $lessonType=null, $page=null, $limit=null, $categoryId=null, $excludeSubject=null ) {
		$conditions = array('user_id'=>$teacherUserId);
		if(!is_null($excludeSubject)) {
			$conditions['subject_id !='] = $excludeSubject;
		}
		if(!is_null($categoryId)) {
			App::import('Model', 'Category');
			$cObj = new Category();
			$category = $cObj->findByCategoryId($categoryId);
			
			if($category && !empty($category['path'])) {
                $conditions['category_id'] = explode(',', $category['path']);
			}
		}
        $conditions['is_enable'] = SUBJECT_IS_ENABLE_TRUE;
		if(!$isOwner) {
			$conditions['is_public'] = SUBJECT_IS_PUBLIC_TRUE;
		}
        if($lessonType) {
            $conditions['lesson_type'] = $lessonType;
        }

        $allConditions = array('conditions'=>$conditions);
        if($page) {
            $allConditions['page'] = $page;
        }
        if($limit) {
            $allConditions['limit'] = $limit;
        }

		return $this->find('all', $allConditions);
	}

	
	public function disable($subjectId) {
		//TODO: check for active waiting teacher lessons, if so - stop
		//Close all invitations, teacher lessons, files tests etc.

        //Disable on Solr
        parent::disable($subjectId);


        //Set disable on DB
        $this->id = $subjectId;
        $this->set(array('is_enable'=>0));
		return $this->save();
	}
	
	public function getNewest($isOwner=true, $limit=4, $page=1) {
		$conditions = array( );
		if(!$isOwner) {
			$conditions['is_enable'] = SUBJECT_IS_ENABLE_TRUE;
			$conditions['is_public'] = SUBJECT_IS_PUBLIC_TRUE;
		}

        $this->bindTeacherOnLessonOffer();

		return $this->find('all', array('conditions'=>$conditions, 
										//'order'=>'created', //It will get done by default order
										'limit'=>$limit,
										'page'=>$page
        ));
	}

    private function bindTeacherOnLessonOffer() {
        $this->bindModel(array('belongsTo'=>array(
                'Teacher' => array(
                    'className' => 'User',
                    'foreignKey'=>'user_id',
                    'fields'=>array('username', 'image_source'/*, 'last_name', 'image', 'student_average_rating', 'student_total_lessons'*/))
            )
            )
        );
    }

    public function beforeSearchBind() {
        $this->bindTeacherOnLessonOffer();
    }
	public function bindStudentOnLessonRequest() {
		$this->bindModel(array('belongsTo'=>array(
											'Student' => array(
												'className' => 'User',
												'foreignKey'=>'user_id',
												'fields'=>array('first_name', 'last_name', 'username', 'image', 'image_source', 'student_average_rating', 'student_total_lessons'))
											)
								)
						);
	}
	
	public function setRating( $subjectId, $rating ) {
/*
        value=CASE
WHEN value+1>100 THEN 100
ELSE value+1 END
WHERE value_enabled<>0;
  */
		$update = array(
            'average_rating'=>$this->getDataSource()->expression('CASE WHEN raters_amount=0 THEN '.$rating.'
                                                                    ELSE ((raters_amount*average_rating)+'.$rating.')/(raters_amount+1) END'),
			//'average_rating'=>$this->getDataSource()->expression('((raters_amount*average_rating)+'.$rating.')/(raters_amount+1)'),
			'raters_amount'	=>$this->getDataSource()->expression('raters_amount +1')
		);


		$this->id = $subjectId;
		$this->set($update);
		return $this->save();
	}
	
	/*public function datetimeToStr( $datetime, $addMinutes=null ) {
        if($addMinutes) {
            if(!is_numeric($datetime)) {
                $datetime = strtotime($datetime);
            }
            $datetime += $addMinutes*MINUTE;
        }

		if(is_numeric($datetime)) {
			$datetime = date('Y-m-d H:i:s', $datetime);
		}


		return $datetime;
	}*/


    /*public function getFileSystem($subjectId, $inPath=null) {
        $this->recursive = -1;
        $subjectData = $this->findBySubjectId($subjectId);
        App::import('Model', 'FileSystem');
        $fsObj = new FileSystem();
        return $fsObj->getFS($subjectData['Subject']['root_file_system_id']);

    }*/

    public function getTests($subjectId) {
        //Get subject tests
        App::import('Model', 'StudentTest');
        $testObj = new StudentTest();
        return $testObj->getTests('subject', $subjectId);
    }

    public function getUserRelationToSubject($subjectId, $userId) {
        $this->recursive = -1;
        $subjectData = $this->findBySubjectId($subjectId);

        if(!$subjectData) {
            return false;
        }

        if($subjectData['Subject']['user_id']==$userId) {
            return 'teacher';
        }

        //Check if the user learned this lesson
        App::import('Model', 'userLesson');
        $ulObj = new userLesson();
        $ulObj->recursive = -1;

        $userLessonData = $ulObj->find('first', array('conditions'=>array(
            'student_user_id'=>$userId,
            'stage'=>array(USER_LESSON_ACCEPTED, USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_TEACHER_RATING,
                            USER_LESSON_PENDING_STUDENT_RATING, USER_LESSON_DONE)
        )));

        if($userLessonData) {
            return 'student';
        }

        return false;
    }


}

?>