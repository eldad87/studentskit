<?php
define('NOTIFICATION_STATUS_PENDING', 0);
define('NOTIFICATION_STATUS_10_MIN_SENT', 1);
define('NOTIFICATION_STATUS_1_HOUR_SENT', 2);
define('NOTIFICATION_STATUS_DONE', 3);

/**
 * Find teacher lessons
 * email teacher
 * find students
 * email students
 *
 * In order to add more emails - just add them to getSettings and create the matching consts + implement in emailTeacher/emailUser
 */
class LessonNotificationShell extends AppShell {
    public $uses = array('UserLesson', 'TeacherLesson', 'Subject');

    //C:\Users\Sivan>c:\Zend\Apache2\htdocs\studentskit\lib\Cake\Console\cake -app c:\Zend\Apache2\htdocs\studentskit\studentskit lesson_notification
    public function main() {
        $this->stdout->styles('flashy', array('text' => 'magenta', 'blink' => false));

        if(!isSet($this->args[0])) {
            $this->out('<flashy>Error:</flashy> Please provide a notifycation type, I.e "1 hour"');
            exit;
        }
        $notificationSettings = $this->getSettings($this->args[0]);

        if(empty($this->args) || !$notificationSettings) {
            $this->out('<flashy>Error:</flashy> notification type not found');
            exit;
        }

        App::uses('CakeEmail', 'Network/Email');

        $this->out('Checking if need to send emails...');

        //Find TeacherLessons
        $pendingTeacherLessons = $this->getTeacherLessonCandidates($notificationSettings['before_notification_status'], $notificationSettings['starts_in']);
        if(!$pendingTeacherLessons) {
            $this->out('No pending lessons');
        }

        while($pendingTeacherLessons) {
            $this->out(count($pendingTeacherLessons).' Pending lessons');

            $i=1;
            foreach($pendingTeacherLessons AS $pendingTeacherLesson) {
                $teacherUserData = $pendingTeacherLesson['User'];
                $pendingTeacherLesson = $pendingTeacherLesson['TeacherLesson'];
                $this->out($i.'. Students count ('.$pendingTeacherLesson['num_of_students'].') for: '.'('.$pendingTeacherLesson['teacher_lesson_id'].') "'.$pendingTeacherLesson['name'].'"');

                //Lock TeacherLesson
                if(!$this->TeacherLesson->lock($pendingTeacherLesson['teacher_lesson_id'], 0)) {
                    $this->out('Cannot lock');
                    continue;
                }


                //Email teacher
                if(!$this->notifyTeacher($pendingTeacherLesson, $notificationSettings, $teacherUserData)) {
                    $this->out('Cannot notify teacher');
                    $this->TeacherLesson->unlock($pendingTeacherLesson['teacher_lesson_id']);
                    continue; //Try again later
                    //TODO: log
                }

                //Find UserLessons
                $userLessonCandidates = $this->getUserLessonCandidates($pendingTeacherLesson['teacher_lesson_id'], $notificationSettings['before_notification_status']);

                while($userLessonCandidates) {
                    foreach($userLessonCandidates AS $userLessonCandidate) {
                        //Email user
                        if(!$this->notifyUser(
                            $pendingTeacherLesson,
                            $notificationSettings,
                            $userLessonCandidate['Student'])) {

                            $this->out('Cannot notify user');
                            continue;
                            //TODO: log
                        }

                        //Mark that email sent
                        $this->setUserLessonStatus($userLessonCandidate['UserLesson']['user_lesson_id'], $notificationSettings['after_notification_status']);
                    }
                    //Find UserLessons
                    $userLessonCandidates = $this->getUserLessonCandidates($pendingTeacherLesson['teacher_lesson_id'], $notificationSettings['before_notification_status']);
                }


                //Mark that all email sent for that lesson/notification
                $this->setTeacherLessonStatus($pendingTeacherLesson['teacher_lesson_id'], $notificationSettings['after_notification_status']);

                $this->TeacherLesson->unlock($pendingTeacherLesson['teacher_lesson_id']);
            }

            //Find TeacherLessons
            $pendingTeacherLessons = $this->getTeacherLessonCandidates($notificationSettings['before_notification_status'], $notificationSettings['starts_in']);
        }

        $this->out('Done');

        $this->out('Affected rows: '.$this->UserLesson->getAffectedRows());
    }

