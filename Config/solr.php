<?php
$config['solr']['config'] = array(
    'timeout'=>10
);
//Dev
App::import('Model', 'Subject');
$config['solr']['servers'][] = array(
    'hostname'=>'localhost',
    'port'=>8080,
    'path'=>'solr',
    'cores'=>array(
        SUBJECT_TYPE_OFFER  => 'offers',
        SUBJECT_TYPE_REQUEST=> 'requests',
        'forum'=> 'forum',
    ),

);
?>