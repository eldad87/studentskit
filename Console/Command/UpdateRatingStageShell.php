<?php
/**
 * 1. Go over all finished lessons
 * 2. Count how many successful students the lesson had
 * 3. Update subject counters: students_amount and total_lessons
 * 4. Update teacher profile teacher_total_teaching_minutes, teacher_students_amount, teacher_total_lessons and lesson stage
 * 5, Update student student_total_lessons, students_total_learning_minutes and lesson stage
 */
class UpdateRatingStageShell extends AppShell {
    public $uses = array('UserLesson', 'TeacherLesson', 'Subject');

    //c:\Zend\Apache2\htdocs\studentskit\lib\Cake\Console\cake -app c:\Zend\Apache2\htdocs\studentskit\studentskit update_rating_stage
    public function main() {
        //Init const
        $this->Subject;
        $this->TeacherLesson;



        //Build find conditions
        $conditions = array(
            'end_datetime < NOW()',
            'payment_status'=>array(PAYMENT_STATUS_DONE, PAYMENT_STATUS_NO_NEED, PAYMENT_STATUS_PARTIAL),
            'rating_status' =>RATING_STATUS_PENDING,

        );

        // 1
        $this->TeacherLesson->recursive = -1;
        $conditions = $this->TeacherLesson->getUnlockedRecordsFindConditions($conditions);


        $this->out('Finding ended lessons...');
        $teacherLessons = $this->TeacherLesson->find('all', array('conditions'=>$conditions, 'limit'=>10));
        $i=1;
        while($teacherLessons) {
            foreach($teacherLessons AS $teacherLesson) {

                $this->out( $i++.'. Ended lesson: '.'('.$teacherLesson['TeacherLesson']['teacher_lesson_id'].') '.$teacherLesson['TeacherLesson']['name']);
                //Lock record
                if(!$this->TeacherLesson->lock($teacherLesson['TeacherLesson']['teacher_lesson_id'], 0)) {
                    $this->out('Cannot lock! continue');
                    continue;
                }


                $this->out('Processing status...');

                // 2
                $this->UserLesson->recursive = -1;
                $userLessonsCount = $this->countUserLessonCandidates($teacherLesson['TeacherLesson']['teacher_lesson_id']);
                $this->out($userLessonsCount.' students found');

                // 3
                $this->out('Updating subject');
                $this->updateTeacherSubjectCounters($teacherLesson['TeacherLesson']['subject_id'], $userLessonsCount);


                // 4
                $this->out('Updating teacher lesson');
                $this->updateTeacherProfileAndLessonStatus($teacherLesson['TeacherLesson']['teacher_lesson_id'], $teacherLesson['TeacherLesson']['duration_minutes'], $userLessonsCount);

                // 5
                if($userLessonsCount) {
                    $this->out('Updating user lesson');
                    $this->updateUserProfileAndLessonStage($teacherLesson['TeacherLesson']['teacher_lesson_id'], $teacherLesson['TeacherLesson']['lesson_type'], $teacherLesson['TeacherLesson']['duration_minutes']);
                }

                //Release lock
                $this->TeacherLesson->unlock($teacherLesson['TeacherLesson']['teacher_lesson_id']);
            }
            // 1 Find the next payment
            $teacherLessons = $this->TeacherLesson->find('all', array('conditions'=>$conditions, 'limit'=>10));
            $this->out('Finding ended lessons...');
        }
        $this->out('EXIT, no ended lessons');
    }



    // Update teacher profile teacher_total_teaching_minutes, teacher_students_amount, teacher_total_lessons
    private function updateTeacherProfileAndLessonStatus( $teacherLessonId, $lessonDurationMinutes, $studentsAmount ) {
        $totalLessons = 1;
        if(!$studentsAmount) { //if no students - no need to increase counters
            $totalLessons = 0;
            $lessonDurationMinutes = 0;
            $studentsAmount = 0;
        }

        $this->TeacherLesson->create(false);

        $this->TeacherLesson->updateAll(array(
            $this->TeacherLesson->User->alias.'.teacher_total_teaching_minutes' =>$this->TeacherLesson->User->alias.'.teacher_total_teaching_minutes+'.$lessonDurationMinutes,
            $this->TeacherLesson->User->alias.'.teacher_students_amount'        =>$this->TeacherLesson->User->alias.'.teacher_students_amount+'.$studentsAmount,
            $this->TeacherLesson->User->alias.'.teacher_total_lessons'          =>$this->TeacherLesson->User->alias.'.teacher_total_lessons+'.$totalLessons,
            $this->TeacherLesson->alias.'.rating_status'                        =>RATING_STATUS_DONE,
        ),array(
            $this->TeacherLesson->alias.'.teacher_lesson_id'=>$teacherLessonId
        ));

        return $this->TeacherLesson->save();
    }

    // Update subject students_amount, total_lessons
    private function updateTeacherSubjectCounters( $subjectId, $studentsAmount ) {
        if(!$studentsAmount) { //if no students - no need to increase counters
            return true;
        }

        $this->Subject->create(false);
        $this->Subject->id = $subjectId;
        $this->Subject->recursive = -1;
        $this->Subject->set(array(
            'students_amount'   =>$this->TeacherLesson->getDataSource()->expression('students_amount'.  ' +'. $studentsAmount),
            'total_lessons'     =>$this->TeacherLesson->getDataSource()->expression('total_lessons'.    ' +1'),
        ));
        return $this->Subject->save();
    }

    // Update student student_total_lessons, students_total_learning_minutes
    private function updateUserProfileAndLessonStage($teacherLessonId, $lessonType, $lessonDurationMinutes) {
        $this->UserLesson->create(false);
        return $this->UserLesson->updateAll(array(
                $this->UserLesson->Student->alias.'.student_total_lessons'          =>$this->UserLesson->Student->alias.'.student_total_lessons+1',
                $this->UserLesson->Student->alias.'.students_total_learning_minutes'=>$this->UserLesson->Student->alias.'.students_total_learning_minutes +'.$lessonDurationMinutes,
                $this->UserLesson->alias.'.stage'                                   =>($lessonType==LESSON_TYPE_LIVE ? USER_LESSON_PENDING_RATING : USER_LESSON_PENDING_STUDENT_RATING) //teacher can't rate student on video lesson
            ), array(
                $this->UserLesson->alias.'.payment_status'    =>array(PAYMENT_STATUS_DONE, PAYMENT_STATUS_NO_NEED),
                $this->UserLesson->alias.'.stage'             =>USER_LESSON_ACCEPTED,
                $this->UserLesson->alias.'.teacher_lesson_id' =>$teacherLessonId)
        );
    }

    // Get all successful students in lesson
    private function countUserLessonCandidates($teacherLessonId) {
        $this->UserLesson->recursive = -1;
        return $this->UserLesson->find('count', array('conditions'=>array('payment_status'  =>array(PAYMENT_STATUS_DONE, PAYMENT_STATUS_NO_NEED),
            'stage'             =>USER_LESSON_ACCEPTED,
            'teacher_lesson_id' =>$teacherLessonId),
        ));
    }
}
