<?php
class Thread extends AppModel {
	public $name = 'Thread';
	public $useTable = 'threads';
	public $primaryKey = 'thread_id';
	
	public $belongsTo = array(
				'To' => array(
					'className' => 'User',
					'foreignKey'=>'to_user_id',
					'fields'=>array('username', 'image_source')
				),
				'By' => array(
					'className' => 'User',
					'foreignKey'=>'by_user_id',
					'fields'=>array('username', 'image_source')
				)
			);
			
	public function createThread($msg=null, $byUserId, $toUserId, $entityType=null, $entityId=null, $attachments=array()) {
		$set = array(
			'by_user_id'				=>$byUserId,
			'by_user_unread_messages'	=>0,
			'to_user_id'				=>$toUserId,
			'to_user_unread_messages'	=>1,
            //title					    =>null,
			'messages'					=>json_encode(array()),
		);
		if($entityType && $entityId) {
			$set['entity_type'] = $entityType;
			$set['entity_id'] 	= $entityId;

            //Set message title
            App::import('Model', 'UserLesson');
            $ulObj = new UserLesson();

            switch($entityType) {
                case 'user_lesson':
                    $ulObj->recursive = -1;
                    $ulData = $ulObj->findByUserLessonId($entityId);
                    $set['title'] = $ulData['UserLesson']['name'];
                    break;

                case 'teacher_lesson':
                    $ulObj->TeacherLesson->recursive = -1;
                    $tlData = $ulObj->TeacherLesson->findByTeacherLessonId($entityId);
                    $set['title'] = $tlData['TeacherLesson']['name'];
                    break;

                case 'subject':
                    $ulObj->Subject->recursive = -1;
                    $sData = $ulObj->Subject->findBySubjectId($entityId);
                    $set['title'] = $sData['Subject']['name'];
                    break;
            }
		}
		
		$this->create(false);
		$this->set($set);
		if(!$this->save()) {
			return false;
		}

        //In case just a thread is needed (I.e first message can upload files)
        if(!$msg) {
            return true;
        }
		
		//We do it in another query so 'modify' will be set.
		return $this->replayMessage($this->id, $byUserId, $msg, $attachments);
	}


    public function afterSave($created) {
        parent::afterSave($created);

        //Set file system
        if($created) {
            App::import('Model', 'FileSystem');
            $fsObj = new FileSystem();

            //Create root filesystem
            $fsObj->createFS('thread', $this->id, 0, 0, $this->data[$this->alias]['title']);
            $rootFS = $fsObj->id;


            return $this->updateAll(array('root_file_system_id'=>$rootFS), array($this->primaryKey=>$this->id));
        }

        return true;
    }

    public function getAllAttachmentIds($threadId) {
        $this->recursive = -1;
        $threadData = $this->findByThreadId($threadId);
        if(!$threadData) {
            return false;
        }
        $messages = json_decode($threadData[$this->alias]['messages'], true);

        $attachments = array();
        foreach($messages AS $message) {
            if($message['attachment']) {
                foreach($message['attachment'] AS $attachment) {
                    $attachments[] = $attachment['id'];
                }
            }
        }

        return array_unique($attachments);
    }

    /**
     * Add a replay message to the thread.
     * if it was invisible (deleted) any of the users - make if visible
     * @param $threadId
     * @param $byUserId
     * @param $msg
     * @param $attachments
     * @return bool|mixed
     */
    public function replayMessage($threadId, $byUserId, $msg, $attachments=array()) {
		$this->recursive = -1;
		$msgData 	= $this->findByThreadId($threadId);
		
		$otherUserByTo = '';
		if($msgData['Thread']['by_user_id']==$byUserId) {
			$otherUserByTo = 'to';
		} else if($msgData['Thread']['to_user_id']==$byUserId) {
			$otherUserByTo = 'by';
		} else {
			return false;
		}
		
		$messages 	= json_decode($msgData['Thread']['messages'], true);
		$messages[]	= $this->createMessage($threadId, $byUserId, $msg, $attachments);
		
		$this->id = $threadId;
		$this->set(array('messages'=>json_encode($messages), 'by_user_visible'=>1, 'to_user_visible'=>1, $otherUserByTo.'_user_unread_messages'=>1));
		return $this->save();
	}
	
	public function getThreadMessages($threadId, $userId) {
		//$this->recursive = -1;
		$threadData = $this->findByThreadId($threadId);
		if(!$threadData) {
			return false;
		}
		
		//Add only other user
		if($threadData['Thread']['by_user_id']==$userId) {
			$threadData['Thread']['other_user'] = $threadData['To'];
		} else if($threadData['Thread']['to_user_id']==$userId) {
			$threadData['Thread']['other_user'] = $threadData['By'];
		} else {
			return false;
		}
		$threadData = $threadData['Thread'];
		

		
		$threadData['messages'] = json_decode($threadData['messages'], true);
		
		unset(	$threadData['by_user_id'],
				$threadData['by_user_type'],
				$threadData['by_user_unread_messages'],
				$threadData['by_user_visible'],
				$threadData['to_user_id'],
				$threadData['to_user_type'],
				$threadData['to_user_unread_messages'],
				$threadData['to_user_visible']);
		return $threadData;
	}

