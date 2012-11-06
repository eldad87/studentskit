<?php

class WatchitooSubjectMeeting extends AppModel {
	public $name 		= 'WatchitooSubjectMeeting';
	public $useTable 	= 'watchitoo_subject_meetings';
	public $primaryKey 	= 'watchitoo_subject_meeting_id';
    //public $actsAs = array('LanguageFilter', 'Time', 'Lock');
    public $belongsTo = array(
        'Subject' => array(
            'className' => 'Subject',
            'foreignKey'=>'subject_id',
        )
    );

}
?>