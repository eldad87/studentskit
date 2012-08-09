<?php
class PathableBehavior extends ModelBehavior {


    public function setup(Model $model, $config = array()) {
        $settings = array(
            'parent_field'=>'parent_'.$model->primaryKey,
            'deep_field'=>'deep',
            'path_field'=>'path'
        );

        $this->settings[$model->alias] = array_merge($settings, $config);

    }

    public function beforeSave(Model $model) {
        parent::beforeSave($model);

        //Prepare path/deep/parent_subject_category_id
        if( !isSet($model->data[$model->alias][$this->settings[$model->alias]['parent_field']]) ) {
            if(!$model->id) {
                //Create new record, no parent id so set default
                $model->data[$model->alias][$this->settings[$model->alias]['deep_field']] = 1;
                $model->data[$model->alias][$this->settings[$model->alias]['path_field']] = null;
                $model->data[$model->alias][$this->settings[$model->alias]['parent_field']] = 0;
            }
        } else if(!$model->data[$model->alias][$this->settings[$model->alias]['parent_field']]) {
            //if($this->id) - user change parent, else its a new main category
            $model->data[$model->alias][$this->settings[$model->alias]['deep_field']] = 1;
            $model->data[$model->alias][$this->settings[$model->alias]['path_field']] = null;
            $model->data[$model->alias][$this->settings[$model->alias]['parent_field']] = 0;
        } else {
            $parentData = $model->find('first', array('conditions'=>array( $model->alias.'.'.$model->primaryKey=> $model->data[$model->alias][$this->settings[$model->alias]['parent_field']])));
            $parentData = $parentData[$model->alias];

            $model->data[$model->alias][$this->settings[$model->alias]['deep_field']] = $parentData[$this->settings[$model->alias]['deep_field']]+1;

            $parentPath = $parentData[$this->settings[$model->alias]['path_field']] ? explode(',', $parentData[$this->settings[$model->alias]['path_field']]) : array();
            $parentPath[] = $parentData[$model->primaryKey];
            $model->data[$model->alias][$this->settings[$model->alias]['path_field']] = implode(',', $parentPath);
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
        $data = $model->find('first', array('conditions'=>array( $model->alias.'.'.$model->primaryKey=> $id )));
        if(!$data) {
            return array();
        }
        $data = $data[$model->alias];

        if($fullPath) {
            $return = array();
            for($deep=1; $deep<=$data[$this->settings[$model->alias]['deep_field']]; $deep++) {
                $hierarchy = $deep;
                if($data[$this->settings[$model->alias]['path_field']]) {
                    $path = explode(',', $data[$this->settings[$model->alias]['path_field']]);
                    $path = array_slice($path, 0, $deep);
                    $hierarchy .= ','.implode(',', $path);
                }
                if($deep==$data[$this->settings[$model->alias]['deep_field']]) {
                    $hierarchy .= ','.$id;
                }

                $return[] = $hierarchy;
            }

            return $return;

        } else {
            $hierarchy = $data[$this->settings[$model->alias]['deep_field']]; //In order to get all children
            if($data[$this->settings[$model->alias]['path_field']]) {
                $hierarchy .= ','.$data[$this->settings[$model->alias]['path_field']];
            }
            $hierarchy .= ','.$id;

            return $hierarchy;
        }
        
    }
    
}
