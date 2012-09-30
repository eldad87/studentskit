<?php
define('NOTIFICATION_STATUS_PENDING', 0);
define('NOTIFICATION_STATUS_10_MIN_SENT', 1);
define('NOTIFICATION_STATUS_1_HOUR_SENT', 2);
define('NOTIFICATION_STATUS_DONE', 3);

/**
 * In order to add more emails - just add them to getSettings and create the matching consts
 */
class LessonNotificationShell extends AppShell {
    public $uses = array('UserLesson', 'TeacherLesson', 'Subject');

    //C:\Users\Sivan>c:\Zend\Apache2\htdocs\studentskit\lib\Cake\Console\cake -app c:\Zend\Apache2\htdocs\studentskit\studentskit lesson_notification
    public function main() {
        if(empty($this->args) || ($this->args[0]!='1 hour' && $this->args[0]!='10 minutes')) {
            $this->stdout->styles('flashy', array('text' => 'magenta', 'blink' => true));

            $this->out('<flashy>Error:</flashy> please select "1 hour" or "10 minutes" notifications');
            exit;
        }
        $settings = $this->getSettings($this->args[0]);

        $this->out('Checking if need to send emails...');

        //Find TeacherLessons
        $pendingTeacherLessons = $this->getTeacherLessonCandidates($settings['before_notification_status'], $settings['end_time']);
        if(!$pendingTeacherLessons) {
            $this->out('No pending lessons');
        }

        while($pendingTeacherLessons) {
            $this->out(count($pendingTeacherLessons).' Pending lessons');

            $i=1;
            foreach($pendingTeacherLessons AS $pendingTeacherLesson) {
                $teacherUserData = $pendingTeacherLesson['User'];
                $pendingTeacherLesson = $pendingTeacherLesson['TeacherLesson'];
                $this->out($i.'. Notifications needed ('.$pendingTeacherLesson['num_of_students'].') for: '.'('.$pendingTeacherLesson['teacher_lesson_id'].') '.$pendingTeacherLesson['name']);

                //Lock TeacherLesson
                if(!$this->TeacherLesson->lock($pendingTeacherLesson['teacher_lesson_id'], 0)) {
                    $this->out('Cannot lock');
                    continue;
                }

                //Email teacher
                $this->emailTeacher($pendingTeacherLesson, $teacherUserData);

                //Find UserLessons
                $userLessonCandidates = $this->getUserLessonCandidates($pendingTeacherLesson['teacher_lesson_id'], $settings['before_notification_status']);

                while($userLessonCandidates) {
                    foreach($userLessonCandidates AS $userLessonCandidate) {
                        //Email user
                        $this->emailUser($pendingTeacherLesson,
                                            $userLessonCandidate['UserLesson']['user_lesson_id'], $userLessonCandidate['UserLesson']['student_user_id'],
                                            $userLessonCandidate['Student']['email'],
                                            $userLessonCandidate['Student']['first_name'], $userLessonCandidate['Student']['last_name']);

                        //Mark that email sent
                        $this->setUserLessonStatus($userLessonCandidate['UserLesson']['user_lesson_id'], $settings['after_notification_status']);
                    }
                    //Find UserLessons
                    $userLessonCandidates = $this->getUserLessonCandidates($pendingTeacherLesson['teacher_lesson_id'], $settings['before_notification_status']);
                }


                //Mark that all email sent for that lesson/notification
                $this->setTeacherLessonStatus($pendingTeacherLesson['teacher_lesson_id'], $settings['after_notification_status']);

                $this->TeacherLesson->unlock($pendingTeacherLesson['teacher_lesson_id']);
            }

            //Find TeacherLessons
            $pendingTeacherLessons = $this->getTeacherLessonCandidates($settings['before_notification_status'], $settings['end_time']);
        }

        $this->out('Done');

        $this->out('Affected rows: '.$this->UserLesson->getAffectedRows());
    }

