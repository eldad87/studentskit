<?php
class Notification extends AppModel {
	public $name = 'Notification';
	public $useTable = 'notifications';
	public $primaryKey = 'notification_id';
    public $belongsTo = array(
        'ByUser' => array(
            'className' => 'User',
            'foreignKey'=>'by_user_id',
            'fields'=>array('username','image_source')
        )
    );

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        Configure::load('notifications');
    }

    public function addNotification($userId, $byUserId, array $message=array()) {
        //Format the message -> create url
        foreach($message['params'] AS $key=>$value) {
            if(!in_array($key, array('teacher_user_id', 'student_user_id', 'name', 'datetime', 'user_lesson_id'))) {
                unset($message['params'][$key]);
            }
        }
        $this->create(false);
        $this->set(array(
                'user_id'       =>$userId,
                'by_user_id'    =>$byUserId,
                'message'       =>$this->formatMessage($userId, $message['message_enum'], $message['params']),
                'message_enum'  =>$message['message_enum'],
                'message_params'=>json_encode($message),
                'link'          =>json_encode($this->formatLink($userId, $message['message_enum'], $message['params'])),
                'unread'        =>1

            ));
        return $this->save();
    }

    private function formatLink($toUserId, $type, $params) {
        return array('/');
    }
    private function formatMessage($toUserId, $enum, $params) {
        static $users = array();

        /* Format according to user language
        App::import('Model', 'User');
        $userObj = new User();
        $userObj->recursive = -1;
        $userData = $userObj->findByUserId($toUserId);
        if(!$userData) {
            return false;
        }
        $userData['language']*/

        //User full name
        $userFullName = '';
        $byUserType = $toUserId==$params['teacher_user_id'] ? 'student' : 'teacher';
        if(isSet($users[$params[$byUserType.'_user_id']])) {
            $userFullName = $users[$params[$byUserType.'_user_id']];
        } else {
            App::import('Model', 'User');
            $userObj = new User();
            $userObj->recursive = -1;
            $userData = $userObj->findByUserId($params[$byUserType.'_user_id']);

            if($userData['User']['title']) {
                $userFullName = $userData['User']['title'].' ';
            }
            $userFullName .= $userData['User']['first_name'];
            if($userData['User']['last_name']) {
                $userFullName .= ' '.$userData['User']['last_name'];
            }

            $users[$params[$byUserType.'_user_id']] = $userFullName;
        }

        return sprintf(Configure::read('notification.'.$enum), $userFullName, $params['name'], $params['datetime']);
    }

    public function getUnreadCount($userId) {
        $this->recursive = -1;
        return $this->find('count', array('conditions'=>array(
            $this->alias.'.user_id'=>$userId,
            'unread'=>1
        )));
    }

    public function getNotifications($userId, $limit=7, $page=1, $markAsRead=true, $unreadOnly=true) {
        $this->recursive = 1;
        $conditions = array( $this->alias.'.user_id'=>$userId );
        if($unreadOnly) {
            $conditions[$this->alias.'.unread'] = 1;
        }
        $results = $this->find('all', array('conditions'=>$conditions,
                                                                'limit'=>$limit,
                                                                'page'=>$page));

        $return = array();
        if(!$results) {
            return $return;
        }


        $markAsReadIds = array();
        foreach($results AS $result) {
            $result[$this->alias]['by_user_image_source'] = $result['ByUser']['image_source'];
            $result[$this->alias]['by_user_username'] = $result['ByUser']['username'];
            $return[] = $result[$this->alias];

            if($markAsRead && $result[$this->alias]['unread']==1) {
                $markAsReadIds[] = $result[$this->alias][$this->primaryKey];
            }
        }

        //Mark notifications as read
        if($markAsReadIds) {
            $this->markAsRead($markAsReadIds);
        }

        return $return;
    }

    public function markAsRead($ids, $where=array()) {
        if(!$ids) {
            return false;
        }
        $this->unbindAll();
        $where[$this->primaryKey] = $ids;
        $this->updateAll(array('unread'=>0), $where);
    }

}
?>