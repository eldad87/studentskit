<?php
class BillingController extends AppController {
	public $name = 'Billing';
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')), 'Security');
    public $uses = array('TeacherLesson', 'AdaptivePayment');

    public function testPreapprovalDetails() {
        $key='PA-2YT36131YS856670L';
        $info = $this->AdaptivePayment->preapprovalDetails($key);
        pr($info); die;
    }
    public function testPaymentDetails() {
        $key='PA-2YT36131YS856670L';
        $ul = 17;
        $info = $this->AdaptivePayment->paymentDetails(null, $ul);
        pr($info); die;
    }

    public function testPaid($teacherLessonId) {
        $a = 1;

        $returnUrl = Configure::read('public_domain').'/';
        $status = $this->TeacherLesson->pay($teacherLessonId, $returnUrl, $returnUrl);
        echo $status;
        die;
    }

    public function index($limit=5, $page=1) {
        //$this->TeacherLesson->recursive = -1;
        $billingHistory = $this->TeacherLesson->find('all',
                                                        array(
                                                            'conditions'=>array(
                                                                'teacher_user_id'=>$this->Auth->user('user_id'),
                                                                'payment_status NOT'=>array(PAYMENT_STATUS_NO_NEED, PAYMENT_STATUS_PENDING)
                                                            ),/*
                                                            'fields'=>array('teacher_lesson_id', 'image', 'name', 'description', 'max_students',
                                                                            'payment_status', 'payment_success_transactions_count', 'payment_per_student_price', 'payment_per_student_commission',
                                                                            'full_group_student_price', 'full_group_total_price', 'duration_minutes'),*/
                                                            'limit'=>$limit,
                                                            'page'=>$page
                                                        )

        );

        $this->set('billingHistory', $billingHistory);
        $this->set('limit', $limit);
        $this->set('page', $page);

        return $this->success(1, array('billingHistory'=>$billingHistory));
    }
}
