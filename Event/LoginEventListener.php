<?php
App::uses('CakeEventManager', 'Event');
/**
 *@property Notification $notification
 */
class LoginEventListener implements CakeEventListener {


    public function implementedEvents() {
        return array(
            'Controller.Accounts.afterLogin' => 'afterLogin',
        );
    }

    public function afterLogin(CakeEvent $event) {
        app::import('Model', 'User');
        $userObj = new User();
        $userObj->id = $event->data['user_id'];
        $userObj->set(array('lastLogin'=>$userObj->getDataSource()->expression('currentLogin'), 'currentLogin'=>$userObj->getDataSource()->expression('NOW()')));

        return $userObj->save();
    }
}
?>