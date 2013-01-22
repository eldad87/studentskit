<?php
class TestsController extends AppController {
	public $name = 'Tests';
	public $uses = array('Subject', 'User', 'UserLesson', 'Test');
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')),/* 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');


    public function beforeFilter() {
        parent::beforeFilter();

        $res = $this->_validateTest($this->request['action'], $this->request['pass']);
        if($res!==true) {
            return $res;
        }
    }


    public function index($subjectId) {
        //Show all available tests
        $this->Test->recursive = -1;
        $subjectTests = $this->Test->find('all', array('conditions'=>array('subject_id'=>$subjectId)));

        $tests = array();
        if($subjectTests) {
            foreach($subjectTests AS $subjectTest) {
                $tests[] = $subjectTest['Test'];
            }
        }

        $this->Subject->recursive = -1;
        $subjectData = $this->Subject->findBySubjectId($subjectId);
        $this->set('creationStage', $subjectData['Subject']['creation_stage']);
        $this->set('subjectId', $subjectId);

        //Check if this is the teacher
        $this->set('isTeacher', ($subjectData['Subject']['user_id']==$this->Auth->user('user_id')) );

        return $this->success(1, array('results'=>array('tests'=>$tests, 'subject_id'=>$subjectId)));
    }

    public function manage($subjectId, $testId=null) {
        $this->set('subjectId', $subjectId);
        $this->set('testId', $testId);

        if($testId) {
            $this->Test->recursive = -1;
            $testData = $this->Test->findByTestId($testId);
            $testData = $testData['Test'];
        } else {
            $testData = array(
                'name'=>null,
                'description'=>null,
                'questions'=>json_encode(array())
            );
        }
        $this->set('testData', $testData);


        return $this->success(1);
    }
    public function save($testId=null) {
        if($testId) {
            $this->Test->id = $testId;
        }

        if(!$this->Test->save($this->request->data)) {
            return $this->error(3, array('results'=>array('validation_errors'=>$this->Test->validationErrors)));
        }

        return $this->success(1, array('results'=>array('test_id'=>$this->Test->id)));
    }

    public function take( $testId ) {
        $this->set('testId', $testId);

        $this->Test->recursive = -1;
        $testData = $this->Test->findByTestId($testId);
        $testData = $testData['Test'];


        return $this->success(1, array('results'=>array('test_id'=>$testId, 'subject_id'=>$testData['subject_id'], 'questions'=>json_decode($testData['questions'], true))));
    }

    public function delete($testId) {

        $this->Test->delete($testId);
        return $this->success(1, array('results'=>array('test_id'=>$this->Test->id)));
    }


    private function _validateTest($action, $params) {
        $subjectId = null;
        switch($action) {
            case 'manage':
                //Make sure the test belongs to the subject
                if(isSet($params[1])) {
                    $this->Test->recursive = -1;
                    $testData = $this->Test->findByTestId($params[1]);
                    if(!$testData || $testData['Test']['subject_id']!=$params[0]) {
                        return $this->error(2);
                    }
                }
                $subjectId = $params[0];
                break;

            case 'delete':
                $this->Test->recursive = -1;
                $testData = $this->Test->findByTestId($params[0]);
                if(!$testData) {
                    return $this->error(2);
                }
                $subjectId = $testData['Test']['subject_id'];
                break;
            case 'index':
            case 'take':
                return true; //TODO:
                break;
            case 'save':
                if(!isSet($this->data['subject_id'])) {
                    return $this->error(2);
                }
                $subjectId = $this->data['subject_id'];
                break;
        }

        $this->Subject->recursive = -1;
        $subjectData = $this->Subject->findBySubjectId($subjectId);
        if(!$subjectData || $subjectData['Subject']['user_id']!=$this->Auth->user('user_id')) {

            return $this->error(3);
        }
        return true;
    }

}