<?php
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
}
?>
