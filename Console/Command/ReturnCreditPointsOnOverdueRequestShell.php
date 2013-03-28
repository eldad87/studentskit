<?php
/**
 * Return CP that allocated on UL when those requests are overdue.
 * I.e when a user make LIVE request a lesson to start on X date.
 *  when this X < NOW - the teacher is no longer able to approve this request, therefore, those CP will remain in the UL.
 * Class ReturnCreditPointsOnOverdueRequestShell
 */
class ReturnCreditPointsOnOverdueRequestShell extends AppShell {
    public $uses = array('UserLesson');

    public function main() {

        $uls = true;
        while($uls) {
            //Find if there are any UL
            $uls = $this->findOverdueRequestsUL();

            foreach($uls AS $ul) {
                $this->UserLesson->setTotalCreditPoints($ul['UserLesson']['user_lesson_id'], 0, PAYMENT_STATUS_RETURN_DUE_TO_OVERDUE_REQUEST);
            }

        }
    }


    private function findOverdueRequestsUL($limit=100) {
        App::import('Model', 'Subject');
        App::import('Model', 'TeacherLesson');
        $this->UserLesson->cacheQueries = false;
        $this->UserLesson->recursive = -1;
        return $this->UserLesson->find('all', array(
            'conditions'=>array(
                'lesson_type'   => LESSON_TYPE_LIVE,
                'datetime <'=> $this->UserLesson->timeExpression('now', false),
                'stage'         => array(   USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_PENDING_STUDENT_APPROVAL,
                    USER_LESSON_RESCHEDULED_BY_TEACHER, USER_LESSON_RESCHEDULED_BY_STUDENT),
                'payment_status'=> PAYMENT_STATUS_PENDING
            ),
            'limit'=>$limit,
            'fields'=>array('user_lesson_id')
        ));
    }
}