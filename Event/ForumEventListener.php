<?php
App::uses('CakeEventManager', 'Event');
/**
 *@property Notification $notification
 */
class ForumEventListener implements CakeEventListener {


    public function implementedEvents() {
        return array(
            'Controller.Accounts.afterLogout'       => 'afterLogout',
            'Controller.Accounts.afterLogin'        => 'afterLogin',
            'Model.Category.afterSave'              => 'afterSaveCategory',
            'Model.Forum.Topic.afterSave'           => 'afterSaveTopic',
            'Model.Forum.Post.afterSave'            => 'afterSavePost',
            'Model.Forum.Topic.afterDelete'         => 'afterDeleteTopic',
        );
    }

    public function afterDeleteTopic(CakeEvent $event) {
        App::import('Vendor', 'Solr');
        $solrObj = new Solr('forum');
        return $solrObj->removeDocumentById($event->data['topic_id']);
    }
    public function afterSaveTopic(CakeEvent $event) {
        //if(isSet($event->subject()->data['Topic']['firstPost_id']) && $event->subject()->data['Topic']['firstPost_id']) {
        return $this->updateSolrTopic($event->data['topic_id']);
        //}
    }
    public function afterSavePost(CakeEvent $event) {
        if(!$event->data['created']) { //update record
            App::import('Model', 'Forum.Post');
            $postObj = new Post();
            $postObj->recursive = 0;
            $postObj->unbindModel(array('belongsTo'=>array('Forum', 'User')));
            $postData = $postObj->findById($event->data['post_id']);
            if($event->data['post_id']==$postData['Topic']['firstPost_id']) { //If this is the first post
                return $this->updateSolrTopic($event->data['post_id']);
            }
        }
        return true;
    }

    private function updateSolrTopic($topicID) {

        App::import('Model', 'Forum.Topic');
        $topicObj = new Topic();
        $topicObj->recursive = -1;
        $topicObj->cacheQueries = false;
        $topicData = $topicObj->findById($topicID);
        if(!$topicData) {
            return false;
        }


        if($topicData['Topic']['firstPost_id']) {
            //Find forum
            $forumData = $topicObj->Forum->findById($topicData['Topic']['forum_id']);
            $forumData = $forumData['Forum'];

            //Find first post
            $topicObj->Post->recursive = -1;
            $firstPostData = $topicObj->Post->find('first', array('id'=>$topicData['Topic']['firstPost_id']));
            if(!$firstPostData) {
                return false;
            }

            $update = array();
            $update['topic_id']     = $topicData['Topic']['id']; //Topic title
            $update['title']        = $topicData['Topic']['title']; //Topic title
            $update['language']     = $topicData['Topic']['language']; //Topic title
            $update['content']      = $firstPostData['Post']['content']; //first topic content
            $update['content']      = $firstPostData['Post']['content']; //first topic content
            $update['forum_id']     = $topicData['Topic']['forum_id'];
            $update['forums']       = $topicObj->Forum->getPathHierarchy($topicData['Topic']['forum_id']);
            $update['read_access']  = (int) $forumData['accessRead'];
            $update['last_modified']= $forumData['modified'] ? $forumData['modified'] : $forumData['created'];

            App::import('Vendor', 'Solr');
            $SolrObj = new Solr('forum');
            return $SolrObj->addDocument($update);
        }
    }

    public function afterSaveCategory(CakeEvent $event) {
        if(!$event->data['created']) {
            return true; //Only new records gets a forum
        }
        $scObj = $event->subject();
        $cData = $scObj->findByCategoryId($event->data['category_id']);
        if(!$cData) {
            return false;
        }
        $cData = $cData['Category'];


        if(!$cData['forum_id']) {

            $update = array();

            //Create category if needed - only to deep 3
            if($cData['deep']<=3) {
                App::import('Model', 'Forum.Forum');
                $forumObj = new Forum();

                $forumData = array(
                    'title'         => $cData['name'],
                    'description'   => $cData['description'],
                );

                //If parent category - chain this new forum to it.
                $forumData['forum_id'] = 0;
                if($cData['parent_category_id']) {
                    $parentData = $scObj->findByCategoryId($cData['parent_category_id']);
                    $forumData['forum_id'] = $parentData['Category']['forum_id'];
                }

                //Set order
                $forumObj->cacheQueries = false;
                $res = $forumObj->find('first', array('conditions'=>array('forum_id'=>$forumData['forum_id']), 'order'=>'orderNo DESC', 'fields'=>array('orderNo')));
                $forumData['orderNo'] = isSet($res[$forumObj->alias]['orderNo']) ?($res[$forumObj->alias]['orderNo']+1) : 1;


                $forumObj->set($forumData);
                $forumObj->save();

                $update['forum_id'] = $forumObj->id;
            } else {
                //Get forum_id from parent at deep 3
                $parentIds = explode(',', $cData['path']);
                $parentData = $this->findByCategoryId($parentIds[2]);
                $update['forum_id'] = $parentData['forum_id'];
            }

            if($update) {
                $scObj->id = $cData['category_id'];
                $scObj->set($update);
                return $scObj->save();
            }
        }

        return true;
    }
    public function afterLogout(CakeEvent $event) {
        $event->subject()->Session->delete('Forum');
    }
    public function afterLogin(CakeEvent $event) {
        $event->subject()->Session->delete('Forum');
    }
}
?>