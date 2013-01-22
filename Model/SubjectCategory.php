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

    /**
     *
     *
        $categories = array(
            array(
            'name'=>'name', 'locale'=>array(
                                'heb'=>array('name'=>x, 'description'=>'y')
                            ),
                'children'=>array(
                    array('name'=>'name')
                )
            )
        );

     * @param $categories
     * @param null $parentId
     */
    public function addBulk($categories, $parentId=null) {
        foreach($categories AS $category) {

            //Save category
            $this->_add($category, 'eng', null, $parentId);
            $id = $this->id;

            //Save it's locale
            if(isSet($category['locale'])) {
                foreach($category['locale'] AS $locale=>$catData) {
                    $this->_add($catData, $locale, $id);
                }
            }

            //Save it's children
            if(isSet($category['children'])) {
                $this->addBulk($category['children'], $id);
            }
        }
    }
    private function _add($category, $locale='eng', $id=null, $parentId=null) {
        //Save category
        if(!isSet($category['description'])) {
            $category['description'] = $category['name'];
        }
        $this->create(false);
        $this->locale = $locale;
        if($id) {
            $this->id = $id;
        }

        $save = array(  'name'          =>$category['name'],
                        'description'   =>$category['description']
        );
        if($parentId) {
            $save['parent_subject_category_id'] = $parentId;
        }
        return $this->save( $save );
    }

    public function getAllCategoriesOptions() {
        $categories = $this->find('threaded', array('parent'=>'parent_subject_category_id'));
        $return = array();
        $this->_getAllThreaded($return, $categories);
        return $return;
    }
    private function _getAllThreaded(&$return, $categories, $deep=0) {

        $deepStr = '';
        if($deep) {
            $deepStr = ' ';
            for($i=0; $i<$deep; $i++) {
                $deepStr .= '-';
            }
            $deepStr .= '> ';
        }
        foreach($categories AS $category) {
            $return[$category['SubjectCategory']['subject_category_id']] = $deepStr.$category['SubjectCategory']['name'];

            if($category['children']) {
                $this->_getAllThreaded($return, $category['children'], ($deep+2));
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