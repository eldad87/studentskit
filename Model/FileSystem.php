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
                                ),
                            ),
                        );

    public function getFS($entityType, $entityId) {

        $this->recursive = -1;
        $fsFromDB = $this->find('threaded', array('conditions'=>array('entity_type'=>$entityType, 'entity_id'=>$entityId)));


        return $this->fixThreaded($fsFromDB);
    }

    /**
     * Fix threaded array, return better format data
        Array
        (
            [0] => Array
            (
                [file_system_id] => 1
                [type] => folder
                [name] => Main folder
                [size_kb] =>
                [extension] =>
                [children] => Array
                    (
                    [0] => Array
                    (
                        [file_system_id] => 2
                        [type] => folder
                        [name] => Sub 1
                        [size_kb] =>
                        [extension] =>
                    )

                    [1] => Array
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

    public function addFolder($entityType, $entityId, $name, $parentId=0) {
        /*if($parentId) {
            //Verify that the parent is a folder
            if(!$this->validateOwnership($parentId, $entityType, $entityId, 'folder')) {
                return false;
            }
        }*/

        $this->create(false);
        $this->set(array(
            'entity_type'=>$entityType,
            'entity_id'=>$entityId,
            'parent_id'=>$parentId,
            'name'=>$name,
            'type'=>'folder'
        ));

        return $this->save();
    }

    public function rename($fileSystemId, $name/*, $entityType=null, $entityId=null*/) {
        /*if($entityId && $entityType) {
            //Validate ownership
            if(!$this->validateOwnership($fileSystemId, $entityType, $entityId)) {
                return false;
            }
        }*/
        $this->create(false);
        $this->id = $fileSystemId;
        $this->set(array('name'=>$name));
        return $this->save();
    }

    //TODO:
    //folders - remove recursive.
    //files - remove from storage
    public function remove($fileSystemId/*, $entityId=null, $entityType=null*/) {
        /*if($entityId && $entityType) {
            //Validate ownership
            if(!$this->validateOwnership($fileSystemId, $entityType, $entityId)) {
                return false;
            }
        }*/

        $this->id = $fileSystemId;
        return $this->delete();
    }

    //TODO:
    public function addFile($entityType, $entityId, $name, $file, $parentId=0) {

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