<?php
class BillingHistory extends   AppModel {
    public $name = 'BillingHistory';
    public $useTable = 'billing_history';
    public $primaryKey = 'billing_history_id';
    public $actsAs = array('Time');

    public $belongsTo 	= array(
        'User' => array(
            'className'	=> 'User',
            'foreignKey'=>'user_id',
            'fields'	=>array('first_name', 'last_name', 'image_source', 'teacher_paypal_id')
        ),
        'Subject' => array(
            'className'	=> 'Subject',
            'foreignKey'=>'subject_id',
            'fields'	=>array('average_rating', 'image_source', 'type', 'is_enable' )
        )
    );

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        Configure::load('billingHistory');
    }

    public function addHistory($amount, $userId, $messageId, $TLId=null, $ULId=null, $params=array()) {

        $save = array(
            'user_id'=>$userId,
            'message_id'=>$messageId,
            'params'=>json_encode($params)
        );


        if($ULId) {
            App::import('Model', 'UserLesson');
            $ulModel = new UserLesson();
            $ulData = $ulModel->findByUserLessonId($ULId);
            $save['subject_id'] = $ulData['UserLesson']['subject_id'];
            $save['teacher_lesson_id'] = $ulData['UserLesson']['teacher_lesson_id'];
            $save['user_lesson_id'] = $ULId;
            $params['lessonName'] = $ulData['UserLesson']['name'];;

        } else if($TLId) {
            App::import('Model', 'TeacherLesson');
            $tlModel = new TeacherLesson();
            $tlData = $tlModel->findByTeacherLessonId($ULId);
            $save['subject_id'] = $tlData['UserLesson']['subject_id'];
            $save['teacher_lesson_id'] = $TLId;
            $params['lessonName'] = $tlData['UserLesson']['name'];;
        }

        $save['message'] = $this->buildMessage($userId, $messageId, $params);

        //Save
        $this->create(false);
        return $this->save($save);
    }

    /**
     * Change language to the User.language
     * set message
     * and return back to the original language
     */
    private function buildMessage($userId, $messageId, $params) {


        //Change languge to User.language
        App::import('Model', 'User');
        $userModel = new User();
        $userModel->recursive = -1;
        $userData = $userModel->findByUserId($userId);


        $currentLang = Configure::read('Config.language');
        Configure::write('Config.language', $userData['User']['language']);


        //Build message
        extract($params);
        $message = __(Configure::read('billingHistory.'.$messageId));
        $message = preg_replace('/\{([A-Za-z]+)\}/e', "$$1", $message);
        Configure::write('Config.language', $currentLang); //Return back to original language

        return $message;
    }
}