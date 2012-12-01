<?php
class SubjectCategory extends AppModel {
	public $name = 'SubjectCategory';
	public $useTable = 'subject_categories';
	public $primaryKey = 'subject_category_id';
    public $actsAs = array('Translate'=>array('name', 'description'), 'Pathable');
    public $translateModel = 'SubjectCategoryi18n';
    public $translateTable = 'subject_categories_i18n';

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        static $eventListenerAttached = false;

        if(!$eventListenerAttached) {
            //Connect the event manager of this model
            App::import( 'Event', 'ForumEventListener');
            $fel = new ForumEventListener();
            CakeEventManager::instance()->attach($fel);
            $eventListenerAttached = true;
        }
    }

    public function getAllCategoriesOptions() {
        $categories = $this->find('threaded', array('parent'=>'parent_subject_category_id'));
        $return = array();
        $this->_getAllThreaded($return, $categories);
        return $return;
    }
    private function _getAllThreaded(&$return, $categories, $deep=0) {
        $deepStr = '';
        for($i=0; $i<$deep; $i++) {
            $deepStr .= '-';
        }
        $deepStr .= '> ';
        foreach($categories AS $category) {
            $return[$category['SubjectCategory']['subject_category_id']] = $deepStr.$category['SubjectCategory']['name'];

            if($category['children']) {
                $this->_getAllThreaded($return, $category['children'], ($deep+1));
            }
        }
    }

    public function afterSave($created) {
        parent::afterSave($created);


        $event = new CakeEvent('Model.SubjectCategory.afterSave', $this, array('subject_category_id'=>$this->id, 'created'=>$created) );
        $this->getEventManager()->dispatch($event);

    }

    public function afterDelete(){
        parent::afterDelete();
        //TODO: delete all sub-categories
        //TODO: move all subjects that belong to the sub-categories or current-category - to the parent-category
    }

}
?>