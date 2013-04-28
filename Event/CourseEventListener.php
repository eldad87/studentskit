<?php
App::uses('CakeEventManager', 'Event');
/**
 *@property Notification $notification
 */
class CourseEventListener implements CakeEventListener {
    private static $m_pInstance;

    private function __construct() {
        //public function UserLessonEventListener() {
        App::import('Model', 'Notification');

        $this->notification = New Notification();
    }

    public static function &getInstance() {
        if (!self::$m_pInstance) {
            self::$m_pInstance = new self();
        }

        return self::$m_pInstance;
    }


    public function implementedEvents() {
        return array(
            'Model.Course.afterAdd'       => 'afterAdd',
        );
    }

    public function afterAdd(CakeEvent $event) {
        //TODO: save into solr
    }
}
?>