<?php
class SignMeUpBehavior extends ModelBehavior {

	public $validate = array(
		/*'username' => array(
			'pattern' => array(
				'rule' => array('custom','/[a-zA-Z0-9\_\-]{4,30}$/i'),
				'message'=> 'Usernames must be 4 characters or longer with no spaces.'
			),
			'usernameExists' => array(
				'rule' => 'isUnique',
				'message' => 'Sorry, this username already exists'
			),
		),*/
		'email' => array(
			'validEmail' => array(
				'rule' => array('email', true),
				'message' => 'Please supply a valid & active email address'
			),
			'emailExists' => array(
				'rule' => 'isUnique',
				'message' => 'Sorry, this email address is already in use'
			),
		),
		'password' => array(
			'match' => array(
				'rule' => array('confirmPassword', 'password', 'password2'),
				'message' => 'Passwords do not match'
			),
			'minRequirements' => array(
				'rule' => array('minLength', 6),
				'message' => 'Passwords need to be at least 6 characters long'
			)
		),
	);

	public function beforeValidate(Model $Model) {
		$this->model = $Model;
		$this->model->validate = array_merge($this->validate, $this->model->validate);
	}

	public function confirmPassword($field, $password1, $password2) {
		if ($this->model->data[$this->model->alias]['password2'] == $this->model->data[$this->model->alias]['password']) {
			return true;
		}
	}
	
	public function beforeSave( Model $Model ) {
		if(!isSet($Model->data['User']['password'])) {
			return true;
		}
		App::uses('AuthComponent', 'Controller/Component');
		$Model->data['User']['password'] = AuthComponent::password($Model->data['User']['password']);
		return true;
	}

	public function generateActivationCode($data) {
		return Security::hash(serialize($data).microtime().rand(1,100), null, true);
	}

}
?>