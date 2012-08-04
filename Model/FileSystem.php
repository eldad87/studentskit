<?php
class FileSystem extends AppModel {
	public $name = 'FileSystem';
	public $useTable = 'file_system';
	public $primaryKey = 'file_system_id';
	
	
	public function getFS($entityType, $entityId) {
		$nodes = $this->find('all', array('conditions'=>array('entity_type'=>$entityType, 'entity_id'=>$entityId)));
		
		//Create default FS
		$folders = array(0=>array('file_system_id'=>0, 'parent_file_system_id'=>0, 'name'=>'root')); 
		$files = array();
		
		//Split nodes into folders/files
		foreach($nodes AS $node) {
			$node = $node['FileSystem'];
			$type = $node['type'];
			
			unset($node['entity_type'], $node['entity_id'], $node['type']);
			
			switch($type) {
				case 'file':
					$files[$node['parent_file_system_id']][] 	= $node;
				break;
				case 'folder':
					unset($node['size_kb'], $node['extension']);
					$folders[$node['parent_file_system_id']][] 	= $node;
				break;
			}
		}
		
		return array('folders'=>$files, 'files'=>$files);
	}
	
	public function addFolder($entityType, $entityId, $name, $parentFileSystemId=0) {
		if($parentFileSystemId) {
			//Verifiy that the parent is a folder
			if(!$this->validateOwnership($parentFileSystemId, $entityType, $entityId, 'folder')) {
				return false;
			}
		}
		
		$this->create();
		$this->set(array(
			'entity_type'=>$entityType,
			'entity_id'=>$entityId,
			'parent_file_system_id'=>$parentFileSystemId,
			'name'=>$name,
			'type'=>'folder'
		));
	}
	
	public function rename($fileSystemId, $name, $entityType=null, $entityId=null) {
		if($entityId && $entityType) {
			//Validate ownership
			if(!$this->validateOwnership($fileSystemId, $entityType, $entityId)) {
				return false;
			}
		}
		$this->create();
		$this->id = $fileSystemId;
		$this->set(array('name'=>$name));
		return $this->save();
	}
	
	//TODO:
	//folders - remove recursive.
	//files - remove from storage
	public function remove($fileSystemId, $entityId=null, $entityType=null) {
		if($entityId && $entityType) {
			//Validate ownership
			if(!$this->validateOwnership($fileSystemId, $entityType, $entityId)) {
				return false;
			}
		}
		
	}
	
	//TODO:
	public function addFile($entityType, $entityId, $name, $file, $parentFileSystemId=0) {
		
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