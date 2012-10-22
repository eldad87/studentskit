<?php

class WatchitooUser extends AppModel {
	public $name 		= 'WatchitooUser';
	public $useTable 	= 'watchitoo_users';
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