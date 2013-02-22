<?php
/**
 * This controller is a warrper for the teacher/user controllers
 * It will render the matching views and be built for ajax
 */
class OrganizerController extends AppController {
	public $name = 'Organizer';
	public $uses = array( 'User' );
	//public $uses = array('Subject', 'User', 'Profile', 'TeacherLesson', 'UserLesson');
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')), 'Security');
	//public $helpers = array('Form', 'Html', 'Js', 'Time');
	public $helpers = array('Layout');

    public function beforeFilter() {
        parent::beforeFilter();

        if($this->RequestHandler->isAjax()) {
            //The views are in Organizer/ajax/user|teacher
            $this->viewPath = $this->viewPath.DS.'ajax'.DS;
            if(strtolower(substr($this->request->params['action'], 0, 4 ))=='user') {
                $this->viewPath .= 'user';
            } else if(strtolower(substr($this->request->params['action'], 0, 7 ))=='teacher'){
                $this->viewPath .= 'teacher';
            }
        }


    }

    public function index() {
    }

    public function calendar() {
        $allLiveLessons = $this->User->getLiveLessons($this->Auth->user('user_id'), true);

        $this->set('allLiveLessons', $allLiveLessons);
    }

}