    private function notifyTeacher($lessonData, $notificationSettings, $teacherData) {
        $this->out('Email Teacher: '.$teacherData['first_name'].' '.$teacherData['last_name']);

        try {
            $emailObj = new CakeEmail('gmail');
            $emailObj->helpers(array('Html', 'Layout'));
            $emailObj->viewVars(array(  'fullName'=>trim($teacherData['first_name'].' '.$teacherData['last_name']),
                                        'lessonData'=>$lessonData, 'isTeacher'=>true, 'startsInMin'=>$notificationSettings['starts_in_minutes'],
                                        'email'=>$teacherData['email'], 'userId'=>$teacherData['user_id']));
            $result = $emailObj->to($teacherData['email'])
                ->template('lesson_notification', 'default')
                ->domain(Configure::read('public_domain'))
                ->emailFormat('both')
                ->from('support@universito.com')
                ->subject('Teacher notification')
                ->send();

            $this->out('Sent: '.var_export($result));
            return $result;

        } catch(Exception $e) {
            $this->out('Exception: '.$e->getMessage());
            return false;
        }
    }
    private function notifyUser($lessonData, $notificationSettings, $UserData) {
        $this->out('Email User');

        try {
            $emailObj = new CakeEmail('gmail');
            $emailObj->helpers(array('Html', 'Layout'));
            $emailObj->viewVars(array(  'fullName'=>trim($UserData['first_name'].' '.$UserData['last_name']),
                                        'lessonData'=>$lessonData, 'isTeacher'=>false, 'startsInMin'=>$notificationSettings['starts_in_minutes'],
                                        'email'=>$UserData['email'], 'userId'=>$UserData['user_id']));
            $result = $emailObj->to($UserData['email'])
                ->template('lesson_notification', 'default')
                ->domain(Configure::read('public_domain'))
                ->emailFormat('both')
                ->from('support@universito.com')
                ->subject('Teacher notification')
                ->send();

            $this->out('Sent: '.var_export($result));
            return $result;

        } catch(Exception $e) {
            $this->out('Exception: '.$e->getMessage());
            return false;
        }
    }

    private function setUserLessonStatus($userLessonId, $notificationStatus) {
        $this->UserLesson->create(false);
        $this->UserLesson->id = $userLessonId;
        return $this->UserLesson->save(array('notification_status'=>$notificationStatus));
    }

    private function setTeacherLessonStatus($teacherLessonId, $notificationStatus) {
        $this->TeacherLesson->create(false);
        $this->TeacherLesson->id = $teacherLessonId;
        $this->TeacherLesson->save(array('notification_status'=>$notificationStatus));
    }

    private function getSettings($notification) {
        $notification = strtolower($notification);

        switch($notification) {
            case '1 hour':
                return array(
                    'starts_in'                 => 'now +1 hour',
                    'before_notification_status'=> NOTIFICATION_STATUS_PENDING,
                    'after_notification_status' => NOTIFICATION_STATUS_1_HOUR_SENT,
                    'view'                      => 'notification',
                    'layout'                    => 'default',
                    'starts_in_minutes'         => 60
                );
                break;

            case '10 minutes':
                return array(
                    'starts_in'                 => 'now +10 minutes',
                    'before_notification_status'=> NOTIFICATION_STATUS_1_HOUR_SENT,
                    'after_notification_status' => NOTIFICATION_STATUS_10_MIN_SENT,
                    'view'                      => 'notification',
                    'layout'                    => 'default',
                    'starts_in_minutes'         => 10
                );
                break;
        }

        return false;
    }

    private function getTeacherLessonCandidates($notificationStatus, $startsIn, $limit=10) {
        $this->Subject; // init const
        //$this->TeacherLesson->recursive = -1;
        $this->TeacherLesson->cacheQueries = false;
        $this->TeacherLesson->unbindAll(array('belongsTo'=>array('User')));
        $this->TeacherLesson->resetRelationshipFields();
        $this->TeacherLesson->recursive = 1;
        echo $this->TeacherLesson->timeExpression('now', false);
        $conditions = array(
            'TeacherLesson.lesson_type'         => LESSON_TYPE_LIVE,                                         // Only live lesson
            'TeacherLesson.datetime >='         => $this->TeacherLesson->timeExpression('now', false),       // Start in 1 hour from now
            'TeacherLesson.datetime <='         => $this->TeacherLesson->timeExpression($startsIn, false),    // Start in 1 hour from now
            'TeacherLesson.payment_status'      => array(PAYMENT_STATUS_NO_NEED, PAYMENT_STATUS_DONE, PAYMENT_STATUS_PARTIAL),      // Sending emails only after payment processed
            'TeacherLesson.notification_status' => $notificationStatus,                                      // Need to send emails
        );

        $conditions = $this->TeacherLesson->getUnlockedRecordsFindConditions($conditions);
        return $this->TeacherLesson->find('all', array( 'conditions'=>$conditions,
            'fields'=>array('TeacherLesson.teacher_lesson_id', 'TeacherLesson.name',
                            'TeacherLesson.lesson_type','TeacherLesson.subject_id',
                            'TeacherLesson.num_of_students',
                            'User.user_id','User.email', 'User.first_name', 'User.last_name' ),
            'limit'=>$limit));
    }
    private function getUserLessonCandidates($teacherLessonId, $notificationStatus) {
        $this->TeacherLesson; // init const
        $this->UserLesson->cacheQueries = false;
        $this->UserLesson->unbindAll(array('belongsTo'=>array('Student')));
        $this->UserLesson->resetRelationshipFields();
        $this->UserLesson->recursive = 1;
        $conditions = array(
            'teacher_lesson_id'             => $teacherLessonId,
            'stage'                         => USER_LESSON_ACCEPTED,
            'payment_status'                => array(PAYMENT_STATUS_NO_NEED, PAYMENT_STATUS_DONE),    // Sending emails only after payment processed
            'notification_status'           => $notificationStatus,                                   // Need to send emails
        );
        //$conditions = $this->TeacherLesson->getUnlockedRecordsFindConditions($conditions);
        return $this->UserLesson->find('all', array( 'conditions'=>$conditions,
            'fields'=>array('Student.user_id', 'Student.email', 'Student.first_name', 'Student.last_name',
                            'UserLesson.user_lesson_id'),
            'limit'=>10));
    }
}