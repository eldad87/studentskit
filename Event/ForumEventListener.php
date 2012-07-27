<?php
App::uses('CakeEventManager', 'Event');
/**
 *@property Notification $notification
 */
class ForumEventListener implements CakeEventListener {


    public function implementedEvents() {
        return array(
            'Controller.Users.afterLogout'       => 'afterLogout',
            'Controller.Users.afterLogin'        => 'afterLogin',
            'Model.SubjectCategory.afterSave'    => 'afterSaveSubjectCategory',
            'Model.Forum.afterSave'              => 'afterSavePost',
        );
    }

    /*public function afterSavePost(CakeEvent $event) {
        //$postObj = $event->subject();

        if(isSet($event->data['data']['content']) && isSet($event->data['post_id'])) {

            $update['post_id']  = $event->data['post_id'];
            $update['content']  = $event->data['data']['content'];
            //TODO: add

            App::import('Vendor', 'Solr');
            $SolrObj = new Solr('forum');
            return $SolrObj->addDocument($update);
        }
    }*/
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