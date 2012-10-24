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
                                'DebugKit.Toolbar');
    public $helpers = array('Facebook.Facebook', 'Layout');
	
	public function beforeFilter() {
        $this->_setLanguage();


		parent::beforeFilter();
		if ($this->request->is('ajax')) {
			$this->autoLayout = false;
			$this->disableCache();
		}

        $this->set('loginClient', $this->Session->read('login_client'));

        /** View params
         *************************************/
        $this->set('user', $this->Auth->user());

        App::import('Utils.Lib', 'Languages');
        $lObj = new Languages();
        $this->set('languages', array_flip($lObj->lists()));

        App::uses('CakeTime', 'Utility');

        $this->set('navButtonSelection', $this->detemintControllerToNavButton());
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
            $locale = $localize->map($this->Session->read('language'));
            //$language = $this->Session->read('language');
        } else {
            //Get language from browser
            $locale = $localize->get();

            $language = $localize->catalog($locale);
            $language = $localize->map($language['localeFallback']);
        }
        Configure::write('Config.language', $language);

        //pr(($this->Session->read('timezone'))); die;
        if($this->Session->read('timezone')) {
            Configure::write('Config.timezone', $this->Session->read('timezone'));
        }

        //Set language direction
        //setlocale(LC_ALL, $locale .'UTF8', $locale['locale'] .'UTF-8', $locale['locale'], 'eng.UTF8', 'eng.UTF-8', 'eng', 'en_US');

        $cataqlog = $localize->catalog($language);
        Configure::write('Config.languageDirection', isSet($cataqlog['direction']) ? $cataqlog['direction'] : 'ltr');
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
	}
}
