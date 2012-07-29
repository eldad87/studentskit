<?php
class PathableBehavior extends ModelBehavior {


    public function setup(Model $model, $config = array()) {
        $settings = array(
            'parent_field'=>'parent_'.$model->primaryKey,
            'deep_field'=>'deep',
            'path_field'=>'path'
        );

        $this->settings[$model->name] = array_merge($settings, $config);

    }

    public function beforeSave(Model $model) {
        parent::beforeSave($model);

        //Prepare path/deep/parent_subject_category_id
        if( !isSet($model->data[$model->name][$this->settings[$model->name]['parent_field']]) ) {
            if(!$model->id) {
                //Create new record, no parent id so set default
                $model->data[$model->name][$this->settings[$model->name]['deep_field']] = 1;
                $model->data[$model->name][$this->settings[$model->name]['path_field']] = null;
                $model->data[$model->name][$this->settings[$model->name]['parent_field']] = 0;
            }
        } else if(!$model->data[$model->name][$this->settings[$model->name]['parent_field']]) {
            //if($this->id) - user change parent, else its a new main category
            $model->data[$model->name][$this->settings[$model->name]['deep_field']] = 1;
            $model->data[$model->name][$this->settings[$model->name]['path_field']] = null;
            $model->data[$model->name][$this->settings[$model->name]['parent_field']] = 0;
        } else {
            $parentData = $model->find('first', array('conditions'=>array( $model->primaryKey=> $model->data[$model->name][$this->settings[$model->name]['parent_field']])));
            $parentData = $parentData[$model->name];

            $model->data[$model->name][$this->settings[$model->name]['deep_field']] = $parentData[$this->settings[$model->name]['deep_field']]+1;

            $parentPath = $parentData[$this->settings[$model->name]['path_field']] ? explode(',', $parentData[$this->settings[$model->name]['path_field']]) : array();
            $parentPath[] = $parentData[$model->primaryKey];
            $model->data[$model->name][$this->settings[$model->name]['path_field']] = implode(',', $parentPath);
        }

        return true;
    }

    public function getPathHierarchy(Model $model, $id, $fullPath=true) {
        if($id==0) {
            if($fullPath) {
                return array();
            }

            return null;
        }


        $model->recursive = -1;
        $data = $model->find('first', array('conditions'=>array( $model->primaryKey=> $id )));
        if(!$data) {
            return array();
        }
        $data = $data[$model->name];

        if($fullPath) {
            $return = array();
            for($deep=1; $deep<=$data[$this->settings[$model->name]['deep_field']]; $deep++) {
                $hierarchy = $deep;
                if($data[$this->settings[$model->name]['path_field']]) {
                    $path = explode(',', $data[$this->settings[$model->name]['path_field']]);
                    $path = array_slice($path, 0, $deep);
                    $hierarchy .= ','.implode(',', $path);
                }
                if($deep==$data[$this->settings[$model->name]['deep_field']]) {
                    $hierarchy .= ','.$id;
                }

                $return[] = $hierarchy;
            }

            return $return;

        } else {
            $hierarchy = $data[$this->settings[$model->name]['deep_field']]; //In order to get all children
            if($data[$this->settings[$model->name]['path_field']]) {
                $hierarchy .= ','.$data[$this->settings[$model->name]['path_field']];
            }
            $hierarchy .= ','.$id;

            return $hierarchy;
        }
    }
}
