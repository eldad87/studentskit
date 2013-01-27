<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sivan
 * Date: 10/9/12
 * Time: 1:22 AM
 * To change this template use File | Settings | File Templates.
 */


Class Watchitoo extends Component {

    private $wObj;
    private $wlmObj;
    private $tlObj;

    public function __construct() {


        App::import('Vendor', 'Watchitoo'.DS.'WatchitooService');
        App::import('Model', 'Subject');
        App::import('Model', 'TeacherLesson');
        App::import('Model', 'WatchitooLessonMeeting');
        App::import('Model', 'WatchitooLessonUser');
        App::import('Model', 'WatchitooSubjectTeacher');
        App::import('Model', 'WatchitooSubjectMeeting');

        $this->wObj  = new WatchitooService();
        $this->tlObj = new TeacherLesson();
        $this->Subject = new Subject();

        //TL meetings
        $this->wlmObj = new WatchitooLessonMeeting();
        $this->wluObj = new WatchitooLessonUser();

        //Subject meetings
        $this->wsmObj = new WatchitooSubjectMeeting();
        $this->wstObj = new WatchitooSubjectTeacher();



    }

    public function getSubjectMeetingSettings($subjectId) {
        //Find subject
        $subjectData = $this->getSubjectData($subjectId);
        if(!$subjectData) {
            return false;
        }

        //Find teacher watchitoo user_id
        $wUID = $this->getWatchitooUserId($subjectData['user_id'], $subjectId);
        if(!$wUID) {
            return false;
        }

        //Get teacher's full name
        $this->wluObj->User->recursive = -1;
        $userData = $this->wluObj->User->findByUserId($subjectData['user_id']);
        if(!$userData) {
            return false;
        }


        //Build teacher's display name
        $displayName = $userData['User']['first_name'];
        if($userData['User']['last_name']) {
            $displayName .= ' '.$userData['User']['last_name'];
        }


        $meetingId = $this->getMeetingId(null, $subjectId);
        if(!$meetingId) {
            return false;
        }

        return array(
            'user_id'=>$subjectData['user_id'],
            'watchitoo_user_id'=>$wUID,
            'display_name'=>$displayName,
            'is_moderator'=>'true',
            'meeting_id'=>$meetingId
        );

    }



    public function getMeetingSettings($teacherLessonId, $userId=null) {
        $tlData = $this->getTLData($teacherLessonId);
        if(!$userId) {
            $userId = $tlData['teacher_user_id'];
        }

        $wUID = $this->getWatchitooUserId($userId, $tlData['teacher_user_id']==$userId ? $tlData['subject_id'] : null);

        //get  user data
        $this->wluObj->User->recursive = -1;
        $userData = $this->wluObj->User->findByUserId($userId);

        $meetingId = $this->getMeetingId($teacherLessonId);

        if(!$tlData || !$wUID || /*!$userData || */!$meetingId) {
            return false;
        }


        $displayName = $userData['User']['first_name'];
        if($userData['User']['last_name']) {
            $displayName .= ' '.$userData['User']['last_name'];
        }


        return array(
            'user_id'=>$userId,
            'watchitoo_user_id'=>$wUID,
            'display_name'=>$displayName,
            'is_moderator'=>$tlData['teacher_user_id']==$userId ? 'true' : 'false',
            'meeting_id'=>$meetingId
        );
    }

    private function getMeetingId($teacherLessonId=null, $subjectId=null) {
        $meetingObj = $id = $searchKey = null;
        if($teacherLessonId) {
            $id = $teacherLessonId;
            $searchKey = 'teacher_lesson_id';
            $meetingObj = $this->wlmObj;
        } else if($subjectId) {
            $id = $subjectId;
            $searchKey = 'subject_id';
            $meetingObj = $this->wsmObj;
        } else {
            return false;
        }


        //Check if entity already have a meeting
        $meetingObj->recursive = -1;
        $meetingObj->cacheQueries = false;
        $mData = $meetingObj->find('first', array('conditions'=>array($searchKey=>$id)));

        if($mData) {
            $this->log('Meeting found', 'watchitoo');
            return $mData[$meetingObj->alias]['meeting_id'];
        }
        $this->log('Meeting NOT found', 'watchitoo');

        if($teacherLessonId) {
            return $this->createLessonMeeting( $teacherLessonId );
        } else {
            return $this->createSubjectMeeting($subjectId);
        }
    }

    private function createSubjectMeeting( $subjectId ) {
        $this->log('Create a meeting', 'watchitoo');

        if(!$this->Subject->lock($subjectId, 0)) {
            $this->log('Cannot lock meeting', 'watchitoo');
            return false;
        }

        //Check if meeting was-not-created yet
        $this->wsmObj->recursive = -1;
        $this->wsmObj->cacheQueries = false;
        $wsmData = $this->wsmObj->findBySubjectId($subjectId);
        if($wsmData) {
            $this->Subject->unlock($subjectId);
            $this->log('Meeting was created by a parallel thread', 'watchitoo');
            return $wsmData['WatchitooSubjectMeeting']['meeting_id'];
        }

        //Get TL data
        $subjectData = $this->getSubjectData($subjectId);
        //Create meeting
        $meetingId = $this->_createMeeting($subjectData['user_id'], $subjectId, $subjectData['name'], $subjectData['description'] );
        if(!$meetingId) {
            $this->Subject->unlock($subjectId);
            return false;
        }

        //Create link meeting_id-teacher_lesson_id
        $this->wsmObj->create(false);
        $this->wsmObj->set(array('subject_id'=>$subjectId, 'meeting_id'=>$meetingId));
        if(!$this->wsmObj->save()) {
            $this->log('Cannot create meeting-Subject link', 'watchitoo');
            $this->Subject->unlock($subjectId);
            return false;
        }


        $this->Subject->unlock($subjectId);

        return $meetingId;
    }

    private function createLessonMeeting( $teacherLessonId ) {
        $this->log('Create a meeting', 'watchitoo');

        if(!$this->tlObj->lock($teacherLessonId, 0)) {
            $this->log('Cannot lock meeting', 'watchitoo');
            return false;
        }

        //Check if meeting was-not-created yet
        $this->wlmObj->recursive = -1;
        $this->wlmObj->cacheQueries = false;
        $wmData = $this->wlmObj->findByTeacherLessonId($teacherLessonId);
        if($wmData) {
            $this->tlObj->unlock($teacherLessonId);
            $this->log('Meeting was created by a parallel thread', 'watchitoo');
            return $wmData['WatchitooLessonMeeting']['meeting_id'];
        }

        //Get TL data
        $tlData = $this->getTLData($teacherLessonId);


        $meetingId = $this->_createMeeting($tlData['teacher_user_id'], $tlData['subject_id'], $tlData['name'], $tlData['description'], $tlData['datetime'] );
        if(!$meetingId) {
            $this->tlObj->unlock($teacherLessonId);
            return false;
        }

        //Create link meeting_id-teacher_lesson_id
        $this->wlmObj->create(false);
        $this->wlmObj->set(array('teacher_lesson_id'=>$teacherLessonId, 'meeting_id'=>$meetingId));
        if(!$this->wlmObj->save()) {
            $this->log('Cannot create meeting-TL link', 'watchitoo');
            $this->tlObj->unlock($teacherLessonId);
            return false;
        }

        //Copy playlist
        $this->copyPlayList($tlData['subject_id'], $meetingId);


        $this->tlObj->unlock($teacherLessonId);

        return $meetingId;
    }

    /**
     * Create the meeting on Watchitoo's servers
     * @param $userId
     * @param $subjectId
     * @param $meetingName
     * @param $meetingDescription
     * @param null $meetingDatetime
     * @return bool
     */
    private function _createMeeting($userId, $subjectId, $meetingName, $meetingDescription, $meetingDatetime=null) {
        //Get Watchitoo's user_id
        $this->log('Get watchitoo user_id', 'watchitoo');
        $wUID = $this->getWatchitooUserId($userId, $subjectId);
        if(!$wUID) {
            $this->log('Cannot create watchitoo user_id', 'watchitoo');
            //$this->tlObj->unlock($teacherLessonId);
            return false;
        }



        //Create meeting
        $this->log('Create meeting', 'watchitoo');
        $meetingData = $this->wObj->saveMeeting(null, $wUID, $meetingName, $meetingDescription, $meetingDatetime);
        if(!isSet($meetingData['data']['meeting_id']) || !$meetingData['data']['meeting_id']) {
            $this->log('Cannot create meeting', 'watchitoo');
            //$this->tlObj->unlock($teacherLessonId);
            return false;
        }

        return $meetingData['data']['meeting_id'];
    }


    private function copyPlayList($subjectId, $meetingId) {
        //TODO: copy Subject's meeting playlist to this one
        //Check if subjectId have meeting-id at all, if not - return
        return true;
    }

    private function getWatchitooUserId($userId, $subjectId=null, $createNewUser=true) {
        //Student
        if(is_null($subjectId)) {
            $this->wluObj->recursive = -1;
            $wUserData = $this->wluObj->findByUserId($userId);
            if($wUserData) {
                $wUserData = $wUserData['WatchitooLessonUser'];
            }
        } else { //Subject teacher
            $this->wstObj->recursive = -1;
            $wUserData = $this->wstObj->findByUserIdAndSubjectId($userId, $subjectId);
            if($wUserData) {
                $wUserData = $wUserData['WatchitooSubjectTeacher'];
            }
        }

        if(!$wUserData && $createNewUser) {
            $this->log('No watchitoo user found, create a new one', 'watchitoo');
            return $this->createWatchitooUser($userId, $subjectId);
        }

        return $wUserData['watchitoo_user_id'];
    }

    private function createWatchitooUser($userId, $subjectId=null) {
        $this->wluObj->User->recursive = -1;
        $userData = $this->wluObj->User->findByUserId($userId);
        if(!$userData) {
            $this->log('User not found', 'watchitoo');
            //User not found
            return false;
        }

        //Create user on Watchitoo, email is changes to universito_email - in order to avoid existing users
        $wUserData = $this->wObj->saveUser(null, 'test8_universito_'.($subjectId ? $subjectId.'_' : null).$userData['User']['email'], $this->getUserPassword($userId), $userData['User']['first_name'], $userData['User']['last_name']);
        if(!isSet($wUserData['data']['user_id']) || !$wUserData['data']['user_id']) {
            $this->log('Cannot create watchitoo user', 'watchitoo');
            return false;
        }

        //Create link user_id-watchitoo_user_id
        $userObj = is_null($subjectId) ? $this->wluObj   : $this->wstObj;
        $userObj->create(false);
        $userObj->set(array('user_id'=>$userId, 'watchitoo_user_id'=>$wUserData['data']['user_id'], 'subject_id'=>$subjectId));
        if(!$userObj->save()) {
            $this->log('Cannot create user_id-watchitoo_user_id link', 'watchitoo');
            return false;
        }


        return $wUserData['data']['user_id'];
    }

    public static function getUserPassword($userId) {
        return $userId*100;
    }


    private function getSubjectData($subjectId) {
        $this->Subject->recursive = -1;

        //Convert time to Watchitoo's time
        $tz = Configure::read('Config.timezone');
        Configure::write('Config.timezone', 'GMT');

        $Data = $this->Subject->findBySubjectId($subjectId);

        Configure::write('Config.timezone', $tz);
        if(!$Data) {
            return false;
        }

        return $Data['Subject'];
    }
    private function getTLData($teacherLessonId) {
        //Get tl data
        $this->tlObj->recursive = -1;

        //Convert time to Watchitoo's time
        $tz = Configure::read('Config.timezone');
        Configure::write('Config.timezone', 'GMT');

        $tlData = $this->tlObj->findByTeacherLessonId($teacherLessonId);
        Configure::write('Config.timezone', $tz);
        if(!$tlData) {
            return false;
        }

        return $tlData['TeacherLesson'];
    }

}