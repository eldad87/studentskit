<?php
define('LESSON_TYPE_VIDEO', 'video');
define('LESSON_TYPE_LIVE', 'live');

define('LESSON_TYPE_VIDEO_NO_ADS_TIME_SEC', DAY*2);


define('SUBJECT_TYPE_OFFER', 1);
define('SUBJECT_TYPE_REQUEST', 2);

define('SUBJECT_IS_ENABLE_FALSE', 0);
define('SUBJECT_IS_ENABLE_TRUE', 1);

define('SUBJECT_IS_PUBLIC_FALSE', 0);
define('SUBJECT_IS_PUBLIC_TRUE', 1);

App::import('Model', 'AppModel');
App::import('Model', 'User'); //for IMAGE_SUBJECT
class Subject extends AppModel {
	public $name = 'Subject';
	public $useTable = 'subjects';
	public $primaryKey = 'subject_id';
    public $actsAs = array(
        'LanguageFilter',
        'Uploader.Attachment' => array(
            'imageUpload'=>array(
                'uploadDir'	            => 'img/Image/',
                'appendNameToUploadDir' => true,
                'flagColumn'            => array('dbColumn'=>'image', 'value'=>IMAGE_SUBJECT), //Flag DB.table.image with value of IMAGE_SUBJECT
                'name'                  => 'formatImageName',
                'dbColumn'              => 'image_source',
                'transforms' => array(
                    array('method'=>'resize','width'=> 200,  'height'=>200,  'append'=>'_resize',   'overwrite'=>true, 'dbColumn'=>'image_resize', 'aspect'=>true, 'mode'=>Uploader::MODE_HEIGHT, 'setAsTransformationSource'=>true),
                    array('method'=>'crop', 'width' => 60,   'height'=>60,   'append'=>'_60x60',    'overwrite'=>true, 'dbColumn'=>'image_crop_60x60'),
                    array('method'=>'crop', 'width' => 72,   'height'=>72,   'append'=>'_72x72',    'overwrite'=>true, 'dbColumn'=>'image_crop_72x72'),
                    array('method'=>'crop', 'width' => 78,   'height'=>78,   'append'=>'_78x78',    'overwrite'=>true, 'dbColumn'=>'image_crop_78x78'),
                    array('method'=>'crop', 'width' => 149,  'height'=>182,  'append'=>'_149x182',  'overwrite'=>true, 'dbColumn'=>'image_crop_149x182'),
                    array('method'=>'crop', 'width' => 188,  'height'=>197,  'append'=>'_188x197',  'overwrite'=>true, 'dbColumn'=>'image_crop_197x197'),
                )
            )
        ),

        'Uploader.FileValidation' => array(
            'imageUpload' => array(
                'extension'	=> array('gif', 'jpg', 'png', 'jpeg'),
                'filesize'	=> 1048576,
                'minWidth'	=> 200,
                'minHeight'	=> 200,
                'required'	=> false
            )
        )
    );

    function formatImageName($name, $field, $file) {
        return String::uuid();
    }

