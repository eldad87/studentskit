<?php

class WeakPassword extends AppModel {
	public $name = 'WeakPassword';
	public $useTable = 'weak_passwords';
	public $primaryKey = 'password_md5';
	public $displayField = 'password';

    public function isInDictionary($password) {
        return
            $this->find('first', array(
                                'conditions'=>array(
                                        $this->primaryKey=>md5($password)
                                )
                            )
                        ) ? true : false;
    }
}
?>