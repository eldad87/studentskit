<?php
App::uses('CakeEventManager', 'Event');
/**
 *@property Notification $notification
 */
class TeacherLessonEventListener implements CakeEventListener {
    private static $m_pInstance;

    private function __construct() {
    //public function UserLessonEventListener() {
        App::import('Model', 'Notification');
        App::import('Model', 'AdaptivePayment');

        $this->notification = New Notification();
        $this->adaptivePayment = new AdaptivePayment();
    }

    public static function &getInstance() {
        if (!self::$m_pInstance) {
            self::$m_pInstance = new self();
        }

        return self::$m_pInstance;
    }


    public function implementedEvents() {
        return array(
            'Model.TeacherLesson.afterAdd'       => 'afterAdd',
        );
    }

    public function afterAdd(CakeEvent $event) {
        //Create meeting on Watchitoo
        App::import('Vendor', 'Watchitoo'.DS.'Watchitoo');
        $wcObj = new Watchitoo();
        return $wcObj->getMeetingSettings($event->data['teacher_lesson']['teacher_lesson_id']) ? true : false;
    }
}
?>