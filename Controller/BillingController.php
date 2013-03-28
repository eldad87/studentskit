<?php
class BillingController extends AppController {
	public $name = 'Billing';
	public $components = array('Session', 'RequestHandler', 'Auth'=>array('loginAction'=>array('controller'=>'Accounts','action'=>'login')), 'Security');
    public $uses = array('BillingHistory');

    public function index($limit=5, $page=1, $teacherLessonId=null) {
        //$this->TeacherLesson->recursive = -1;
        if($teacherLessonId) {
            $billingHistory = $this->BillingHistory->find('all', array(
                                                                        'conditions'=>array('BillingHistory.teacher_lesson_id'=>$teacherLessonId, 'BillingHistory.user_id'=>$this->Auth->user('user_id')),
                                                                        'order'=>'created desc'
                                                                    ));
        } else {
            $billingHistory = $this->BillingHistory->find('all',
                                                            array(
                                                                'conditions'=>array(
                                                                    'BillingHistory.user_id'=>$this->Auth->user('user_id')
                                                                ),
                                                                'limit'=>$limit,
                                                                'page'=>$page,
                                                                'order'=>'created desc'
                                                            )

            );
        }

        $this->set('billingHistory', $billingHistory);
        $this->set('limit', $limit);
        $this->set('page', $page);

        return $this->success(1, array('billingHistory'=>$billingHistory));
    }
}
