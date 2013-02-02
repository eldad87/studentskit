<?php
class AccountsController extends AppController {
	public $name = 'Accounts';
	public $uses = array('User');
	public $components = array('Session',
                                //'Facebook.Connect'=>array(  'noAuth'=>false, 'model'=>'User' ),
                                'RequestHandler',
                                'SignMeUp.SignMeUp'=>array(
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
                                    ),
                                    'authorize' => 'Controller'
                                ));


	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('login', 'forgotten_password', 'register', 'activate', 'setTimezone', 'setLocale', 'setLanguage', 'setLanguagesOfRecords'));
	}

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        static $eventListenerAttached = false;

        if(!$eventListenerAttached) {
            //Connect the event manager of this model
            App::import( 'Event', 'ForumEventListener');
            $fel = new ForumEventListener();
            CakeEventManager::instance()->attach($fel);

            App::import( 'Event', 'LoginEventListener');
            $lel = new LoginEventListener();
            CakeEventManager::instance()->attach($lel);
            $eventListenerAttached = true;
        }
    }
	
	
	public function login() {
        if(!isSet($this->request->query['login_client'])) {
            $this->request->query['login_client'] = 'default';
        }
        //Check were we need to redirect the user to
        if(Router::url( $this->here, true )!=$this->referer() && !$this->Auth->loginRedirect) {
            $redirect = $this->Auth->redirect();
            if(!$redirect || $redirect=='/') {
                $redirect = $this->referer();
            }

            $this->Auth->redirect($redirect);
        }

		if ($this->request->is('post') || $this->Auth->user()) {
            //pr($this->request->data); die;
			if ($this->Auth->login()) {
                $active = (int) $this->Auth->user('active');
                if(!$active) {
                    $this->Auth->logout();
                    $this->Session->setFlash(__('Please activate you\'r account'), 'flash_error'/*, 'default', array(), 'auth'*/);
                    $this->redirect(array('action'=>'activate'));
                } else if($active==2) {
                    $this->Auth->logout();
                    $this->Session->setFlash(__('You have been banned!'), 'flash_error'/*, 'default', array(), 'auth'*/);
                    $this->redirect('/');
                }

                $event = new CakeEvent('Controller.Accounts.afterLogin', $this, array('user_id'=>$this->Auth->user('user_id')) );
                $this->getEventManager()->dispatch($event);


                //$this->Session->write('locale', $this->Auth->user('locale'));
                $this->Session->write('login_client', $this->request->query['login_client']);
                $this->Session->write('timezone', $this->Auth->user('timezone'));
                Configure::write('Config.timezone_set_by_user', true);
                $this->Session->write('language', $this->Auth->user('language'));
                if($lor = json_decode($this->Auth->user('languages_of_records'))) {
                    $this->Session->write('languages_of_records', $lor);
                }

                if ($this->RequestHandler->isAjax()) {
                    return $this->success(1, array('user_id'=>$this->Auth->user('user_id')));
                }

                return $this->redirect($this->Auth->redirect());
			} else {
                if ($this->RequestHandler->isAjax()) {
                    return $this->error(1);
                }
				$this->Session->setFlash(__('Email or password is incorrect'), 'flash_error'/*, 'default', array(), 'auth'*/);
			}
	    }
	}

	public function logout() {

        $userId = $this->Auth->user('user_id');

        $event = new CakeEvent('Controller.Accounts.beforeLogout', $this, array('user_id'=>$userId) );
        $this->getEventManager()->dispatch($event);

		$this->Auth->logout();

        /*$cookieName = $this->Connect->FB->getMetadataCookie();
        var_dump($cookieName); die;
        if(array_key_exists($cookieName, $_COOKIE)) {
            unset($_COOKIE[$cookieName]);
        }*/
        $this->Connect->FB->destroySession();
//        $this->Session->delete('FB'); //delete the FB session data
        //$this->Session->destroy();
        /*$this->Session->delete('locale');
        $this->Session->delete('timezone');*/

        $event = new CakeEvent('Controller.Accounts.afterLogout', $this, array('user_id'=>$userId) );
        $this->getEventManager()->dispatch($event);

		if ($this->RequestHandler->isAjax()) {
            return $this->success(1);
        }

		return $this->redirect('/');

	}

    public function setTimezone($continent, $region=null) {
        $timezone = $continent;
        if($region) {
            $timezone .= '/' . $region;
        }

        if(!$timezone) {
            return $this->error(1);
        }

        $this->Session->write('timezone', $timezone);

        if($this->Auth->user()) {
            $this->User->create(false);
            $this->User->id = $this->Auth->user('user_id');
            if(!$this->User->save(array('timezone'=>$timezone))) {
                return $this->error(2);
            }
        }
        return $this->success(1, array('timezone'=>$timezone));
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
            $this->User->create(false);
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
            $this->User->create(false);
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
        $res = $this->SignMeUp->register();
        if($this->RequestHandler->isAjax()) {
            if($res) {
                extract($this->SignMeUp->settings);
                if (empty($activation_field)) {
                    $this->login();
                }
                return $this->success(1, array('user_id'=>$this->Auth->user('user_id')));
            } else {
                return $this->error(1, array('validation_errors'=>$this->User->validationErrors));
            }
        }

	}

	public function activate() {
		$this->SignMeUp->activate();
	}

	public function forgotten_password() {
		$this->SignMeUp->forgottenPassword();
	}

    public function changePassword() {
       //1. Check that all required fields provided
        if(!isSet($this->request->data['User']['current_password']) || empty($this->request->data['User']['current_password'])) {
            $this->User->invalidate('current_password', __('Invalid Current Password'));
            return $this->error(1, array('validation_errors'=>$this->User->validationErrors));
        }


        //2. check if current password is matching the user input
        $this->User->create(false);
        $this->User->recursive = -1;
        $userData = $this->User->find('first', array('fields'=>array('password'), 'conditions'=>array('user_id'=>$this->Auth->user('user_id'))));

        //App::import('Component', 'Auth');
        if($userData['User']['password']!=AuthComponent::password($this->request->data['User']['current_password'])) {
            $this->User->invalidate('current_password', __('Wrong Current Password'));
            return $this->error(2, array('validation_errors'=>$this->User->validationErrors));
        }

        //Try to update the user
        $this->User->id = $this->Auth->user('id');
        //$this->User->set(array('password'=>$this->request->data['password'], 'password2'=>$this->request->data['password2']));
        if(!$this->User->save($this->request->data, true, array('password', 'password2'))) {
            return $this->error(3, array('validation_errors'=>$this->User->validationErrors));
        }

        //Password changed!
        return $this->success(1);
    }
}
