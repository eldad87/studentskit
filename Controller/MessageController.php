<?php
class MessageController extends AppController {
	public $name = 'Message';
	public $uses = array('Thread', 'User', 'FileSystem');
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')),/* 'Security'*/);
	//public $helpers = array('Form', 'Html', 'Js', 'Time');

    /**
     * Show user main message list
     * @param int $limit
     * @param int $page
     * @return array
     */
    public function index($limit=10, $page=1) {
        $this->set('limit', $limit);
        $this->set('page', $page);

		$threads = $this->Thread->getUserThreadsLastMessage($this->Auth->user('user_id'), $page, $limit);
        return $this->success(1, array('threads'=>$threads));
	}

    public function getList($limit=3, $page=1) {
        $threads = $this->Thread->getUserThreadsLastMessage($this->Auth->user('user_id'), $page, $limit);
        return $this->success(1, array('threads'=>$threads));
    }

    public function viewThread($threadId) {
        $thread = $this->Thread->getThreadMessages($threadId, $this->Auth->user('user_id'));

        $fs = $this->FileSystem->getFS($thread['root_file_system_id'], null, array('parent_id'=>0));
        $this->set('fs', $fs);

        //make the thread as read.
        $this->Thread->setAsRead($threadId, $this->Auth->user('user_id'));
        $this->set('thread', $thread);
        return $this->success(1, array('thread'=>$thread));

    }

    /**
     * Make the thread is invisible for a given user
     * @param $threadId
     * @return array
     */
    public function deleteThread($threadId) {
        if($this->Thread->markThreadAsInvisibleToUser($threadId, $this->Auth->user('user_id'))) {
            return $this->success(1, array('thread_id'=>$threadId));
        }

        return $this->error(1, array('results'=>array('thread_id'=>$threadId, 'validation_errors'=>$this->Thread->validationErrors)));
    }


    public function getUnreadThreadCount() {
        $unreadCount = $this->Thread->getUnreadCount($this->Auth->user('user_id'));
        return $this->success(1, array('unreadCount'=>$unreadCount));
    }
	
	public function findThread($byUserId, $toUserId, $entityType=null, $entityId=null) {
		//TODO: find by subject/lesson id, including from/to
	}


	public function sendMessage() {
		if(!isSet($this->request->data['message']) || !$this->request->data['message']) {
			return $this->error(1);
		}
		
		$results = false;
		if(isSet($this->request->data['thread_id']) && $this->request->data['thread_id']) {
			$results = $this->Thread->replayMessage($this->request->data['thread_id'],
                                                    $this->Auth->user('user_id'),
                                                    $this->request->data['message'],
                                                    $this->getAttachments());
			
		} else if(isSet($this->request->data['to_user_id']) && $this->request->data['to_user_id']) {
            $entityType = $entityId = null;
            if(isSet($this->request->data['entity_type']) && !empty($this->request->data['entity_type']) &&
                isSet($this->request->data['entity_id']) && !empty($this->request->data['entity_id']) ) {
                $entityType = $this->request->data['entity_type'];
                $entityId = $this->request->data['entity_id'];
            }

            //Check there is an existing thread that match the user criteria
            if($existingThreadId = $this->Thread->getThreadId($this->Auth->user('user_id'), $this->request->data['to_user_id'], $entityType, $entityId)) {

                $this->Thread->id = $existingThreadId;
                $results = $this->Thread->replayMessage($existingThreadId,
                                                        $this->Auth->user('user_id'),
                                                        $this->request->data['message'],
                                                        $this->getAttachments());
            } else {
                //Create a new thread
			    $results = $this->Thread->createThread( $this->request->data['message'],
                                                        $this->Auth->user('user_id'),
                                                        $this->request->data['to_user_id'],
                                                        $entityType,
                                                        $entityId,
                                                        $this->getAttachments());
            }
			
		} else {
			return $this->error(2);
		}
		
		if(!$results) {
			return $this->error(3);
		}

		return $this->success(1, array('results'=>array('thread_id'=>$this->Thread->id, 'message'=>$this->Thread->createMessage($this->Thread->id, $this->Auth->user('user_id'), $this->request->data['message'], $this->getAttachments()))));
	}
    /**
     * Attachments must be IDs in $this->request->data['attachment']
     * @return array
     */
    private function getAttachments() {
        if(isSet($this->request->data['attachment'])) {
            return $this->request->data['attachment'];
        }
        return array();
    }

}
