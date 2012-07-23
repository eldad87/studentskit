<?php
define('IMAGE_NONE', 0);
define('IMAGE_SUBJECT_OWNER', 1);
define('IMAGE_SUBJECT', 2);

class Profile extends AppModel {
	public $name = 'Profile';
	public $useTable = 'profile';
	public $primaryKey = 'user_id';
	
	
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule'	=> 'numeric',
				'required'		=> true,
				'allowEmpty' 	=> false,
				'message' => 'This field contain numeric value'
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


    public function afterSave($created) {
        parent::afterSave($created);
        if(!$created) {
            return false;
        }

        //check if image was updated
        if(isSet($this->data['Subject']['image']) && $this->data['Subject']['image']) {
            //update subjects with default user image
            App::import('Model', 'Subject');
            $subjectObj = new Subject();
            $subjectObj->updateAll(array('image'=>IMAGE_SUBJECT_OWNER), array('image'=>IMAGE_NONE, 'user_id'=>$this->id));
        }
    }
}
?>