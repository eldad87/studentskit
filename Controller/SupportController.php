<?php
class SupportController extends AppController {
    public $name = 'Support';
    public $uses = array('Contact');

    public function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow(array('index','about', 'contact', 'FAQ', 'termsOfUse', 'privacyAndPolicy', 'dosAndDonts', 'ip', 'privacyAndPolicy'));
    }


    public function index() {
        $this->redirect(array('action'=>'about'));
    }

    public function about() {
    }

    public function contact() {
        //tell if this called from popup
        $this->set('ajax', $this->request->is('ajax'));

        //If topic is provided, then use hidden field - otherwise, show dropdown
        $this->set('topic', $this->request->query('topic'));

        $this->set('topics', $this->Contact->getTopics());
        $this->set('post', false);
        if (!empty($this->request->data)) {

            $this->set('post', true);
            $this->Contact->set($this->request->data);
            if($this->Contact->validates()) {
                $contact = $this->request->data['Contact'];

                App::uses('CakeEmail', 'Network/Email');
                $emailObj = new CakeEmail('gmail');

                $emailObj->helpers(array('Html', 'Layout'));
                $emailObj->viewVars($contact);

                try {
                    $result = $emailObj->to('support@universito.com')
                        ->template('contact', 'default')
                        ->domain(Configure::read('public_domain'))
                        ->emailFormat('both')
                        ->from('support@universito.com')
                        ->subject($contact['subject'])
                        ->send();
                } catch (Exception $e) {
                    //TODO: log
                    $result = false;
                }

                $this->set('sent', ($result ? true : false));
            }
        } else {
            $subject = $this->request->query('subject');//Default subject
            if($subject) {
                $this->request->data['Contact']['subject'] = $subject;
            }

        }

    }

    public function FAQ() {

    }

    public function termsOfUse() {

    }

    public function privacyAndPolicy() {

    }

    public function dosAndDonts() {

    }
    public function ip() {

    }
}
?>