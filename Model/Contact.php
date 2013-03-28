<?php
class Contact extends AppModel {
    public $name = 'Contact';
    public $useTable = false;

    const CONTACT_SUPPORT           = 1;
    const CONTACT_SUGGESTION        = 2;
    const CONTACT_SALES             = 3;
    const CONTACT_REFUND            = 4;
    const CONTACT_ABUSE             = 5;
    const CONTACT_LEGAL             = 6;
    const CONTACT_FEEDBACK_REMOVAL  = 7;
    const CONTACT_FLAG              = 8;
    const CONTACT_OTHER             = 9;


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
            Contact::CONTACT_SUPPORT            => __('Support'),
            Contact::CONTACT_SUGGESTION         => __('Suggestion'),
            Contact::CONTACT_SALES              => __('Sales'),
            Contact::CONTACT_REFUND             => __('Refund'),
            Contact::CONTACT_ABUSE              => __('Abuse'),
            Contact::CONTACT_LEGAL              => __('Legal'),
            Contact::CONTACT_FEEDBACK_REMOVAL   => __('Feedback removal'),
            Contact::CONTACT_FLAG               => __('Flag'),
            Contact::CONTACT_OTHER              => __('Other')
        );
    }

}
?>