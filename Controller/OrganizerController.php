<?php
/**
 * This controller is a warrper for the teacher/user controllers
 * It will render the matching views and be built for ajax
 */
class OrganizerController extends AppController {
	public $name = 'Organizer';
	//public $uses = array('Subject', 'User', 'Profile', 'TeacherLesson', 'UserLesson');
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')), 'Security');
	//public $helpers = array('Form', 'Html', 'Js', 'Time');
	///public $helpers = array('Watchitoo');

    public function beforeFilter() {
        parent::beforeFilter();

        if($this->RequestHandler->isAjax()) {
            //The views are in Organizer/ajax/user|teacher
            $this->viewPath = $this->viewPath.DS.'ajax'.DS;
            if(strtolower(substr($this->request->params['action'], 0, 4 ))=='user') {
                $this->viewPath .= 'user';
            } else {
                $this->viewPath .= 'teacher';
            }
        }


    }

    public function index() {
    }

}
