<?php
class SubjectCategory extends AppModel {
	public $name = 'SubjectCategory';
	public $useTable = 'subject_categories';
	public $primaryKey = 'subject_category_id';
    public $actsAs = array('Pathable' /*=> array(
        'parent_field'  => 'parent_subject_category_id',
        'right' => 'right_node'
    )*/);

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        static $eventListenterAttached = false;

        if(!$eventListenterAttached) {
            //Connect the event manager of this model
            App::import( 'Event', 'ForumEventListener');
            $fel = new ForumEventListener();
            CakeEventManager::instance()->attach($fel);
            $eventListenterAttached = true;
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

    /*public function getPathHierarchy($sc_id, $fullPath=true) {
        if($sc_id==0) {
            if($fullPath) {
                return array();
            }

            return null;
        }


        $this->recursive = -1;
        $data = $this->findBySubjectCategoryId($sc_id);
        if(!$data) {
            return array();
        }
        $data = $data['SubjectCategory'];

        if($fullPath) {
            $return = array();
            for($deep=1; $deep<=$data['deep']; $deep++) {
                $hierarchy = $deep;
                if($data['path']) {
                    $path = explode(',', $data['path']);
                    $path = array_slice($path, 0, $deep);
                    $hierarchy .= ','.implode(',', $path);
                }
                if($deep==$data['deep']) {
                    $hierarchy .= ','.$sc_id;
                }

                $return[] = $hierarchy;
            }

            return $return;

        } else {
            $hierarchy = $data['deep']; //In order to get all children
            if($data['path']) {
                $hierarchy .= ','.$data['path'];
            }
            $hierarchy .= ','.$sc_id;

            return $hierarchy;
        }
    }*/

    public function afterSave($created) {
        parent::afterSave($created);


        $event = new CakeEvent('Model.SubjectCategory.afterSave', $this, array('subject_category_id'=>$this->id, 'created'=>$created) );
        $this->getEventManager()->dispatch($event);

    }

    /*public function beforeSave() {
        parent::beforeSave();

        //Prepare path/deep/parent_subject_category_id
        if( !isSet($this->data['SubjectCategory']['parent_subject_category_id']) ) {
            if(!$this->id) {
                //Create new record, no parent id so set default
                $this->data['SubjectCategory']['deep'] = 1;
                $this->data['SubjectCategory']['path'] = null;
                $this->data['SubjectCategory']['parent_subject_category_id'] = 0;
            }
        } else if(!$this->data['SubjectCategory']['parent_subject_category_id']) {
            //if($this->id) - user change parent, else its a new main category
            $this->data['SubjectCategory']['deep'] = 1;
            $this->data['SubjectCategory']['path'] = null;
            $this->data['SubjectCategory']['parent_subject_category_id'] = 0;
        } else {
            $parentData = $this->findBySubjectCategoryId($this->data['SubjectCategory']['parent_subject_category_id']);
            $parentData = $parentData['SubjectCategory'];

            $this->data['SubjectCategory']['deep'] = $parentData['deep']+1;

            $parentPath = $parentData['path'] ? explode(',', $parentData['path']) : array();
            $parentPath[] = $parentData['subject_category_id'];
            $this->data['SubjectCategory']['path'] = implode(',', $parentPath);
        }

        return true;
    }*/

    public function afterDelete(){
        parent::afterDelete();
        //TODO: delete all sub-categories
        //TODO: move all subjects that belong to the sub-categories or current-category - to the parent-category
    }

}
?>