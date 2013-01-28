<?php
class FileSystem extends AppModel {
    public $name = 'FileSystem';
    public $useTable = 'file_system';
    public $primaryKey = 'file_system_id';

    public $actsAs = array('Tree',
                            'Uploader.Attachment' => array(
                                'fileUpload'=>array(
                                    'uploadDir'	            => 'file_system/',
                                    'dbColumn'              => 'file_source',
                                    's3' => array(
                                        'accessKey' => 'AKIAIV2BMVHTLRF64V7Q',
                                        'secretKey' => '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',
                                        'bucket'    => 'local_eldad',
                                        'ssl'       => false,
                                        'path'      => 'fs'
                                    )
                                ),

                            ),
                        );

    public function beforeSave($options = array()) {
        parent::beforeSave($options);

        //New record
        if(!$this->isExists()) {
            //Fill in inherited data if missing on NEW records

            $parentId = $this->data[$this->alias]['parent_id'];
            $this->recursive = -1;
            $parentData = $this->findByFileSystemId($parentId);

            //Fill entity_type && entity_id
            if(!isSet($this->data[$this->alias]['entity_type']) && !isSet($this->data[$this->alias]['entity_id'])) {
                $this->data[$this->alias]['entity_type'] = $parentData[$this->alias]['entity_type'];
                $this->data[$this->alias]['entity_id'] = $parentData[$this->alias]['entity_id'];
            }

            //Fill permission
            if(!isSet($this->data[$this->alias]['permission'])) {
                $this->data[$this->alias]['permission'] = $parentData[$this->alias]['permission'];
            }
        }

    }

    /*public function getFSBySubjectId($subjectId) {
        App::import('Model', 'Subject');
        $subjectObj = new Subject();

        $subjectObj->recursive = -1;
        $subjectData = $this->Subject->find('first', array('conditions'=>array('subject_id'=>$subjectId)));

        return $this->getFS( $subjectData['Subject']['root_file_system_id'] );
    }*/

    public function getFS($fileSystemId) {

        //Find root
        $this->recursive = -1;
        $fsData = $this->findByFileSystemId($fileSystemId);

        //Find children
        $this->recursive = -1;
        $fsFromDB = $this->find('threaded', array('conditions'=>array('entity_type'=>$fsData['FileSystem']['entity_type'], 'entity_id'=>$fsData['FileSystem']['entity_id'])));

        $fsFromDB = $this->fixThreaded($fsFromDB);

        //Find the wanted path in tree
        if($fileSystemId) {
            //Find path to the requested node
            $path = $this->getPath($fileSystemId, array('file_system_id', 'parent_id'));

            //Remove the last record - which is the note the user is looking for
            $lastNode = array_pop($path);

            //Drill in tree
            foreach($path AS $node) {
                $fsFromDB = $fsFromDB[$node['FileSystem']['file_system_id']]['children'];
            }

            //Append last node
            $fsFromDB = array($lastNode['FileSystem']['file_system_id']=>$fsFromDB[$lastNode['FileSystem']['file_system_id']]);
        }

        return $fsFromDB;
    }

    /**
     * Fix threaded array, return better format data
        Array
        (
            [1] => Array
            (
                [file_system_id] => 1
                [type] => folder
                [name] => Main folder
                [size_kb] =>
                [extension] =>
                [children] => Array
                    (
                    [2] => Array
                    (
                        [file_system_id] => 2
                        [type] => folder
                        [name] => Sub 1
                        [size_kb] =>
                        [extension] =>
                    )

                    [3] => Array
                    (
                        [file_system_id] => 3
                        [type] => folder
                        [name] => Sub 2
                        [size_kb] =>
                        [extension] =>
                    )
            )
        )
     *
     * @param $fs
     * @return array
     */
    private function fixThreaded($fs) {
        $return = array();

        foreach($fs AS $f) {

            unset($f['FileSystem']['parent_id'], $f['FileSystem']['lft'], $f['FileSystem']['rght'],
                    $f['FileSystem']['entity_id'], $f['FileSystem']['entity_type']);

            $return[$f['FileSystem']['file_system_id']]  = $f['FileSystem'];


            if($f['children']) {
                $return[$f['FileSystem']['file_system_id']]['children'] = $this->fixThreaded($f['children']);
            }

        }

        return $return;
    }

    /**
     * Use this ass parent_id for records you want to place in root
     *
     * Create root file system
     * @param $entityType - subject|user_lesson|teacher_lesson
     * @param $entityId
     * @param $permission - 0, all can browse/download, >0, can rename/delete/upload to
     * @param $parentId
     * @param $name
     *
     * @return bool|mixed
     */
    public function createFS($entityType, $entityId, $permission=0, $parentId=0, $name=null) {
        switch(strtolower($entityType)) {
            case 'subject':
                App::import('Model', 'Subject');
                $obj = new Subject();
                break;
            case 'teacher_lesson':
                App::import('Model', 'TeacherLesson');
                $obj = new TeacherLesson();
                break;
            case 'user_lesson':
                App::import('Model', 'UserLesson');
                $obj = new UserLesson();
                break;
        }

        if(!isSet($obj)) {
            return false;
        }


        //Get entity name for root folder
        $obj->recursive = -1;
        $entityData = $obj->find('first', array('conditions'=>array($obj->primaryKey=>$entityId)));
        if(!$entityData) {
            return false;
        }

        //Create root folder
        $this->create(false);
        $this->set(array(
            'entity_type'   =>$entityType,
            'entity_id'     =>$entityId,
            'permission'    =>$permission,
            'parent_id'     =>$parentId,
            'name'          =>$name ? $name : $entityData[$obj->alias]['name'],
            'type'          =>'folder'
        ));

        return $this->save();
    }

    public function addFolder($parentId, $name ) {
        $this->create(false);
        $this->set(array(
            'parent_id' =>$parentId,
            'name'      =>$name,
            'type'      =>'folder'
        ));

        return $this->save();
    }

    public function rename($fileSystemId, $name) {
        $this->create(false);
        $this->id = $fileSystemId;
        $this->set(array('name'=>$name));
        return $this->save();
    }

    public function remove($fileSystemId) {
        $this->id = $fileSystemId;
        return $this->delete();
    }

    private function validateOwnership($fileSystemId, $entityType, $entityId, $type=null)  {
        $conditions = array('entity_type'=>$entityType, 'entity_id'=>$entityId, 'file_system_id'=>$fileSystemId);

        if($type) {
            $conditions['type'] = $type;
        }
        return $this->find('first', array('conditions'=>$conditions)) ? true : false;
    }
}
?>