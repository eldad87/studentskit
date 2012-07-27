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

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        static $eventListenterAttached = false;

        if(!$eventListenterAttached) {
            //Connect the event manager of this model
            App::import( 'Event', 'ForumEventListener');
            $fel = new ForumEventListener();
            CakeEventManager::instance()->attach($fel);
            $eventListenterAttached = true;
        }
    }
	
	
	public function login() {
        //Check were we need to redirect the user to
        if(Router::url( $this->here, true )!=$this->referer() && !$this->Auth->loginRedirect) {
            $redirect = $this->Auth->redirect();
            if(!$redirect || $redirect=='/') {
                $redirect = $this->referer();
            }

            $this->Auth->redirect($redirect);
        }

		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
                $active = (int) $this->Auth->user('active');
                if(!$active) {
                    $this->Auth->logout();
                    $this->Session->setFlash(__('Please activate you\'r account'), 'default', array(), 'auth');
                    $this->redirect(array('action'=>'activate'));
                } else if($active==2) {
                    $this->Auth->logout();
                    $this->Session->setFlash(__('You have been banned!'), 'default', array(), 'auth');
                    $this->redirect('/');
                }

                $event = new CakeEvent('Controller.Users.afterLogin', $this, array('user_id'=>$this->Auth->user('user_id')) );
                $this->getEventManager()->dispatch($event);

				return $this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Username or password is incorrect'), 'default', array(), 'auth');
			}
	    }
	}

	public function logout() {

        $userId = $this->Auth->user('user_id');

        $event = new CakeEvent('Controller.Users.beforeLogout', $this, array('user_id'=>$userId) );
        $this->getEventManager()->dispatch($event);

		$this->Auth->logout();

        $event = new CakeEvent('Controller.Users.afterLogout', $this, array('user_id'=>$userId) );
        $this->getEventManager()->dispatch($event);


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
