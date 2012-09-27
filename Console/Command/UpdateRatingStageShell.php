<?php
class UpdateRatingStageShell extends AppShell {
    public $uses = array('UserLesson', 'TeacherLesson', 'Subject');

    //C:\Users\Sivan>c:\Zend\Apache2\htdocs\studentskit\lib\Cake\Console\cake -app c:\Zend\Apache2\htdocs\studentskit\studentskit update_rating_stage
    public function main() {
        $this->out('Updating rating stage...');
        $this->UserLesson->TeacherLesson; // init const
        $this->UserLesson->recursive = -1;
        $this->UserLesson->updateAll(array('stage'=>USER_LESSON_PENDING_RATING), array(
            'UserLesson.end_datetime < NOW()',
            'UserLesson.stage'=>USER_LESSON_ACCEPTED,
            'OR'=>array(array('UserLesson.payment_status'=>PAYMENT_STATUS_DONE),
                array('UserLesson.payment_status'=>PAYMENT_STATUS_NO_NEED))
        ));

        $this->out('Affected rows: '.$this->UserLesson->getAffectedRows());
    }
}
