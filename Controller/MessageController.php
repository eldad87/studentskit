<?php
class MessageController extends AppController {
	public $name = 'Message';
	public $uses = array('Thread', 'User');
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')),/* 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');
	
	public function index() {
		$threads = $this->Thread->getUserThreadsLastMessage($this->Auth->user('user_id'));
		return $this->success(1, array('threads'=>$threads));
	}
	
	public function findThread() {
		//TODO: find by subject/lesson id, including from/to
	}
	
	public function getThread($thread) {
		$thread = $this->Thread->getThread($thread, $this->Auth->user('user_id'));
		
		if(!$thread) {
			return $this->error(1);
		}
		return $this->success(1, array('thread'=>$thread));
	}
	
	public function sendMessage() {
		if(!isSet($this->request->data['message']) || !$this->request->data['message']) {
			return $this->error(1);
		}
		
		$results = false;
		if(isSet($this->request->data['thread_id']) && $this->request->data['thread_id']) {
			$results = $this->Thread->replayMessage($this->request->data['thread_id'], $this->Auth->user('user_id'), $this->request->data['message']);
			
		} else if(isSet($this->request->data['to_user_id']) && $this->request->data['to_user_id']) {
			$results = $this->Thread->createThread($this->request->data['message'], $this->Auth->user('user_id'), $this->request->data['to_user_id']);
			
		} else {
			return $this->error(2);
		}
		
		if(!$results) {
			return $this->error(3);
		}
		
		return $this->success(1, array('thread_id'=>$this->Thread->id));
	}

}