	public $validate = array(
		'name'=> array(
			'between' => array(
            	'required'	=> 'create',
				'allowEmpty'=> false,
                'rule'    	=> array('between', 2, 45),
				'message' 	=> 'Between %d to %d characters',
                'last'      =>true
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
        'language'=> array(
            'inList' => array(
                'required'	=> 'create',
                'allowEmpty'=> false,
                'rule'    	=> array('inList', array('eng','heb')),
                'message' 	=> 'Please select a language',
                'last'      =>true
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
			'range' 		=> array(
				'required'	=> 'create',
				'allowEmpty'=> true,
				'rule'    	=> array('between', 1, 1024),
				'message' 	=> 'Lesson must have more then %d or less then %d students'
			),
			'max_students' 	=> array(
				'required'	=> 'create',
				'allowEmpty'=> true,
				'rule'    	=> 'maxStudentsCheck',
				'message' 	=> 'You must set group price'
			)
		),
		/*'full_group_total_price'=> array(
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
	);


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

        $exists = $this->exists(!empty($this->data['Subject'][$this->primaryKey]) ? $this->data['Subject'][$this->primaryKey] : $this->id);
        $this->calcFullGroupPriceIfNeeded($this->data['Subject'], $exists );
        $this->extraValidation($this);
    }

	public function beforeSave($options=array()) {
		parent::beforeSave($options);

        $pKey = !empty($this->data[$this->name][$this->primaryKey]) ? $this->data[$this->name][$this->primaryKey] : $this->id;
        $exists = $this->exists($pKey);
        if($exists) {
            unset($this->data[$this->name]['lesson_type']);
        }

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
                                    'image_crop_60x60'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_60x60'],  $this->useDbConfig).'\'',
                                    'image_crop_72x72'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_72x72'],  $this->useDbConfig).'\'',
                                    'image_crop_149x182'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_149x182'],$this->useDbConfig).'\'',
                                    'image_crop_149x182'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_149x182'],$this->useDbConfig).'\'',
                                    'image_crop_197x197'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_197x197'],$this->useDbConfig).'\''),

                                array($tlObj->name.'.subject_id'=>$pKey));

            //Update subject user lessons
            App::import('Model', 'userLesson');
            $ulObj = new userLesson();
            $ulObj->recursive = -1;
            $ulObj->updateAll(array('image'             =>IMAGE_SUBJECT,
                                    'image_source'      =>'\''.Sanitize::escape($this->data['Subject']['image_source'],      $this->useDbConfig).'\'',

                                    'image_resize'      =>'\''.Sanitize::escape($this->data['Subject']['image_resize'],      $this->useDbConfig).'\'',
                                    'image_crop_60x60'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_60x60'],  $this->useDbConfig).'\'',
                                    'image_crop_72x72'  =>'\''.Sanitize::escape($this->data['Subject']['image_crop_72x72'],  $this->useDbConfig).'\'',
                                    'image_crop_149x182'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_149x182'],$this->useDbConfig).'\'',
                                    'image_crop_149x182'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_149x182'],$this->useDbConfig).'\'',
                                    'image_crop_197x197'=>'\''.Sanitize::escape($this->data['Subject']['image_crop_197x197'],$this->useDbConfig).'\''),

                                    array($ulObj->name.'.subject_id'=>$pKey));
        }

        if(isSet($this->data['Subject']['subject_category_id'])) {
            //bind subject to a forum
            App::import('Model', 'SubjectCategory');
            $scObj = new SubjectCategory();
            $scData = $scObj->findBySubjectCategoryId($this->data['Subject']['subject_category_id']);
            if($scData && $scData['SubjectCategory']['forum_id']) {
                $this->data['Subject']['forum_id'] = $scData['SubjectCategory']['forum_id'];
            }
        }

        return true;
	}

    /*public static function calcFullGroupStudentPriceIfNeeded(&$data, $existingRecord) {
        //Calculate full_group_student_price
        if(	isSet($data['max_students']) && $data['max_students']>1  &&
            $data['full_group_total_price'] && !empty($data['full_group_total_price'])) {

            App::import('Model', 'Subject');
            $data['full_group_student_price'] = Subject::calcStudentFullGroupPrice( $data['1_on_1_price'], $data['full_group_total_price'], $data['max_students'], $data['max_students'] );
        } else {
            unset(	$data['max_students'],
            $data['full_group_total_price'],
            $data['full_group_student_price']);

            if(!$existingRecord) {
                $data['max_students'] = 1;
            }
        }
    }*/
    public static function calcFullGroupPriceIfNeeded(&$data, $existingRecord) {
        //Calculate full_group_student_price
        if(	isSet($data['max_students']) && $data['max_students']>1  &&
            $data['full_group_student_price'] && !empty($data['full_group_student_price'])) {

            App::import('Model', 'Subject');
            //$data['full_group_student_price'] = Subject::calcStudentFullGroupPrice( $data['1_on_1_price'], $data['full_group_total_price'], $data['max_students'], $data['max_students'] );
            $data['full_group_total_price'] = $data['full_group_student_price']*$data['max_students'];
        } else {
            unset(	$data['max_students'],
            $data['full_group_total_price'],
            $data['full_group_student_price']);

            if(!$existingRecord) {
                $data['max_students'] = 1;
            }
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
            unset($objData['full_group_student_price'], $objData['full_group_total_price']);
        }
    }


    public function afterSave($created) {
        parent::afterSave($created);

        if( isSet($this->data['Subject']['name']) ||
            isSet($this->data['Subject']['description']) ||
            isSet($this->data['Subject']['language']) ||
            isSet($this->data['Subject']['lesson_type']) ||
            isSet($this->data['Subject']['avarage_rating']) ||
            isSet($this->data['Subject']['is_public']) ||
            isSet($this->data['Subject']['subject_category_id'])) {


            //Find the subject
            $this->recursive = -1;
            $subjectData = $this->findBySubjectId($this->id);
            $subjectData = $subjectData['Subject'];

            //TODO: add user location, max_students and total_group_price


            $update['subject_id']               = $subjectData['subject_id'];
            $update['language']                 = $subjectData['language'];
            $update['name']                     = $subjectData['name'];
            $update[$update['language'].'_t']   = $subjectData['description'];
            $update['1_on_1_price']             = $subjectData['1_on_1_price'];
            $update['lesson_type']              = intval($subjectData['lesson_type']);
            $update['avarage_rating']           = $subjectData['avarage_rating'];
            $update['is_public']                = (boolean) $subjectData['is_public'];
            $update['last_modified']            = $subjectData['modified'] ? $subjectData['modified'] : $subjectData['created'];

            if($subjectData['subject_category_id'] && !empty($subjectData['subject_category_id'])) {
                var_dump($subjectData['subject_category_id']);
                App::import('Model', 'SubjectCategory');
                $scObj = new SubjectCategory();
                $update['categories']   = $scObj->getPathHierarchy($subjectData['subject_category_id'], true);
                $update['category_id']  = $subjectData['subject_category_id'];
            } else {
                unset($update['categories'], $update['category_id']);
            }

            App::import('Vendor', 'Solr');
            $SolrObj = new Solr($subjectData['type']);
            return $SolrObj->addDocument($update);

        }
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

    public function searchSuggestions($query, $subjectType) {
        App::import('Vendor', 'Solr');
        $solrObj = new Solr($subjectType);
        $query['search_fields'] = false;
        $query = $this->_solrDefaultQueryParams($query);

        return $solrObj->suggest( $query, (($query['page']-1)*$query['limit']), $query['limit'] );
    }

    public function search( $query, $subjectType ) {
        App::import('Vendor', 'Solr');
        $solrObj = new Solr($subjectType);

        $query = $this->_solrDefaultQueryParams($query);
        $results = $solrObj->query( $query, array('subject_id'), (($query['page']-1)*$query['limit']), $query['limit'] );
        if(!$results || !isSet($results->response->numFound) || !$results->response->numFound) {
            return array();
        }

        //Build conditions
        $conditions = array();

        foreach($results->response->docs AS $doc) {
            if(!isSet($conditions['subject_id'])) {
                $conditions['subject_id'] = array();
            }
            $conditions['subject_id'][] = $doc->subject_id;
        }

        if($subjectType==SUBJECT_TYPE_REQUEST) {
            $this->bindStudentOnLessonRequest();
        }

        $return = array();
        $return['subjects'] = $this->find('all', array('conditions'=>$conditions));


        if(isSet($results['facet_counts']['facet_fields'])) {
            $facetName = key($results['facet_counts']['facet_fields']);
            $return['facet']['name'] = $facetName;
            $return['facet']['results'] = (array) $results['facet_counts']['facet_fields'][$facetName];
        }

        return $return;
    }

    private function _solrDefaultQueryParams($query) {
        if(isSet($query['fq']['category_id'])) {
            App::import('Model', 'SubjectCategory');
            $scObj = new SubjectCategory();
            $hierarchy = $scObj->getPathHierarchy($query['fq']['category_id'], false);

            $query['facet'] = array('field'=>'categories', 'mincount'=>1);
            if($hierarchy) {
                //$query['fq']['categories'] = $hierarchy; //Remove all subjects that not related to this category
                $query['fq'][] = '{!raw f=categories}'.$hierarchy;


                $hierarchy = explode(',', $hierarchy);
                $hierarchy[0]++;
                $query['facet']['prefix'] = implode(',', $hierarchy);
            } else {
                $query['facet']['prefix'] = '1,';
            }

            unset($query['fq']['category_id']);


            //$query['fq'][] = '{!raw f=categories}1,2';
        }

        if(isSet($query['search']) && !isSet($query['search_fields'])) {
            $query['search_fields'] = array('name'=>5, 'description'=>0.4);
        }

        if(!isSet($query['page'])) {
            $query['page'] = 1;
        }
        if(!isSet($query['limit'])) {
            $query['limit'] = 12;
        }

        return $query;
    }




	/*public function search( $subjectType=SUBJECT_TYPE_OFFER, $ownerSearch=true, $lang=null, $userId=null, $name=null, $lessonType=null, $categoryId=null, $limit=12, $page=1 ) {
        App::import('Vendor', 'Solr');
        $solrObj = new Solr($subjectType);

        $query = array();
        if(!$name) {
            $query['search'] = '*';
        } else {
            $query['search'] = $name;
            $query['search_fields'] = array('name'=>5, 'description'=>0.4);
        }
        if($userId) {

            //array('lang'=>'(EN OR FR)', 'is_public'=>true)
            $conditions['fq']['user_id'] = intval($userId);
        }
        if($lessonType) {
            $conditions['fq']['lesson_type'] = intval($lessonType);
        }
        if(!$ownerSearch) {
            $conditions['fq']['is_public'] = SUBJECT_IS_PUBLIC_TRUE;
            //$conditions['is_enable'] = SUBJECT_IS_PUBLIC_TRUE;
        }
        if($lang) {
            $conditions['fq']['language'] = $lang;
        }

        if($categoryId) {
            App::import('Model', 'SubjectCategory');
            $scObj = new SubjectCategory();
            $hierarchy = $scObj->getPathHierarchy($categoryId);
            $conditions['facet'] = array('field'=>'categories', 'prefix'=>$hierarchy, 'mincount'=>1);
        }

        $results = $solrObj->query( $query, array('subject_id'), (($page-1)*$limit), $limit );

        if(!$results || !isSet($results->response->numFound) || !$results->response->numFound) {
            return array();
        }

        //Build conditions
        $conditions = array();

        foreach($results->response->docs AS $doc) {
            if(!isSet($conditions['subject_id'])) {
                $conditions['subject_id'] = array();
            }
            $conditions['subject_id'][] = $doc->subject_id;
        }

          if($subjectType==SUBJECT_TYPE_REQUEST) {
              $this->bindStudentOnLessonRequest();
          }
		
		return $this->find('all', array('conditions'=>$conditions));
	}*/
	
	/**
	 * 
	 * Get ratings that was submited by the students on this subject
	 * @param unknown_type $subjectId
	 * @param unknown_type $limit
	 * @param unknown_type $page
	 */
	public function getRatingByStudents( $subjectId, $limit=12, $page=1 ) {
		App::import('Model', 'UserLesson');
		$ulObj = new UserLesson();
		$conditions = array('UserLesson.subject_id'=>$subjectId, 'stage'=>array(USER_LESSON_PENDING_TEACHER_RATING, USER_LESSON_DONE));
		
		return $ulObj->find('all', array(	'conditions'=>$conditions,
														'fields'=>array('student_user_id', 'rating_by_student', 'comment_by_student', 'student_image', 'datetime'),
														'limit'=>$limit,
														'page'=>$page));
	}
	
	/*public function getbyCatalog($catalogId, $excludeSubjectId=null, $limit=12, $page=1) {
		$conditions = array('catalog_id'=>$catalogId, 'type'=>SUBJECT_TYPE_OFFER, 'is_enable'=>SUBJECT_IS_ENABLE_TRUE, 'is_public'=>SUBJECT_IS_PUBLIC_TRUE);
		if(!is_null($excludeSubjectId)) {
			$conditions['subject_id !='] = $excludeSubjectId;
		}
		
		//TODO: get owner image
		return $this->find('all', array('conditions'=>$conditions,
										'limit'=>$limit,
										'page'=>$page));
	}*/
	public function getOffersByTeacher($teacherUserId, $isOwner=true, $lessonType=null, $page=null, $limit=null, $categoryId=null, $excludeSubject=null ) {
		$conditions = array('user_id'=>$teacherUserId, 'type'=>SUBJECT_TYPE_OFFER);
		if(!is_null($excludeSubject)) {
			$conditions['subject_id !='] = $excludeSubject;
		}
		if(!is_null($categoryId)) {
			App::import('Model', 'SubjectCategory');
			$scObj = new SubjectCategory();
			$category = $scObj->findBySubjectCategoryId($categoryId);
			
			if($category && !empty($category['path'])) {
                $conditions['subject_category_id'] = explode(',', $category['path']);
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
    public function getOffersByStudent($userId, $limit=12, $page=1) {
        $this->bindStudentOnLessonRequest();
        return $this->find('all', array('conditions'=>array(
                                                        'Subject.user_id'=>$userId,
                                                        'type'=>SUBJECT_TYPE_REQUEST,
                                                        'is_enable'=>SUBJECT_IS_PUBLIC_TRUE
                                                        ),
                                            'limit'=>$limit,
                                            'page'=>$page
                                        ));
    }
	
	public function disable($subjectId) {
		//TODO: check for active waiting teacher lessons, if so - stop
		//Close all invitations, teacher lessons, files tests etc.


        //Remove from solr
        $subjectData = $this->findBySubjectId($subjectId);
        $subjectData = $subjectData['Subject'];
        App::import('Vendor', 'Solr');
        $solrObj = new Solr($subjectData['type']);
        $solrObj->removeDocumentById($subjectId);


        //Set disable on DB
        $this->id = $subjectId;
        $this->set(array('is_enable'=>0));
		return $this->save();
	}
	
	public function getNewest($isOwner=true, $type=SUBJECT_TYPE_OFFER, $limit=4, $page=1) {
		$conditions = array('type'=>$type );
		if(!$isOwner) {
			$conditions['is_enable'] = SUBJECT_IS_ENABLE_TRUE;
			$conditions['is_public'] = SUBJECT_IS_PUBLIC_TRUE;
		}
		
		if($type==SUBJECT_TYPE_REQUEST) {
			$this->bindStudentOnLessonRequest();
		}
		return $this->find('all', array('conditions'=>$conditions, 
										'order'=>'created', 
										'limit'=>$limit,
										'page'=>$page
        ));
	}
	
	private function bindStudentOnLessonRequest() {
		$this->bindModel(array('belongsTo'=>array(
											'Student' => array(
												'className' => 'User',
												'foreignKey'=>'user_id',
												'fields'=>array('first_name', 'last_name', 'image', 'student_avarage_rating', 'student_total_lessons'))
											)
								)
						);
	}
	
	public function setRating( $subectId, $rating ) {
		$update = array(
			'avarage_rating'=>'((raters_amount*avarage_rating)+'.$rating.')/(raters_amount+1)',
			'raters_amount'	=>'raters_amount+1'
		);
		$this->id = $subectId;
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


    public function getFileSystem($subjectId) {

        App::import('Model', 'FileSystem');
        $fsObj = new FileSystem();
        return $fsObj->getFS('subject', $subjectId);

    }

    public function getTests($subjectId) {
        //Get subject tests
        App::import('Model', 'StudentTest');
        $testObj = new StudentTest();
        return $testObj->getTests('subject', $subjectId);
    }


}
?>