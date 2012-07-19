<?php
class SubjectCategory extends Model {
	public $name = 'SubjectCategory';
	public $useTable = 'subject_categories';
	public $primaryKey = 'subject_category_id';

    public function getHierarchy($sc_id) {
        $this->recursive = -1;
        $data = $this->findBySubjectCategoryId($sc_id);
        $data = $data['SubjectCategory'];

        $hierarchy = $data['deep'];
        if($data['path']) {
            $hierarchy .= ','.$data['path'];
        }
        $hierarchy .= ','.$sc_id;

        return $hierarchy;
    }

    public function beforeSave() {
        parent::beforeSave();

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
    }

    public function afterDelete(){
        parent::afterDelete();
        $this->id;
        //TODO: delete all sub-categories
        //TODO: move all subjects that belong to the sub-categories or current-category - to the parent-category
    }

}
?>