<?php
define('LESSON_TYPE_VIDEO', 1);
define('LESSON_TYPE_LIVE', 2);


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
	public $validate = array(


		'name'=> array(
			'between' => array(
            	'required'	=> 'create',
				'allowEmpty'=> false,
                'rule'    	=> array('between', 2, 45),
				'message' 	=> 'Between 2 to 45 characters',
                'last'      =>true
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
        'language'=> array(
            'inList' => array(
                'required'	=> 'create',
                'allowEmpty'=> false,
                'rule'    	=> array('inList', array('en', 'he', 'pl', 'fr')), //'he'=>'he', 'en'=>'en', 'fr'=>'fr', 'pl'=>'pl'
                'message' 	=> 'Please select a language',
                'last'      =>true
            )
        ),
        'lesson_type'=> array(
            'inList' => array(
                'required'	=> 'create',
                'allowEmpty'=> false,
                'rule'    	=> array('inList', array('1', '2') ),//array(LESSON_TYPE_VIDEO, LESSON_TYPE_LIVE)
                'message' 	=> 'Please select a lesson type',
                'last'      =>true
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


	public function fullGroupTotalPriceCheck( $price ) {
		if(!isSet($this->data['Subject']['max_students'])) {
			$this->invalidate('max_students', 'Please enter a valid max students');
			return false;
		} else  {
			if(	isSet($this->data['Subject']['full_group_total_price']) && !empty($this->data['Subject']['full_group_total_price']) && 
				$this->data['Subject']['max_students'] && $this->data['Subject']['1_on_1_price']) {
				
				//Check if full_group_total_price is MORE then  max_students*1_on_1_price
				$maxAllowed = $this->data['Subject']['max_students']*$this->data['Subject']['1_on_1_price'];
				if($this->data['Subject']['full_group_total_price']>$maxAllowed) {
					$this->invalidate('max_students', 'Group price error, max is '.$maxAllowed.'. (max students * 1 on 1 price)');
				}
			}
		}
		return true;
	}
	public function maxStudentsCheck( $maxStudents ) {
		if($maxStudents['max_students']>1 && (!isSet($this->data['Subject']['full_group_total_price']) || !$this->data['Subject']['full_group_total_price'])) {
			$this->invalidate('full_group_total_price', 'Please enter a valid group price or set Max students to 1');
			return false;
		}
		return true;
	}
	
	public function beforeSave($options=array()) {
		parent::beforeSave($options);
		
		//Calculate full_group_student_price
		if(	isSet($this->data['Subject']['max_students']) && $this->data['Subject']['max_students']>1  && 
			$this->data['Subject']['full_group_total_price'] && !empty($this->data['Subject']['full_group_total_price'])) {
				
			$this->data['Subject']['full_group_student_price'] = $this->calcGroupPrice(	$this->data['Subject']['1_on_1_price'], $this->data['Subject']['full_group_total_price'], 
																							$this->data['Subject']['max_students'], $this->data['Subject']['max_students']); 
		} else {
			unset(	$this->data['Subject']['max_students'], 
					$this->data['Subject']['full_group_total_price'], 
					$this->data['Subject']['full_group_student_price']);
		}

        //TODO: save description as array('lang'=>description); and lang
	}

    public function afterSave($created) {
        if(!$created) {
            return false;
        }

        //Find the subject
        $this->recursive = -1;
        $subjectData = $this->findBySubjectId($this->id);
        $subjectData = $subjectData['Subject'];

        //TODO: add user location


        $update['subject_id']               = $subjectData['subject_id'];
        $update['language']                 = $subjectData['language'];
        $update['name']                     = $subjectData['name'];
        $update[$update['language'].'_t']   = $subjectData['description'];
        $update['1_on_1_price']             = $subjectData['1_on_1_price'];
        $update['lesson_type']              = intval($subjectData['lesson_type']);
        $update['avarage_rating']           = $subjectData['avarage_rating'];
        $update['is_public']                = (boolean) $subjectData['is_public'];

        if($subjectData['subject_category_id']) {
            App::import('Model', 'SubjectCategory');
            $scObj = new SubjectCategory();
            $update['categories']   = $scObj->getHierarchy($subjectData['subject_category_id']);
            $update['category_id']  = $subjectData['subject_category_id'];
        } else {
            $update['categories']   = null;
            $update['category_id']  = null;
        }


        App::import('Vendor', 'Solr');
        $SolrObj = new Solr($subjectData['type']);
        return $SolrObj->addDocument($update);
    }
	
	public function calcGroupPrice( $onOnOnePrice, $totalGroupPrice, $maxStudents, $currentStudents ) {

		return round(($onOnOnePrice+(($totalGroupPrice-$onOnOnePrice)/($maxStudents-1))*($currentStudents-1))/$currentStudents);
	}
	
	public function search( $subjectType=SUBJECT_TYPE_OFFER, $ownerSearch=true, $lang=null, $userId=null, $name=null, $lessonType=null, $categoryId=null, $limit=12, $page=1 ) {
        App::import('Vendor', 'Solr');
        $solrObj = new Solr($subjectType);

        $query = array();
        if($name) {
            $query['search'] = $name;
            $query['search_fields'] = array('name'=>5, 'description'=>0.4);
        }
        if($userId) {

            //array('lang'=>'(EN OR FR)'/*, 'subject_id'=>'(1 OR 4)'*/, 'is_public'=>true)
            $conditions['fq']['user_id'] = intval($userId);
        }
        if($lessonType) {
            $conditions['fq']['lesson_type'] = intval($lessonType);
        }
        if(!$ownerSearch) {
            $conditions['fq']['is_public'] = SUBJECT_IS_ENABLE_TRUE;
            //$conditions['is_enable'] = SUBJECT_IS_PUBLIC_TRUE;
        }
        if($lang) {
            $conditions['fq']['language'] = $lang;
        }

        if($categoryId) {
            App::import('Model', 'SubjectCategory');
            $scObj = new SubjectCategory();
            $hierarchy = $scObj->getHierarchy($categoryId);
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

        //Go over results, generate conditions for finding the matching subjects in our DB.

        /*$conditions = array( 'type'=>$subjectType );
          if($name) {
              $conditions['name LIKE'] = '%'.$name.'%';
          }
          if($userId) {
              $conditions['Subject.user_id'] = $userId;
          }
          if($lessonType) {
              $conditions['lesson_type'] = $lessonType;
          }
          if(!$ownerSearch) {
              $conditions['is_enable'] = SUBJECT_IS_ENABLE_TRUE;
              $conditions['is_public'] = SUBJECT_IS_PUBLIC_TRUE;
          }
          if($categoryId) {
              App::import('Model', 'SubjectCategory');
              $scObj = new SubjectCategory();
              $category = $scObj->findBySubjectCategoryId($categoryId);

              if($category) {
                  $conditions['category BETWEEN ? AND ?'] = array(
                      $category['SubjectCategory']['left'],
                      $category['SubjectCategory']['right']
                  );
              }
          }*/

          if($subjectType==SUBJECT_TYPE_REQUEST) {
              $this->bindStudentOnLessonRequest();
          }
		
		return $this->find('all', array('conditions'=>$conditions));
	}
	
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
	
	public function getbyCatalog($catalogId, $excludeSubjectId=null, $limit=12, $page=1) {
		$conditions = array('catalog_id'=>$catalogId, 'type'=>SUBJECT_TYPE_OFFER, 'is_enable'=>SUBJECT_IS_ENABLE_TRUE, 'is_public'=>SUBJECT_IS_PUBLIC_TRUE);
		if(!is_null($excludeSubjectId)) {
			$conditions['subject_id !='] = $excludeSubjectId;
		}
		
		//TODO: get owner image
		return $this->find('all', array('conditions'=>$conditions,
										'limit'=>$limit,
										'page'=>$page));
	}
	public function getbyTeacher($teacherUserId, $isOwner=true, $type=SUBJECT_TYPE_OFFER, $page=1, $limit=12, $categoryId=null, $excludeSubject=null ) {
		$conditions = array('user_id'=>$teacherUserId, 'type'=>SUBJECT_TYPE_OFFER);
		if(!is_null($excludeSubject)) {
			$conditions['subject_id !='] = $excludeSubject;
		}
		if(!is_null($categoryId)) {
			App::import('Model', 'SubjectCategory');
			$scObj = new SubjectCategory();
			$category = $scObj->findBySubjectCategoryId($categoryId);
			
			if($category) {
				$conditions['category BETWEEN ? AND ?'] = array(
					$category['SubjectCategory']['left'],
					$category['SubjectCategory']['right']
				);
			}
		}
        $conditions['is_enable'] = SUBJECT_IS_ENABLE_TRUE;
		if(!$isOwner) {
			$conditions['is_public'] = SUBJECT_IS_PUBLIC_TRUE;
		}
		
		return $this->find('all', array('conditions'=>$conditions,
										'limit'=>$limit,
										'page'=>$page));
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
										'page'=>$page));
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
	
	public function datetimeToStr( $datetime ) {
		if(is_numeric($datetime)) {
			$datetime = date('Y-m-d H:i:s', $datetime);
		}
		return $datetime;
	}
	
}
?>