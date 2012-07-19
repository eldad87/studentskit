<?php
class UsersController extends AppController {
	public $name = 'Users';
	public $uses = array('User');
	public $components = array('Session', 'RequestHandler', 'SignMeUp.SignMeUp'=>array(
																'activation_field'=>false,
																'useractive_field'=>false,
																'password_reset_field'=>'password_reset',
																'username_field'=>'first_name',
															),
															'Auth' => array(
																/*'loginAction' => array(
																	'controller' => 'User',
																	'action' => 'login',
																),*/
																'authenticate' => array(
																	'Form' => array(
																		'fields' => array('username' => 'email')
																	)
																)
															));


	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('login', 'forgotten_password', 'register', 'activate'));
	}
	
	
	
	public function login() {
		if($this->Auth->loggedIn()) {
			if ($this->Auth->login()) {
				return $this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Username or password is incorrect'), 'default', array(), 'auth');
			}
		}
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				return $this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Username or password is incorrect'), 'default', array(), 'auth');
			}
	    }
	}
	
	public function logout() {
		$this->Auth->logout();
		if (!$this->RequestHandler->isAjax()) {
			return $this->redirect($this->Auth->redirect());
		} else {
			return true;
		}
	}
	
	public function register() {
		$this->SignMeUp->register();
	}

	public function activate() {
		$this->SignMeUp->activate();
	}

	public function forgotten_password() {
		$this->SignMeUp->forgottenPassword();
	}
}
