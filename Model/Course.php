<?php
App::import('Model', 'AppModel');
App::import('Model', 'User'); //for IMAGE_SUBJECT
class Course extends AppModel {
    public $name = 'Course';
    public $useTable = 'courses';
    public $primaryKey = 'course_id';
    public $actsAs = array(
        'Lock',
        'Lesson',
        'LanguageFilter',
        'Uploader.Attachment' => array(
            'image_source' => array(
                'finalPath'     => 'img/courses/',
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
                    'folder'    => 'img/courses/'
                )
            ),
            'video_source'=>array(
                'finalPath'	            => 'vid/courses/about_videos/',
                'appendNameToUploadDir' => true,
                'name'                  => 'formatFileName',
                'allowEmpty'            => true,
                'transport' => array(
                    'class'     => 's3',
                    'accessKey' => 'AKIAIV2BMVHTLRF64V7Q',
                    'secretKey' => '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',
                    'bucket'    => S3_BUCKET,
                    'region'    => 'us-east-1',
                    'folder'    => 'vid/courses/about_videos/'
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
                'message' 	=> 'Enter a valid group price'
            ),
            'full_group_student_price' 	=> array(
                'allowEmpty'=> true,
                'rule'    	=> 'fullGroupStudentPriceCheck',
                'message' 	=> 'You must set a student full group price'
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
        static $eventListenerAttached = false;

        if(!$eventListenerAttached) {
            //Connect the event manager of this model
            App::import( 'Event', 'CourseEventListener');
            $cel =& CourseEventListener::getInstance();
            CakeEventManager::instance()->attach($cel);
            $eventListenerAttached = true;
        }

        $this->virtualFields['id'] = sprintf('%s.%s', $this->alias, $this->primaryKey); //Uploader
    }


    public function beforeValidate($options=array()) {
        parent::beforeValidate($options);

        //Calc full-group-price in case that we have 1-or-more live lessons
        /*App::import('Model', 'Subject');
        $exists = $this->exists(!empty($this->data[$this->alias][$this->primaryKey]) ? $this->data[$this->alias][$this->primaryKey] : null);
        Subject::calcFullGroupPriceIfNeeded($this->data[$this->alias], $exists );*/
    }


    public function afterSave($created) {
        parent::afterSave($created);

        if( isSet($this->data[$this->alias]['name']) ||
            isSet($this->data[$this->alias]['description']) ||
            isSet($this->data[$this->alias]['language']) ||
            isSet($this->data[$this->alias]['lesson_type']) ||
            isSet($this->data[$this->alias]['average_rating']) ||
            isSet($this->data[$this->alias]['is_public']) ||
            isSet($this->data[$this->alias]['1_on_1_price']) ||
            isSet($this->data[$this->alias]['category_id'])) {


            //Find the subject
            $this->recursive = -1;
            $courseData = $this->findByCourseId($this->id);
            $courseData = $courseData[$this->alias];


            $update['subject_id']               = $courseData['subject_id'];
            $update['language']                 = $courseData['language'];
            $update['name']                     = $courseData['name'];
            $update[$update['language'].'_t']   = $courseData['description'];
            $update['1_on_1_price']             = $courseData['1_on_1_price'];
            $update['lesson_type']              = intval($courseData['lesson_type']);
            $update['average_rating']           = $courseData['average_rating'];
            $update['is_public']                = (boolean) $courseData['is_public'];
            $update['last_modified']            = $courseData['modified'] ? $courseData['modified'] : $courseData['created'];

            if($courseData['category_id'] && !empty($courseData['category_id'])) {
                App::import('Model', 'Category');
                $cObj = new Category();
                $update['categories']   = $cObj->getPathHierarchy($courseData['category_id'], true);
                $update['category_id']  = $courseData['category_id'];
            } else {
                unset($update['categories'], $update['category_id']);
            }

            App::import('Vendor', 'Solr');
            $SolrObj = new Solr($courseData['type']);
            if(!$SolrObj->addDocument($update)) {
                return false;
            }
        }

        return true;
    }
    


}