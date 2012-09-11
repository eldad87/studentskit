<?php
class Thread extends AppModel {
	public $name = 'Thread';
	public $useTable = 'threads';
	public $primaryKey = 'thread_id';
	
	public $belongsTo = array(
				'To' => array(
					'className' => 'User',
					'foreignKey'=>'to_user_id',
					'fields'=>array('first_name', 'last_name', 'image')
				),
				'By' => array(
					'className' => 'User',
					'foreignKey'=>'by_user_id',
					'fields'=>array('first_name', 'last_name', 'image')
				)
			);
			
	public function createThread($msg, $byUserId, $toUserId, $entityType=null, $entityId=null) {
		$set = array(
			'by_user_id'				=>$byUserId,
			//'by_user_type'			=>$byUser['type'],
			'by_user_unread_messages'	=>0,
			'to_user_id'				=>$toUserId,
			//'to_user_type'			=>$toUser['type'],
			'to_user_unread_messages'	=>1,
			'messages'					=>json_encode(array())
		);
		if($entityType && $entityId) {
			$set['entity_type'] = $entityType;
			$set['entity_id'] 	= $entityId;
		}
		
		$this->create(false);
		$this->set($set);
		if(!$this->save()) {
			return false;
		}
		
		//We do it in another query so 'modify' will be set.
		return $this->replayMessage($this->id, $byUser['id'], $msg);
	}
	
	public function replayMessage($threadId, $byUserId, $msg) {
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
		$messages[]	= $this->createMessage($byUserId, $msg);
		
		$this->id = $threadId;
		$this->set(array('messages'=>json_encode($messages), $otherUserByTo.'_user_unread_messages'=>1));
		return $this->save();
	}
	
	public function getThread($threadId, $userId) {
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
				$threadData['to_user_id'],
				$threadData['to_user_type'],
				$threadData['to_user_unread_messages']);
		return $threadData;
	}
	public function getUserThreadsLastMessage($userId, $page=1, $limit=10) {
		//Show subject image
		//Get all messages from DB
		$messagesData = $this->find('all', array(	'conditions'=>array(
														'OR'=>array('by_user_id'=>$userId, 'to_user_id'=>$userId)
													),
													'order'=>'modified'
											)
		);
		
		//Get the latest message from each theread and if its unread.
		$messages = array();
		if($messagesData) {
			foreach($messagesData AS $data) {
				$messageData = $data['Thread'];
				
				//Detirment if the reader is the to/by
				$userByTo = 'by';
				$otherUserByTo = 'To';
				if($userId==$messageData['to_user_id']) {
					$userByTo = 'to';
					$otherUserByTo = 'By';
				}
				
				
				
				$threadMessages = json_decode($messageData['messages'], true);
				
				$messages[] = array(
					'thread_id'			=>$messageData['thread_id'],
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
	
	
	private function createMessage($byUserId, $message, $timestamp=null) {
		if(!$timestamp) {
			$timestamp = time();
		}
		
		return array(
			'user_id'	=>$byUserId,
			'message'	=>$message,
			'timestamp'	=>$timestamp
		);
	}
		
	
}
?>