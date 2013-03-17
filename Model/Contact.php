<?php
class Contact extends AppModel {
    public $name = 'Contact';
    public $useTable = false;

    public $_schema = array(
        'subject'   => array('type' => 'string' , 'null' => false, 'default' => '', 'length' => '255'),
        'subject'   => array('type' => 'string' , 'null' => false, 'default' => '', 'length' => '255'),
        'full_name' => array('type' => 'string' , 'null' => false, 'default' => '', 'length' => '255'),
        'email'     => array('type' => 'string' , 'null' => false, 'default' => '', 'length' => '255'),
        'phone'     => array('type' => 'string' , 'null' => false, 'default' => '', 'length' => '255'),
        'message'   => array('type' => 'text'   , 'null' => false, 'default' => ''),
    );

    public $validate = array(
        'topic' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'required' => true,
                'message'=>'Please select a topic'
            ),
            'topic_list_check' 	=> array(
                'allowEmpty'=> true,
                'rule'    	=> 'checkIfTopicInList',
                'message' 	=> 'Please select a topic from list'
            )
        ),

        'subject' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'required' => true,
                'message'=>'Please insert your subject'
            )
        ),
        'email' => array(
            'email' => array(
                'rule' => array('email'),
                'required' => true,
                'message'=>'please insert your email address'
            )
        ),
        'message' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'required' => true,
                'message'=>'please enter your message'
            )
        ),
    );

    public function checkIfTopicInList() {
        if(!isSet($this->data[$this->name]['topic']) ||
            empty($this->data[$this->name]['topic'])) {
            return false;
        }

        $topics = $this->getTopics();
        if(!in_array($this->data[$this->name]['topic'], array_keys($topics))) {
            return false;
        }

        return true;
    }

    public function getTopics() {
        return array(
            1=>__('Support'),
            2=>__('Suggestion'),
            3=>__('Sales'),
            4=>__('Refund'),
            5=>__('Abuse'),
            6=>__('Legal'),
            7=>__('Feedback removal'),
            8=>__('Flag'),
            9=>__('Other')
        );
    }
}
?>