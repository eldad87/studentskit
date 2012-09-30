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
	public $components = array('RequestHandler', 'Session', 'DebugKit.Toolbar');
	
	public function beforeFilter() {
        $this->_setLanguage();

		parent::beforeFilter();
		if ($this->request->is('ajax')) {
			$this->autoLayout = false;
			$this->disableCache();
		}
	}

    private function _setLanguage() {
        App::uses('L10n', 'I18n');
        $localize = new L10n();

        $language /*= $locale */= null;
        if($this->Session->read('language')) {
            $language = $this->Session->read('language');
            $locale = $localize->map($this->Session->read('language'));
            //$language = $this->Session->read('language');
            Configure::write('Config.language', $language);
        } else {
            //Get language from browser
            $locale = $localize->get();

            $language = $localize->catalog($locale);
            $language = $localize->map($language['localeFallback']);
        }

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
