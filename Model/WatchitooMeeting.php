<?php

class WatchitooMeeting extends AppModel {
	public $name 		= 'WatchitooMeeting';
	public $useTable 	= 'watchitoo_meetings';
	public $primaryKey 	= 'watchitoo_meeting_id';
    //public $actsAs = array('LanguageFilter', 'Time', 'Lock');
    public $belongsTo = array(
        'TeacherLesson' => array(
            'className' => 'TeacherLesson',
            'foreignKey'=>'teacher_lesson_id',
        )
    );

}
?>