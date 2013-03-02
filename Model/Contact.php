<?php
class Contact extends AppModel {
    public $name = 'Contact';
    public $useTable = false;

    public $_schema = array(
        'subject'   => array('type' => 'string' , 'null' => false, 'default' => '', 'length' => '255'),
        'full_name' => array('type' => 'string' , 'null' => false, 'default' => '', 'length' => '255'),
        'email'     => array('type' => 'string' , 'null' => false, 'default' => '', 'length' => '255'),
        'phone'     => array('type' => 'string' , 'null' => false, 'default' => '', 'length' => '255'),
        'message'   => array('type' => 'text'   , 'null' => false, 'default' => ''),
    );

    public $validate = array(
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
}
?>