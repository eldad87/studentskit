<?php
class AccountsController extends AppController {
	public $name = 'Accounts';
	public $uses = array('User');
	public $components = array('Session', 'RequestHandler', 'SignMeUp.SignMeUp'=>array(
																'activation_field'=>false,
																'useractive_field'=>false,
																'password_reset_field'=>'password_reset',
																'username_field'=>'first_name',
															),
															'Auth' => array(
																'loginAction' => array(
																	'controller' => 'Accounts',
																	'action' => 'login',
																),
																'authenticate' => array(
																	'Form' => array(
																		'fields' => array('username' => 'email')
																	)
																)
															));


	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('login', 'forgotten_password', 'register', 'activate', 'setTimezone', 'setLocale'));
	}

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        static $eventListenterAttached = false;

        if(!$eventListenterAttached) {
            //Connect the event manager of this model
            App::import( 'Event', 'ForumEventListener');
            $fel = new ForumEventListener();
            CakeEventManager::instance()->attach($fel);

            App::import( 'Event', 'LoginEventListener');
            $lel = new LoginEventListener();
            CakeEventManager::instance()->attach($lel);
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

                $event = new CakeEvent('Controller.Accounts.afterLogin', $this, array('user_id'=>$this->Auth->user('user_id')) );
                $this->getEventManager()->dispatch($event);


                //$this->Session->write('locale', $this->Auth->user('locale'));
                $this->Session->write('timezone', $this->Auth->user('timezone'));
                $this->Session->write('language', $this->Auth->user('language'));
                if($lor = json_decode($this->Auth->user('languages_of_records'))) {
                    $this->Session->write('languages_of_records', $lor);
                }

                if ($this->RequestHandler->isAjax()) {
                    return $this->success(1);
                }

                return $this->redirect($this->Auth->redirect());
			} else {
                if ($this->RequestHandler->isAjax()) {
                    return $this->error(1);
                }
				$this->Session->setFlash(__('Email or password is incorrect'), 'default', array(), 'auth');
			}
	    }
	}

	public function logout() {
        $userId = $this->Auth->user('user_id');

        $event = new CakeEvent('Controller.Accounts.beforeLogout', $this, array('user_id'=>$userId) );
        $this->getEventManager()->dispatch($event);

		$this->Auth->logout();
        /*$this->Session->delete('locale');
        $this->Session->delete('timezone');*/

        $event = new CakeEvent('Controller.Accounts.afterLogout', $this, array('user_id'=>$userId) );
        $this->getEventManager()->dispatch($event);


		if ($this->RequestHandler->isAjax()) {
            return $this->success(1);
        }
		return $this->redirect($this->Auth->redirect());

	}

    public function setTimezone($timezone=null) {
        if(!$timezone) {
            return $this->error(1);
        }

        $this->Session->write('timezone', $timezone);

        if($this->Auth->user()) {
            $this->User->create();
            $this->User->id = $this->Auth->user('user_id');
            if(!$this->User->save(array('timezone'=>$timezone))) {
                return $this->error(2);
            }
        }
        return $this->success(1);
    }

    /*public function setLocale($locale=null) {
        if(!$locale) {
            return $this->error(1);
        }

        $this->Session->write('locale', $locale);

        if($this->Auth->user()) {
            $this->User->create();
            $this->User->id = $this->Auth->user('user_id');
            if(!$this->User->save(array('locale'=>$locale))) {
                return $this->error(2);
            }
        }
        return $this->success(1);
    }*/
    public function setLanguage($language=null) {
        if(!$language) {
            return $this->error(1);
        }
        $this->Session->write('language', $language);

        if($this->Auth->user()) {
            $this->User->create();
            $this->User->id = $this->Auth->user('user_id');
            if(!$this->User->save(array('language'=>$language))) {
                return $this->error(2);
            }
        }
        return $this->success(1);
    }
    public function setLanguagesOfRecords($languages=null) {
        if(!$languages) {
            return $this->error(1);
        }

        $languages = explode(',', $languages);
        $this->Session->write('languages_of_records', $languages);

        if($this->Auth->user()) {
            $this->User->create();
            $this->User->id = $this->Auth->user('user_id');
            if($languages = json_encode($languages)) {
                if(!$this->User->save(array('languages_of_records'=>$languages))) {
                    return $this->error(2);
                }
            }
        }
        return $this->success(1);
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
