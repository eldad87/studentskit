<?php

class WatchitooSubjectTeacher extends AppModel {
	public $name 		= 'WatchitooSubjectTeacher';
	public $useTable 	= 'watchitoo_subject_teachers';
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