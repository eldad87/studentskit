<?php

class WatchitooLessonMeeting extends AppModel {
	public $name 		= 'WatchitooLessonMeeting';
	public $useTable 	= 'watchitoo_lesson_meetings';
	public $primaryKey 	= 'watchitoo_lesson_meeting_id';
    //public $actsAs = array('LanguageFilter', 'Time', 'Lock');
    public $belongsTo = array(
        'TeacherLesson' => array(
            'className' => 'TeacherLesson',
            'foreignKey'=>'teacher_lesson_id',
        )
    );

}
?>