    private function emailTeacher($lessonData, $teacherData) {
        $this->out('Email Teacher');
        pr($lessonData);
        pr($teacherData);
    }
    private function emailUser($lessonData, $userLessonId, $userId, $email, $firstName, $lastName) {
        $this->out('Email User');
        $this->out('User Lesson Id: '.$userLessonId);
        $this->out('User ID: '.$userId);
        $this->out('Email: '.$email);
        $this->out('First Name: '.$firstName);
        $this->out('Last Name: '.$lastName);
        pr($lessonData);
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
        switch($notification) {
            case '1 hour':
                return array(
                    'end_time'=>'now +1 hour',
                    'before_notification_status'=>NOTIFICATION_STATUS_PENDING,
                    'after_notification_status'=>NOTIFICATION_STATUS_1_HOUR_SENT
                );
                break;
                return array(
                    'end_time'=>'now +10 minutes',
                    'before_notification_status'=>NOTIFICATION_STATUS_1_HOUR_SENT,
                    'after_notification_status'=>NOTIFICATION_STATUS_10_MIN_SENT
                );
                break;
        }
    }

    private function getTeacherLessonCandidates($notificationStatus, $endTime) {
        $this->Subject; // init const
        //$this->TeacherLesson->recursive = -1;
        $this->TeacherLesson->cacheQueries = false;
        $this->TeacherLesson->unbindAll(array('belongsTo'=>array('User')));
        $this->TeacherLesson->resetRelationshipFields();
        $this->TeacherLesson->recursive = 1;
        $conditions = array(
            'TeacherLesson.lesson_type'           =>LESSON_TYPE_LIVE,                                         // Only live lesson
            'TeacherLesson.datetime >='           =>$this->TeacherLesson->timeExpression('now', false),       // Start in 1 hour from now
            'TeacherLesson.datetime <='           =>$this->TeacherLesson->timeExpression($endTime, false),    // Start in 1 hour from now
            'NOT'=>array(
                'TeacherLesson.payment_status'    =>array(PAYMENT_STATUS_PENDING, PAYMENT_STATUS_ERROR),      // Sending emails only after payment processed
                ),
            'TeacherLesson.notification_status'   =>$notificationStatus,                                      // Need to send emails
        );
        $conditions = $this->TeacherLesson->getUnlockedRecordsFindConditions($conditions);
        return $this->TeacherLesson->find('all', array( 'conditions'=>$conditions,
                                                        'fields'=>array('TeacherLesson.teacher_lesson_id', 'TeacherLesson.num_of_students', 'TeacherLesson.name',
                                                                        'User.email', 'User.first_name', 'User.last_name', 'User.email' ),
                                                        'limit'=>10));
    }
    private function getUserLessonCandidates($teacherLessonId, $notificationStatus) {
        $this->TeacherLesson; // init const
        $this->UserLesson->cacheQueries = false;
        $this->UserLesson->unbindAll(array('belongsTo'=>array('Student')));
        $this->UserLesson->resetRelationshipFields();
        $this->UserLesson->recursive = 1;
        $conditions = array(
            'teacher_lesson_id'     =>$teacherLessonId,
            'stage'                 =>USER_LESSON_ACCEPTED,
            'NOT'=>array(
                'payment_status'     =>array(PAYMENT_STATUS_PENDING, PAYMENT_STATUS_ERROR),  // Sending emails only after payment processed
            ),
            'notification_status'   =>$notificationStatus,                                   // Need to send emails
        );
        //$conditions = $this->TeacherLesson->getUnlockedRecordsFindConditions($conditions);
        return $this->UserLesson->find('all', array( 'conditions'=>$conditions,
                                                        'fields'=>array('UserLesson.user_lesson_id', 'UserLesson.student_user_id', 'Student.email', 'Student.first_name', 'Student.last_name'),
                                                        'limit'=>10));
    }
}