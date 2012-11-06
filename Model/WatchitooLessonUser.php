<?php

class WatchitooLessonUser extends AppModel {
	public $name 		= 'WatchitooLessonUser';
	public $useTable 	= 'watchitoo_lesson_users';
	public $primaryKey 	= 'user_id';
    //public $actsAs = array('LanguageFilter', 'Time', 'Lock');
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey'=>'user_id',
        )
    );

}
?>