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
        //error 1 - not found
        //error 2 - permission denied
        return true;
    }

}