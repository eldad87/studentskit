<?php
class ExpressCheckout extends AppModel {
    public $name = 'ExpressCheckout';
    public $useTable = 'express_checkout';
    public $primaryKey = 'express_checkout_id';

    public $belongsTo = array(
        'PendingUserLesson' => array(
            'className' => 'PendingUserLesson',
            'foreignKey'=>'pending_user_lesson_id'
        )
    );

    public $actsAs = array( 'Lock' );
}