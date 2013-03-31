<?php
class FileSystem extends AppModel {
    public $name = 'FileSystem';
    public $useTable = 'file_system';
    public $primaryKey = 'file_system_id';

    public $actsAs = array('Tree',
        'Uploader.Attachment' => array(
            'file_source' => array(
                'finalPath'     => 'file_system/',
                'nameCallback'  => 'formatImageName',
                'overwrite'     => true,
                'allowEmpty'    => false,
                'transport' => array(
                    'class'     => 's3',
                    'accessKey' => 'AKIAIV2BMVHTLRF64V7Q',
                    'secretKey' => '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',
                    'bucket'    => S3_BUCKET,
                    'region'    => 'us-east-1',
                    'folder'    => 'file_system/'
                ),
                'metaColumns' => array(
                    'ext'       => 'extension',
                    'size'      => 'size_kb',
                    'basename'  => 'name'
                )
            )
        ),
        'Uploader.FileValidation' => array(
            'file_source' => array(
                'required'	=> true
            )
        )
    );

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['id'] = sprintf('%s.%s', $this->alias, $this->primaryKey); //Uploader
    }

    //Change upload folder
    public function beforeTransport($options) {
        $options['folder'] .= String::uuid() . '/';
        return $options;
    }

    //Remove the "resize-100x100" from transformations file
    public function formatImageName($name, $file) {
        return $this->getUploadedFile()->name();
    }

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

    public function getFS($fileSystemId, $permission=null, $conditions=array()) {

        //Find root
        $this->recursive = -1;
        $fsData = $this->findByFileSystemId($fileSystemId);

        $conditions['entity_type'] = $fsData['FileSystem']['entity_type'];
        $conditions['entity_id'] = $fsData['FileSystem']['entity_id'];

        if($permission) {
            //Make sure that student see only his content/public content
            $conditions['permission'] = array(0, $permission);
        }

        //Find children
        $this->recursive = -1;
        $fsFromDB = $this->find('threaded', array('conditions'=>$conditions));

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
     * @param $permission - 0, all can browse/download, (student user id) >0, can rename/delete/upload to
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
            case 'thread':
                App::import('Model', 'Thread');
                $obj = new Thread();
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
        $defaultName = isSet($entityData[$obj->alias]['name']) ? $entityData[$obj->alias]['name'] : $entityData[$obj->alias]['title'];
        $this->create(false);
        $this->set(array(
            'entity_type'   =>$entityType,
            'entity_id'     =>$entityId,
            'permission'    =>$permission,
            'parent_id'     =>$parentId,
            'name'          =>$name ? $name : $defaultName,
            'type'          =>'folder',
            'deletable'     =>0
        ));

        return $this->save();
    }

    public function isRootFS($fileSystemId) {
        $this->recursive = -1;
        $fsData = $this->findByFileSystemId($fileSystemId);
        if(!$fsData) {
            return false;
        }

        if($fsData['FileSystem']['type']!='folder') {
            return false;
        }


        //Find the entity that his FS belongs to
        $entity = strtolower($fsData['FileSystem']['entity_type']);
        switch($entity) {
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

        $obj->recursive = -1;
        $entityData = $obj->find('first', array('conditions'=>array($obj->primaryKey=>$fsData['FileSystem']['entity_id'])));
        if(!$entityData) {
            return false;
        }

        //Check if this is the -users-upload main folder
        if($entity=='subject') {
            if($entityData[$obj->alias]['user_upload_root_file_system_id']==$fileSystemId) {
                return true;
            }
        }

        //Check if root
        return $entityData[$obj->alias]['root_file_system_id']==$fileSystemId;
    }

    public function addFolder($parentId, $name, $deletable=true ) {
        $this->create(false);
        $this->set(array(
            'parent_id' =>$parentId,
            'name'      =>$name,
            'type'      =>'folder',
            'deletable' => ($deletable ? 1 : 0)
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