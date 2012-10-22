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
    private $wmObj;
    private $tlObj;

    public function __construct() {


        App::import('Vendor', 'Watchitoo'.DS.'WatchitooService');
        App::import('Model', 'TeacherLesson');
        App::import('Model', 'WatchitooMeeting');
        App::import('Model', 'WatchitooUser');
        App::import('Model', 'WatchitooSubjectTeacher');

        $this->wObj  = new WatchitooService();
        $this->wmObj = new WatchitooMeeting();
        $this->wuObj = new WatchitooUser();
        $this->wstObj = new WatchitooSubjectTeacher();
        $this->tlObj = new TeacherLesson();

    }

    public function getMeetingSettings($teacherLessonId, $userId) {
        $tlData = $this->getTLData($teacherLessonId);
        $wUID = $this->getWatchitooUserId($userId, $tlData['teacher_user_id']==$userId ? $tlData['subject_id'] : null);
        $this->wuObj->User->recursive = -1;
        $userData = $this->wuObj->User->findByUserId($userId);


        if(!$tlData || !$wUID || !$userData) {
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
        );
    }

    public function getMeetingId($teacherLessonId) {
        //Check if tl already have a meeting
        $this->wmObj->recursive = -1;
        $this->wmObj->cacheQueries = false;
        $wmData = $this->wmObj->findByTeacherLessonId($teacherLessonId);
        if($wmData) {
            $this->log('Meeting found', 'watchitoo');
            return $wmData['WatchitooMeeting']['meeting_id'];
        }
        $this->log('Meeting NOT found', 'watchitoo');

        return $this->createMeeting( $teacherLessonId );
    }


    private function createMeeting( $teacherLessonId ) {
        $this->log('Create a meeting', 'watchitoo');

        if(!$this->tlObj->lock($teacherLessonId, 0)) {
            $this->log('Cannot lock meeting', 'watchitoo');
            return false;
        }

        //Check if meeting was-not-created yet
        $this->wmObj->recursive = -1;
        $this->wmObj->cacheQueries = false;
        $wmData = $this->wmObj->findByTeacherLessonId($teacherLessonId);
        if($wmData) {
            $this->tlObj->unlock($teacherLessonId);
            $this->log('Meeting was created by a parallel thread', 'watchitoo');
            return $wmData['WatchitooMeeting']['meeting_id'];
        }

        //Get TL data
        $tlData = $this->getTLData($teacherLessonId);
        if(!$tlData) {
            $this->log('Cannot find TL', 'watchitoo');
            $this->tlObj->unlock($teacherLessonId);
            //No TL found
            return false;
        }

        //Get Watchitoo's user_id
        $this->log('Get watchitoo user_id', 'watchitoo');
        $wUID = $this->getWatchitooUserId($tlData['teacher_user_id'], $tlData['subject_id']);
        if(!$wUID) {
            $this->log('Cannot create watchitoo user_id', 'watchitoo');
            $this->tlObj->unlock($teacherLessonId);
            return false;
        }



        //Create meeting
        $this->log('Create meeting', 'watchitoo');
        $meetingData = $this->wObj->saveMeeting(null, $wUID, $tlData['name'], $tlData['description'], $tlData['datetime']);
        if(!isSet($meetingData['data']['meeting_id']) || !$meetingData['data']['meeting_id']) {
            $this->log('Cannot create meeting', 'watchitoo');
            $this->tlObj->unlock($teacherLessonId);
            return false;
        }

        //Copy playlist
        $this->copyPlayList($tlData['subject_id'], $meetingData['data']['meeting_id']);

        //Create link meeting_id-teacher_lesson_id
        $this->wmObj->create(false);
        $this->wmObj->set(array('teacher_lesson_id'=>$teacherLessonId, 'meeting_id'=>$meetingData['data']['meeting_id']));
        if(!$this->wmObj->save()) {
            $this->log('Cannot create meeting-TL link', 'watchitoo');
            $this->tlObj->unlock($teacherLessonId);
            return false;
        }

        $this->tlObj->unlock($teacherLessonId);

        return $meetingData['data']['meeting_id'];
    }

    private function copyPlayList($subjectId, $meetingId) {
        //TODO: copy Subject's meeting playlist to this one
        return true;
    }

    private function getWatchitooUserId($userId, $subjectId=null, $createNewUser=true) {
        //Student
        if(is_null($subjectId)) {
            $this->wuObj->recursive = -1;
            $wUserData = $this->wuObj->findByUserId($userId);
            if($wUserData) {
                $wUserData = $wUserData['WatchitooUser'];
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
        $this->wuObj->User->recursive = -1;
        $userData = $this->wuObj->User->findByUserId($userId);
        if(!$userData) {
            $this->log('User not found', 'watchitoo');
            //User not found
            return false;
        }

        //Create user on Watchitoo, email is changes to universito_email - in order to avoid existing users
        $wUserData = $this->wObj->saveUser(null, 'test3_universito_'.($subjectId ? $subjectId.'_' : null).$userData['User']['email'], $this->getUserPassword($userId), $userData['User']['first_name'], $userData['User']['last_name']);
        if(!isSet($wUserData['data']['user_id']) || !$wUserData['data']['user_id']) {
            $this->log('Cannot create watchitoo user', 'watchitoo');
            return false;
        }

        //Create link user_id-watchitoo_user_id
        $userObj = is_null($subjectId) ? $this->wuObj : $this->wstObj;
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