<?php
class LivePaymentShell extends AppShell {
    public $uses = array('UserLesson', 'TeacherLesson', 'Subject');

    //C:\Users\Sivan>c:\Zend\Apache2\htdocs\studentskit\lib\Cake\Console\cake -app c:\Zend\Apache2\htdocs\studentskit\studentskit live_payment
    public function main() {
        //Init const
        $this->Subject;
        $this->TeacherLesson;

        //Build find conditions

        $conditions = array(
            $this->TeacherLesson->alias.'.lesson_type'      =>LESSON_TYPE_LIVE,
            $this->TeacherLesson->alias.'.payment_status'   =>PAYMENT_STATUS_PENDING,
            $this->TeacherLesson->alias.'.datetime <'       =>$this->TeacherLesson->timeExpression('now -'.Configure::read('transfer_cp_to_teacher_after_x_hours').' hour', false)); //72 hours old lesson

        $this->TeacherLesson->recursive = -1;
        $conditions = $this->TeacherLesson->getUnlockedRecordsFindConditions($conditions);

        //Check if payment needed
        $this->out('Finding next pending payment...');
        $paymentNeeded = $this->TeacherLesson->find('first', array('conditions'=>$conditions));

        $i=1;
        while($paymentNeeded) {
            $this->out( $i++.'. Payment needed for: '.'('.$paymentNeeded['TeacherLesson']['teacher_lesson_id'].') '.$paymentNeeded['TeacherLesson']['name']);
            //Lock record
            if(!$this->TeacherLesson->lock($paymentNeeded['TeacherLesson']['teacher_lesson_id'], 0)) {
                $this->out('Cannot lock! continue');
                continue;
            }

            //Pay
            $this->out('Processing payment...');
            $status = $this->TeacherLesson->pay($paymentNeeded['TeacherLesson']['teacher_lesson_id']);
            switch($status) {
                case PAYMENT_STATUS_DONE:
                    $this->out('Done');
                    break;
                case PAYMENT_STATUS_PARTIAL:
                    $this->out('Partial');
                    break;
                case PAYMENT_STATUS_ERROR:
                default:
                    $this->out('Error');
                    break;
            }

            //Release lock
            $this->TeacherLesson->unlock($paymentNeeded['TeacherLesson']['teacher_lesson_id']);

            //Find the next payment
            $paymentNeeded = $this->TeacherLesson->find('first', array('conditions'=>$conditions));
            $this->out('Finding next pending payment...');
        }
        $this->out('EXIT, no more pending payment');
    }
}
