<?php
App::import('Model', 'Subject');
class WishList extends SolrSearch {
    protected $solrCore = SUBJECT_TYPE_REQUEST;

    public $name = 'WishList';
    public $useTable = 'wish_list';
    public $primaryKey = 'wish_list_id';
    public $actsAs = array(
        'LanguageFilter',
        'Lesson',
        'Uploader.Attachment' => array(
            'image_source' => array(
                'finalPath'     => 'img/wish_list/',
                'nameCallback'  => 'formatImageName',
                'overwrite'     => true,
                'allowEmpty'    => true,
                'transforms'    => array(
                    'image_resize'=>array(  'method'=>'resize','width'=> 200,  'height'=>210,  'append'=>'_resize', 'overwrite'=>true,
                        'aspect'=>true, 'mode'=>'height', 'setAsTransformationSource'=>true,    'nameCallback'  => 'formatImageName' ),
                    'image_crop_72x72'  =>array('method'=>'crop', 'width' => 72,   'height'=>72,   'append'=>'_72x72',    'nameCallback'  => 'formatImageName', 'overwrite'     => true ),
                ),
                'transport' => array(
                    'class'     => 's3',
                    'accessKey' => 'AKIAIV2BMVHTLRF64V7Q',
                    'secretKey' => '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',
                    'bucket'    => S3_BUCKET,
                    'region'    => 'us-east-1',
                    'folder'    => 'img/wish_list/'
                )
            ),
        ),
        'Uploader.FileValidation' => array(
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
                'rule'    	=> array('inList', array(LESSON_TYPE_VIDEO, LESSON_TYPE_LIVE, LESSON_TYPE_COURSE) ),//array(LESSON_TYPE_VIDEO, LESSON_TYPE_LIVE)
                'message' 	=> 'Please select a lesson type',
                //'last'      =>true
            )
        ),
        'duration_minutes'=> array(
            'range' 		=> array(
                'required'	=> 'create',
                'allowEmpty'=> false,
                'rule'    	=> array('range', 4, 241),
                'message' 	=> 'Duration must be more then %d minutes and less then %d minutes'
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
        )
    );

    public $belongsTo = array(
        'Student' => array(
            'className' => 'User',
            'foreignKey'=>'student_user_id',
            'fields'    =>array('first_name', 'last_name', 'username', 'image', 'image_source', 'student_avarage_rating', 'student_total_lessons')
        ),
    );

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

        //No need to validate duration for video lessons
        if(isSet($this->data[$this->alias]['lesson_type']) &&  $this->data[$this->alias]['lesson_type']==LESSON_TYPE_VIDEO) {
            $this->validator()->remove('duration_minutes');
        }
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
        $this->virtualFields['id'] = sprintf('%s.%s', $this->alias, $this->primaryKey); //Uploader
    }

    public function searchSuggestions($query) {
        unset($query['fq']['is_public']);
        return parent::searchSuggestions($query);
    }
    public function search($query) {
        unset($query['fq']['is_public']);
        return parent::search($query);
    }

    public function afterSave($created) {
        parent::afterSave($created);

        if( isSet($this->data[$this->alias]['name']) ||
            isSet($this->data[$this->alias]['description']) ||
            isSet($this->data[$this->alias]['language']) ||
            isSet($this->data[$this->alias]['1_on_1_price']) ||
            isSet($this->data[$this->alias]['category_id'])) {


            //Find the subject
            $this->recursive = 1;
            $wishAllData = $this->findByWishListId($this->id);
            $wishData = $wishAllData[$this->alias];

            $update['wish_list_id']             = $wishData['wish_list_id'];
            $update['language']                 = $wishData['language'];
            $update['name']                     = $wishData['name'];
            $update[$update['language'].'_t']   = $wishData['description'];
            $update['1_on_1_price']             = $wishData['1_on_1_price'];
            $update['avarage_rating']           = $wishAllData['Student']['student_avarage_rating'];
            $update['lesson_type']              = intval($wishData['lesson_type']);
            $update['last_modified']            = $wishData['modified'] ? $wishData['modified'] : $wishData['created'];

            if($wishData['category_id'] && !empty($wishData['category_id'])) {
                App::import('Model', 'Category');
                $cObj = new Category();
                $update['categories']   = $cObj->getPathHierarchy($wishData['category_id'], true);
                $update['category_id']  = $wishData['category_id'];
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


    public function getOffersByStudent($userId, $limit=12, $page=1) {
        return $this->find('all', array('conditions'=>array(
                                            'student_user_id'=>$userId,
                                            'is_enable'=>SUBJECT_IS_ENABLE_TRUE
                                        ),
                                        'limit'=>$limit,
                                        'page'=>$page
        ));
    }

    public function getNewest($isOwner=true, $limit=4, $page=1) {
        $conditions = array( );
        if(!$isOwner) {
            $conditions['is_enable'] = SUBJECT_IS_ENABLE_TRUE;
        }

        return $this->find('all', array('conditions'=>$conditions,
            //'order'=>'created', //It will get done by default order
            'limit'=>$limit,
            'page'=>$page
        ));
    }

    public function disable($subjectId) {
        //TODO: Close all invitations

        //Disable on Solr
        parent::disable($subjectId);


        //Set disable on DB
        $this->id = $subjectId;
        $this->set(array('is_enable'=>0));
        return $this->save();
    }


    protected function beforeSearchBind() {
    }
}