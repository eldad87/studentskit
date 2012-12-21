<?php
class NotificationsController extends AppController {
	public $name = 'Notifications';
	public $uses = array('Notification');

    public function markAsRead() {
        $this->Notification->markAsRead($this->request->data['notification_ids'], array('user_id'=>$this->Auth->user('user_id')));
        return $this->success(1);
    }
	public function index($limit=7, $page=1, $markAsRead=true) {
		$notifications = $this->Notification->getNotifications($this->Auth->user('user_id'), $limit, $page, $markAsRead);
        return $this->success(1, array('notifications'=>$notifications));
	}

    public function getUnreadNotificationsCount() {
        $unreadCount = $this->Notification->getUnreadCount($this->Auth->user('user_id'));
        return $this->success(1, array('unreadCount'=>$unreadCount));
    }
}
