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
class Subject extends AppModel {
	public $name = 'Subject';
	public $useTable = 'subjects';
	public $primaryKey = 'subject_id';
    public $actsAs = array('LanguageFilter');
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
                'rule'    	=> array('inList', array('en','he')),
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
		'full_group_total_price'=> array(
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
		),
	);


	public function fullGroupTotalPriceCheck( $price ) {
		if(!isSet($this->data['Subject']['max_students'])) {
			$this->invalidate('max_students', __('Please enter a valid max students'));
			//return false;
		} else  {
			if(	isSet($this->data['Subject']['full_group_total_price']) && !empty($this->data['Subject']['full_group_total_price']) &&
				isSet($this->data['Subject']['max_students']) && $this->data['Subject']['max_students'] &&
                isSet($this->data['Subject']['1_on_1_price']) && $this->data['Subject']['1_on_1_price']) {

				//Check if full_group_total_price is MORE then  max_students*1_on_1_price
				$maxAllowed = $this->data['Subject']['max_students']*$this->data['Subject']['1_on_1_price'];
				if($this->data['Subject']['full_group_total_price']>$maxAllowed) {
					$this->invalidate('max_students', sprintf(__('Group price error, max is %d (max students * 1 on 1 price)', $maxAllowed)));

                    //Check if total group price is LESS then 1 on 1 price (1 on 1 price is NOT 0)
                } else if($this->data['Subject']['full_group_total_price']<=$this->data['Subject']['1_on_1_price']) {
                    $this->invalidate('full_group_total_price', sprintf(__('Full group price must be more the 1 on 1 price (%d)'), $this->data['Subject']['1_on_1_price']) );
                }
			}
		}
		return true;
	}
	public function maxStudentsCheck( $maxStudents ) {
		if($maxStudents['max_students']>1 && (!isSet($this->data['Subject']['full_group_total_price']) || !$this->data['Subject']['full_group_total_price'])) {
			$this->invalidate('full_group_total_price', __('Please enter a valid group price or set Max students to 1'));
			//return false;
		}
		return true;
	}

    public function beforeValidate($options=array()) {
        parent::beforeValidate($options);
        App::import('Model', 'Subject');

        $exists = $this->exists(!empty($this->data['TeacherLesson'][$this->primaryKey]) ? $this->data['Subject'][$this->primaryKey] : null);
        $this->calcFullGroupStudentPriceIfNeeded($this->data['Subject'], $exists );
        $this->extraValidation($this);
    }

	public function beforeSave($options=array()) {
		parent::beforeSave($options);

        //TODO: save description as array('lang'=>description); and lang

        //New record
        if( !$this->id && !isSet($this->data['Subject']['subject_id'])) {
            if(!isSet($this->data['Subject']['image'])) {
                //Without image - Set user image value
                App::import('Model', 'User');
                $userObj = new User();
                $UserData = $userObj->findByUserId($this->data['Subject']['user_id']);
                $this->data['Subject']['image'] = $UserData['User']['image'];
            } else {
                //Just making sure the right flag is set for subject-image
                $this->data['Subject']['image'] = IMAGE_SUBJECT;
            }
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

    public static function calcFullGroupStudentPriceIfNeeded(&$data, $existingRecord) {
        //Calculate full_group_student_price
        if(	isSet($data['max_students']) && $data['max_students']>1  &&
            $data['full_group_total_price'] && !empty($data['full_group_total_price'])) {

            App::import('Model', 'Subject');
            $data['full_group_student_price'] = Subject::calcGroupPrice( $data['1_on_1_price'], $data['full_group_total_price'], $data['max_students'], $data['max_students'] );
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
	
	public static function calcGroupPrice( $onOnOnePrice, $totalGroupPrice, $maxStudents, $currentStudents ) {

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