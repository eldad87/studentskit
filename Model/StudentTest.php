<?php
class StudentTest extends AppModel {
	public $name = 'StudentTest';
	public $useTable = 'student_tests';
	public $primaryKey = 'student_test_id';
	
	public function getTests($entityType, $entityId) {
		$tests = array();
		
		$testsData = $this->find('all', array('conditions'=>array('entity_type'=>$entityType, 'entity_id'=>$entityType)));
		foreach($testsData AS $testData) {
			$tests[] = new TestManager($testData['Test']['test_id']);
		}
		
		/*if($entityType=='lesson') {
			//Add the subject lessons
			App::import('Model', 'UserLesson');
			$ulObj = new UserLesson();
			$ulData = $ulObj->findByUserLessonId($entityId);
			
			$sTest = $this->getTests('subject', $ulData['UserLesson']['subject_id']);
			$tests = am($tests, $sTest);
		}*/
		
		return $tests;
	}
}


class TestManager {
	private $testId = false;  
	private $name;
	private $description;
	private $questions = array();
	
	public function TestManager($testId) {
		//Load data from DB
		$testobj = new StudentTest();
		$testData = $testobj->findByTestId($testId);
		if(!$testData) {
			return false;
		}
		$testData = $testData['Test'];
		$this->testId = $testId;
		
		//Set data to object
		$this->setName($testData['name']);
		$this->setDescription($testData['description']);
		foreach($testData['questions'] AS $question) {
			$this->addQuestion(new TestQuestion(question));
		}
	}
	
	public function save($entityType=null, $entityId=null) {
		$testobj = new StudentTest();
		
		$set = array(
				'name'			=>$this->name,
				'description'	=>$this->description,
				'questions'		=>$this->exportQuestions()
		);
		if($entityType && $entityId) {
			$set['entity_id'] 	= $entityId;
			$set['entity_type'] = $entityType;
		} else if($this->testId) {
			$testobj->id = $this->testId;
		} else {
			return false;
		}
		$testobj->set($set);
		if(!$testobj->save()) {
			return false;
		}
		return $testobj->id;
	}
	
	private function exportQuestions() {
		
		foreach($this->questions AS $question) {
			$return['questions'][] = $question->export();
		}
		
		return $return;
	}
	
	public function setName($name) {
		$this->name 		= $name;
	} 
	public function setDescription($description) {
		$this->description 	= $description;
	} 
	/**
	 * 
	 * Add question to the array
	 * @param unknown_type $question
	 */
	public function addQuestion( TestQuestion $question ) {
		$this->questions[] = $question;
	}
}

class TestQuestion {
	private $question;
	private $answers = array();
	private $rightAnswer;
	
	public function TestQuestion( $export ) {
		$this->setQuestion($export['question']);
		foreach($export['answers'] AS $answer ) {
			$this->addAnswer($answer['answer'], $answer['true']);
		}
	}
	
	public function export() {
		return array(
			'question'		=>$this->question,
			'answers'		=>$this->answers
		);
	}
	
	public function setQuestion( $question ) {
		$this->question = $question;
	}
	
	public function addAnswer( $answer, $isTrue=false ) {
		$this->answers[] = array('answer', 'true'=>$answer);
		return count($this->answers);
	}
}
?>