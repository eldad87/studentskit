<?php
class Test extends AppModel {
	public $name = 'Test';
	public $useTable = 'tests';
	public $primaryKey = 'test_id';

    public $validate = array(
        'name'=> array(
            'between' => array(
                'required'	=> 'create',
                'allowEmpty'=> false,
                'rule'    	=> array('between', 2, 45),
                'message' 	=> 'Name must have %d to %d characters',
                'last'      =>true
            )
        ),
        'description'=> array(
            'minLength' 	=> array(
                'required'	=> 'create',
                'allowEmpty'=> false,
                'rule'    	=> array('minLength', 15),
                'message' 	=> 'Description must have more then %d characters'
            )
        ),
        'questions'=> array(
            'questionsCheck'=> array(
                    'required'	=> 'create',
                    'allowEmpty'=> false,
                    'rule'    	=> 'questionsCheck',
                    'message' 	=> 'Please set questions'
            )
        ),
    );

    public function questionsCheck($data) {
        if(!$data || !$data['questions']) {
            //$this->invalidate('questions', __('Please set questions'));
            return false;
        }

        foreach($data['questions'] AS $question) {
            if(!isSet($question['q']) || !$question['q']  ||
                !isSet($question['a']) || !$question['a'] ||
                !isSet($question['ra']) || !is_numeric($question['ra'])) {
                $this->invalidate('questions', __('Please Make sure you set the all the Questions, Answers and select the Right-Answer'));
                return false;
            }
            if(count($question['a'])<=1) {
                $this->invalidate('questions', __('Each question must have 2 or more answers'));
            }
            foreach($question['a'] AS $answer) {
                if(!$answer) {
                    $this->invalidate('questions', __('Do not set empty answers'));
                }
            }
        }
        return true;
    }

    public function beforeSave($options = array()) {
        parent::beforeSave($options);

        if(isSet($this->data[$this->alias]['questions'])) {
            $this->data[$this->alias]['questions'] = json_encode($this->data[$this->alias]['questions']);
        }
    }

    public function beforeValidate($options = array()) {
        parent::beforeValidate($options);
    }
}
?>