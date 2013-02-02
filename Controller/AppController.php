<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $components = array('RequestHandler', 'Session',
                                'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login'), 'authenticate' => array('Form' => array('fields' => array('username' => 'email')))),
                                'Facebook.Connect'=>array(  /*'noAuth'=>false, */'model'=>'User' ),
                                'DebugKit.Toolbar');
    public $helpers = array('Facebook.Facebook', 'Layout', 'TimeTZ');
    public $jsSettings = array();

    public function isAuthorized() {
        return $this->Auth->user('user_id');
    }

	public function beforeFilter() {
        if (!$this->request->is('requested')) {
            $this->set('facebookUser', $this->Connect->user());
        }
        //$this->set('localUser', $this->user());
        /** Language + Datetime
         *************************************/
        $this->_setLanguage();


		parent::beforeFilter();
		if ($this->request->is('ajax')) {
			$this->autoLayout = false;
			$this->disableCache();
		}


        $this->set('loginClient', $this->Session->read('login_client'));

        /** View params
         *************************************/
        App::import('Utils.Lib', 'Languages');
        $lObj = new Languages();
        $this->set('languages', array_flip($lObj->lists()));

        App::uses('CakeTime', 'Utility');

        $this->set('navButtonSelection', $this->detemintControllerToNavButton());
	}

    public function beforeRender() {
        parent::beforeRender();

        $this->set('user', $this->Auth->user());

        if($this->Auth->user('user_id')) {
            $this->setJSSetting('user_id', $this->Auth->user('user_id'));
        }

        $this->set('jsSettings', $this->jsSettings);
    }

    private function detemintControllerToNavButton() {
        $navButtons = array(
            'home'          =>false,
            'board'         =>false,
            'account'       =>false,
            'request'       =>false,
            'howItWorks'    =>false,
        );
        switch(strtolower($this->name)) {
            case 'home':
            case 'accounts':
            case 'order':
            case 'lessons':
                $navButtons['home']=true;
                break;
            case 'message':
            case 'teacher':
            case 'student':
            case 'organizer':
                $navButtons['account']=true;
                break;
            case 'requests':
                $navButtons['request']=true;
                break;
            case 'forum':
            case 'posts':
            case 'reports':
            case 'search':
            case 'staff':
            case 'stations':
            case 'topics':
            case 'users':
            case 'requests':
                $navButtons['board']=true;
                break;
            default:
                $navButtons['home']=true;
                break;
        }

        return $navButtons;
    }

    public function beforeFacebookSave() {

        if($this->Connect->user('locale')) {
            App::import('core', 'L10m');
            $l10m = new L10n();
            $lang = $l10m->catalog(strtolower(str_replace('_', '-', $this->Connect->user('locale'))));
            if(is_array($lang)) {
                $lang = $lang['localeFallback'];
            }
            $this->Connect->authUser['User']['language']    = $lang;
        }
        if($this->Connect->user('timezone')) {
            $this->Connect->authUser['User']['timezone']    = 'UTC '.$this->Connect->user('timezone');
            Configure::write('Config.timezone_set_by_user', true);
        }

        if($this->Connect->user('gender')) {
            $this->Connect->authUser['User']['gender'] = $this->Connect->user('gender');
        }
        if($this->Connect->user('currency')) {
            $this->Connect->authUser['User']['currency'] = $this->Connect->user('currency');
        }

        $this->Connect->authUser['User']['email']       = $this->Connect->user('email');
        $this->Connect->authUser['User']['first_name']  = $this->Connect->user('first_name');
        $this->Connect->authUser['User']['last_name']   = $this->Connect->user('last_name');
        $this->Connect->authUser['User']['active']      = 1;
        $this->Connect->authUser['User']['password2']   = $this->Connect->authUser['User']['password'];
        return true;
    }

    /*public function afterFacebookLogin() {
        //$this->redirect($this->Auth->loginAction);
    }*/

    private function _setLanguage() {
        App::uses('L10n', 'I18n');
        $localize = new L10n();

        $language /*= $locale */= null;
        if($this->Session->read('language')) {
            $language = $this->Session->read('language');
            //$locale = $localize->map($this->Session->read('language'));
            //$language = $this->Session->read('language');
        } else {
            //Get language from browser
            $locale = $localize->get();

            $language = $localize->catalog($locale);
            //$language = $localize->map($language['localeFallback']); //en
            $language = $language['localeFallback']; //eng
        }


        Configure::write('Config.language', $language);

        //pr(($this->Session->read('timezone'))); die;
        if($this->Session->read('timezone')) {
            Configure::write('Config.timezone', $this->Session->read('timezone'));
            Configure::write('Config.timezone_set_by_user', true);
        }


        if($this->Session->read('languages_of_records')) {

            $lor = array();
            foreach($this->Session->read('languages_of_records') AS $lang) {
                $lor[$lang] = $localize->catalog($lang);
                $lor[$lang] = $lor[$lang]['language'];
            }

            Configure::write('Config.languages_of_records', $lor);
        }

        //Set language direction
        //setlocale(LC_ALL, $locale .'UTF8', $locale['locale'] .'UTF-8', $locale['locale'], 'eng.UTF8', 'eng.UTF-8', 'eng', 'en_US');

        $cataqlog = $localize->catalog($language);
        Configure::write('Config.languageDirection', isSet($cataqlog['direction']) ? $cataqlog['direction'] : 'ltr');
    }

    protected function setJSSetting($key, $val) {
        $this->jsSettings[$key] = $val;
    }
	
	protected function error( $code, $data=array() ) {
		return $this->apiMessage('error', $code, $data);
	} 
	protected function success( $code, $data=array() ) {
		return $this->apiMessage('success', $code, $data);
	}
	private function apiMessage( $type, $code, array $extra=array() ) {
		//$this->set('xstatus', $type);
		//$this->set('xstatuscode', $code);
		Configure::load('api');
		$data = Configure::read($this->name.'.'.$this->params['action'].'.'.$type.'.'.$code);


        //Fix image
        $extra = $this->fixImage($extra);

		$response = array(
				'code'=>array($code),
				'type'=>array($type),
				'title'=>array($data['title']),
				'description'=>array($data['description']),
		);
		
		if($extra) {
			key($extra);
			$response[key($extra)] = $extra[key($extra)];
		}
		
		if($this->responseType=='array') {
			return $response;
		}
        if ($this->request->is('requested')) {
            return $response;

        }
		
		$this->set('response', array('response'=>$response));
		$this->set('_serialize', 'response');


        if(Configure::read('debug')==2 && !isSet($this->params['ext']) && !$this->RequestHandler->isAjax()) {
			$this->autoLayout = false;
			$this->autoRender = false;
			$this->viewPath = 'debug';
			$this->render('debug');
		}

        if($this->RequestHandler->isAjax()) {
            $this->viewPath = $this->viewPath.DS.'ajax';
        }

	}

    /**
     * Create 'image' parameter with the requested image size
     * @param $data
     * @param null $image
     * @return array
     */
    private function fixImage($data, $image=null) {
        if(!$image) {
            if(!isSet($this->request->query['image'])) {
                return $data;
            }
            $image = $this->request->query['image'];
            $image = explode('x', $image);
        }

        App::import('Helper', 'Layout');
        $lhObj = new LayoutHelper(new View(null));


        foreach($data AS $key=>$val) {
            if(is_array($val)) {
                $data[$key] = $this->fixImage($val);
            } else if($key=='image_source'){
                $data['image'] = $lhObj->image($val['image_source'], $image[0], $image[1]);
            }
        }

        return $data;
    }

    protected function getCurrentParamsWithDifferentURL($url=array(), $removeKeys=array()) {
        $query = $this->request->query;
        if($query) {
            foreach($removeKeys AS $key) {
                unset($query[$key]);
            }
            $url['?'] = $query;
        }
        return Router::url( $url );
    }
}
