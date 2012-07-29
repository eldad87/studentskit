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
            //'Model.SubjectCategory.afterSave'  => 'afterSaveSubjectCategory',
            'Model.Forum.Topic.afterSave'        => 'afterSaveTopic',
            'Model.Forum.Post.afterSave'         => 'afterSavePost',
            'Model.Forum.Topic.afterDelete'      => 'afterDeleteTopic',
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

    public function afterSaveSubjectCategory(CakeEvent $event) {
        $scObj = $event->subject();
        $scData = $scObj->findBySubjectCategoryId($event->data['subject_category_id']);
        if(!$scData) {
            return false;
        }
        $scData = $scData['SubjectCategory'];


        if(!$scData['forum_id']) {

            $update = array();

            //Create category if needed - only to deep 3
            if($scData['deep']<=3) {
                App::import('Model', 'Forum.Forum');
                $forumObj = new Forum();

                $forumData = array(
                    'title'         => $scData['name'],
                    'description'   => $scData['description'],
                );

                //If parent category - chain this new forum to it.
                if($scData['parent_subject_category_id']) {
                    $parentData = $scObj->findBySubjectCategoryId($scData['parent_subject_category_id']);
                    $forumData['forum_id'] = $parentData['SubjectCategory']['forum_id'];
                }

                $forumObj->set($forumData);
                $forumObj->save();

                $update['forum_id'] = $forumObj->id;
            } else {
                //Get forum_id from parent at deep 3
                $parentIds = explode(',', $scData['path']);
                $parentData = $this->findBySubjectCategoryId($parentIds[2]);
                $update['forum_id'] = $parentData['forum_id'];
            }

            if($update) {
                $scObj->id = $scData['subject_category_id'];
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

    public function beforeAccept(CakeEvent $event) {
        App::import('Model', 'TeacherLesson');
        $tlObj = new TeacherLesson();

        if(!$event->data['user_lesson']['teacher_lesson_id']) {
            //Create a lesson + set student_user_id

            if(!$tlObj->add(array('type'=>'user_lesson','id'=>$event->data['user_lesson']['user_lesson_id']), null, null, array('teacher_user_id'=>$event->data['user_lesson']['teacher_user_id'],
                'student_user_id'=>$event->data['user_lesson']['student_user_id'],
                'num_of_students'=>$tlObj->getDataSource()->expression('num_of_students+1')))) {
                return false;
            }


            $event->result['teacher_lesson_id'] = $tlObj->id;
        } else {
            $counter = $event->subject()->getAcceptLessonCounter($event->data['user_lesson']['stage']);
            //Update the num_of_pending_invitations counter
            $this->TeacherLesson->id = $event->data['user_lesson']['teacher_lesson_id'];

            $this->TeacherLesson->set(array($counter=>$tlObj->getDataSource()->expression($counter.'-1'), 'num_of_students'=>$tlObj->getDataSource()->expression('num_of_students+1')));
            if(!$this->TeacherLesson->save()) {
                return false;
            }
        }

        return true;
    }
}
?>