<?php
class SupportController extends AppController {
    public $name = 'Support';
    public $uses = array('Contact');

    public function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow(array('index','about', 'contact', 'FAQ', 'termsAndConditions', 'privacyAndPolicy', 'refundPolicy'));
    }


    public function index() {
        $this->redirect(array('action'=>'about'));
    }

    public function about() {
    }

    public function contact() {
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

                $result = $emailObj->to('support@universito.com')
                    ->template('contact', 'default')
                    ->domain(Configure::read('public_domain'))
                    ->emailFormat('both')
                    ->from('support@universito.com')
                    ->subject($contact['subject'])
                    ->send();

                $this->set('sent', ($result ? true : false));
            }
        }

    }

    public function FAQ() {

    }

    public function termsAndConditions() {

    }

    public function privacyAndPolicy() {

    }

    public function refundPolicy() {

    }
}
?>