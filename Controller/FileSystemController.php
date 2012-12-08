<?php
class FileSystemController extends AppController {
	public $name = 'FileSystem';
	public $uses = array('Subject', 'User', 'UserLesson', 'FileSystem');
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')),/* 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');

    public function beforeFilter() {
        parent::beforeFilter();

        $res = $this->_validateFS($this->request['action'], $this->request['pass']);
        if($res!==true) {
            return $res;
        }
    }

    public function fileSystem($entityType, $entityId, $creationStage=null) {
        $subjectId = $entityId;

        //If UserLesson - Load UL data
        if($entityType=='user_lesson') {
            //Load user lesson
            $this->UserLesson->recursive = -1;
            $ulData = $this->UserLesson->find('first', array('conditions'=>array('user_lesson_id'=>$entityId)));
            $ulData = $ulData['UserLesson'];

            $subjectId = $ulData['subject_id'];
        }

        //Find subject data
        $this->Subject->recursive = -1;
        $subjectData = $this->Subject->find('first', array('conditions'=>array('subject_id'=>$subjectId)));
        $subjectData = $subjectData['Subject'];

        //Load subject FS
        $fs = $this->FileSystem->getFS($subjectData['root_file_system_id']);

        /**
         * For UserLesson:
         * root/UserUploadDir/UserFolder/ ->
         *  root/UserFolder/
         */
        //If UserLesson - change FS to UL view
        if($entityType=='user_lesson') {
            //Find user upload folder
            $userFolder = $fs[$subjectData['root_file_system_id']]['children'] //Root
                                [$subjectData['user_upload_root_file_system_id']]['children'] //User upload root
                                    [$ulData['root_file_system_id']]; //User folder

            //Set a new name of the upload folder
            $userFolder['name'] = __('Upload your content here');

            //Remove user upload root
            unset($fs[$subjectData['root_file_system_id']]['children'][$subjectData['user_upload_root_file_system_id']]);

            //Add user upload folder to root
            $fs[$subjectData['root_file_system_id']]['children'] //Root
                [$userFolder['file_system_id']] = $userFolder; //User folder
        }


        $this->set('fileSystem', $fs);
        $this->set('subjectId', $subjectId);
        $this->set('creationStage', $creationStage);

        return $this->success(1);
    }

    public function uploadFile() {

        /**
         * HACK STARTs
         * AttachmentBehavior doesn't know how to handle ajax files.
         * The code below, transform the ajax upload into regular FORM upload and add it to $this->request->data
         */
        $ajaxField = 'fileUpload';

        $name = $_GET[$ajaxField];
        $mime = Uploader::mimeType($name);
        if ($mime) {
            $input = fopen("php://input", "r");
            $temp = tmpfile();

            $this->request->data[$ajaxField] = array(
                'name'      => $name,
                'type'      => $mime,
                'stream'    => true,
                'tmp_name'  => $temp,
                'error'     => 0,
                'size'      => stream_copy_to_stream($input, $temp)
            );

            fclose($input);
        }
        /**
         * HACK ENDs
         */

        App::import('Model', 'FileSystem');
        $fsObj = new FileSystem();

        $this->request->data['type']        = 'file';
        $this->request->data['name']        = $name;
        $this->request->data['size_kb']     = $this->request->data[$ajaxField]['size'];
        $this->request->data['parent_id']   = $this->request->query['path'][count($this->request->query['path'])-1];;

        if(!$fsObj->save( $this->request->data )) {
            echo json_encode(array('success' => false, 'data' => array()));
        } else {
            echo json_encode(array('success' => true, 'data' => array(
               'file_system_id' =>$fsObj->id,
               'name'           =>$name,
               'type'           =>'file',
               'size_kb'        =>$this->request->data[$ajaxField]['size'],
               'extension'      =>Uploader::ext($name),
               'path'           =>isSet($this->request->query['path']) ? $this->request->query['path'] : array()
            )));
        }

        die;
    }

    public function addFolder($parentFileSystemId=0) {
        if(!$this->FileSystem->addFolder( $parentFileSystemId, $this->data['FileSystem']['name'])) {
            return $this->error(4);
        }

        return $this->success(1, array('results'=>array('file_system_id'=>$this->FileSystem->id, 'name'=>$this->data['FileSystem']['name'], 'type'=>'folder')));
    }
    public function rename($fileSystemId) {
        if(!$this->FileSystem->rename($fileSystemId, $this->data['FileSystem']['name'])) {
            return $this->error(4);
        }

        return $this->success(1, array('results'=>array('file_system_id'=>$fileSystemId, 'name'=>$this->data['FileSystem']['name'])));
    }
    public function delete($fileSystemId) {
        if(!$this->FileSystem->remove($fileSystemId)) {
            return $this->error(4);
        }

        return $this->success(1, array('results'=>array('file_system_id'=>$fileSystemId)));
    }


    public function download($fileSystemId) {
        //Find the subject
        $this->FileSystem->recursive = -1;
        $fsData = $this->FileSystem->findByFileSystemId($fileSystemId);

        $this->redirect($fsData['FileSystem']['file_source']);

    }

    private function _validateFS($action, $params) {

        //Find FS data
        switch($action) {
            case 'download':
                $fsData = $this->_findByFileSystemId($params[0]);
                break;
            case 'delete':
                $fsData = $this->_findByFileSystemId($params[0]);
                break;
            case 'rename':
                $fsData = $this->_findByFileSystemId($params[0]);
                break;
            case 'addFolder':
                $fsData = $this->_findByFileSystemId($params[0]);
                break;
            case 'uploadFile':
                $parentId = $this->request->query['path'][count($this->request->query['path'])-1];
                $fsData = $this->_findByFileSystemId($parentId);
                break;
            case 'fileSystem':
                //Load object
                if($params[0]=='subject') {
                    $obj = $this->Subject;
                } else if($params[0]=='user_lesson') {
                    $obj = $this->UserLesson;
                }
                $obj->recursive-1;
                $objData = $obj->find('first', array('conditions'=>array($obj->primaryKey=>$params[1])));

                $parentId = $objData[$obj->alias]['root_file_system_id'];
                $fsData = $this->_findByFileSystemId($parentId);
                break;
        }

        if(!isSet($fsData) || !$fsData) {
            return $this->error(1);
        }
        $fsData = $fsData['FileSystem'];


        //Check if this is a Teacher or student
        $subjectId = $fsData['entity_id'];
        if($fsData['entity_type']=='user_lesson') {
            $this->UserLesson->recursive = -1;
            $ulData = $this->UserLesson->findByUserLessonId($fsData['entity_id']);
            $subjectId = $ulData['UserLesson']['subject_id'];
        }
        $userRelation = $this->Subject->getUserRelationToSubject($subjectId, $this->Auth->user('user_id'));
        if(!$userRelation) {
            //Not a teacher/student
            return $this->error(2);
        }

        if($userRelation=='teacher') {
            return true;
        }


        //Check if student have permission
        switch($action) {
            case 'fileSystem':
            case 'download':
                if($fsData['permission']==0 ||
                        $fsData['permission']==$this->Auth->user('user_id')) {
                    return true;
                }
                break;
            case 'delete':
            case 'rename':
            case 'addFolder':
            case 'upload':
                if($fsData['permission']==$this->Auth->user('user_id')) {
                    return true;
                }
                break;
        }

        return $this->error(3);
    }

    private function _findByFileSystemId($fileSystemId) {
        //Find the subject
        $this->FileSystem->recursive = -1;
        return $this->FileSystem->findByFileSystemId($fileSystemId);
    }

    /*public function testFS() {
        $subjectId = 106;
        $subjectType = 'subject';

        App::import('Model', 'FileSystem');
        $fsObj = new FileSystem();

        $fsObj->addFolder($subjectType, $subjectId, 'Main folder');
        $id = $fsObj->id;

            $fsObj->addFolder($subjectType, $subjectId, 'Sub 1', $id);
            $fsObj->addFolder($subjectType, $subjectId, 'Sub 2', $id);

            $fsObj->addFolder($subjectType, $subjectId, 'Sub 3', $id);
            $id = $fsObj->id;

                $fsObj->addFolder($subjectType, $subjectId, 'Sub Sub 1', $id);
                $fsObj->addFolder($subjectType, $subjectId, 'Sub Sub 2', $id);



        $fsObj->addFolder($subjectType, $subjectId, 'Main folder 2');
        $id = $fsObj->id;

            $fsObj->addFolder($subjectType, $subjectId, 'Sub 3', $id);
            $fsObj->addFolder($subjectType, $subjectId, 'Sub 4', $id);


    }*/
}