    /**
     *
     * Get the last updated threads with the last message in them
     * @param $userId
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getUserThreadsLastMessage($userId, $page=1, $limit=10) {
		//Show subject image
		//Get all messages from DB
        $this->recursive = 1;
		$messagesData = $this->find('all', array(	'conditions'=>array(
														'OR'=>array(array('by_user_id'=>$userId, 'by_user_visible'=>1),
                                                                    array('to_user_id'=>$userId, 'to_user_visible'=>1))
													),
													'order'=>'modified DESC',
                                                    'page'=>$page,
                                                    'limit'=>$limit
											)
		);
		
		//Get the latest message from each thread and if its unread.
		$messages = array();
		if($messagesData) {
			foreach($messagesData AS $data) {
				$messageData = $data['Thread'];
				
				//Determent if the reader is the to/by
				$userByTo = 'by';
				$otherUserByTo = 'To';
				if($userId==$messageData['to_user_id']) {
					$userByTo = 'to';
					$otherUserByTo = 'By';
				}


				$threadMessages = json_decode($messageData['messages'], true);
				
				$messages[] = array(
					'thread_id'			=>$messageData['thread_id'],
					'title'			    =>$messageData['title'],
					'entity_type'		=>$messageData['entity_type'],
					'entity_id'			=>$messageData['entity_id'],
					'unread_messages'	=>$messageData[$userByTo.'_user_unread_messages'],
					'last_message'		=>$threadMessages[(count($threadMessages)-1)],
					'other_user'		=>$data[$otherUserByTo]
				);
				
			}
		}
		
		return $messages;
	}

    /**
     * Will make the it invisible to the given user in getUserThreadsLastMessage && getUnreadCount
     * @param $threadId
     * @param $userId
     * @return bool|mixed
     */
    public function markThreadAsInvisibleToUser($threadId, $userId) {

        $this->recursive = -1;
        $threadData = $this->findByThreadId($threadId);
        if(!$threadData) {
            return false;
        }

        //Find which field we need to update
        $update = array();
        if($threadData['Thread']['by_user_id']==$userId) {
            $update['by_user_visible'] = 0;

        } else if($threadData['Thread']['to_user_id']==$userId) {
            $update['to_user_visible'] = 0;
        } else {
            return false;
        }

        $this->id = $threadId;
        return $this->save($update);

    }

    public function getThreadId($byUserId, $toUserId, $entityType=null, $entityId=null) {
        $conditions = array(
            'OR'=>array(array('by_user_id'=>$byUserId, 'to_user_id'=>$toUserId),
                array('by_user_id'=>$toUserId, 'to_user_id'=>$byUserId))
        );

        if($entityType && $entityId) {
            $conditions['entity_type'] = $entityType;
            $conditions['entity_id'] = $entityId;

        }

        //Get all messages from DB
        $this->recursive = -1;
        $threadData = $this->find('first', array(	'conditions'=>$conditions,
                                                    'order'=>'modified'
                                            ));

        return $threadData ? $threadData[$this->alias][$this->primaryKey] : false;
    }

    public function setAsRead($threadId, $userId) {
        //Load thread
        $this->recursive = -1;
        $threadData = $this->findByThreadId($threadId);
        if(!$threadData) {
            return false;
        }

        //Find out which field should be updates
        $update = array();
        if($threadData['Thread']['by_user_id']==$userId) {
            $update['by_user_unread_messages'] = 0;
        } else if($threadData['Thread']['to_user_id']==$userId) {
            $update['to_user_unread_messages'] = 0;
        } else {
            return false;
        }

        //Update DB
        $this->id = $threadId;
        return $this->save($update);
    }

    public function getUnreadCount($userId) {
        $this->recursive = -1;
        return $this->find('count', array('conditions'=>array(
            'OR'=>array(
                array('by_user_id'=>$userId, 'by_user_unread_messages'=>1, 'by_user_visible'=>1),
                array('to_user_id'=>$userId, 'to_user_unread_messages'=>1, 'to_user_visible'=>1),
            )
        )));
    }
	
	
	public function createMessage($threadId, $byUserId, $message, $attachments=array(), $timestamp=null) {
		if(!$timestamp) {
			$timestamp = time();
        }
		
		return array(
			'user_id'	    => $byUserId,
			'message'	    => $message,
            'attachment'    => $this->attachments($threadId, $attachments),
			'timestamp'	    => $timestamp
		);
	}

    /**
     * Load attachment name's
     * @param $threadId
     * @param $attachments
     * @return array
     */
    private function attachments($threadId, $attachments) {
        App::import('Model', 'FileSystem');
        $fsModel = new FileSystem();
        $fsModel->recursive = -1;
        $attachmentsData = $fsModel->find('all', array(
            'conditions'=>array(
                'file_system_id'=>$attachments,
                'entity_id'     =>$threadId,
                'entity_type'   =>'thread'
            ),
            'fields'=>array('file_system_id', 'name')
        ));

        if(!$attachmentsData) {
            return array();
        }


        $return = array();
        foreach($attachmentsData AS $attachment) {
            $return[] = array(
                'id'    =>$attachment['FileSystem']['file_system_id'],
                'name'  =>$attachment['FileSystem']['name']
            );
        }

        return $return;
    }
}
?>